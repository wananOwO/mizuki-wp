import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const repoRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const auditPath = path.join(repoRoot, 'tools', 'audit-local.mjs');
const auditSource = fs.readFileSync(auditPath, 'utf8');

assert.match(
	auditSource,
	/export\s+function\s+resolveAuditPaths\s*\(/,
	'audit-local must expose side-effect-free route/log helpers for regression coverage',
);

const {
	buildRouteAuditTargets,
	captureLogOffsets,
	findForbiddenDiagnostics,
	readAppendedLog,
	resolveAuditPaths,
	validateRouteResponse,
} = await import(`${pathToFileURL(auditPath).href}?regression=${Date.now()}`);

const fixtureRoot = fs.mkdtempSync(path.join(os.tmpdir(), 'mizuki-audit-regression-'));

try {
	const olderContainer = path.join(fixtureRoot, 'mizuki-wp-native-wordpress-older');
	const newerContainer = path.join(fixtureRoot, 'mizuki-wp-native-wordpress-newer');
	const olderRoot = path.join(olderContainer, 'wordpress');
	const newerRoot = path.join(newerContainer, 'wordpress');
	fs.mkdirSync(path.join(olderRoot, 'wp-content'), { recursive: true });
	fs.mkdirSync(path.join(newerRoot, 'wp-content'), { recursive: true });
	fs.utimesSync(olderContainer, new Date('2026-07-09T00:00:00Z'), new Date('2026-07-09T00:00:00Z'));
	fs.utimesSync(newerContainer, new Date('2026-07-10T00:00:00Z'), new Date('2026-07-10T00:00:00Z'));

	const discovered = resolveAuditPaths({ env: {}, tempDir: fixtureRoot });
	assert.equal(discovered.wordpressRoot, newerRoot, 'the newest native WordPress fixture is selected');
	assert.equal(discovered.wpDebugLog, path.join(newerRoot, 'wp-content', 'debug.log'));
	assert.equal(discovered.phpErrorLog, path.join(fixtureRoot, 'mizuki-wp-php-error.log'));

	const customDebug = path.join(fixtureRoot, 'custom-wp-debug.log');
	const customPhpError = path.join(fixtureRoot, 'custom-php-error.log');
	const overridden = resolveAuditPaths({
		env: {
			MIZUKI_WP_ROOT: olderRoot,
			MIZUKI_WP_DEBUG_LOG: customDebug,
			MIZUKI_PHP_ERROR_LOG: customPhpError,
		},
		tempDir: fixtureRoot,
	});
	assert.deepEqual(overridden, {
		wordpressRoot: olderRoot,
		wpDebugLog: customDebug,
		phpErrorLog: customPhpError,
	});

	const wpDebugLog = path.join(newerRoot, 'wp-content', 'debug.log');
	const phpErrorLog = path.join(fixtureRoot, 'mizuki-wp-php-error.log');
	fs.writeFileSync(wpDebugLog, '[old] PHP Warning: historical only\n旧日志\n');
	fs.writeFileSync(phpErrorLog, '[old] clean server output\n');
	const offsets = captureLogOffsets({ wpDebugLog, phpErrorLog });
	assert.equal(offsets.wpDebugLog, fs.statSync(wpDebugLog).size, 'offsets use bytes, not characters');
	assert.equal(offsets.phpErrorLog, fs.statSync(phpErrorLog).size);

	const ordinaryServerLines = [
		'[Fri Jul 10 08:00:00 2026] 127.0.0.1:54321 Accepted',
		'[Fri Jul 10 08:00:00 2026] 127.0.0.1:54321 [200]: GET /',
		'[Fri Jul 10 08:00:00 2026] 127.0.0.1:54321 Closing',
	].join('\n');
	fs.appendFileSync(wpDebugLog, ordinaryServerLines);
	fs.appendFileSync(phpErrorLog, '\n[Fri Jul 10 08:00:01 2026] PHP Deprecated: new failure\n');

	const wpDelta = readAppendedLog(wpDebugLog, offsets.wpDebugLog);
	const phpDelta = readAppendedLog(phpErrorLog, offsets.phpErrorLog);
	assert.equal(wpDelta, ordinaryServerLines, 'only bytes appended after audit start are returned');
	assert.deepEqual(findForbiddenDiagnostics(wpDelta), [], 'normal PHP -S request lines are allowed');
	assert.match(phpDelta, /new failure/);
	assert.deepEqual(findForbiddenDiagnostics(phpDelta), [
		'[Fri Jul 10 08:00:01 2026] PHP Deprecated: new failure',
	]);
	assert.deepEqual(findForbiddenDiagnostics([
		'PHP Warning: bad include',
		'PHP Fatal error: uncaught failure',
		'PHP Recoverable fatal error: invalid callback',
		'PHP Catchable fatal error: invalid argument',
		'PHP Parse error: unexpected token',
		'PHP Core Warning: startup configuration failed',
		'PHP Core Error: runtime initialization failed',
		'PHP Compile Warning: compile warning',
		'PHP Compile Error: compile failure',
		'PHP User Warning: application warning',
		'PHP User Notice: application notice',
		'PHP User Deprecated: deprecated application call',
		'PHP User Error: application error',
		'PHP Error: generic runtime error',
		'PHP Startup: extension initialization failed',
		'PHP Notice: undefined index',
		'PHP Deprecated: legacy call',
		'WordPress database error Table missing for query SELECT 1',
	].join('\n')), [
		'PHP Warning: bad include',
		'PHP Fatal error: uncaught failure',
		'PHP Recoverable fatal error: invalid callback',
		'PHP Catchable fatal error: invalid argument',
		'PHP Parse error: unexpected token',
		'PHP Core Warning: startup configuration failed',
		'PHP Core Error: runtime initialization failed',
		'PHP Compile Warning: compile warning',
		'PHP Compile Error: compile failure',
		'PHP User Warning: application warning',
		'PHP User Notice: application notice',
		'PHP User Deprecated: deprecated application call',
		'PHP User Error: application error',
		'PHP Error: generic runtime error',
		'PHP Startup: extension initialization failed',
		'PHP Notice: undefined index',
		'PHP Deprecated: legacy call',
		'WordPress database error Table missing for query SELECT 1',
	]);

	const missingAtStart = path.join(fixtureRoot, 'created-during-audit.log');
	assert.equal(captureLogOffsets({ missingAtStart }).missingAtStart, 0);
	fs.writeFileSync(missingAtStart, 'PHP Notice: created during audit\n');
	assert.equal(findForbiddenDiagnostics(readAppendedLog(missingAtStart, 0)).length, 1);

	const baseUrl = 'http://127.0.0.1:8888/';
	const routeTargets = buildRouteAuditTargets({
		baseUrl,
		postUrls: [
			`${baseUrl}first-post/`,
			`${baseUrl}second-post/`,
			`${baseUrl}third-post/#comments`,
			`${baseUrl}first-post/`,
			'https://external.example/post/',
		],
		categoryUrls: [
			`${baseUrl}category/news/`,
			`${baseUrl}category/notes/`,
			`${baseUrl}category/news/#top`,
		],
		searchQuery: 'mizuki audit',
	});
	assert.equal(routeTargets.length, 7, 'home, search, three posts, and two categories are audited');
	assert.deepEqual(routeTargets.map(({ kind }) => kind), [
		'home', 'search', 'post', 'post', 'post', 'category', 'category',
	]);
	assert.equal(routeTargets[1].url, `${baseUrl}?s=mizuki+audit`);
	assert.equal(routeTargets[4].url, `${baseUrl}third-post/`, 'hashes are removed before requesting');
	assert.throws(
		() => buildRouteAuditTargets({
			baseUrl,
			postUrls: [`${baseUrl}one/`, `${baseUrl}two/`],
			categoryUrls: [`${baseUrl}category/one/`, `${baseUrl}category/two/`],
		}),
		/at least 3 post URLs/,
	);

	assert.deepEqual(validateRouteResponse({
		url: `${baseUrl}first-post/`,
		status: 200,
		body: '<body class="wp-theme-mizuki-wp">ok</body>',
	}), []);
	assert.deepEqual(validateRouteResponse({
		url: `${baseUrl}broken/`,
		status: 503,
		body: '<html>maintenance</html>',
	}), [
		`${baseUrl}broken/ returned HTTP 503`,
		`${baseUrl}broken/ response does not contain wp-theme-mizuki-wp`,
	]);
} finally {
	const fixturePath = path.resolve(fixtureRoot);
	assert.ok(fixturePath.startsWith(path.resolve(os.tmpdir()) + path.sep));
	fs.rmSync(fixturePath, { recursive: true, force: true });
}

console.log('PASS: local audit route and appended-log gates.');

import assert from 'node:assert/strict';
import { mkdtempSync, mkdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const packer = join(root, 'tools', 'pack-theme.mjs');
const expectedVersion = JSON.parse(readFileSync(join(root, 'package.json'), 'utf8')).version;
const fixtureRoot = mkdtempSync(join(tmpdir(), 'mizuki-package-regression-'));
const sourceRoot = join(fixtureRoot, 'theme');
const themeRoot = join(sourceRoot, 'mizuki-wp');
const output = join(fixtureRoot, 'theme.zip');

let validateArchiveEntryNames;
let requiredThemeFiles;
try {
	({
		validateArchiveEntryNames,
		REQUIRED_THEME_FILES: requiredThemeFiles,
	} = await import('../tools/package-validation.mjs'));
} catch (error) {
	assert.fail(`package archive validation API is unavailable: ${error.message}`);
}

const validEntryNames = requiredThemeFiles.map((entry) => `mizuki-wp/${entry}`);
assert.doesNotThrow(() => validateArchiveEntryNames(validEntryNames, 'mizuki-wp'));
assert.throws(
	() => validateArchiveEntryNames([...validEntryNames, '../outside.php'], 'mizuki-wp'),
	/unsafe|traversal|root/i,
);
assert.throws(
	() => validateArchiveEntryNames([...validEntryNames, 'other-theme/index.php'], 'mizuki-wp'),
	/root|top-level/i,
);
assert.throws(
	() => validateArchiveEntryNames([...validEntryNames, 'mizuki-wp\\assets\\bad.css'], 'mizuki-wp'),
	/backslash|separator|unsafe/i,
);
assert.throws(
	() => validateArchiveEntryNames([...validEntryNames, 'mizuki-wp/FILTER-INTEGRATION.md'], 'mizuki-wp'),
	/development|documentation|forbidden/i,
);

function write(relativePath, contents = 'fixture') {
	const target = join(themeRoot, relativePath);
	mkdirSync(dirname(target), { recursive: true });
	writeFileSync(target, contents);
}

function runPacker() {
	return spawnSync(process.execPath, [packer], {
		cwd: root,
		env: {
			...process.env,
			SRC_DIR: sourceRoot,
			THEME: 'mizuki-wp',
			OUT: output,
		},
		encoding: 'utf8',
	});
}

try {
	write('style.css', `/*\nTheme Name: Mizuki\nVersion: ${expectedVersion}\n*/\n`);
	write('functions.php', "<?php\ndefine( 'MIZUKI_VERSION', '0.0.0' );\n");
	write('readme.txt', `Stable tag: ${expectedVersion}\n`);

	const result = runPacker();

	assert.notEqual(
		result.status,
		0,
		`packer accepted mismatched theme metadata:\n${result.stdout}${result.stderr}`,
	);
	assert.match(`${result.stdout}${result.stderr}`, /version/i);

	write('functions.php', `<?php\ndefine( 'MIZUKI_VERSION', '${expectedVersion}' );\n`);
	const missingAssets = runPacker();
	assert.notEqual(
		missingAssets.status,
		0,
		`packer accepted a theme without required runtime assets:\n${missingAssets.stdout}${missingAssets.stderr}`,
	);
	assert.match(`${missingAssets.stdout}${missingAssets.stderr}`, /missing|required|asset|font/i);

	for (const relativePath of [
		'index.php',
		'header.php',
		'footer.php',
		'inc/enqueue.php',
		'inc/parts/banner.php',
		'inc/parts/navbar.php',
		'assets/css/mizuki-main.css',
		'assets/css/mizuki-fonts.css',
		'assets/css/mizuki-layout-modes.css',
		'assets/css/mizuki-overrides.css',
		'assets/css/mizuki-settings-panel.css',
		'assets/js/banner-carousel.js',
		'assets/js/mizuki-theme.js',
		'assets/fonts/ZenMaruGothic-Medium.ttf',
		'assets/fonts/loli.ttf',
	]) {
		write(relativePath);
	}

	const incompleteRuntime = runPacker();
	assert.notEqual(
		incompleteRuntime.status,
		0,
		`packer accepted a theme missing files required by functions.php:\n${incompleteRuntime.stdout}${incompleteRuntime.stderr}`,
	);
	assert.match(`${incompleteRuntime.stdout}${incompleteRuntime.stderr}`, /missing|required|setup|template-tags/i);

	for (const relativePath of [
		'inc/setup.php',
		'inc/template-tags.php',
		'inc/customizer.php',
		'inc/cpt.php',
		'inc/default-content.php',
		'inc/api-handlers.php',
		'inc/parts/sidebar.php',
	]) {
		write(relativePath);
	}

	const validFixture = runPacker();
	assert.equal(validFixture.status, 0, `${validFixture.stdout}${validFixture.stderr}`);

	write('tests/debug-helper.mjs');
	const developmentFile = runPacker();
	assert.notEqual(
		developmentFile.status,
		0,
		`packer included a development-only file:\n${developmentFile.stdout}${developmentFile.stderr}`,
	);
	assert.match(`${developmentFile.stdout}${developmentFile.stderr}`, /forbidden|development|tests|debug/i);
	console.log('Package tool regression: PASS');
} finally {
	rmSync(fixtureRoot, { recursive: true, force: true });
}

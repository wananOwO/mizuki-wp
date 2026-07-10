<?php
/** Ensure metadata and cache-busting versions stay in lockstep. */

$root        = dirname( __DIR__ );
$functions   = file_get_contents( $root . '/theme/mizuki-wp/functions.php' );
$style       = file_get_contents( $root . '/theme/mizuki-wp/style.css' );
$readme      = file_get_contents( $root . '/theme/mizuki-wp/readme.txt' );
$package     = json_decode( file_get_contents( $root . '/package.json' ), true );
$package_lock = json_decode( file_get_contents( $root . '/package-lock.json' ), true );
$release     = file_get_contents( $root . '/tools/create-release.py' );
$expected    = '1.2.0';
$failures    = array();

preg_match( "/define\( 'MIZUKI_VERSION', '([^']+)' \)/", $functions, $constant_match );
preg_match( '/^Version:\s*(\S+)/m', $style, $metadata_match );
preg_match( '/^Stable tag:\s*(\S+)/mi', $readme, $stable_match );
preg_match( "/^TAG = 'v([^']+)'/m", $release, $release_match );

$constant_version = $constant_match[1] ?? '';
$metadata_version = $metadata_match[1] ?? '';
$stable_version   = $stable_match[1] ?? '';
$package_version  = $package['version'] ?? '';
$lock_version     = $package_lock['version'] ?? '';
$lock_root_version = $package_lock['packages']['']['version'] ?? '';
$release_version  = $release_match[1] ?? '';

foreach (
	array(
		'MIZUKI_VERSION'       => $constant_version,
		'style.css Version'     => $metadata_version,
		'readme Stable tag'     => $stable_version,
		'package.json version'  => $package_version,
		'package-lock version'  => $lock_version,
		'package-lock root'     => $lock_root_version,
		'create-release.py TAG' => $release_version,
	) as $label => $version
) {
	if ( $expected !== $version ) {
		$failures[] = "{$label} must be {$expected}; found {$version}.";
	}
}

if ( false !== strpos( $release, '恢复右侧边栏' ) ) {
	$failures[] = 'Release notes must not claim grid mode restores the right sidebar.';
}

foreach ( array( '叠加壁纸', '网站公告', '悬停子菜单', '字体' ) as $required_note ) {
	if ( false === strpos( $release, $required_note ) ) {
		$failures[] = "Release notes must mention {$required_note}.";
	}
}

if ( false === strpos( $release, 'tools/pack-theme.mjs' ) ) {
	$failures[] = 'Release notes must name the current Node package builder.';
}

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, "FAIL: {$failure}\n" );
	}
	exit( 1 );
}

echo "PASS: all theme/release versions and release notes match {$expected}.\n";

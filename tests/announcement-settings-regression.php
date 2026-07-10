<?php
/**
 * Regression coverage for the shared website announcement setting.
 */

$root              = dirname( __DIR__ );
$customizer_path   = $root . '/theme/mizuki-wp/inc/customizer.php';
$sidebar_path      = $root . '/theme/mizuki-wp/inc/parts/sidebar.php';
$customizer_source = file_get_contents( $customizer_path );
$sidebar_source    = file_get_contents( $sidebar_path );

$failures = array();
$passes   = 0;

function announcement_assert( $condition, $message ) {
	global $failures, $passes;

	if ( $condition ) {
		++$passes;
		echo "PASS: {$message}\n";
		return;
	}

	$failures[] = $message;
	echo "FAIL: {$message}\n";
}

function announcement_assert_contains( $needle, $haystack, $message ) {
	announcement_assert( false !== strpos( $haystack, $needle ), $message );
}

function announcement_source_between( $source, $start_marker, $end_marker ) {
	$start = strpos( $source, $start_marker );
	if ( false === $start ) {
		return '';
	}

	$end = strpos( $source, $end_marker, $start );
	if ( false === $end ) {
		return '';
	}

	return substr( $source, $start, $end - $start );
}

announcement_assert( false !== $customizer_source, 'customizer source is readable' );
announcement_assert( false !== $sidebar_source, 'sidebar source is readable' );

$admin_save_branch = announcement_source_between(
	$customizer_source,
	"isset( \$_POST['mizuki_settings_nonce'] )",
	'$banner_image     = get_theme_mod'
);
$admin_markup = announcement_source_between(
	$customizer_source,
	'<form method="post" action="">',
	"submit_button( __( '"
);

announcement_assert_contains(
	"add_section( 'mizuki_announcement'",
	$customizer_source,
	'Customizer registers an announcement section'
);
announcement_assert_contains(
	"add_setting( 'mizuki_announcement'",
	$customizer_source,
	'Customizer registers the mizuki_announcement setting'
);
announcement_assert_contains(
	"'sanitize_callback' => 'sanitize_textarea_field'",
	$customizer_source,
	'Customizer sanitizes the announcement as plain multiline text'
);
announcement_assert_contains(
	"add_control( 'mizuki_announcement'",
	$customizer_source,
	'Customizer registers the matching announcement control'
);
announcement_assert_contains(
	"current_user_can( 'edit_theme_options' )",
	$admin_save_branch,
	'admin save gate requires edit_theme_options'
);
announcement_assert_contains(
	"set_theme_mod( 'mizuki_announcement'",
	$admin_save_branch,
	'admin save branch writes mizuki_announcement'
);
announcement_assert(
	1 === preg_match(
		"/sanitize_textarea_field\\(\\s*wp_unslash\\(\\s*\\\$_POST\\['mizuki_announcement'\\]\\s*\\)\\s*\\)/",
		$admin_save_branch
	),
	'admin save branch unslashes before sanitizing the announcement'
);

$capability_position = strpos( $admin_save_branch, "current_user_can( 'edit_theme_options' )" );
$first_write_position = strpos( $admin_save_branch, 'set_theme_mod(' );
announcement_assert(
	false !== $capability_position
		&& false !== $first_write_position
		&& $capability_position < $first_write_position,
	'capability is checked before any settings write'
);

announcement_assert_contains(
	'name="mizuki_announcement"',
	$admin_markup,
	'admin form renders the announcement field'
);
announcement_assert_contains(
	'esc_textarea( $announcement )',
	$admin_markup,
	'admin form escapes the saved announcement for textarea output'
);
announcement_assert_contains(
	"get_theme_mod( 'mizuki_announcement'",
	$sidebar_source,
	'sidebar reads the shared announcement theme mod'
);
announcement_assert_contains(
	'esc_html( $announcement )',
	$sidebar_source,
	'sidebar renders the announcement as plain text'
);
announcement_assert(
	false === strpos( $sidebar_source, 'wp_kses_post( $announcement )' ),
	'sidebar does not allow post HTML in the announcement'
);

define( 'ABSPATH', true );

$GLOBALS['announcement_test_value'] = '<strong>Line one</strong>' . "\n" . 'Line two & more';

function get_theme_mod( $name, $default = false ) {
	return 'mizuki_announcement' === $name
		? $GLOBALS['announcement_test_value']
		: $default;
}

function mizuki_get_theme_mod( $name, $default = false ) {
	return get_theme_mod( $name, $default );
}

function __( $text, $domain = null ) {
	return $text;
}

function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function wp_kses_post( $text ) {
	return $text;
}

function mizuki_profile_card() {}
function mizuki_left_tags_widget() {}

function mizuki_widget_open( $title, $delay = 0, $class = '' ) {
	echo '<section><h2>' . esc_html( $title ) . '</h2>';
}

function mizuki_widget_close() {
	echo '</section>';
}

require_once $sidebar_path;

ob_start();
mizuki_render_left_sidebar();
$sidebar_markup = ob_get_clean();

$escaped_announcement = '&lt;strong&gt;Line one&lt;/strong&gt;' . "\n" . 'Line two &amp; more';
announcement_assert_contains(
	$escaped_announcement,
	$sidebar_markup,
	'rendered sidebar escapes announcement HTML while retaining multiline text'
);
announcement_assert(
	false === strpos( $sidebar_markup, '<strong>Line one</strong>' ),
	'rendered sidebar contains no announcement HTML element'
);

echo "\n{$passes} passed, " . count( $failures ) . " failed\n";
exit( $failures ? 1 : 0 );

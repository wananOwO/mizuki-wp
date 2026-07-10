<?php
/**
 * Regression coverage for the bundled Mizuki webfonts.
 */

$root         = dirname( __DIR__ );
$theme        = $root . '/theme/mizuki-wp';
$font_css     = $theme . '/assets/css/mizuki-fonts.css';
$enqueue_file = $theme . '/inc/enqueue.php';
$failures     = array();
$passes       = 0;

function font_check( $condition, $message ) {
	global $failures, $passes;
	if ( $condition ) {
		++$passes;
		return;
	}
	$failures[] = $message;
}

function font_matches( $pattern, $source, $message ) {
	font_check( 1 === preg_match( $pattern, $source ), $message );
}

$css     = file_exists( $font_css ) ? file_get_contents( $font_css ) : '';
$enqueue = file_get_contents( $enqueue_file );

font_check( file_exists( $theme . '/assets/fonts/ZenMaruGothic-Medium.ttf' ), 'Zen Maru Gothic font file must be packaged.' );
font_check( file_exists( $theme . '/assets/fonts/loli.ttf' ), 'Loli font file must be packaged.' );
font_check( file_exists( $font_css ), 'A dedicated bundled-font stylesheet must exist.' );
font_check( 2 === substr_count( $css, '@font-face' ), 'The stylesheet must declare exactly two font faces.' );
font_check( false !== strpos( $css, '../fonts/ZenMaruGothic-Medium.ttf' ), 'Zen Maru Gothic must reference the packaged file.' );
font_check( false !== strpos( $css, '../fonts/loli.ttf' ), 'Loli must reference the packaged file.' );
font_matches( '/font-family:\s*["\']Zen Maru Gothic["\'][^}]*font-weight:\s*500[^}]*font-display:\s*swap/s', $css, 'Zen Maru Gothic must use weight 500 and font-display swap.' );
font_matches( '/font-family:\s*["\']Loli["\'][^}]*font-weight:\s*400[^}]*font-display:\s*swap/s', $css, 'Loli must use weight 400 and font-display swap.' );
font_matches( '/--font-body:\s*["\']Zen Maru Gothic["\']/', $css, '--font-body must select Zen Maru Gothic.' );
font_matches( '/--font-cjk:\s*["\']Loli["\']/', $css, '--font-cjk must select Loli.' );
font_matches( '/body\s*\{[^}]*font-family:\s*var\(--font-body\),\s*var\(--font-cjk\)/s', $css, 'Body must use the upstream body/CJK stack order.' );
font_check( false !== strpos( $enqueue, "'mizuki-fonts', 'mizuki-fonts.css', array( 'mizuki-main' )" ), 'The font stylesheet must be enqueued after mizuki-main.' );

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, "FAIL: {$failure}\n" );
	}
	fwrite( STDERR, sprintf( "Font loading regression: %d passed, %d failed.\n", $passes, count( $failures ) ) );
	exit( 1 );
}

printf( "Font loading regression: %d passed, 0 failed.\n", $passes );

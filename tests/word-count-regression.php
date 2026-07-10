<?php
/**
 * Focused regression coverage for Unicode post counts and the render harness.
 */

$root = dirname( __DIR__ );

define( 'ABSPATH', true );

if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $content, $encoding = null ) {
		return iconv_strlen( $content, $encoding ?: 'UTF-8' );
	}
}

$GLOBALS['word_count_test_content'] =
	'<p>' . "\u{4F60}\u{597D}" . '</p>' .
	'<p>Hello' . "\u{00A0}\u{3000}\n\t" . "\u{4E16}\u{754C}\u{1F680}" . '</p>';

function wp_strip_all_tags( $content ) {
	return strip_tags( $content );
}

function get_post_field( $field, $post_id ) {
	if ( 'post_content' === $field && 43 === $post_id ) {
		return str_repeat( "\u{4E2D}", 301 );
	}

	return 'post_content' === $field && 42 === $post_id
		? $GLOBALS['word_count_test_content']
		: '';
}

function get_the_ID() {
	return 42;
}

require_once $root . '/theme/mizuki-wp/inc/template-tags.php';

$failures = array();

function word_count_check( $condition, $message ) {
	global $failures;

	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$helper_exists = function_exists( 'mizuki_count_content_characters' );
word_count_check( $helper_exists, 'Unicode-aware content-count helper is missing.' );

if ( $helper_exists ) {
	word_count_check(
		10 === mizuki_count_content_characters( $GLOBALS['word_count_test_content'] ),
		'HTML and Unicode whitespace must be removed before mixed content is counted.'
	);
	word_count_check(
		3 === mizuki_count_content_characters( '<p>A&nbsp;&amp;&#x4E2D;</p>' ),
		'HTML entities must be decoded to their visible characters before counting.'
	);
}

word_count_check(
	10 === mizuki_word_count(),
	'mizuki_word_count() must count the current post with the Unicode-aware rule.'
);
word_count_check(
	2 === mizuki_reading_time( 43 ),
	'Reading time must reuse the extension-independent Unicode counter.'
);

$index_source = file_get_contents( $root . '/theme/mizuki-wp/index.php' );
$template_tags_source = file_get_contents( $root . '/theme/mizuki-wp/inc/template-tags.php' );
word_count_check(
	false !== strpos( $template_tags_source, 'preg_match_all' ),
	'Unicode counting needs a PCRE fallback when mbstring is unavailable.'
);
word_count_check(
	false === strpos( $template_tags_source, 'mb_strlen' ),
	'Template helpers must not require the optional mbstring extension.'
);
word_count_check(
	false === strpos( $index_source, 'str_word_count' ),
	'Homepage cards must not use str_word_count().'
);
word_count_check(
	false !== strpos( $index_source, 'mizuki_word_count(' ),
	'Homepage cards must use the shared per-post count.'
);

$command = escapeshellarg( PHP_BINARY )
	. ' -d display_errors=1 -d error_reporting=32767 '
	. escapeshellarg( $root . '/test-full-render.php' )
	. ' 2>&1';
$render_output = array();
$render_status = 0;
exec( $command, $render_output, $render_status );
$render_text = implode( "\n", $render_output );

word_count_check( 0 === $render_status, 'Full-render harness must exit successfully.' );
word_count_check(
	1 !== preg_match( '/\b(?:Warning|Fatal error|Deprecated):/i', $render_text ),
	'Full-render harness must emit no PHP diagnostics.'
);

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, "FAIL: {$failure}\n" );
	}

	exit( 1 );
}

echo "PASS: Unicode word count and full-render regression checks.\n";

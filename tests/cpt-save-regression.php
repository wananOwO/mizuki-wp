<?php
/** Focused coverage for CPT metadata save authorization and persistence. */

$root = dirname( __DIR__ );
define( 'ABSPATH', true );

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback ) {}
}

function cpt_test_reset( $post_type = 'mizuki_friend', $can_edit = true ) {
	$GLOBALS['cpt_test'] = array(
		'post_type'  => $post_type,
		'can_edit'   => $can_edit,
		'nonce_valid' => true,
		'updates'    => array(),
		'deletes'    => array(),
		'calls'      => array(),
	);
	$_POST = array();
}

function current_user_can( $capability, $post_id = null ) {
	$GLOBALS['cpt_test']['calls'][] = array( 'capability', $capability, $post_id );
	return $GLOBALS['cpt_test']['can_edit'];
}

function get_post_type( $post_id ) {
	return $GLOBALS['cpt_test']['post_type'];
}

function wp_verify_nonce( $nonce, $action ) {
	$GLOBALS['cpt_test']['calls'][] = array( 'nonce', $nonce, $action );
	return $GLOBALS['cpt_test']['nonce_valid'];
}

function wp_unslash( $value ) {
	$GLOBALS['cpt_test']['calls'][] = array( 'unslash', $value );
	return is_array( $value ) ? array_map( 'stripslashes', $value ) : stripslashes( $value );
}

function sanitize_textarea_field( $value ) {
	$GLOBALS['cpt_test']['calls'][] = array( 'textarea', $value );
	return trim( strip_tags( $value ) );
}

function esc_url_raw( $value ) {
	$GLOBALS['cpt_test']['calls'][] = array( 'url', $value );
	return '' === $value ? '' : 'url:' . $value;
}

function update_post_meta( $post_id, $key, $value ) {
	$GLOBALS['cpt_test']['updates'][ $key ] = array( $post_id, $value );
}

function delete_post_meta( $post_id, $key ) {
	$GLOBALS['cpt_test']['deletes'][] = array( $post_id, $key );
}

function cpt_test_check( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$autosave_child = isset( $argv[1] ) && '--autosave-child' === $argv[1];
if ( $autosave_child ) {
	define( 'DOING_AUTOSAVE', true );
}

require_once $root . '/theme/mizuki-wp/inc/cpt.php';

if ( $autosave_child ) {
	cpt_test_reset();
	$_POST = array(
		'mizuki_friend_nonce' => 'valid',
		'mizuki_friend_desc'  => 'must not save',
	);
	mizuki_save_meta_fields( 77 );
	exit(
		empty( $GLOBALS['cpt_test']['updates'] ) && empty( $GLOBALS['cpt_test']['deletes'] )
			? 0
			: 1
	);
}

$failures = array();
$guard_cases = array(
	'capability denied' => array( 'mizuki_friend', false, true, array( 'mizuki_friend_nonce' => 'valid', 'mizuki_friend_desc' => 'blocked' ) ),
	'unsupported post type' => array( 'post', true, true, array( 'mizuki_friend_nonce' => 'valid', 'mizuki_friend_desc' => 'blocked' ) ),
	'missing nonce' => array( 'mizuki_friend', true, true, array( 'mizuki_friend_desc' => 'blocked' ) ),
	'invalid nonce' => array( 'mizuki_friend', true, false, array( 'mizuki_friend_nonce' => 'invalid', 'mizuki_friend_desc' => 'blocked' ) ),
);

foreach ( $guard_cases as $label => $case ) {
	cpt_test_reset( $case[0], $case[1] );
	$GLOBALS['cpt_test']['nonce_valid'] = $case[2];
	$_POST = $case[3];
	mizuki_save_meta_fields( 77 );
	cpt_test_check( empty( $GLOBALS['cpt_test']['updates'] ), "{$label} must not persist metadata." );
	cpt_test_check( empty( $GLOBALS['cpt_test']['deletes'] ), "{$label} must not delete metadata." );
}

$command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' --autosave-child 2>&1';
$autosave_output = array();
$autosave_status = 0;
exec( $command, $autosave_output, $autosave_status );
cpt_test_check( 0 === $autosave_status, 'DOING_AUTOSAVE must prevent metadata writes.' );

// Valid nonce with omitted fields is different from explicitly submitted empty values.
cpt_test_reset();
$_POST = array( 'mizuki_friend_nonce' => 'valid' );
mizuki_save_meta_fields( 77 );
cpt_test_check( empty( $GLOBALS['cpt_test']['updates'] ), 'Omitted friend fields must leave existing metadata untouched.' );

cpt_test_reset();
$_POST = array(
	'mizuki_friend_nonce' => 'valid',
	'mizuki_friend_url'   => 'https:\\/\\/example.test\\/path',
	'mizuki_friend_desc'  => "  Keep \\'quotes\\' <b>text</b>  ",
);
mizuki_save_meta_fields( 77 );
cpt_test_check(
	in_array( array( 'capability', 'edit_post', 77 ), $GLOBALS['cpt_test']['calls'], true ),
	'Save must check edit_post capability for the current post ID.'
);
cpt_test_check(
	in_array( array( 'nonce', 'valid', 'mizuki_friend_save' ), $GLOBALS['cpt_test']['calls'], true ),
	'Friend metadata must verify the valid nonce against the friend-save action.'
);
cpt_test_check(
	array(
		'_mizuki_friend_url'  => array( 77, 'url:https://example.test/path' ),
		'_mizuki_friend_desc' => array( 77, "Keep 'quotes' text" ),
	) === $GLOBALS['cpt_test']['updates'],
	'Valid friend metadata must be unslashed, sanitized, and persisted under the expected keys.'
);
cpt_test_check(
	array( 'unslash', 'url', 'unslash', 'textarea' ) === array_values(
		array_filter(
			array_column( $GLOBALS['cpt_test']['calls'], 0 ),
			function ( $call ) {
				return in_array( $call, array( 'unslash', 'url', 'textarea' ), true );
			}
		)
	),
	'Friend URL and description must pass through unslash before their respective sanitizer.'
);

cpt_test_reset();
$_POST = array(
	'mizuki_friend_nonce' => 'valid',
	'mizuki_friend_url'   => '',
	'mizuki_friend_desc'  => '',
);
mizuki_save_meta_fields( 77 );
cpt_test_check(
	array(
		'_mizuki_friend_url'  => array( 77, '' ),
		'_mizuki_friend_desc' => array( 77, '' ),
	) === $GLOBALS['cpt_test']['updates'],
	'Explicitly empty friend fields must be persisted as empty values.'
);

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, "FAIL: {$failure}\n" );
	}
	exit( 1 );
}

echo "PASS: CPT metadata save authorization and persistence checks.\n";

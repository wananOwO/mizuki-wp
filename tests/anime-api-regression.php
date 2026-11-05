<?php
/** Behavioral coverage for remote anime fetching, caching, and refresh guards. */

define( 'ABSPATH', true );
define( 'HOUR_IN_SECONDS', 3600 );

set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

$passes = 0;
function anime_check( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	++$GLOBALS['passes'];
}

function anime_reset( $mode = 'bangumi' ) {
	$GLOBALS['mods'] = array(
		'mizuki_anime_mode' => $mode,
		'mizuki_bangumi_user_id' => 'test-user',
		'mizuki_bilibili_vmid' => '42',
		'mizuki_anime_cache_hours' => 6,
	);
	$GLOBALS['transients'] = $GLOBALS['writes'] = $GLOBALS['deleted'] = $GLOBALS['requests'] = array();
	$GLOBALS['responses'] = array();
	$GLOBALS['nonce_valid'] = true;
	$GLOBALS['can_manage'] = true;
	$_SERVER = array( 'REMOTE_ADDR' => '192.0.2.1' );
}

function add_action( $hook, $callback ) {}
function mizuki_get_theme_mod( $key, $default = false ) { return $GLOBALS['mods'][ $key ] ?? $default; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function wp_kses_post( $value ) { return strip_tags( $value, '<p><strong><em>' ); }
function __( $value, $domain = '' ) { return $value; }
function esc_html__( $value, $domain = '' ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function get_transient( $key ) { return $GLOBALS['transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) {
	$GLOBALS['transients'][ $key ] = $value;
	$GLOBALS['writes'][ $key ] = array( $value, $ttl );
}
function delete_transient( $key ) {
	$GLOBALS['deleted'][] = $key;
	unset( $GLOBALS['transients'][ $key ] );
}
class WP_Error {}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_remote_get( $url, $args ) {
	$GLOBALS['requests'][] = array( $url, $args );
	if ( ! $GLOBALS['responses'] ) {
		throw new RuntimeException( 'Unexpected HTTP request: ' . $url );
	}
	return array_shift( $GLOBALS['responses'] );
}
function wp_remote_retrieve_response_code( $response ) { return $response['status']; }
function wp_remote_retrieve_body( $response ) { return $response['body']; }
function anime_response( $body, $status = 200 ) {
	return array( 'status' => $status, 'body' => is_string( $body ) ? $body : json_encode( $body ) );
}

// WordPress terminates these calls; exceptions let each test inspect that boundary.
class Anime_Ajax_Exit extends RuntimeException {
	public $success;
	public $data;
	public function __construct( $success, $data, $status ) {
		parent::__construct( 'AJAX response', $status );
		$this->success = $success;
		$this->data = $data;
	}
}
function check_ajax_referer( $action, $field ) {
	anime_check( 'mizuki_anime_refresh' === $action && 'nonce' === $field, 'Refresh verifies its own nonce action and field.' );
	if ( ! $GLOBALS['nonce_valid'] ) {
		throw new Anime_Ajax_Exit( false, null, 403 );
	}
}
function current_user_can( $capability ) {
	anime_check( 'manage_options' === $capability, 'Refresh requires administrator capability.' );
	return $GLOBALS['can_manage'];
}
function wp_send_json_error( $data, $status = 200 ) { throw new Anime_Ajax_Exit( false, $data, $status ); }
function wp_send_json_success( $data ) { throw new Anime_Ajax_Exit( true, $data, 200 ); }
function anime_refresh_request() {
	try {
		mizuki_ajax_refresh_anime_data();
	} catch ( Anime_Ajax_Exit $response ) {
		return $response;
	}
	throw new RuntimeException( 'Refresh did not terminate with JSON.' );
}

require dirname( __DIR__ ) . '/theme/mizuki-wp/inc/api-handlers.php';

$providers = array(
	'bangumi' => array( 'key' => 'mizuki_bangumi_data_test-user', 'id' => 'mizuki_bangumi_user_id', 'statuses' => array( 'watching', 'planned', 'completed', 'onhold', 'dropped' ) ),
	'bilibili' => array( 'key' => 'mizuki_bilibili_data_42', 'id' => 'mizuki_bilibili_vmid', 'statuses' => array( 'planned', 'watching', 'completed' ) ),
);
function anime_queue_collection( $provider, $title ) {
	if ( 'bangumi' === $provider ) {
		$GLOBALS['responses'][] = anime_response( array( 'data' => array( array( 'subject' => array( 'id' => 7, 'name_cn' => $title ) ) ) ) );
	} else {
		$GLOBALS['responses'][] = anime_response( array( 'code' => 0, 'data' => array( 'total' => 1 ) ) );
		$GLOBALS['responses'][] = anime_response( array( 'code' => 0, 'data' => array( 'list' => array( array( 'title' => $title ) ) ) ) );
	}
}

foreach ( $providers as $provider => $config ) {
	foreach ( array( array(), array( array( 'title' => 'Cached' ) ) ) as $cached ) {
		anime_reset( $provider );
		$GLOBALS['transients'][ $config['key'] ] = $cached;
		anime_check( $cached === mizuki_get_anime_list(), "$provider: even an empty cache is a hit." );
		anime_check( array() === $GLOBALS['requests'] && array() === $GLOBALS['writes'], "$provider: cache hits do not fetch or rewrite." );
	}

	anime_reset( $provider );
	$GLOBALS['mods'][ $config['id'] ] = '';
	anime_check( array() === mizuki_get_anime_list() && array() === $GLOBALS['requests'], "$provider: missing account does not contact upstream." );

	anime_reset( $provider );
	foreach ( $config['statuses'] as $status ) {
		anime_queue_collection( $provider, $status );
	}
	$data = mizuki_get_anime_list();
	anime_check( $config['statuses'] === array_column( $data, 'status' ), "$provider: cold cache synchronously fetches every collection status." );
	anime_check( $config['statuses'] === array_column( $data, 'title' ), "$provider: collection data survives normalization." );
	anime_check( array( $data, 6 * HOUR_IN_SECONDS ) === $GLOBALS['writes'][ $config['key'] ], "$provider: fetched data is cached under the account with configured TTL." );
	$count = count( $GLOBALS['requests'] );
	anime_check( ( 'bangumi' === $provider ? 5 : 6 ) === $count && array() === $GLOBALS['responses'], "$provider: no per-item detail requests." );
	$collection_types = array();
	foreach ( $GLOBALS['requests'] as $request ) {
		parse_str( parse_url( $request[0], PHP_URL_QUERY ), $query );
		if ( 'bangumi' === $provider || '1' === $query['ps'] ) {
			$collection_types[] = 'bangumi' === $provider ? $query['type'] : $query['follow_status'];
		}
	}
	anime_check( ( 'bangumi' === $provider ? array( '3', '1', '2', '4', '5' ) : array( '1', '2', '3' ) ) === $collection_types, "$provider: upstream collection IDs match the normalized statuses." );
	anime_check( $data === mizuki_get_anime_list() && $count === count( $GLOBALS['requests'] ), "$provider: subsequent reads reuse fetched data." );

	// A transient outage must not become a cached empty list that hides recovery.
	anime_reset( $provider );
	$GLOBALS['responses'] = array_fill( 0, count( $config['statuses'] ), new WP_Error() );
	anime_check( array() === mizuki_get_anime_list(), "$provider: outages return an empty list safely." );
	anime_check( array() === $GLOBALS['writes'], "$provider: failed fetches do not poison the cache." );
	foreach ( $config['statuses'] as $status ) {
		anime_queue_collection( $provider, $status );
	}
	anime_check( count( $config['statuses'] ) === count( mizuki_get_anime_list() ), "$provider: next read retries and recovers without cron." );
	foreach ( array( 'outage', 'empty' ) as $result ) {
		anime_reset( $provider );
		$GLOBALS['transients'][ $config['key'] ] = array( array( 'title' => 'Last good data' ) );
		$empty_response = 'bangumi' === $provider
			? anime_response( array( 'data' => array() ) )
			: anime_response( array( 'code' => 0, 'data' => array( 'total' => 0 ) ) );
		$GLOBALS['responses'] = array_fill( 0, count( $config['statuses'] ), 'outage' === $result ? new WP_Error() : $empty_response );
		mizuki_refresh_remote_anime_data();
		anime_check( array() === $GLOBALS['writes'] && array( array( 'title' => 'Last good data' ) ) === get_transient( $config['key'] ), "$provider: $result background refresh preserves last good cached data." );
	}

	foreach ( array( new WP_Error(), anime_response( array(), 503 ), anime_response( '{broken' ), anime_response( array( 'code' => -1 ) ) ) as $failure ) {
		anime_reset( $provider );
		if ( 'bilibili' === $provider ) {
			$GLOBALS['responses'][] = anime_response( array( 'code' => 0, 'data' => array( 'total' => 1 ) ) );
		}
		$GLOBALS['responses'][] = $failure;
		$items = 'bangumi' === $provider
			? mizuki_fetch_bangumi_collection( 'test-user', 3, 'watching' )
			: mizuki_fetch_bilibili_collection( '42', 2, 'watching' );
		anime_check( array() === $items && array() === $GLOBALS['responses'], "$provider: transport, HTTP, and invalid responses stop fetching safely." );
	}
}

anime_reset();
$GLOBALS['responses'] = array(
	anime_response( array( 'data' => array_fill( 0, 50, array( 'subject' => array( 'name' => 'Page one' ) ) ) ) ),
	anime_response( array( 'data' => array( array( 'subject' => array( 'name' => 'Page two' ) ) ) ) ),
);
$items = mizuki_fetch_bangumi_collection( 'test-user', 3, 'watching' );
anime_check( 51 === count( $items ) && 'Page two' === $items[50]['title'], 'Bangumi retains all pages in order.' );
foreach ( $GLOBALS['requests'] as $index => $request ) {
	parse_str( parse_url( $request[0], PHP_URL_QUERY ), $query );
	anime_check( '2' === $query['subject_type'] && '3' === $query['type'] && '50' === $query['limit'] && (string) ( $index * 50 ) === $query['offset'], 'Bangumi uses anime collection filters and advances the offset.' );
	anime_check( 15 === $request[1]['timeout'] && ! empty( $request[1]['headers']['User-Agent'] ), 'Bangumi requests have a timeout and identifying User-Agent.' );
}

anime_reset( 'bilibili' );
$GLOBALS['responses'] = array(
	anime_response( array( 'code' => 0, 'data' => array( 'total' => 31 ) ) ),
	anime_response( array( 'code' => 0, 'data' => array( 'list' => array_fill( 0, 30, array( 'title' => 'Page one' ) ) ) ) ),
	anime_response( array( 'code' => 0, 'data' => array( 'list' => array( array( 'title' => 'Page two' ) ) ) ) ),
);
$items = mizuki_fetch_bilibili_collection( '42', 2, 'watching' );
anime_check( 31 === count( $items ) && 'Page two' === $items[30]['title'], 'Bilibili retains all pages in order.' );
foreach ( $GLOBALS['requests'] as $index => $request ) {
	parse_str( parse_url( $request[0], PHP_URL_QUERY ), $query );
	anime_check( '42' === $query['vmid'] && '2' === $query['follow_status'] && (string) max( 1, $index ) === $query['pn'] && ( 0 === $index ? '1' : '30' ) === $query['ps'], 'Bilibili probes the count then requests each data page.' );
	anime_check( 15 === $request[1]['timeout'], 'Bilibili requests have a timeout.' );
}

foreach ( array( 'nonce', 'capability', 'rate' ) as $denial ) {
	anime_reset();
	$GLOBALS['transients'][ $providers['bangumi']['key'] ] = array( array( 'title' => 'Keep me' ) );
	$GLOBALS['nonce_valid'] = 'nonce' !== $denial;
	$GLOBALS['can_manage'] = 'capability' !== $denial;
	if ( 'rate' === $denial ) {
		$GLOBALS['transients'][ 'mizuki_rl_anime_data_' . md5( $_SERVER['REMOTE_ADDR'] ) ] = 20;
	}
	$response = anime_refresh_request();
	anime_check( ! $response->success && ( 'rate' === $denial ? 429 : 403 ) === $response->getCode(), "$denial: unauthorized or throttled refresh is rejected." );
	anime_check( array() === $GLOBALS['deleted'] && array() === $GLOBALS['requests'], "$denial: rejected refresh cannot clear cache or fetch upstream." );
}

foreach ( $providers as $provider => $config ) {
	anime_reset( $provider );
	$GLOBALS['transients'][ $config['key'] ] = array( array( 'title' => 'Stale' ) );
	$GLOBALS['transients']['unrelated'] = array( 'preserve' );
	foreach ( $config['statuses'] as $status ) {
		anime_queue_collection( $provider, $status );
	}
	$response = anime_refresh_request();
	anime_check( $response->success && count( $config['statuses'] ) === $response->data['count'], "$provider: authorized refresh reports the new item count." );
	anime_check( array( $config['key'] ) === $GLOBALS['deleted'] && array( 'preserve' ) === $GLOBALS['transients']['unrelated'], "$provider: refresh invalidates only the selected account cache." );
}

anime_reset();
for ( $i = 0; $i < 20; ++$i ) {
	anime_check( mizuki_check_rate_limit( 'anime_data', 20, 60 ), 'Rate limit admits requests through the boundary.' );
}
$key = 'mizuki_rl_anime_data_' . md5( $_SERVER['REMOTE_ADDR'] );
anime_check( ! mizuki_check_rate_limit( 'anime_data', 20, 60 ) && array( 20, 60 ) === $GLOBALS['writes'][ $key ], 'The twenty-first request is denied without another counter write.' );
$_SERVER['REMOTE_ADDR'] = '192.0.2.2';
anime_check( mizuki_check_rate_limit( 'anime_data', 20, 60 ), 'Rate limits are isolated per client address.' );
anime_check( mizuki_check_rate_limit( 'other_action', 20, 60 ), 'Rate limits are isolated per action.' );

echo "PASS: {$passes} anime API assertions\n";

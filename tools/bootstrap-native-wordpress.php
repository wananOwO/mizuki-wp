<?php
/**
 * Populate the disposable native-PHP WordPress test site.
 *
 * Usage: MIZUKI_WP_ROOT=/path/to/wordpress php bootstrap-native-wordpress.php
 */

$root = getenv( 'MIZUKI_WP_ROOT' );
if ( ! $root || ! is_file( $root . '/wp-load.php' ) ) {
	fwrite( STDERR, "MIZUKI_WP_ROOT must point to a WordPress installation.\n" );
	exit( 1 );
}

require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';

if ( ! is_blog_installed() ) {
	fwrite( STDERR, "WordPress is not installed.\n" );
	exit( 1 );
}

$plugin_result = activate_plugin( 'sqlite-database-integration/load.php', '', false, true );
if ( is_wp_error( $plugin_result ) ) {
	fwrite( STDERR, 'SQLite plugin activation failed: ' . $plugin_result->get_error_message() . "\n" );
	exit( 1 );
}

switch_theme( 'mizuki-wp' );
if ( 'mizuki-wp' !== get_stylesheet() ) {
	fwrite( STDERR, 'Theme activation failed; active stylesheet is ' . get_stylesheet() . "\n" );
	exit( 1 );
}

$fixtures = array(
	array(
		'title'   => '中文字符统计测试',
		'content' => '<p>春天的风穿过城市，带来新的故事。</p><p>这是用于验证中文字符统计与文章卡片布局的内容。</p>',
		'excerpt' => '验证中文文章卡片的字符统计。',
	),
	array(
		'title'   => 'Mixed Language Layout Test',
		'content' => '<p>Mizuki combines a quiet reading experience with 清晰的中文排版。</p><p>Second paragraph for a stable card height.</p>',
		'excerpt' => 'A mixed English and Chinese post for layout testing.',
	),
	array(
		'title'   => '网格模式与右侧栏验证',
		'content' => '<p>切换到网格模式后，右侧栏应隐藏，文章区域应扩展为两列。</p>',
		'excerpt' => '验证网格模式下的列布局和侧栏显示。',
	),
);

$category = wp_create_category( 'Theme Verification' );
$created  = array();

foreach ( $fixtures as $index => $fixture ) {
	$existing = get_page_by_title( $fixture['title'], OBJECT, 'post' );
	$post_id  = $existing ? $existing->ID : wp_insert_post(
		array(
			'post_title'    => $fixture['title'],
			'post_content'  => $fixture['content'],
			'post_excerpt'  => $fixture['excerpt'],
			'post_status'   => 'publish',
			'post_category' => array( $category ),
			'tags_input'    => array( 'Mizuki', 'Regression' ),
		)
	);

	if ( is_wp_error( $post_id ) ) {
		fwrite( STDERR, 'Post creation failed: ' . $post_id->get_error_message() . "\n" );
		exit( 1 );
	}

	if ( ! has_post_thumbnail( $post_id ) ) {
		$source = get_theme_file_path( '/assets/desktop-banner/' . ( $index + 1 ) . '.webp' );
		$temp   = wp_tempnam( 'mizuki-cover-' . ( $index + 1 ) . '.webp' );
		if ( $temp && copy( $source, $temp ) ) {
			$attachment_id = media_handle_sideload(
				array(
					'name'     => 'mizuki-cover-' . ( $index + 1 ) . '.webp',
					'tmp_name' => $temp,
				),
				$post_id,
				$fixture['title']
			);
			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	$created[] = array(
		'id'    => (int) $post_id,
		'title' => $fixture['title'],
	);
}

update_option( 'blogname', 'Mizuki Native Test' );
update_option( 'blogdescription', 'WordPress PHP 8.4 integration verification' );
update_option( 'posts_per_page', 10 );
update_option( 'permalink_structure', '' );

echo wp_json_encode(
	array(
		'wordpress' => get_bloginfo( 'version' ),
		'php'       => PHP_VERSION,
		'theme'     => get_stylesheet(),
		'db_engine' => defined( 'DB_ENGINE' ) ? DB_ENGINE : 'unknown',
		'posts'     => $created,
	),
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
) . PHP_EOL;

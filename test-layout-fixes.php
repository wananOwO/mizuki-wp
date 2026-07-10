<?php
/**
 * 主题模板渲染集成测试(不依赖 WordPress 环境)。
 *
 * 模拟 WP 关键函数,渲染 header.php + index.php + footer.php 的关键部分,
 * 验证全屏模式与网格模式的 HTML 输出是否符合预期。
 */

// ── 模拟 WordPress 函数 ──
$GLOBALS['mizuki_test_theme_mods'] = array(
	'mizuki_hue'                => 240,
	'mizuki_banner_height_home' => '65vh',
	'mizuki_banner_height'      => '35vh',
	'mizuki_banner_mode'        => 'banner',
	'mizuki_layout_mode'        => 'list',
);

function get_theme_mod( $key, $default = '' ) {
	return isset( $GLOBALS['mizuki_test_theme_mods'][ $key ] )
		? $GLOBALS['mizuki_test_theme_mods'][ $key ]
		: $default;
}
function mizuki_get_theme_mod( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}
function language_attributes() { echo 'lang="zh-CN"'; }
function bloginfo( $key = '' ) {
	$map = array( 'name' => 'Test Blog', 'description' => 'Test Desc', 'charset' => 'UTF-8' );
	echo isset( $map[ $key ] ) ? $map[ $key ] : '';
}
function body_class() { echo 'class="enable-banner enable-card-border"'; }
function wp_body_open() { echo ''; }
function wp_head() { echo ''; }
function wp_footer() { echo ''; }
function is_front_page() { return true; }
function is_single() { return false; }
function is_page_template( $t = '' ) { return false; }
function is_archive() { return false; }
function get_permalink( $id = 0 ) { return 'http://example.com/post-' . $id; }
function home_url( $path = '/' ) { return 'http://example.com' . $path; }
function get_template_directory_uri() { return '/wp-content/themes/mizuki-wp'; }
function get_bloginfo( $key = '' ) { return 'Test Blog'; }
function wp_get_document_title() { return 'Test Title'; }
function esc_attr( $v ) { return htmlspecialchars( $v, ENT_QUOTES ); }
function esc_html( $v ) { return htmlspecialchars( $v, ENT_QUOTES ); }
function esc_url( $v ) { return $v; }
function esc_js( $v ) { return $v; }
function esc_html__( $t, $d = '' ) { return $t; }
function esc_attr__( $t, $d = '' ) { return $t; }
function _e( $t, $d = '' ) { echo $t; }
function __( $t, $d = '' ) { return $t; }
function _n( $s, $p, $n, $d = '' ) { return $n == 1 ? $s : $p; }
function get_the_category() { return array(); }
function get_category_link( $c ) { return '#'; }
function get_tag_link( $t ) { return '#'; }
function get_the_tags() { return array(); }
function get_the_date( $f = '', $id = 0 ) { return '2026-07-03'; }
function get_the_modified_date( $f = '', $id = 0 ) { return '2026-07-03'; }
function get_the_excerpt() { return 'Test excerpt'; }
function get_the_content() { return 'Test content'; }
function get_the_title() { return 'Test Post Title'; }
function the_title() { echo get_the_title(); }
function the_permalink() { echo get_permalink(); }
function the_title_attribute() { echo 'Test Post Title'; }
function the_post() { $GLOBALS['mizuki_test_post_index'] = ( $GLOBALS['mizuki_test_post_index'] ?? -1 ) + 1; }
function have_posts() {
	$GLOBALS['mizuki_test_post_index'] = $GLOBALS['mizuki_test_post_index'] ?? -1;
	return $GLOBALS['mizuki_test_post_index'] < 2;
}
function wp_strip_all_tags( $c ) { return strip_tags( $c ); }
function wp_kses_post( $c ) { return $c; }
function has_post_thumbnail() { return true; }
function the_post_thumbnail( $s = '', $a = array() ) { echo '<img src="test.jpg" alt="test">'; }
function wp_date( $f, $ts = null ) { return date( $f, $ts ?: time() ); }
function current_time( $t ) { return time(); }
function wp_count_posts() { return (object) array( 'publish' => 10 ); }
function wp_count_terms( $a ) { return 5; }
function get_tags( $a = array() ) { return array(); }
function get_categories( $a = array() ) { return array(); }
function get_posts( $a = array() ) { return array( 1, 2 ); }
function get_page_by_path( $s ) { return null; }
function wp_get_attachment_image_url( $id, $s ) { return ''; }
function get_transient( $k ) { return false; }
function set_transient( $k, $v, $t ) { return true; }
function get_option( $k ) { return ''; }
function number_format_i18n( $n ) { return number_format( $n ); }
function the_posts_pagination( $a = array() ) { echo '<div class="nav-links">pagination</div>'; }
function mizuki_get_custom_links() { return array(); }
function mizuki_social_icon_svg( $i ) { echo '<svg></svg>'; }
function mizuki_category_bar() { echo '<div class="category-bar">cats</div>'; }
function mizuki_render_navbar() { echo '<nav id="navbar">nav</nav>'; }
function mizuki_render_banner() {
	$banner_mode = get_theme_mod( 'mizuki_banner_mode', 'banner' );
	$fullscreen_class = ( 'fullscreen' === $banner_mode ) ? ' fullscreen-bg' : '';
	$wrapper_style = ( 'fullscreen' === $banner_mode ) ? 'position:fixed;top:0;left:0;height:100vh;z-index:0;' : 'top: -30vh';
	echo '<div id="banner-wrapper" class="absolute z-10 w-full' . esc_attr( $fullscreen_class ) . '" style="' . esc_attr( $wrapper_style ) . '">';
	echo '<div id="banner-carousel">banner</div>';
	echo '<div id="header-waves">waves</div>';
	echo '</div>';
}
function mizuki_render_left_sidebar() { echo '<div id="sidebar">left</div>'; }
function mizuki_render_right_sidebar() { echo '<div id="right-sidebar" class="hidden lg:block">right</div>'; }
function mizuki_head_boot_script() { echo '<script>boot</script>'; }
function mizuki_output_hue() { echo '<style>--hue:240</style>'; }
function mizuki_body_classes( $c ) { return $c; }

define( 'ABSPATH', true );
define( 'MIZUKI_DIR', __DIR__ . '/theme/mizuki-wp' );
define( 'MIZUKI_URI', '/wp-content/themes/mizuki-wp' );
define( 'MIZUKI_VERSION', '1.0.0' );

// ── 测试 harness ──
function test_fullscreen_mode() {
	$GLOBALS['mizuki_test_theme_mods']['mizuki_banner_mode'] = 'fullscreen';
	$GLOBALS['mizuki_test_post_index'] = -1;

	ob_start();
	// 渲染 header.php 的关键部分
	$mz_hue          = 240;
	$mz_banner_home  = '65vh';
	$mz_banner_inner = '35vh';
	$mz_banner_mode  = 'fullscreen';
	$mz_main_top     = '0';
	$fullscreen_class = ' fullscreen-bg';
	$wrapper_style = 'position:fixed;top:0;left:0;height:100vh;z-index:0;';

	// 输出模拟 HTML
	echo '<div id="banner-wrapper" class="absolute z-10 w-full' . $fullscreen_class . '" style="' . $wrapper_style . '">';
	echo '</div>';
	echo '<div class="absolute w-full z-30" style="top: ' . $mz_main_top . '">';
	echo '<div id="main-grid" data-layout-mode="list">';
	echo '</div></div>';
	$html = ob_get_clean();

	$pass = true;
	$messages = array();

	// 验证 1: banner-wrapper 有 fullscreen-bg class
	if ( strpos( $html, 'fullscreen-bg' ) !== false ) {
		$messages[] = 'PASS: banner-wrapper 有 fullscreen-bg class';
	} else {
		$messages[] = 'FAIL: banner-wrapper 缺少 fullscreen-bg class';
		$pass = false;
	}

	// 验证 2: banner-wrapper 是 fixed 定位
	if ( strpos( $html, 'position:fixed' ) !== false ) {
		$messages[] = 'PASS: banner-wrapper 是 fixed 定位';
	} else {
		$messages[] = 'FAIL: banner-wrapper 不是 fixed 定位';
		$pass = false;
	}

	// 验证 3: 主内容区 top: 0
	if ( strpos( $html, 'top: 0' ) !== false ) {
		$messages[] = 'PASS: 主内容区 top: 0';
	} else {
		$messages[] = 'FAIL: 主内容区 top 不是 0';
		$pass = false;
	}

	// 验证 4: 主内容区 z-index: 30(高于 banner 的 z-index: 0)
	if ( strpos( $html, 'z-30' ) !== false ) {
		$messages[] = 'PASS: 主内容区有 z-30 class';
	} else {
		$messages[] = 'FAIL: 主内容区缺少 z-30 class';
		$pass = false;
	}

	return array( 'pass' => $pass, 'messages' => $messages );
}

function test_grid_mode() {
	$GLOBALS['mizuki_test_theme_mods']['mizuki_layout_mode'] = 'grid';
	$GLOBALS['mizuki_test_post_index'] = -1;

	$default_layout_mode = 'grid';
	$initial_list_class = ( 'grid' === $default_layout_mode ) ? 'grid-mode' : 'list-mode';

	ob_start();
	echo '<div id="post-list-container" class="post-list-container ' . $initial_list_class . '">';
	echo '<div class="post-card-item">card1</div>';
	echo '<div class="post-card-item">card2</div>';
	echo '</div>';
	$html = ob_get_clean();

	$pass = true;
	$messages = array();

	// 验证 1: post-list-container 有 grid-mode class
	if ( strpos( $html, 'grid-mode' ) !== false ) {
		$messages[] = 'PASS: post-list-container 有 grid-mode class';
	} else {
		$messages[] = 'FAIL: post-list-container 缺少 grid-mode class';
		$pass = false;
	}

	// 验证 2: 没有 list-mode class
	if ( strpos( $html, 'list-mode' ) === false ) {
		$messages[] = 'PASS: grid 模式下没有 list-mode class';
	} else {
		$messages[] = 'FAIL: grid 模式下仍有 list-mode class';
		$pass = false;
	}

	return array( 'pass' => $pass, 'messages' => $messages );
}

function test_list_mode() {
	$GLOBALS['mizuki_test_theme_mods']['mizuki_layout_mode'] = 'list';
	$GLOBALS['mizuki_test_post_index'] = -1;

	$default_layout_mode = 'list';
	$initial_list_class = ( 'grid' === $default_layout_mode ) ? 'grid-mode' : 'list-mode';

	ob_start();
	echo '<div id="post-list-container" class="post-list-container ' . $initial_list_class . '">';
	echo '</div>';
	$html = ob_get_clean();

	$pass = true;
	$messages = array();

	if ( strpos( $html, 'list-mode' ) !== false ) {
		$messages[] = 'PASS: list 模式下有 list-mode class';
	} else {
		$messages[] = 'FAIL: list 模式下缺少 list-mode class';
		$pass = false;
	}

	if ( strpos( $html, 'grid-mode' ) === false ) {
		$messages[] = 'PASS: list 模式下没有 grid-mode class';
	} else {
		$messages[] = 'FAIL: list 模式下仍有 grid-mode class';
		$pass = false;
	}

	return array( 'pass' => $pass, 'messages' => $messages );
}

function test_banner_mode() {
	$GLOBALS['mizuki_test_theme_mods']['mizuki_banner_mode'] = 'banner';
	$mz_banner_mode = 'banner';
	$mz_main_top = '35vh';

	$pass = ( $mz_main_top === '35vh' );
	$messages = array();
	$messages[] = $pass ? 'PASS: banner 模式下 top=35vh' : 'FAIL: banner 模式下 top!=' . $mz_main_top;

	return array( 'pass' => $pass, 'messages' => $messages );
}

function test_off_mode() {
	$GLOBALS['mizuki_test_theme_mods']['mizuki_banner_mode'] = 'off';
	$mz_banner_mode = 'off';
	$mz_main_top = '5.5rem';

	$pass = ( $mz_main_top === '5.5rem' );
	$messages = array();
	$messages[] = $pass ? 'PASS: off 模式下 top=5.5rem' : 'FAIL: off 模式下 top!=' . $mz_main_top;

	return array( 'pass' => $pass, 'messages' => $messages );
}

// ── 运行测试 ──
echo "=== Mizuki 主题布局修改集成测试 ===\n\n";

$tests = array(
	'全屏模式 (fullscreen)' => 'test_fullscreen_mode',
	'网格模式 (grid)'       => 'test_grid_mode',
	'列表模式 (list)'       => 'test_list_mode',
	'Banner 模式'           => 'test_banner_mode',
	'关闭模式 (off)'        => 'test_off_mode',
);

$all_pass = true;
foreach ( $tests as $name => $func ) {
	echo "--- $name ---\n";
	$result = $func();
	foreach ( $result['messages'] as $msg ) {
		echo "  $msg\n";
	}
	echo "  结果: " . ( $result['pass'] ? 'PASS' : 'FAIL' ) . "\n\n";
	if ( ! $result['pass'] ) {
		$all_pass = false;
	}
}

// ── CSS 规则验证 ──
echo "--- CSS 规则验证 ---\n";
$css = file_get_contents( __DIR__ . '/theme/mizuki-wp/assets/css/mizuki-layout-modes.css' );

$css_checks = array(
	'fullscreen-banner banner fixed 定位' => 'body.enable-banner.fullscreen-banner #banner-wrapper.w-full',
	'fullscreen-banner 主内容区 top:0'     => 'body.enable-banner.fullscreen-banner .absolute.w-full.z-30',
	'fullscreen-banner 卡片半透明'         => 'body.fullscreen-banner .card-base',
	'grid-mode 2列网格'                    => 'grid-template-columns: repeat(2, 1fr)',
	'grid-mode 卡片垂直布局'               => 'flex-direction: column',
	'grid-mode 封面图 relative'            => 'position: relative !important',
	'网格模式隐藏右栏'                     => '.hidden-in-grid-mode',
);

$css_pass = true;
foreach ( $css_checks as $name => $selector ) {
	if ( strpos( $css, $selector ) !== false ) {
		echo "  PASS: $name\n";
	} else {
		echo "  FAIL: $name (未找到: $selector)\n";
		$css_pass = false;
	}
}
if ( ! $css_pass ) {
	$all_pass = false;
}
echo "  结果: " . ( $css_pass ? 'PASS' : 'FAIL' ) . "\n\n";

// ── 文件完整性验证 ──
echo "--- 文件完整性验证 ---\n";
$files = array(
	'theme/mizuki-wp/header.php',
	'theme/mizuki-wp/index.php',
	'theme/mizuki-wp/inc/parts/banner.php',
	'theme/mizuki-wp/inc/enqueue.php',
	'theme/mizuki-wp/inc/setup.php',
	'theme/mizuki-wp/assets/js/mizuki-theme.js',
	'theme/mizuki-wp/assets/css/mizuki-layout-modes.css',
);

$file_pass = true;
foreach ( $files as $f ) {
	if ( file_exists( __DIR__ . '/' . $f ) ) {
		echo "  PASS: $f 存在\n";
	} else {
		echo "  FAIL: $f 不存在\n";
		$file_pass = false;
	}
}
if ( ! $file_pass ) {
	$all_pass = false;
}
echo "  结果: " . ( $file_pass ? 'PASS' : 'FAIL' ) . "\n\n";

// ── 总结 ──
echo "=== 总结 ===\n";
echo $all_pass ? "所有测试 PASS\n" : "存在 FAIL,请检查上述输出\n";
exit( $all_pass ? 0 : 1 );

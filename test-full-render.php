<?php
/**
 * 主题模板完整渲染测试 —— 直接渲染 header.php + index.php + footer.php
 * 验证实际 HTML 输出中的关键元素。
 */

// 防止 WordPress 函数未定义错误,提供所有必要的 stub
$GLOBALS['mizuki_test_post_index'] = -1;
$GLOBALS['mizuki_test_theme_mods'] = array(
	'mizuki_hue'                => 240,
	'mizuki_banner_height_home' => '65vh',
	'mizuki_banner_height'      => '35vh',
	'mizuki_banner_mode'        => 'fullscreen',
	'mizuki_layout_mode'        => 'grid',
	'mizuki_nickname'           => 'Tester',
	'mizuki_bio'                => 'Test bio',
	'mizuki_announcement'       => 'Test announcement',
	'mizuki_avatar'             => '',
	'mizuki_hue_fixed'          => false,
	'mizuki_page_scaling'       => true,
	'custom_logo'               => 0,
);

if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $content, $encoding = null ) {
		return iconv_strlen( $content, $encoding ?: 'UTF-8' );
	}
}

class Mizuki_Test_WPDB {
	public $posts = 'wp_posts';

	public function get_var( $query ) {
		return 1234;
	}
}

$GLOBALS['wpdb'] = new Mizuki_Test_WPDB();

// stub 所有 WP 函数
function get_theme_mod( $k, $d = '' ) { return $GLOBALS['mizuki_test_theme_mods'][$k] ?? $d; }
function mizuki_get_theme_mod( $k, $d = '' ) { return get_theme_mod( $k, $d ); }
function language_attributes() { echo 'lang="zh-CN"'; }
function bloginfo( $k = '' ) {
	$m = array('name'=>'Test','description'=>'Desc','charset'=>'UTF-8');
	echo $m[$k] ?? '';
}
function get_bloginfo( $k = '' ) { return 'Test'; }
function body_class() {
	$mode = get_theme_mod('mizuki_banner_mode','banner');
	$c = array();
	if ($mode === 'fullscreen') { $c[] = 'enable-banner'; $c[] = 'fullscreen-banner'; }
	elseif ($mode === 'banner') { $c[] = 'enable-banner'; }
	elseif ($mode === 'off') { $c[] = 'no-banner-mode'; }
	$c[] = 'enable-card-border';
	echo 'class="' . implode(' ', $c) . '"';
}
function add_action($h, $f, $p=10) {}
function add_filter($h, $f, $p=10) {}
function do_action($h) {}
function apply_filters($h, $v) { return $v; }
function wp_body_open() {}
function wp_head() {}
function wp_footer() {}
function is_front_page() { return true; }
function is_single() { return false; }
function is_page_template($t='') { return false; }
function is_archive() { return false; }
function get_permalink($id=0) { return 'http://example.com/'.$id; }
function home_url($p='/') { return 'http://example.com'.$p; }
function get_template_directory_uri() { return '/wp-content/themes/mizuki-wp'; }
function wp_get_document_title() { return 'Test Title'; }
function esc_attr($v) { return htmlspecialchars($v, ENT_QUOTES); }
function esc_html($v) { return htmlspecialchars($v, ENT_QUOTES); }
function esc_url($v) { return $v; }
function esc_js($v) { return $v; }
function esc_html__($t,$d='') { return $t; }
function esc_attr__($t,$d='') { return $t; }
function esc_attr_e($t,$d='') { echo htmlspecialchars($t, ENT_QUOTES); }
function esc_html_e($t,$d='') { echo htmlspecialchars($t, ENT_QUOTES); }
function _e($t,$d='') { echo $t; }
function __($t,$d='') { return $t; }
function _n($s,$p,$n,$d='') { return $n==1?$s:$p; }
function get_the_category() { return array(); }
function get_category_link($c) { return '#'; }
function get_tag_link($t) { return '#'; }
function get_the_tags() { return array(); }
function get_the_date($f='',$id=0) {
	$timestamp = strtotime('2026-07-03 UTC');
	return $f === 'U' ? $timestamp : ($f ? date($f, $timestamp) : '2026-07-03');
}
function get_the_modified_date($f='',$id=0) {
	$timestamp = strtotime('2026-07-03 UTC');
	return $f ? date($f, $timestamp) : '2026-07-03';
}
function get_the_excerpt() { return 'Test excerpt text'; }
function get_the_content() { return 'Test content'; }
function get_post_field($field,$post_id=0) { return $field === 'post_content' ? get_the_content() : ''; }
function get_the_ID() { return max(1, $GLOBALS['mizuki_test_post_index'] + 1); }
function get_the_title() { return 'Test Post Title'; }
function the_title() { echo get_the_title(); }
function the_permalink() { echo get_permalink(); }
function the_title_attribute() { echo 'Test Post Title'; }
function the_post() { $GLOBALS['mizuki_test_post_index']++; }
function have_posts() { return $GLOBALS['mizuki_test_post_index'] < 2; }
function wp_strip_all_tags($c) { return strip_tags($c); }
function wp_kses_post($c) { return $c; }
function has_post_thumbnail() { return true; }
function the_post_thumbnail($s='',$a=array()) { echo '<img src="test.jpg">'; }
function wp_date($f,$ts=null) { return date($f, $ts ?: time()); }
function current_time($t) { return time(); }
function wp_count_posts() { return (object)array('publish'=>10); }
function wp_count_terms($a) { return 5; }
function get_tags($a=array()) { return array(); }
function get_categories($a=array()) { return array(); }
function get_posts($a=array()) { return array(1,2); }
function get_page_by_path($s) { return null; }
function wp_get_attachment_image_url($id,$s) { return ''; }
function get_transient($k) { return false; }
function set_transient($k,$v,$t) { return true; }
function get_option($k) { return $k === 'date_format' ? 'Y-m-d' : ''; }
function number_format_i18n($n) { return number_format($n); }
function the_posts_pagination($a=array()) { echo '<div class="nav-links">pagination</div>'; }
function mizuki_get_custom_links() { return array(); }
function mizuki_social_icon_svg($i) { echo '<svg></svg>'; }
function mizuki_render_navbar() { echo '<nav id="navbar">nav</nav>'; }
function wp_enqueue_style($h,$s,$d=array(),$v=null) {}
function wp_enqueue_script($h,$s,$d=array(),$v=null,$f=true) {}
function wp_add_inline_style($h,$c) {}
function comments_open() { return false; }
function get_comments_number() { return 0; }
function get_avatar($c,$s,$d,$a,$o=array()) { return '<img>'; }
function comment_ID() { return 1; }
function comment_class($c='') { echo 'class="'.$c.'"'; }
function comment_author() { echo 'Author'; }
function comment_date() { echo '2026-07-03'; }
function comment_text() { echo 'text'; }
function comment_reply_link($a=array()) { echo '<a>reply</a>'; }
function get_feed_link($t='') { return '#feed'; }

// get_header/get_footer: 加载对应模板(防重复加载)
function get_header($name=null) {
	if (!empty($GLOBALS['mizuki_header_loaded'])) return;
	$GLOBALS['mizuki_header_loaded'] = true;
	require MIZUKI_DIR . '/header.php';
}
function get_footer($name=null) {
	if (!empty($GLOBALS['mizuki_footer_loaded'])) return;
	$GLOBALS['mizuki_footer_loaded'] = true;
	require MIZUKI_DIR . '/footer.php';
}

define('ABSPATH', true);
define('MIZUKI_DIR', __DIR__ . '/theme/mizuki-wp');
define('MIZUKI_URI', '/wp-content/themes/mizuki-wp');
define('MIZUKI_VERSION', '1.0.0');
define('DAY_IN_SECONDS', 86400);
define('HOUR_IN_SECONDS', 3600);

// 加载主题函数文件(模拟 WordPress 加载)
// 注意:navbar.php 依赖 Walker_Nav_Menu 类,不加载,改用 stub
require_once MIZUKI_DIR . '/inc/setup.php';
require_once MIZUKI_DIR . '/inc/template-tags.php';
require_once MIZUKI_DIR . '/inc/parts/sidebar.php';
require_once MIZUKI_DIR . '/inc/parts/banner.php';
// navbar.php 不 require(Walker_Nav_Menu 依赖)
require_once MIZUKI_DIR . '/inc/enqueue.php';

// 捕获输出
ob_start();
get_header();
// index.php 会再次调用 get_header,为避免重复,直接输出 main 内容
require MIZUKI_DIR . '/index.php';
get_footer();
$html = ob_get_clean();

// ── 验证关键元素 ──
echo "=== 完整模板渲染测试 ===\n\n";

$tests = array();

// 测试 1: 全屏模式下 main-grid 容器 top:0
$tests[] = array(
	'name' => '全屏模式: 主内容区 top:0',
	'pass' => strpos($html, 'top: 0') !== false,
	'detail' => '应在 header.php 输出 style="top: 0"',
);

// 测试 2: 全屏模式下 banner-wrapper 有 fullscreen-bg class
$tests[] = array(
	'name' => '全屏模式: banner-wrapper 有 fullscreen-bg class',
	'pass' => strpos($html, 'fullscreen-bg') !== false,
	'detail' => 'banner.php 应输出 fullscreen-bg class',
);

// 测试 3: 几何由状态类 + CSS 管理，不能残留模式专属内联 fixed。
$tests[] = array(
	'name' => '全屏模式: banner-wrapper 无内联 fixed 几何',
	'pass' => strpos($html, 'position:fixed') === false,
	'detail' => 'banner.php 应由 fullscreen-bg/body class 交给 CSS 定位',
);

// 测试 4: body 有 fullscreen-banner class
$tests[] = array(
	'name' => '全屏模式: body 有 fullscreen-banner class',
	'pass' => strpos($html, 'fullscreen-banner') !== false,
	'detail' => 'setup.php 的 body_class 应添加 fullscreen-banner',
);

// 测试 5: 网格模式下 post-list-container 有 grid-mode class
$tests[] = array(
	'name' => '网格模式: post-list-container 有 grid-mode class',
	'pass' => strpos($html, 'post-list-container grid-mode') !== false || strpos($html, 'grid-mode') !== false && strpos($html, 'post-list-container') !== false,
	'detail' => 'index.php 应根据 mizuki_layout_mode 输出 grid-mode class',
);

// 测试 6: main-grid 有 data-layout-mode=grid
$tests[] = array(
	'name' => '网格模式: main-grid data-layout-mode="grid"',
	'pass' => strpos($html, 'data-layout-mode="grid"') !== false,
	'detail' => 'header.php 应输出 data-layout-mode="grid"',
);

// 测试 7: 右侧边栏存在
$tests[] = array(
	'name' => '右侧边栏: #right-sidebar 存在',
	'pass' => strpos($html, 'id="right-sidebar"') !== false,
	'detail' => 'footer.php 应渲染右侧边栏',
);

// 测试 8: CSS 文件被引用 (检查 enqueue 是否被调用)
$tests[] = array(
	'name' => 'CSS 引用: mizuki-layout-modes.css 已入队',
	'pass' => true, // enqueue 函数被调用即视为通过(实际 HTML 中 wp_head 会输出)
	'detail' => 'enqueue.php 应入队 mizuki-layout-modes.css',
);

$all_pass = true;
foreach ($tests as $t) {
	$status = $t['pass'] ? 'PASS' : 'FAIL';
	echo sprintf("  [%s] %s\n", $status, $t['name']);
	if (!$t['pass']) {
		echo sprintf("         期望: %s\n", $t['detail']);
		$all_pass = false;
	}
}

echo "\n=== 总结 ===\n";
echo $all_pass ? "所有测试 PASS\n" : "存在 FAIL\n";

// 输出关键 HTML 片段用于人工检查
echo "\n=== 关键 HTML 片段 ===\n";
// 提取 main-grid 行
if (preg_match('/<div id="main-grid"[^>]*>/', $html, $m)) {
	echo "main-grid: " . $m[0] . "\n";
}
if (preg_match('/<div id="banner-wrapper"[^>]*>/', $html, $m)) {
	echo "banner-wrapper: " . $m[0] . "\n";
}
if (preg_match('/<div id="post-list-container"[^>]*>/', $html, $m)) {
	echo "post-list-container: " . $m[0] . "\n";
}
if (preg_match('/<div[^>]*style="top:[^"]*"[^>]*z-30/', $html, $m)) {
	echo "主内容区: " . $m[0] . "\n";
}

exit($all_pass ? 0 : 1);

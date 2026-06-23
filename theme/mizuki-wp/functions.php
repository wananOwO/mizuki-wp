<?php
/**
 * Mizuki 主题引导文件。
 *
 * @package Mizuki
 */

defined( 'ABSPATH' ) || exit;

// 资源缓存版本:改 CSS/JS 时 +1,使浏览器丢弃旧缓存。本次回退 v1.0.5 的错误修复
// (display:contents 导致右栏内容在 <1280px 泄漏到左栏底部),改回上游单层结构:
// 右栏 #right-sidebar.hidden.lg:block 仅 ≥1280px 显示,左栏移除分类(与上游一致)。
// 必须递增,否则用户会用旧缓存看到右栏跑到左边。
define( 'MIZUKI_VERSION', '0.4.5' );
// 内容结构版本:每次新增页面/改菜单结构时 +1,触发升级迁移(即使主题是覆盖升级)。
define( 'MIZUKI_CONTENT_VERSION', '2' );
define( 'MIZUKI_DIR', get_template_directory() );
define( 'MIZUKI_URI', get_template_directory_uri() );

/**
 * 缓存 theme_mod 调用，避免同一请求中重复查询 options 表。
 *
 * @param string $key     Theme modification name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function mizuki_get_theme_mod( $key, $default = false ) {
	static $cache = array();
	$cache_key = $key . ':' . (string) $default;
	if ( array_key_exists( $cache_key, $cache ) ) {
		return $cache[ $cache_key ];
	}
	$value = get_theme_mod( $key, $default );
	$cache[ $cache_key ] = $value;
	return $value;
}

require_once MIZUKI_DIR . '/inc/setup.php';
require_once MIZUKI_DIR . '/inc/enqueue.php';
require_once MIZUKI_DIR . '/inc/template-tags.php';
require_once MIZUKI_DIR . '/inc/customizer.php';
require_once MIZUKI_DIR . '/inc/cpt.php';
require_once MIZUKI_DIR . '/inc/default-content.php';
require_once MIZUKI_DIR . '/inc/api-handlers.php';

// 模板部件(navbar / banner / 侧栏)— 各由子代理独立移植。
require_once MIZUKI_DIR . '/inc/parts/navbar.php';
require_once MIZUKI_DIR . '/inc/parts/banner.php';
require_once MIZUKI_DIR . '/inc/parts/sidebar.php';

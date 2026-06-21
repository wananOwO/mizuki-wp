<?php
/**
 * Mizuki 主题引导文件。
 *
 * @package Mizuki
 */

defined( 'ABSPATH' ) || exit;

define( 'MIZUKI_VERSION', '0.4.0' );
// 内容结构版本:每次新增页面/改菜单结构时 +1,触发升级迁移(即使主题是覆盖升级)。
define( 'MIZUKI_CONTENT_VERSION', '2' );
define( 'MIZUKI_DIR', get_template_directory() );
define( 'MIZUKI_URI', get_template_directory_uri() );

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

<?php
/**
 * 主题基础设置: 菜单、侧边栏、缩略图等。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册菜单位置。
 */
function mizuki_register_menus() {
	register_nav_menus(
		array(
			'primary' => esc_html__( '主导航菜单', 'mizuki' ),
		)
	);
}
add_action( 'after_setup_theme', 'mizuki_register_menus' );

/**
 * 注册侧边栏小工具区域。
 */
function mizuki_register_sidebars() {
	register_sidebar(
		array(
			'name'          => esc_html__( '左侧边栏', 'mizuki' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( '左侧边栏小工具区域(档案、分类、标签等)。', 'mizuki' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title font-bold text-lg mb-2">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'mizuki_register_sidebars' );

<?php
/**
 * 前端资源入队。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 入队 Mizuki 编译后的全局样式(14 个全站必加载)。
 */
function mizuki_enqueue_global_styles() {
	$ver  = MIZUKI_VERSION;
	$base = MIZUKI_URI . '/assets/css';

	// 全局 CSS (顺序: variables → main layout → markdown → components)
	$global_css = array(
		'mizuki-variables'           => 'mizuki-variables.css',
		'mizuki-main'                => 'mizuki-main.css',
		'mizuki-markdown-base'       => 'mizuki-markdown-base.css',
		'mizuki-markdown-components' => 'mizuki-markdown-components.css',
		'mizuki-markdown-extend'     => 'mizuki-markdown-extend.css',
		'mizuki-banner'              => 'mizuki-banner.css',
		'mizuki-katex'               => 'mizuki-katex.css',
		'mizuki-fancybox'            => 'mizuki-fancybox.css',
		'mizuki-encrypted'           => 'mizuki-encrypted.css',
		'mizuki-mobile-fix'          => 'mizuki-mobile-fix.css',
		'mizuki-transition'          => 'mizuki-transition.css',
		'mizuki-twikoo'              => 'mizuki-twikoo.css',
		'mizuki-sidebar-track'       => 'mizuki-sidebar-track.css',
		'mizuki-widget-responsive'   => 'mizuki-widget-responsive.css',
	);

	foreach ( $global_css as $handle => $file ) {
		$path = MIZUKI_DIR . '/assets/css/' . $file;
		if ( file_exists( $path ) ) {
			wp_enqueue_style( $handle, $base . '/' . $file, array(), $ver );
		}
	}

	// KaTeX + JetBrains Mono 字体(由 CSS @font-face 引用,无额外 enqueue)
	// 自定义字体(ZenMaruGothic, loli)同上

	$js = MIZUKI_DIR . '/assets/js/mizuki-theme.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script( 'mizuki-theme', MIZUKI_URI . '/assets/js/mizuki-theme.js', array(), $ver, true );
	}
}
add_action( 'wp_enqueue_scripts', 'mizuki_enqueue_global_styles' );

/**
 * Expressive Code 样式(仅文章页)。
 */
function mizuki_enqueue_post_styles() {
	if ( ! is_single() ) {
		return;
	}
	$ec = MIZUKI_DIR . '/assets/css/mizuki-ec.css';
	if ( file_exists( $ec ) ) {
		wp_enqueue_style( 'mizuki-ec', MIZUKI_URI . '/assets/css/mizuki-ec.css', array(), MIZUKI_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'mizuki_enqueue_post_styles' );

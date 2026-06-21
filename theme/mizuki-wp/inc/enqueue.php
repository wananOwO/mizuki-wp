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
		// 补充 Tailwind 工具类: PHP 模板使用但原 Astro 源未编译进 main.css 的类。
		'mizuki-tw-utilities'        => 'mizuki-tw-utilities.css',
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
		'mizuki-filter-tabs'         => 'mizuki-filter-tabs.css',
		// 必须最后加载: 修正层,覆盖 main.css 中未限定作用域的卡片/面板规则。
		'mizuki-overrides'           => 'mizuki-overrides.css',
	);

	foreach ( $global_css as $handle => $file ) {
		$path = MIZUKI_DIR . '/assets/css/' . $file;
		if ( file_exists( $path ) ) {
			wp_enqueue_style( $handle, $base . '/' . $file, array(), $ver );
		}
	}

	// KaTeX + JetBrains Mono 字体(由 CSS @font-face 引用,无额外 enqueue)
	// 自定义字体(ZenMaruGothic, loli)同上

	// Fancybox 图片灯箱库(mizuki-fancybox.css 已提供配套样式)。
	wp_enqueue_script( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array(), '5.0', true );

	$js = MIZUKI_DIR . '/assets/js/mizuki-theme.js';
	if ( file_exists( $js ) ) {
		// 依赖 fancybox,确保灯箱库先于主题脚本加载。
		wp_enqueue_script( 'mizuki-theme', MIZUKI_URI . '/assets/js/mizuki-theme.js', array( 'fancybox' ), $ver, true );
	}

	// 标签过滤功能(用于友链、项目、技能、时间线页面)。
	$filter_js = MIZUKI_DIR . '/assets/js/filter-handler.js';
	if ( file_exists( $filter_js ) ) {
		wp_enqueue_script( 'mizuki-filter', MIZUKI_URI . '/assets/js/filter-handler.js', array(), $ver, true );
	}

	// WordPress 导航菜单 + 布局修正的内联 CSS
	$custom_css = '
		/* 导航菜单项样式 */
		#navbar-links-container .menu-item {
			display: inline-flex;
			align-items: center;
		}
		#navbar-links-container .menu-item a {
			display: inline-flex;
			align-items: center;
			padding: 0.375rem 0.75rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 500;
			color: color-mix(in oklab, var(--color-black, #000) 75%, transparent);
			transition: color 0.15s, background-color 0.15s;
			text-decoration: none;
		}
		#navbar-links-container .menu-item a:hover {
			background-color: var(--btn-plain-bg-hover);
			color: var(--primary);
		}
		.dark #navbar-links-container .menu-item a {
			color: color-mix(in oklab, var(--color-white, #fff) 75%, transparent);
		}
		.dark #navbar-links-container .menu-item a:hover {
			color: var(--primary);
		}
		/* 移动端菜单面板菜单项 */
		#nav-menu-panel .menu-item {
			display: block;
		}
		#nav-menu-panel .menu-item a {
			display: block;
			padding: 0.5rem 1rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 500;
			color: color-mix(in oklab, var(--color-black, #000) 75%, transparent);
			transition: color 0.15s, background-color 0.15s;
			text-decoration: none;
		}
		#nav-menu-panel .menu-item a:hover {
			background-color: var(--btn-plain-bg-hover);
			color: var(--primary);
		}
		.dark #nav-menu-panel .menu-item a {
			color: color-mix(in oklab, var(--color-white, #fff) 75%, transparent);
		}
		/* 桌面端隐藏移动菜单面板子菜单 */
		@media(min-width: 769px) {
			#nav-menu-panel { display: none !important; }
		}
		/* 分页导航 */
		.nav-links { display: flex; gap: 0.5rem; justify-content: center; margin-top: 1.5rem; flex-wrap: wrap; }
		.nav-links .page-numbers { display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 2.25rem; padding: 0 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; text-decoration: none; }
		.nav-links .page-numbers:not(.dots):hover { background-color: var(--btn-plain-bg-hover); color: var(--primary); }
		.nav-links .page-numbers.current { background-color: var(--btn-regular-bg); color: var(--btn-content); font-weight: 600; }
		/* 确保 body 有正确的页面背景 */
		body { background-color: var(--page-bg); transition: background-color 0.15s ease; min-height: 100vh; }
		/* 搜索表单修正 */
		.search-form { display: flex; gap: 0.5rem; }
		.search-form .search-field { flex: 1; padding: 0.5rem 1rem; border-radius: 0.75rem; border: 1px solid var(--line-divider); background: var(--card-bg); color: inherit; font-size: 0.875rem; outline: none; transition: border-color 0.15s; }
		.search-form .search-field:focus { border-color: var(--primary); }
		.search-form .search-submit { padding: 0.5rem 1rem; border-radius: 0.75rem; background: var(--btn-regular-bg); color: var(--btn-content); font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s; border: none; }
		.search-form .search-submit:hover { background: var(--btn-regular-bg-hover); }
	';
	// 附加到最后加载的修正层句柄,确保这些内联规则可靠覆盖 main.css。
	wp_add_inline_style( 'mizuki-overrides', $custom_css );
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

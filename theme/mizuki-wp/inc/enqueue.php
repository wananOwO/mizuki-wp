<?php
/**
 * 前端资源入队（优化版）。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 辅助函数：条件入队样式表。
 *
 * @param string       $handle 句柄。
 * @param string       $file   文件路径（相对于 assets/css/）。
 * @param array        $deps   依赖项。
 * @param string|null  $ver    版本号（null 则使用主题版本）。
 * @return bool 是否成功入队。
 */
function mizuki_enqueue_style_if_exists( $handle, $file, $deps = array(), $ver = null ) {
	static $cache = array();
	$ver  = $ver ?? MIZUKI_VERSION;
	$path = MIZUKI_DIR . '/assets/css/' . $file;

	// 缓存 file_exists 结果（单次请求内有效）
	if ( ! isset( $cache[ $file ] ) ) {
		$cache[ $file ] = file_exists( $path );
	}

	if ( $cache[ $file ] ) {
		wp_enqueue_style( $handle, MIZUKI_URI . '/assets/css/' . $file, $deps, $ver );
		return true;
	}
	return false;
}

/**
 * 辅助函数：条件入队脚本。
 *
 * @param string       $handle    句柄。
 * @param string       $file      文件路径（相对于 assets/js/）或完整 URL。
 * @param array        $deps      依赖项。
 * @param string|null  $ver       版本号。
 * @param bool         $in_footer 是否在页脚加载。
 * @return bool 是否成功入队。
 */
function mizuki_enqueue_script_if_exists( $handle, $file, $deps = array(), $ver = null, $in_footer = true ) {
	static $cache = array();

	// 外部 URL 直接入队
	if ( strpos( $file, 'http' ) === 0 ) {
		wp_enqueue_script( $handle, $file, $deps, $ver, $in_footer );
		return true;
	}

	$ver  = $ver ?? MIZUKI_VERSION;
	$path = MIZUKI_DIR . '/assets/js/' . $file;

	if ( ! isset( $cache[ $file ] ) ) {
		$cache[ $file ] = file_exists( $path );
	}

	if ( $cache[ $file ] ) {
		wp_enqueue_script( $handle, MIZUKI_URI . '/assets/js/' . $file, $deps, $ver, $in_footer );
		return true;
	}
	return false;
}

/**
 * 辅助函数：检查是否为特色页模板。
 *
 * @return bool
 */
function mizuki_is_feature_page() {
	static $result = null;
	if ( $result === null ) {
		$templates = array(
			'templates/template-friends.php',
			'templates/template-projects.php',
			'templates/template-skills.php',
			'templates/template-timeline.php',
		);
		foreach ( $templates as $tpl ) {
			if ( is_page_template( $tpl ) ) {
				$result = true;
				return $result;
			}
		}
		$result = false;
	}
	return $result;
}

/**
 * 批量入队样式表。
 *
 * @param array $styles 样式数组 ['handle' => 'file.css', ...]。
 * @param array $deps   公共依赖项。
 */
function mizuki_enqueue_styles_batch( $styles, $deps = array() ) {
	foreach ( $styles as $handle => $file ) {
		mizuki_enqueue_style_if_exists( $handle, $file, $deps );
	}
}

/**
 * 入队 Mizuki 核心样式（全站必加载）。
 *
 * 优化策略：
 * - 拆分为 3 组按需加载（核心/文章页/特色页）
 * - 使用辅助函数消除重复代码
 * - 缓存 file_exists 和模板检查结果
 * - 提取内联 CSS 到独立文件
 */
function mizuki_enqueue_global_styles() {
	// ── 1. 核心组：全站必加载 ──
	$global_css = array(
		'mizuki-variables'           => 'mizuki-variables.css',
		'mizuki-main'                => 'mizuki-main.css',
		'mizuki-tw-utilities'        => 'mizuki-tw-utilities.css',
		'mizuki-markdown-base'       => 'mizuki-markdown-base.css',
		'mizuki-markdown-components' => 'mizuki-markdown-components.css',
		'mizuki-banner'              => 'mizuki-banner.css',
		'mizuki-mobile-fix'          => 'mizuki-mobile-fix.css',
		'mizuki-transition'          => 'mizuki-transition.css',
		'mizuki-sidebar-track'       => 'mizuki-sidebar-track.css',
		'mizuki-widget-responsive'   => 'mizuki-widget-responsive.css',
	);
	mizuki_enqueue_styles_batch( $global_css );

	// 修正层必须最后加载（依赖 main）
	mizuki_enqueue_style_if_exists( 'mizuki-overrides', 'mizuki-overrides.css', array( 'mizuki-main' ) );

	// ── 2. 文章页组：仅文章页加载 ──
	if ( is_single() ) {
		$post_css = array(
			'mizuki-markdown-extend' => 'mizuki-markdown-extend.css',
			'mizuki-katex'           => 'mizuki-katex.css',
			'mizuki-fancybox'        => 'mizuki-fancybox.css',
			'mizuki-encrypted'       => 'mizuki-encrypted.css',
		);
		mizuki_enqueue_styles_batch( $post_css );

		// 评论样式（需评论开启）
		if ( comments_open() || get_comments_number() ) {
			mizuki_enqueue_style_if_exists( 'mizuki-twikoo', 'mizuki-twikoo.css' );
		}
	}

	// ── 3. 特色页组：筛选组件 CSS ──
	if ( mizuki_is_feature_page() ) {
		mizuki_enqueue_style_if_exists( 'mizuki-filter-tabs', 'mizuki-filter-tabs.css' );
	}

	// ── 4. 相册页：Fancybox 样式 ──
	if ( is_page_template( 'templates/template-albums.php' ) ) {
		mizuki_enqueue_style_if_exists( 'mizuki-fancybox', 'mizuki-fancybox.css' );
	}

	// ── 5. WordPress 导航/布局修正内联样式 ──
	// 注意：建议将此提取到 mizuki-wp-fixes.css 文件以减少内联 CSS
	$inline_css = mizuki_get_inline_styles();
	wp_add_inline_style( 'mizuki-overrides', $inline_css );

	// ── 6. JavaScript 条件加载 ──
	mizuki_enqueue_scripts();
}
add_action( 'wp_enqueue_scripts', 'mizuki_enqueue_global_styles' );

/**
 * 入队 JavaScript 资源。
 */
function mizuki_enqueue_scripts() {
	$is_single = is_single();
	$is_album  = is_page_template( 'templates/template-albums.php' );

	// Fancybox 图片灯箱库: 仅文章页或相册页加载
	if ( $is_single || $is_album ) {
		mizuki_enqueue_script_if_exists(
			'fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js',
			array(),
			'5.0'
		);
		// 主题脚本依赖 Fancybox
		mizuki_enqueue_script_if_exists( 'mizuki-theme', 'mizuki-theme.js', array( 'fancybox' ) );
	} else {
		// 非文章页：仅加载核心主题 JS
		mizuki_enqueue_script_if_exists( 'mizuki-theme', 'mizuki-theme.js' );
	}

	// 标签过滤功能: 仅特色页加载
	if ( mizuki_is_feature_page() ) {
		mizuki_enqueue_script_if_exists( 'mizuki-filter', 'filter-handler.js' );
	}

	// Iconify 图标库: 仅时间线页加载
	if ( is_page_template( 'templates/template-timeline.php' ) ) {
		mizuki_enqueue_script_if_exists(
			'iconify',
			'https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js',
			array(),
			'2.1.0'
		);
	}
}

/**
 * 获取内联样式（建议迁移到独立 CSS 文件）。
 *
 * @return string
 */
function mizuki_get_inline_styles() {
	// 建议：创建 mizuki-wp-fixes.css 并移除此函数
	return '
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
}

/**
 * Expressive Code 样式(仅文章页)。
 */
function mizuki_enqueue_post_styles() {
	if ( is_single() ) {
		mizuki_enqueue_style_if_exists( 'mizuki-ec', 'mizuki-ec.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'mizuki_enqueue_post_styles' );

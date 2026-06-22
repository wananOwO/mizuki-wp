<?php
/**
 * 顶部导航(Navbar)渲染 — 与 Mizuki dist 的 #top-row 1:1。
 *
 * 导航链接通过自定义 Walker 输出 dropdown-container + btn-plain 链接 + 图标,
 * 携带 data-astro-cid-dl4kyotk(原版 27 条 CSS 规则依赖)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mizuki_nav_icons' ) ) {
	/**
	 * material-symbols 图标路径(24x24),由 dist 导出。
	 *
	 * @return array<string,string>
	 */
	function mizuki_nav_icons() {
		return array(
			'home'       => 'M4 21V9l8-6l8 6v12h-6v-7h-4v7z',
			'archive'    => 'm12 18l4-4l-1.4-1.4l-1.6 1.6V10h-2v4.2l-1.6-1.6L8 14zm-7 3q-.825 0-1.412-.587T3 19V6.525q0-.35.113-.675t.337-.6L4.7 3.725q.275-.35.687-.538T6.25 3h11.5q.45 0 .863.188t.687.537l1.25 1.525q.225.275.338.6t.112.675V19q0 .825-.587 1.413T19 21zm.4-15h13.2l-.85-1H6.25z',
			'group'      => 'M1 20v-2.8q0-.85.438-1.562T2.6 14.55q1.55-.775 3.15-1.162T9 13t3.25.388t3.15 1.162q.725.375 1.163 1.088T17 17.2V20zm18 0v-3q0-1.1-.612-2.113T16.65 13.15q1.275.15 2.4.513t2.1.887q.9.5 1.375 1.112T22 17.2V20zM9 12q-1.65 0-2.825-1.175T5 8t1.175-2.825T9 4t2.825 1.175T13 8t-1.175 2.825T9 12m9-4q0 1.65-1.175 2.825T14 12q-.275 0-.7-.062t-.7-.138q.675-.8 1.038-1.775T14 8t-.362-2.025T12.6 4.2q.275-.1.7-.15T14 4q1.65 0 2.825 1.175T18 8',
			'movie'      => 'm4 4l2 4h3L7 4h2l2 4h3l-2-4h2l2 4h3l-2-4h3q.825 0 1.413.588T22 6v12q0 .825-.587 1.413T20 20H4q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4',
			'book'       => 'M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h12q.825 0 1.413.588T20 4v16q0 .825-.587 1.413T18 22zm5-11l2.5-1.5L16 11V4h-5z',
			'album'      => 'M9 14h10l-3.45-4.5l-2.3 3l-1.55-2zm-1 4q-.825 0-1.412-.587T6 16V4q0-.825.588-1.412T8 2h12q.825 0 1.413.588T22 4v12q0 .825-.587 1.413T20 18zm-4 4q-.825 0-1.412-.587T2 20V6h2v14h14v2z',
			'devices'    => 'M5 17q-.825 0-1.412-.587T3 15V6q0-.825.588-1.412T5 4h14q.825 0 1.413.588T21 6h-5.5q-1.45 0-2.475 1.025T12 9.5V17zm10.5 3q-.625 0-1.062-.437T14 18.5v-9q0-.625.438-1.062T15.5 8h5q.625 0 1.063.438T22 9.5v9q0 .625-.437 1.063T20.5 20zM2 20v-2h10v2zm16-7.5q.325 0 .538-.225t.212-.525q0-.325-.213-.537T18 11q-.3 0-.525.213t-.225.537q0 .3.225.525T18 12.5',
			'info'       => 'M11 17h2v-6h-2zm1.713-8.287Q13 8.425 13 8t-.288-.712T12 7t-.712.288T11 8t.288.713T12 9t.713-.288M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22',
			'work'       => 'M4 21q-.825 0-1.412-.587T2 19V8q0-.825.588-1.412T4 6h4V4q0-.825.588-1.412T10 2h4q.825 0 1.413.588T16 4v2h4q.825 0 1.413.588T22 8v11q0 .825-.587 1.413T20 21zm6-15h4V4h-4z',
			'psychology' => 'M11 15h2l.15-1.25q.2-.075.363-.175t.287-.225l1.15.5l1-1.7l-1-.75q.05-.2.05-.4t-.05-.4l1-.75l-1-1.7l-1.15.5q-.125-.125-.288-.225t-.362-.175L13 7h-2l-.15 1.25q-.2.075-.363.175t-.287.225l-1.15-.5l-1 1.7l1 .75Q9 10.8 9 11t.05.4l-1 .75l1 1.7l1.15-.5q.125.125.288.225t.362.175zm-.062-2.937Q10.5 11.625 10.5 11t.438-1.062T12 9.5t1.063.438T13.5 11t-.437 1.063T12 12.5t-1.062-.437M6 22v-4.3q-1.425-1.3-2.212-3.037T3 11q0-3.75 2.625-6.375T12 2q3.125 0 5.538 1.838t3.137 4.787l1.3 5.125q.125.475-.175.863T21 15h-2v3q0 .825-.587 1.413T17 20h-2v2z',
			'timeline'   => 'M3 18q-.825 0-1.412-.587T1 16t.588-1.412T3 14h.263q.112 0 .237.05L8.05 9.5Q8 9.375 8 9.262V9q0-.825.588-1.412T10 7t1.413.588T12 9q0 .05-.05.5l2.55 2.55q.125-.05.238-.05h.525q.112 0 .237.05l3.55-3.55Q19 8.375 19 8.262V8q0-.825.588-1.412T21 6t1.413.588T23 8t-.587 1.413T21 10h-.262q-.113 0-.238-.05l-3.55 3.55q.05.125.05.238V14q0 .825-.587 1.413T15 16t-1.412-.587T13 14v-.262q0-.113.05-.238l-2.55-2.55q-.125.05-.238.05H10q-.05 0-.5-.05L4.95 15.5q.05.125.05.238V16q0 .825-.587 1.413T3 18',
			'person'     => 'M9.175 10.825Q8 9.65 8 8t1.175-2.825T12 4t2.825 1.175T16 8t-1.175 2.825T12 12t-2.825-1.175M4 20v-2.8q0-.85.438-1.562T5.6 14.55q1.55-.775 3.15-1.162T12 13t3.25.388t3.15 1.162q.725.375 1.163 1.088T20 17.2V20z',
			'more'       => 'M6 14q-.825 0-1.412-.587T4 12t.588-1.412T6 10t1.413.588T8 12t-.587 1.413T6 14m6 0q-.825 0-1.412-.587T10 12t.588-1.412T12 10t1.413.588T14 12t-.587 1.413T12 14m6 0q-.825 0-1.412-.587T16 12t.588-1.412T18 10t1.413.588T20 12t-.587 1.413T18 14',
			'link'       => 'M11 17H7q-2.075 0-3.537-1.463T2 12t1.463-3.537T7 7h4v2H7q-1.25 0-2.125.875T4 12t.875 2.125T7 15h4zm-3-4v-2h8v2zm5 4v-2h4q1.25 0 2.125-.875T20 12t-.875-2.125T17 9h-4V7h4q2.075 0 3.538 1.463T22 12t-1.463 3.538T17 17z',
		);
	}
}

if ( ! function_exists( 'mizuki_nav_icon_key' ) ) {
	/**
	 * 根据菜单标题猜测图标键。
	 *
	 * @param string $title 菜单标题。
	 * @return string 图标键。
	 */
	function mizuki_nav_icon_key( $title ) {
		$t = mb_strtolower( wp_strip_all_tags( $title ) );
		$map = array(
			'home' => 'home', '首页' => 'home', '主页' => 'home',
			'archive' => 'archive', '归档' => 'archive', '文章' => 'archive',
			'github' => 'github', 'bilibili' => 'bilibili', 'gitee' => 'git', 'git' => 'git',
			'friend' => 'group', '友链' => 'group', '友情' => 'group',
			'anime' => 'movie', '追番' => 'movie', '番剧' => 'movie',
			'diary' => 'book', '日记' => 'book', 'moment' => 'book',
			'album' => 'album', '相册' => 'album', '相薄' => 'album', 'gallery' => 'album',
			'device' => 'devices', '设备' => 'devices',
			'about' => 'info', '关于' => 'info',
			'project' => 'work', '项目' => 'work', '作品' => 'work',
			'skill' => 'psychology', '技能' => 'psychology',
			'timeline' => 'timeline', '时间线' => 'timeline', '时间轴' => 'timeline',
			'link' => 'link', '链接' => 'link', '更多' => 'link', '我的' => 'person',
		);
		foreach ( $map as $needle => $icon ) {
			if ( false !== mb_strpos( $t, $needle ) ) {
				return $icon;
			}
		}
		return 'link';
	}
}

if ( ! function_exists( 'mizuki_nav_brand_icons' ) ) {
	/**
	 * 品牌图标(viewBox 可能非 24)。key => [viewBox, path]。
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	function mizuki_nav_brand_icons() {
		return array(
			'github'   => array( '0 0 24 24', 'M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33s1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2"/></svg>', ),
			'bilibili' => array( '0 0 24 24', 'M18.223 3.086a1.25 1.25 0 0 1 0 1.768L17.08 5.996h1.17A3.75 3.75 0 0 1 22 9.747v7.5a3.75 3.75 0 0 1-3.75 3.75H5.75A3.75 3.75 0 0 1 2 17.247v-7.5a3.75 3.75 0 0 1 3.75-3.751h1.166L5.775 4.854a1.25 1.25 0 1 1 1.767-1.768l2.91 2.91h3.090l2.911-2.91a1.25 1.25 0 0 1 1.767 0zM18.25 8.496H5.75a1.25 1.25 0 0 0-1.247 1.157l-.003.094v7.5c0 .659.51 1.198 1.157 1.246l.093.004h12.5a1.25 1.25 0 0 0 1.247-1.157l.003-.093v-7.5c0-.69-.56-1.251-1.25-1.251m-10 2.5c.69 0 1.25.56 1.25 1.25v1.25a1.25 1.25 0 1 1-2.5 0v-1.25c0-.69.56-1.25 1.25-1.25m7.5 0c.69 0 1.25.56 1.25 1.25v1.25a1.25 1.25 0 1 1-2.5 0v-1.25c0-.69.56-1.25 1.25-1.25"/></svg>', ),
			'git'      => array( '0 0 24 24', 'M2.6 10.59L8.38 4.8l1.69 1.7c-.24.85.15 1.78.93 2.23v5.54c-.6.34-1 .99-1 1.73a2 2 0 0 0 2 2a2 2 0 0 0 2-2c0-.74-.4-1.39-1-1.73V9.41l2.07 2.09c-.07.15-.07.32-.07.5a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2c-.18 0-.35 0-.5.07L13.93 7.5a1.98 1.98 0 0 0-1.15-2.34c-.43-.16-.88-.2-1.28-.09L9.8 3.38l.79-.78c.78-.79 2.04-.79 2.82 0l7.99 7.99c.79.78.79 2.04 0 2.82l-7.99 7.99c-.78.79-2.04.79-2.82 0L2.6 13.41c-.79-.78-.79-2.04 0-2.82"/></svg>', ),
		);
	}
}

if ( ! function_exists( 'mizuki_nav_icon_svg' ) ) {
	/**
	 * 输出导航图标 SVG(支持 material 与品牌图标)。
	 *
	 * @param string $key 图标键。
	 * @param string $cls SVG class(默认顶栏样式)。
	 * @return string SVG 标记。
	 */
	function mizuki_nav_icon_svg( $key, $cls = 'text-[1.1rem] lg:mr-2 flex-shrink-0' ) {
		$brands = mizuki_nav_brand_icons();
		if ( isset( $brands[ $key ] ) ) {
			return '<svg viewBox="' . esc_attr( $brands[ $key ][0] ) . '" width="1em" height="1em" class="' . esc_attr( $cls ) . '"><path fill="currentColor" d="' . $brands[ $key ][1];
		}
		$icons = mizuki_nav_icons();
		$d     = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['link'];
		return '<svg viewBox="0 0 24 24" width="1em" height="1em" class="' . esc_attr( $cls ) . '"><path fill="currentColor" d="' . $d . '"/></svg>';
	}
}

if ( ! class_exists( 'Mizuki_Navbar_Walker' ) ) {
	/**
	 * 顶部导航 Walker:输出 Mizuki dropdown-container + 多级下拉菜单。
	 *
	 * 顶级无子项 -> dropdown-container > a(普通链接)。
	 * 顶级有子项 -> dropdown-container > button.dropdown-trigger + dropdown-menu > dropdown-content。
	 * 子项       -> a.dropdown-item(外链带新标签 + 右上角箭头)。
	 */
	class Mizuki_Navbar_Walker extends Walker_Nav_Menu {
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				$output .= '<div class="dropdown-menu" data-dropdown-menu><div class="dropdown-content">';
			}
		}
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				$output .= '</div></div>';
			}
		}
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$title        = $item->title;
			$url          = $item->url ? $item->url : '#';
			$external     = ( '_blank' === $item->target );
			$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );
			$ext_attr     = $external ? ' target="_blank" rel="noopener noreferrer"' : '';

			if ( 0 === $depth ) {
				$output .= '<div class="dropdown-container group" data-dropdown data-astro-cid-dl4kyotk>';
				$icon    = mizuki_nav_icon_svg( mizuki_nav_icon_key( $title ) );
				if ( $has_children ) {
					$arrow   = '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.25rem] transition-transform duration-200 dropdown-arrow ml-1 flex-shrink-0 hidden lg:inline"><path fill="currentColor" d="M11.625 14.913q-.175-.063-.325-.213l-4.6-4.6q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l3.9 3.9l3.9-3.9q.275-.275.7-.275t.7.275t.275.7t-.275.7l-4.6 4.6q-.15.15-.325.213t-.375.062t-.375-.062"/></svg>';
					$output .= '<button class="btn-plain scale-animation rounded-lg h-11 font-bold w-11 lg:w-auto lg:px-5 active:scale-95 dropdown-trigger flex items-center justify-center lg:justify-start whitespace-nowrap" aria-expanded="false" aria-haspopup="true" data-dropdown-trigger data-astro-cid-dl4kyotk>' . $icon . '<span class="truncate hidden lg:inline" data-astro-cid-dl4kyotk>' . esc_html( $title ) . '</span>' . $arrow . '</button>';
				} else {
					$output .= '<a aria-label="' . esc_attr( $title ) . '" href="' . esc_url( $url ) . '"' . $ext_attr . ' class="btn-plain scale-animation rounded-lg h-11 font-bold w-11 lg:w-auto lg:px-5 active:scale-95 flex items-center justify-center lg:justify-start whitespace-nowrap" data-astro-cid-dl4kyotk>' . $icon . '<span class="truncate hidden lg:inline" data-astro-cid-dl4kyotk>' . esc_html( $title ) . '</span></a>';
				}
			} else {
				$icon    = mizuki_nav_icon_svg( mizuki_nav_icon_key( $title ), 'text-[1rem] mr-2' );
				$output .= '<a href="' . esc_url( $url ) . '"' . $ext_attr . ' class="dropdown-item" aria-label="' . esc_attr( $title ) . '" data-astro-cid-dl4kyotk><div class="flex items-center" data-astro-cid-dl4kyotk>' . $icon . '<span data-astro-cid-dl4kyotk>' . esc_html( $title ) . '</span></div>';
				if ( $external ) {
					$output .= '<svg viewBox="0 0 640 640" width="1em" height="1em" class="text-[0.75rem] text-black/25 dark:text-white/25 ml-2"><path fill="currentColor" d="M384 64c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L265.3 329.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L512 173.3V256c0 17.7 14.3 32 32 32s32-14.3 32-32V96c0-17.7-14.3-32-32-32zM160 128c-53 0-96 43-96 96v256c0 53 43 96 96 96h256c53 0 96-43 96-96v-96c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H160c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32z"/></svg>';
				}
				$output .= '</a>';
			}
		}
		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			if ( 0 === $depth ) {
				$output .= '</div>';
			}
		}
	}
}

if ( ! function_exists( 'mizuki_nav_page_url' ) ) {
	/**
	 * 按页面 slug 解析 URL,页面不存在时回退到 /slug/。
		使用静态缓存避免同一请求中重复调用 get_page_by_path()。
	 *
	 * @param string $slug 页面别名。
	 * @return string URL。
	 */
	function mizuki_nav_page_url( $slug ) {
		static $cache = array();
		if ( isset( $cache[ $slug ] ) ) {
			return $cache[ $slug ];
		}
		$page = get_page_by_path( $slug );
		$cache[ $slug ] = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
		return $cache[ $slug ];
	}
}

if ( ! function_exists( 'mizuki_nav_groups' ) ) {
	/**
	 * 导航分组配置(对照原版 navBarConfig.ts,排除"设备")。
	 * 配置驱动 = 始终分组,与数据库菜单无关;可用 filter 'mizuki_nav_groups' 定制。
	 *
	 * @return array
	 */
	function mizuki_nav_groups() {
		$groups = array(
			array( 'title' => __( '首页', 'mizuki' ), 'url' => home_url( '/' ), 'icon' => 'home' ),
			array( 'title' => __( '归档', 'mizuki' ), 'url' => mizuki_nav_page_url( 'archive' ), 'icon' => 'archive' ),
		);

		// 「链接」下拉:来自后台「社交链接」自定义列表;为空则整组隐藏(不再硬编码开发者链接)。
		$link_children = array();
		foreach ( mizuki_get_custom_links() as $cl ) {
			$link_children[] = array(
				'title'    => $cl['name'],
				'url'      => $cl['url'],
				'external' => true,
				'icon'     => $cl['icon'],
			);
		}
		if ( $link_children ) {
			$groups[] = array(
				'title'    => __( '链接', 'mizuki' ),
				'icon'     => 'link',
				'children' => $link_children,
			);
		}

		$groups = array_merge( $groups, array(
			array(
				'title'    => __( '我的', 'mizuki' ),
				'icon'     => 'person',
				'children' => array(
					array( 'title' => __( '追番', 'mizuki' ), 'url' => mizuki_nav_page_url( 'anime' ), 'icon' => 'movie' ),
					array( 'title' => __( '日记', 'mizuki' ), 'url' => mizuki_nav_page_url( 'diary' ), 'icon' => 'book' ),
					array( 'title' => __( '相册', 'mizuki' ), 'url' => mizuki_nav_page_url( 'albums' ), 'icon' => 'album' ),
				),
			),
			array(
				'title'    => __( '关于', 'mizuki' ),
				'icon'     => 'info',
				'children' => array(
					array( 'title' => __( '关于', 'mizuki' ), 'url' => mizuki_nav_page_url( 'about' ), 'icon' => 'person' ),
					array( 'title' => __( '友链', 'mizuki' ), 'url' => mizuki_nav_page_url( 'friends' ), 'icon' => 'group' ),
				),
			),
			array(
				'title'    => __( '更多', 'mizuki' ),
				'icon'     => 'more',
				'children' => array(
					array( 'title' => __( '项目', 'mizuki' ), 'url' => mizuki_nav_page_url( 'projects' ), 'icon' => 'work' ),
					array( 'title' => __( '技能', 'mizuki' ), 'url' => mizuki_nav_page_url( 'skills' ), 'icon' => 'psychology' ),
					array( 'title' => __( '时间线', 'mizuki' ), 'url' => mizuki_nav_page_url( 'timeline' ), 'icon' => 'timeline' ),
				),
			),
		) );
		return apply_filters( 'mizuki_nav_groups', $groups );
	}
}

if ( ! function_exists( 'mizuki_render_nav_links' ) ) {
	/**
	 * 渲染桌面端导航(分组下拉),配置驱动。
	 */
	function mizuki_render_nav_links() {
		foreach ( mizuki_nav_groups() as $group ) {
			$icon     = mizuki_nav_icon_svg( isset( $group['icon'] ) ? $group['icon'] : 'link' );
			$children = isset( $group['children'] ) ? $group['children'] : array();
			echo '<div class="dropdown-container group" data-dropdown data-astro-cid-dl4kyotk>';
			if ( $children ) {
				$arrow = '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.25rem] transition-transform duration-200 dropdown-arrow ml-1 flex-shrink-0 hidden lg:inline"><path fill="currentColor" d="M11.625 14.913q-.175-.063-.325-.213l-4.6-4.6q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l3.9 3.9l3.9-3.9q.275-.275.7-.275t.7.275t.275.7t-.275.7l-4.6 4.6q-.15.15-.325.213t-.375.062t-.375-.062"/></svg>';
				echo '<button class="btn-plain scale-animation rounded-lg h-11 font-bold w-11 lg:w-auto lg:px-5 active:scale-95 dropdown-trigger flex items-center justify-center lg:justify-start whitespace-nowrap" aria-expanded="false" aria-haspopup="true" data-dropdown-trigger data-astro-cid-dl4kyotk>' . $icon . '<span class="truncate hidden lg:inline" data-astro-cid-dl4kyotk>' . esc_html( $group['title'] ) . '</span>' . $arrow . '</button>';
				echo '<div class="dropdown-menu" data-dropdown-menu data-astro-cid-dl4kyotk><div class="dropdown-content" data-astro-cid-dl4kyotk>';
				foreach ( $children as $child ) {
					$ext      = ! empty( $child['external'] );
					$ext_attr = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
					$cicon    = mizuki_nav_icon_svg( isset( $child['icon'] ) ? $child['icon'] : 'link', 'text-[1rem] mr-2' );
					echo '<a href="' . esc_url( $child['url'] ) . '"' . $ext_attr . ' class="dropdown-item" aria-label="' . esc_attr( $child['title'] ) . '" data-astro-cid-dl4kyotk><div class="flex items-center" data-astro-cid-dl4kyotk>' . $cicon . '<span data-astro-cid-dl4kyotk>' . esc_html( $child['title'] ) . '</span></div>';
					if ( $ext ) {
						echo '<svg viewBox="0 0 640 640" width="1em" height="1em" class="text-[0.75rem] text-black/25 dark:text-white/25 ml-2"><path fill="currentColor" d="M384 64c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L265.3 329.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L512 173.3V256c0 17.7 14.3 32 32 32s32-14.3 32-32V96c0-17.7-14.3-32-32-32zM160 128c-53 0-96 43-96 96v256c0 53 43 96 96 96h256c53 0 96-43 96-96v-96c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H160c-17.7 0-32-14.3-32-32V224c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32z"/></svg>';
					}
					echo '</a>';
				}
				echo '</div></div>';
			} else {
				$ext      = ! empty( $group['external'] );
				$ext_attr = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
				echo '<a aria-label="' . esc_attr( $group['title'] ) . '" href="' . esc_url( $group['url'] ) . '"' . $ext_attr . ' class="btn-plain scale-animation rounded-lg h-11 font-bold w-11 lg:w-auto lg:px-5 active:scale-95 flex items-center justify-center lg:justify-start whitespace-nowrap" data-astro-cid-dl4kyotk>' . $icon . '<span class="truncate hidden lg:inline" data-astro-cid-dl4kyotk>' . esc_html( $group['title'] ) . '</span></a>';
			}
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'mizuki_render_nav_mobile' ) ) {
	/**
	 * 渲染移动端导航面板(分组折叠;父项用 details 原生折叠)。
	 */
	function mizuki_render_nav_mobile() {
		foreach ( mizuki_nav_groups() as $group ) {
			$children = isset( $group['children'] ) ? $group['children'] : array();
			$icon     = mizuki_nav_icon_svg( isset( $group['icon'] ) ? $group['icon'] : 'link', 'text-[1.1rem] mr-2 flex-shrink-0' );
			if ( $children ) {
				echo '<details class="mizuki-m-group">';
				echo '<summary class="mizuki-m-summary flex items-center justify-between px-4 py-2.5 rounded-lg cursor-pointer font-medium"><span class="flex items-center">' . $icon . esc_html( $group['title'] ) . '</span><svg viewBox="0 0 24 24" width="1em" height="1em" class="mizuki-m-arrow transition-transform"><path fill="currentColor" d="M8.12 9.29L12 13.17l3.88-3.88a.996.996 0 1 1 1.41 1.41l-4.59 4.59a.996.996 0 0 1-1.41 0L6.7 10.7a.996.996 0 0 1 0-1.41c.39-.38 1.03-.39 1.42 0"/></svg></summary>';
				echo '<div class="mizuki-m-children pl-3">';
				foreach ( $children as $child ) {
					$ext      = ! empty( $child['external'] );
					$ext_attr = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
					$cicon    = mizuki_nav_icon_svg( isset( $child['icon'] ) ? $child['icon'] : 'link', 'text-[1rem] mr-2 flex-shrink-0' );
					echo '<a href="' . esc_url( $child['url'] ) . '"' . $ext_attr . ' class="flex items-center px-4 py-2 rounded-lg">' . $cicon . esc_html( $child['title'] ) . '</a>';
				}
				echo '</div></details>';
			} else {
				$ext      = ! empty( $group['external'] );
				$ext_attr = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
				echo '<a href="' . esc_url( $group['url'] ) . '"' . $ext_attr . ' class="flex items-center px-4 py-2.5 rounded-lg font-medium">' . $icon . esc_html( $group['title'] ) . '</a>';
			}
		}
	}
}

if ( ! function_exists( 'mizuki_render_navbar' ) ) {
	/**
	 * 输出 Mizuki 顶部导航。
	 */
	function mizuki_render_navbar() {
		$logo = '';
		if ( has_custom_logo() ) {
			$cid = mizuki_get_theme_mod( 'custom_logo' );
			$src = wp_get_attachment_image_url( $cid, 'full' );
			if ( $src ) {
				$logo = '<img src="' . esc_url( $src ) . '" alt="" class="h-[1.75rem] w-[1.75rem] mr-2 object-contain" loading="lazy">';
			}
		}
		?>
<!-- 顶部导航 -->
<div id="top-row" class="z-50 pointer-events-none relative transition-all duration-700 max-w-(--page-width) px-0 md:px-4 mx-auto" data-astro-cid-haiuh7kc>
	<div id="navbar-wrapper" class="pointer-events-auto sticky top-0 transition-all" data-astro-cid-haiuh7kc>
		<div id="navbar" class="z-50 onload-animation group" data-transparent-mode="semifull" data-is-home="<?php echo is_front_page() ? 'true' : 'false'; ?>">
			<div class="absolute h-8 left-0 right-0 -top-8 bg-[var(--card-bg)] transition"></div>
			<div class="!overflow-visible max-w-[var(--page-width)] h-[4.5rem] mx-auto flex items-center justify-between px-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-plain scale-animation rounded-lg h-[2.5rem] md:h-[3.25rem] md:[.enable-banner_&]:h-[2.5rem] md:[.enable-banner_&]:group-[.scrolled]:h-[3.25rem] px-5 font-bold active:scale-95 shrink-0 transition-all duration-300">
					<div class="flex flex-row items-center text-md">
						<?php echo $logo; // phpcs:ignore ?>
						<span class="dark:text-white text-black"><?php bloginfo( 'name' ); ?></span>
					</div>
				</a>
				<div id="navbar-links-container" class="hidden md:flex items-center space-x-1 transition-opacity duration-300">
					<?php mizuki_render_nav_links(); // 配置驱动的分组下拉(与原版一致,始终分组)。 ?>
				</div>
				<div class="flex items-center gap-1">
					<button id="theme-toggle" aria-label="<?php esc_attr_e( '切换明暗', 'mizuki' ); ?>" class="btn-plain scale-animation rounded-lg h-11 w-11 active:scale-90">
						<svg class="text-[1.25rem]" viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 21q-3.75 0-6.375-2.625T3 12t2.625-6.375T12 3q.35 0 .688.025t.662.075q-1.025.725-1.638 1.888T11.1 7.5q0 2.25 1.575 3.825T16.5 12.9q1.35 0 2.512-.612T20.9 10.65q.05.325.075.663T21 12q0 3.75-2.625 6.375T12 21"/></svg>
					</button>
					<?php if ( ! mizuki_get_theme_mod( 'mizuki_hue_fixed', false ) ) : ?>
					<button aria-label="<?php esc_attr_e( 'Display Settings', 'mizuki' ); ?>" class="btn-plain scale-animation rounded-lg h-11 w-11 active:scale-90" id="display-settings-switch">
						<svg class="text-[1.25rem]" viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 22q-2.05 0-3.875-.788t-3.187-2.15t-2.15-3.187T2 12q0-2.075.813-3.9t2.2-3.175T8.25 2.788T12.05 2q2 0 3.788.738t3.125 2.025t2.137 3T21.1 11.4q.275 2.45-1.225 4.025T16 17h-1.85q-.225 0-.312.125t-.088.275q0 .3.375.863t.375 1.287q0 1.05-.725 1.75T12 22m-5.5-9q.65 0 1.075-.425T8 11.5t-.425-1.075T6.5 10t-1.075.425T5 11.5t.425 1.075T6.5 13m3-4q.65 0 1.075-.425T11 7.5t-.425-1.075T9.5 6t-1.075.425T8 7.5t.425 1.075T9.5 9m5 0q.65 0 1.075-.425T16 7.5t-.425-1.075T14.5 6t-1.075.425T13 7.5t.425 1.075T14.5 9m3 4q.65 0 1.075-.425T19 11.5t-.425-1.075T17.5 10t-1.075.425T16 11.5t.425 1.075T17.5 13"/></svg>
					</button>
					<?php endif; ?>
					<button aria-label="<?php esc_attr_e( 'Menu', 'mizuki' ); ?>" name="Nav Menu" class="btn-plain scale-animation rounded-lg w-11 h-11 active:scale-90 md:!hidden" id="nav-menu-switch">
						<svg class="text-[1.25rem]" viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="m12 21l-4.5-4.5l1.45-1.45L12 18.1l3.05-3.05l1.45 1.45zM8.95 9.05L7.5 7.6L12 3.1l4.5 4.5l-1.45 1.45L12 5.9z"/></svg>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- 移动端导航菜单面板(分组折叠) -->
	<div id="nav-menu-panel" class="float-panel float-panel-closed fixed right-4 top-[5.25rem] px-2 py-2 max-h-[80vh] overflow-y-auto z-50">
		<?php mizuki_render_nav_mobile(); ?>
	</div>
	<!-- 显示设置面板 -->
	<div id="display-setting" class="float-panel float-panel-closed fixed right-4 top-[5.25rem] px-4 py-4 w-64 z-50">
		<?php if ( ! mizuki_get_theme_mod( 'mizuki_hue_fixed', false ) ) : ?>
		<div class="mb-3">
			<label class="text-50 text-xs font-medium mb-2 block"><?php esc_html_e( '主题色', 'mizuki' ); ?></label>
			<input type="range" id="panel-hue-slider" min="0" max="360" step="1"
			       data-default="<?php echo esc_attr( mizuki_get_theme_mod( 'mizuki_hue', 240 ) ); ?>"
			       class="w-full accent-[var(--primary)]" />
		</div>
		<?php endif; ?>
	</div>
</div>
		<?php
	}
}

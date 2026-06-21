<?php
/**
 * 默认内容与导航兜底。
 *
 * 解决两个核心问题:
 *  1. 特色页面(友链/追番/日记/时间线/项目/技能)在前台没有入口。
 *  2. 未分配导航菜单时,导航栏 / 移动端"+"面板为空,点击像是没反应。
 *
 * 主题启用时自动创建特色页面 + 默认导航菜单;同时为 wp_nav_menu
 * 提供 fallback,保证导航永远有内容。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 特色页面定义: slug => array( 标题, 页面模板相对路径 )。
 *
 * @return array
 */
function mizuki_feature_pages() {
	return array(
		'friends'  => array( '友链', 'templates/template-friends.php' ),
		'anime'    => array( '追番', 'templates/template-anime.php' ),
		'diary'    => array( '说说', 'templates/template-diary.php' ),
		'timeline' => array( '时间线', 'templates/template-timeline.php' ),
		'projects' => array( '项目', 'templates/template-projects.php' ),
		'skills'   => array( '技能', 'templates/template-skills.php' ),
		'albums'   => array( '相册', 'templates/template-albums.php' ),
		'archive'  => array( '归档', 'templates/template-archive.php' ),
		'about'    => array( '关于', 'templates/template-about.php' ),
	);
}

/**
 * 主题启用 / 升级时创建特色页面与分组菜单(幂等 + 版本迁移)。
 *
 * 用内容版本(MIZUKI_CONTENT_VERSION)而非一次性布尔开关作为守卫:
 * 覆盖升级主题文件后,只要版本号提升,就会补建新增页面并重建分组菜单。
 */
function mizuki_create_default_content() {
	$stored = (string) get_option( 'mizuki_content_version', '' );
	if ( MIZUKI_CONTENT_VERSION === $stored ) {
		return; // 已是最新结构,无需迁移。
	}

	$page_ids = array();
	foreach ( mizuki_feature_pages() as $slug => $info ) {
		list( $title, $template ) = $info;

		// 仅当模板文件确实存在时才创建对应页面。
		if ( ! file_exists( MIZUKI_DIR . '/' . $template ) ) {
			continue;
		}

		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$page_ids[ $slug ] = $existing->ID;
			// 确保已存在的页面也指向正确模板。
			if ( get_post_meta( $existing->ID, '_wp_page_template', true ) !== $template ) {
				update_post_meta( $existing->ID, '_wp_page_template', $template );
			}
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
			$page_ids[ $slug ] = $page_id;
		}
	}

	mizuki_seed_demo_content();
	mizuki_rebuild_grouped_menu( $page_ids );

	update_option( 'mizuki_content_version', MIZUKI_CONTENT_VERSION );
	update_option( 'mizuki_default_content_created', 1 ); // 保留旧标志(向后兼容)。
}
add_action( 'after_switch_theme', 'mizuki_create_default_content' );
// 兜底: 覆盖升级(未触发 after_switch_theme)时,在下次进入后台时执行迁移
// (由内容版本号保证仅在版本变化时执行)。
add_action( 'admin_init', 'mizuki_create_default_content' );

/**
 * 为各特色 CPT 填充占位示例内容(仅当该类型当前没有任何内容时)。
 * 使用者可在后台直接编辑或删除这些示例。
 */
function mizuki_seed_demo_content() {
	// 友链 (同步原项目 friends.astro)
	mizuki_seed_cpt_posts(
		'mizuki_friend',
		array(
			array(
				'title' => 'Astro',
				'meta'  => array(
					'_mizuki_friend_url'  => 'https://astro.build',
					'_mizuki_friend_desc' => '现代化的内容驱动型网站构建框架。',
				),
				'terms' => array( 'friend_tag' => array( 'Framework' ) ),
			),
			array(
				'title' => 'WordPress',
				'meta'  => array(
					'_mizuki_friend_url'  => 'https://wordpress.org',
					'_mizuki_friend_desc' => '世界上最流行的开源建站系统。',
				),
				'terms' => array( 'friend_tag' => array( 'Blog' ) ),
			),
			array(
				'title' => 'Mizuki',
				'meta'  => array(
					'_mizuki_friend_url'  => 'https://github.com/LyraVoid/Mizuki',
					'_mizuki_friend_desc' => '本主题的原始 Astro 项目。',
				),
				'terms' => array( 'friend_tag' => array( 'Framework', 'Blog' ) ),
			),
		)
	);

	// 追番 (使用 anime_status taxonomy)
	mizuki_seed_cpt_posts(
		'mizuki_anime',
		array(
			array(
				'title'   => '示例番剧 · 在看',
				'content' => '这是一个示例追番条目,请在后台替换为你正在观看的作品。',
				'meta'    => array(
					'_mizuki_anime_score'    => '9.0',
					'_mizuki_anime_progress' => '6/12',
				),
				'terms'   => array( 'anime_status' => array( 'watching' ) ),
			),
			array(
				'title'   => '示例番剧 · 想看',
				'content' => '这是一个示例追番条目,标记为想看。',
				'terms'   => array( 'anime_status' => array( 'planned' ) ),
			),
			array(
				'title'   => '示例番剧 · 看完',
				'content' => '这是一个示例追番条目,已看完。',
				'meta'    => array(
					'_mizuki_anime_score' => '8.5',
				),
				'terms'   => array( 'anime_status' => array( 'completed' ) ),
			),
		)
	);

	// 说说
	mizuki_seed_cpt_posts(
		'mizuki_diary',
		array(
			array(
				'title'   => '第一条说说',
				'content' => '欢迎使用 Mizuki 主题!这是一条示例说说,可在后台「说说」中编辑或删除。',
			),
			array(
				'title'   => '随手记录',
				'content' => '今天也是写代码的一天,记录一下此刻的心情。',
			),
		)
	);

	// 项目 (同步原项目 projects.astro 的 category 分类)
	mizuki_seed_cpt_posts(
		'mizuki_project',
		array(
			array(
				'title' => 'Mizuki for WordPress',
				'meta'  => array(
					'_mizuki_project_desc'   => 'Mizuki 主题的 WordPress 移植版,完整还原视觉与特色功能。',
					'_mizuki_project_status' => 'active',
					'_mizuki_project_tech'   => 'PHP, WordPress, Tailwind, JavaScript',
					'_mizuki_project_source' => 'https://github.com/LyraVoid/Mizuki',
				),
				'terms' => array( 'project_category' => array( 'web' ) ),
			),
			array(
				'title' => '示例项目',
				'meta'  => array(
					'_mizuki_project_desc'   => '这是一个示例项目,请在后台替换为你自己的作品。',
					'_mizuki_project_status' => 'completed',
					'_mizuki_project_tech'   => 'Vue, Vite, TypeScript',
				),
				'terms' => array( 'project_category' => array( 'web' ) ),
			),
		)
	);

	// 技能 (同步原项目 skills.astro 的 category 分类)
	mizuki_seed_cpt_posts(
		'mizuki_skill',
		array(
			array(
				'title'   => 'JavaScript',
				'content' => '前端交互与脚本开发。',
				'meta'    => array( '_mizuki_skill_level' => '90', '_mizuki_skill_icon' => 'devicon-javascript-plain' ),
				'terms'   => array( 'skill_category' => array( 'frontend' ) ),
			),
			array(
				'title'   => 'PHP',
				'content' => '服务端开发与 WordPress 主题/插件。',
				'meta'    => array( '_mizuki_skill_level' => '80', '_mizuki_skill_icon' => 'devicon-php-plain' ),
				'terms'   => array( 'skill_category' => array( 'backend' ) ),
			),
			array(
				'title'   => 'CSS',
				'content' => '响应式布局与动效。',
				'meta'    => array( '_mizuki_skill_level' => '85', '_mizuki_skill_icon' => 'devicon-css3-plain' ),
				'terms'   => array( 'skill_category' => array( 'frontend' ) ),
			),
			array(
				'title'   => 'Python',
				'content' => '脚本与数据处理。',
				'meta'    => array( '_mizuki_skill_level' => '60', '_mizuki_skill_icon' => 'devicon-python-plain' ),
				'terms'   => array( 'skill_category' => array( 'backend' ) ),
			),
			array(
				'title'   => 'MySQL',
				'content' => '关系型数据库管理。',
				'meta'    => array( '_mizuki_skill_level' => '70', '_mizuki_skill_icon' => 'devicon-mysql-plain' ),
				'terms'   => array( 'skill_category' => array( 'database' ) ),
			),
			array(
				'title'   => 'Git',
				'content' => '版本控制与团队协作。',
				'meta'    => array( '_mizuki_skill_level' => '85', '_mizuki_skill_icon' => 'devicon-git-plain' ),
				'terms'   => array( 'skill_category' => array( 'tools' ) ),
			),
		)
	);

	// 相册(示例,使用内置 banner 图作为照片;无特色图时取第一张为封面)。
	$mz_banner = get_template_directory_uri() . '/assets/desktop-banner';
	mizuki_seed_cpt_posts(
		'mizuki_album',
		array(
			array(
				'title' => '示例相册',
				'meta'  => array(
					'_mizuki_album_images' => $mz_banner . '/1.webp' . "\n" . $mz_banner . '/2.webp' . "\n" . $mz_banner . '/3.webp' . "\n" . $mz_banner . '/4.webp',
				),
			),
		)
	);
}

/**
 * 当某 CPT 没有任何内容时,批量插入示例条目。
 *
 * @param string $post_type CPT 名称。
 * @param array  $items     条目数组,每项含 title / content(可选) / meta(可选)。
 */
function mizuki_seed_cpt_posts( $post_type, $items ) {
	if ( ! post_type_exists( $post_type ) ) {
		return;
	}
	// 已有内容则不再注入示例,避免覆盖使用者数据。
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	if ( ! empty( $existing ) ) {
		return;
	}

	foreach ( $items as $item ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => $post_type,
				'post_title'   => $item['title'],
				'post_content' => isset( $item['content'] ) ? $item['content'] : '',
				'post_status'  => 'publish',
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( ! empty( $item['meta'] ) ) {
				foreach ( $item['meta'] as $key => $value ) {
					update_post_meta( $post_id, $key, $value );
				}
			}
			// 设置分类/标签
			if ( ! empty( $item['terms'] ) ) {
				foreach ( $item['terms'] as $taxonomy => $term_slugs ) {
					wp_set_object_terms( $post_id, $term_slugs, $taxonomy );
				}
			}
		}
	}
}

/**
 * (重)建分组导航菜单并分配到 primary(升级安全)。
 *
 * 仅当 primary 未分配,或当前分配的就是主题自建的「Mizuki 主菜单」时,
 * 才清空并按分组结构重建,避免覆盖用户自定义的其它菜单。
 *
 * @param array $page_ids slug => page ID 映射。
 */
function mizuki_rebuild_grouped_menu( $page_ids ) {
	$locations = get_theme_mod( 'nav_menu_locations' );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	$menu_name = 'Mizuki 主菜单';
	$menu      = wp_get_nav_menu_object( $menu_name );
	$menu_id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );
	if ( ! $menu_id || is_wp_error( $menu_id ) ) {
		return;
	}

	$primary = ( ! empty( $locations['primary'] ) ) ? (int) $locations['primary'] : 0;
	// 仅在 primary 为空、或就是本主题自建菜单时才重建(不动用户自定义菜单)。
	if ( 0 !== $primary && $primary !== $menu_id ) {
		return;
	}

	// 清空现有项,按分组结构重建(把旧版扁平菜单升级为分组下拉)。
	$existing_items = wp_get_nav_menu_items( $menu_id );
	if ( $existing_items ) {
		foreach ( $existing_items as $it ) {
			wp_delete_post( (int) $it->ID, true );
		}
	}

	// 顶级简单项:首页。
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '首页',
		'menu-item-url'    => home_url( '/' ),
		'menu-item-status' => 'publish',
	) );

	// 顶级简单项:归档(页面)。
	if ( isset( $page_ids['archive'] ) ) {
		mizuki_add_page_menu_item( $menu_id, $page_ids['archive'], 0 );
	}

	// 下拉组:链接(外部)— 与原版 navBarConfig 的 Links 组一致(GitHub/Bilibili/Gitee)。
	$links_parent   = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '链接',
		'menu-item-url'    => '#',
		'menu-item-status' => 'publish',
	) );
	$external_links = array(
		array( 'GitHub', 'https://github.com/LyraVoid/Mizuki' ),
		array( 'Bilibili', 'https://space.bilibili.com/701864046' ),
		array( 'Gitee', 'https://gitee.com/matsuzakayuki/Mizuki' ),
	);
	foreach ( $external_links as $ext ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $ext[0],
			'menu-item-url'       => $ext[1],
			'menu-item-target'    => '_blank',
			'menu-item-parent-id' => $links_parent,
			'menu-item-status'    => 'publish',
		) );
	}

	// 下拉组:我的 → 追番 / 说说 / 相册。
	$my_parent = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '我的',
		'menu-item-url'    => '#',
		'menu-item-status' => 'publish',
	) );
	foreach ( array( 'anime', 'diary', 'albums' ) as $slug ) {
		if ( isset( $page_ids[ $slug ] ) ) {
			mizuki_add_page_menu_item( $menu_id, $page_ids[ $slug ], $my_parent );
		}
	}

	// 下拉组:关于 → 关于 / 友链。
	$about_parent = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '关于',
		'menu-item-url'    => '#',
		'menu-item-status' => 'publish',
	) );
	foreach ( array( 'about', 'friends' ) as $slug ) {
		if ( isset( $page_ids[ $slug ] ) ) {
			mizuki_add_page_menu_item( $menu_id, $page_ids[ $slug ], $about_parent );
		}
	}

	// 下拉组:更多 → 项目 / 技能 / 时间线。
	$others_parent = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '更多',
		'menu-item-url'    => '#',
		'menu-item-status' => 'publish',
	) );
	foreach ( array( 'projects', 'skills', 'timeline' ) as $slug ) {
		if ( isset( $page_ids[ $slug ] ) ) {
			mizuki_add_page_menu_item( $menu_id, $page_ids[ $slug ], $others_parent );
		}
	}

	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * 向菜单添加一个指向页面的菜单项(可指定父项实现下拉)。
 *
 * @param int $menu_id   菜单 ID。
 * @param int $page_id   页面 ID。
 * @param int $parent_id 父菜单项 ID(0 = 顶级)。
 * @return int 新菜单项 ID。
 */
function mizuki_add_page_menu_item( $menu_id, $page_id, $parent_id = 0 ) {
	return (int) wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => get_the_title( $page_id ),
		'menu-item-object'    => 'page',
		'menu-item-object-id' => (int) $page_id,
		'menu-item-type'      => 'post_type',
		'menu-item-parent-id' => (int) $parent_id,
		'menu-item-status'    => 'publish',
	) );
}

/**
 * 导航菜单兜底输出: 未分配菜单时,渲染首页 + 已存在的特色页面链接。
 *
 * 保证桌面导航栏与移动端"+"面板永远有可见内容(避免"点击没反应")。
 *
 * @param array $args wp_nav_menu 参数。
 */
function mizuki_default_nav_menu( $args ) {
	$links   = array();
	$links[] = array( '首页', home_url( '/' ) );

	foreach ( mizuki_feature_pages() as $slug => $info ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status ) {
			$links[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
		}
	}

	$html = '';
	foreach ( $links as $link ) {
		$html .= '<li class="menu-item"><a href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></li>';
	}

	// 尊重 items_wrap (header.php 使用 '%3$s',即仅输出条目)。
	if ( isset( $args['items_wrap'] ) ) {
		$menu_id    = isset( $args['menu_id'] ) ? $args['menu_id'] : '';
		$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : '';
		$output     = sprintf( $args['items_wrap'], esc_attr( $menu_id ), esc_attr( $menu_class ), $html );
	} else {
		$output = $html;
	}

	if ( ! empty( $args['echo'] ) ) {
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 已逐段转义。
		return;
	}
	return $output;
}

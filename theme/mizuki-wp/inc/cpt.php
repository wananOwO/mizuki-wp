<?php
/**
 * 自定义文章类型 + 元字段 + 自定义分类。
 *
 * 完全同步 Mizuki 原项目的分类系统：
 * - skills: category (frontend/backend/database/tools/other)
 * - projects: category (web/mobile/desktop/other)
 * - friends: tags (自由标签)
 * - timeline: type (education/work/project/achievement)
 * - anime: status (watching/completed/planned)
 * - diary: 无分类
 * - album: 无分类
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册自定义分类 taxonomy。
 */
function mizuki_register_taxonomies() {
	// ── 技能分类 (同步原项目 skills.astro 的 category) ──
	register_taxonomy( 'skill_category', 'mizuki_skill', array(
		'labels'            => array(
			'name'          => '技能分类',
			'singular_name' => '技能分类',
			'search_items'  => '搜索分类',
			'all_items'     => '所有分类',
			'edit_item'     => '编辑分类',
			'add_new_item'  => '添加分类',
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'skill-category' ),
	) );

	// ── 项目分类 (同步原项目 projects.astro 的 category) ──
	register_taxonomy( 'project_category', 'mizuki_project', array(
		'labels'            => array(
			'name'          => '项目分类',
			'singular_name' => '项目分类',
			'search_items'  => '搜索分类',
			'all_items'     => '所有分类',
			'edit_item'     => '编辑分类',
			'add_new_item'  => '添加分类',
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'project-category' ),
	) );

	// ── 友链标签 (同步原项目 friends.astro 的 tags) ──
	register_taxonomy( 'friend_tag', 'mizuki_friend', array(
		'labels'            => array(
			'name'          => '友链标签',
			'singular_name' => '友链标签',
			'search_items'  => '搜索标签',
			'all_items'     => '所有标签',
			'edit_item'     => '编辑标签',
			'add_new_item'  => '添加标签',
		),
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'friend-tag' ),
	) );

	// ── 时间线类型 (同步原项目 timeline.astro 的 type) ──
	register_taxonomy( 'timeline_type', 'mizuki_diary', array(
		'labels'            => array(
			'name'          => '时间线类型',
			'singular_name' => '时间线类型',
			'search_items'  => '搜索类型',
			'all_items'     => '所有类型',
			'edit_item'     => '编辑类型',
			'add_new_item'  => '添加类型',
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'timeline-type' ),
	) );

	// ── 追番状态 (同步原项目 anime 数据的 status) ──
	register_taxonomy( 'anime_status', 'mizuki_anime', array(
		'labels'            => array(
			'name'          => '追番状态',
			'singular_name' => '追番状态',
			'search_items'  => '搜索状态',
			'all_items'     => '所有状态',
			'edit_item'     => '编辑状态',
			'add_new_item'  => '添加状态',
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'anime-status' ),
	) );

	// ── 通用文章标签 (用于 post 类型的时间线) ──
	register_taxonomy( 'timeline_type', 'post', array(
		'labels'            => array(
			'name'          => '时间线类型',
			'singular_name' => '时间线类型',
			'search_items'  => '搜索类型',
			'all_items'     => '所有类型',
			'edit_item'     => '编辑类型',
			'add_new_item'  => '添加类型',
		),
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'timeline-type' ),
	) );
}
add_action( 'init', 'mizuki_register_taxonomies' );

/**
 * 注册 6 个特色页 CPT(收纳到"Mizuki 主题"菜单下)。
 */
function mizuki_register_cpts() {
	$cpts = array(
		'mizuki_anime'   => array(
			'label'    => '追番',
			'singular' => '追番',
			'icon'     => 'dashicons-video-alt3',
			'supports' => array( 'title', 'editor', 'thumbnail' ),
		),
		'mizuki_friend'  => array(
			'label'    => '友链',
			'singular' => '友链',
			'icon'     => 'dashicons-groups',
			'supports' => array( 'title', 'thumbnail' ),
		),
		'mizuki_diary'   => array(
			'label'    => '日记',
			'singular' => '日记',
			'icon'     => 'dashicons-book',
			'supports' => array( 'title', 'editor' ),
		),
		'mizuki_album'   => array(
			'label'    => '相册',
			'singular' => '相册',
			'icon'     => 'dashicons-format-gallery',
			'supports' => array( 'title' ),
		),
		'mizuki_project' => array(
			'label'    => '项目',
			'singular' => '项目',
			'icon'     => 'dashicons-portfolio',
			'supports' => array( 'title', 'editor', 'thumbnail' ),
		),
		'mizuki_skill'   => array(
			'label'    => '技能',
			'singular' => '技能',
			'icon'     => 'dashicons-awards',
			'supports' => array( 'title', 'thumbnail' ),
		),
	);

	foreach ( $cpts as $slug => $cfg ) {
		// 根据 CPT 类型设置不同的 taxonomies
		$taxonomies = array();
		if ( 'mizuki_skill' === $slug ) {
			$taxonomies = array( 'skill_category' );
		} elseif ( 'mizuki_project' === $slug ) {
			$taxonomies = array( 'project_category' );
		} elseif ( 'mizuki_friend' === $slug ) {
			$taxonomies = array( 'friend_tag' );
		} elseif ( 'mizuki_anime' === $slug ) {
			$taxonomies = array( 'anime_status' );
		}

		register_post_type( $slug, array(
			'labels'              => array(
				'name'          => $cfg['label'],
				'singular_name' => $cfg['singular'],
				'add_new_item'  => '添加' . $cfg['singular'],
				'edit_item'     => '编辑' . $cfg['singular'],
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'mizuki-theme-settings',
			'menu_icon'           => $cfg['icon'],
			'supports'            => $cfg['supports'],
			'taxonomies'          => $taxonomies,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
		) );
	}
}
add_action( 'init', 'mizuki_register_cpts' );

/**
 * 注册元字段 meta box。
 */
function mizuki_add_meta_boxes() {
	// Anime meta box
	add_meta_box( 'mizuki_anime_fields', '追番信息', 'mizuki_anime_fields_cb', 'mizuki_anime', 'normal', 'high' );
	// Friend meta box
	add_meta_box( 'mizuki_friend_fields', '友链信息', 'mizuki_friend_fields_cb', 'mizuki_friend', 'normal', 'high' );
	// Diary meta box
	add_meta_box( 'mizuki_diary_fields', '日记信息', 'mizuki_diary_fields_cb', 'mizuki_diary', 'normal', 'high' );
	// Album meta box
	add_meta_box( 'mizuki_album_fields', '相册信息', 'mizuki_album_fields_cb', 'mizuki_album', 'normal', 'high' );
	// Project meta box
	add_meta_box( 'mizuki_project_fields', '项目信息', 'mizuki_project_fields_cb', 'mizuki_project', 'normal', 'high' );
	// Skill meta box
	add_meta_box( 'mizuki_skill_fields', '技能信息', 'mizuki_skill_fields_cb', 'mizuki_skill', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mizuki_add_meta_boxes' );

function mizuki_anime_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_anime_save', 'mizuki_anime_nonce' );
	$score    = get_post_meta( $post->ID, '_mizuki_anime_score', true );
	$url      = get_post_meta( $post->ID, '_mizuki_anime_url', true );
	$progress = get_post_meta( $post->ID, '_mizuki_anime_progress', true );
	?>
	<table class="form-table">
	<tr><th>评分 (0-10)</th><td><input type="number" name="mizuki_anime_score" value="<?php echo esc_attr( $score ); ?>" min="0" max="10" step="0.1" class="small-text"></td></tr>
	<tr><th>链接</th><td><input type="url" name="mizuki_anime_url" value="<?php echo esc_url( $url ); ?>" class="regular-text"></td></tr>
	<tr><th>进度</th><td><input type="text" name="mizuki_anime_progress" value="<?php echo esc_attr( $progress ); ?>" class="regular-text" placeholder="如: 12/24"></td></tr>
	</table>
	<p class="description">追番状态请使用右侧「追番状态」分类面板设置（在看/看完/想看）。</p>
	<?php
}

function mizuki_friend_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_friend_save', 'mizuki_friend_nonce' );
	$furl = get_post_meta( $post->ID, '_mizuki_friend_url', true );
	$desc = get_post_meta( $post->ID, '_mizuki_friend_desc', true );
	?>
	<table class="form-table">
	<tr><th>链接</th><td><input type="url" name="mizuki_friend_url" value="<?php echo esc_url( $furl ); ?>" class="regular-text"></td></tr>
	<tr><th>简介</th><td><textarea name="mizuki_friend_desc" rows="3" class="large-text"><?php echo esc_textarea( $desc ); ?></textarea></td></tr>
	</table>
	<p class="description">标签请使用右侧「友链标签」面板设置。</p>
	<?php
}

function mizuki_diary_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_diary_save', 'mizuki_diary_nonce' );
	$imgs = get_post_meta( $post->ID, '_mizuki_diary_images', true );
	?>
	<table class="form-table">
	<tr><th>配图 URL</th><td><textarea name="mizuki_diary_images" rows="3" class="large-text" placeholder="每行一个图片 URL"><?php echo esc_textarea( $imgs ); ?></textarea></td></tr>
	</table>
	<?php
}

function mizuki_album_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_album_save', 'mizuki_album_nonce' );
	$imgs = get_post_meta( $post->ID, '_mizuki_album_images', true );
	?>
	<table class="form-table">
	<tr><th>相册图片</th><td><textarea name="mizuki_album_images" rows="3" class="large-text" placeholder="每行一个图片 URL"><?php echo esc_textarea( $imgs ); ?></textarea></td></tr>
	</table>
	<?php
}

function mizuki_project_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_project_save', 'mizuki_project_nonce' );
	$url    = get_post_meta( $post->ID, '_mizuki_project_url', true );
	$desc   = get_post_meta( $post->ID, '_mizuki_project_desc', true );
	$status = get_post_meta( $post->ID, '_mizuki_project_status', true );
	$tech   = get_post_meta( $post->ID, '_mizuki_project_tech', true );
	$source = get_post_meta( $post->ID, '_mizuki_project_source', true );
	?>
	<table class="form-table">
	<tr><th>项目链接</th><td><input type="url" name="mizuki_project_url" value="<?php echo esc_url( $url ); ?>" class="regular-text"></td></tr>
	<tr><th>源码链接</th><td><input type="url" name="mizuki_project_source" value="<?php echo esc_url( $source ); ?>" class="regular-text" placeholder="如: https://github.com/..."></td></tr>
	<tr><th>项目简介</th><td><textarea name="mizuki_project_desc" rows="3" class="large-text"><?php echo esc_textarea( $desc ); ?></textarea></td></tr>
	<tr><th>技术栈</th><td><input type="text" name="mizuki_project_tech" value="<?php echo esc_attr( $tech ); ?>" class="regular-text" placeholder="逗号分隔, 如: Astro, TypeScript, Tailwind"></td></tr>
	<tr><th>状态</th><td>
		<select name="mizuki_project_status">
			<option value="active" <?php selected( $status, 'active' ); ?>>进行中</option>
			<option value="completed" <?php selected( $status, 'completed' ); ?>>已完成</option>
			<option value="paused" <?php selected( $status, 'paused' ); ?>>暂停</option>
		</select>
	</td></tr>
	</table>
	<p class="description">项目分类请使用右侧「项目分类」面板设置（web/mobile/desktop/other）。</p>
	<?php
}

function mizuki_skill_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_skill_save', 'mizuki_skill_nonce' );
	$level = get_post_meta( $post->ID, '_mizuki_skill_level', true );
	$icon  = get_post_meta( $post->ID, '_mizuki_skill_icon', true );
	?>
	<table class="form-table">
	<tr><th>熟练度 (0-100)</th><td><input type="number" name="mizuki_skill_level" value="<?php echo esc_attr( $level ); ?>" min="0" max="100" class="small-text"></td></tr>
	<tr><th>图标 class</th><td><input type="text" name="mizuki_skill_icon" value="<?php echo esc_attr( $icon ); ?>" class="regular-text" placeholder="如: devicon-html5-plain"></td></tr>
	</table>
	<p class="description">技能分类请使用右侧「技能分类」面板设置（frontend/backend/database/tools/other）。</p>
	<?php
}

/**
 * 保存元字段。
 */
function mizuki_save_meta_fields( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$mizuki_cpts = array( 'mizuki_anime', 'mizuki_friend', 'mizuki_diary', 'mizuki_album', 'mizuki_project', 'mizuki_skill' );
	if ( ! in_array( get_post_type( $post_id ), $mizuki_cpts, true ) ) {
		return;
	}

	// Anime
	if ( isset( $_POST['mizuki_anime_nonce'] ) && wp_verify_nonce( $_POST['mizuki_anime_nonce'], 'mizuki_anime_save' ) ) {
		$fields = array( 'mizuki_anime_score', 'mizuki_anime_url', 'mizuki_anime_progress' );
		foreach ( $fields as $f ) {
			if ( isset( $_POST[ $f ] ) ) {
				update_post_meta( $post_id, '_' . $f, sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) );
			}
		}
	}
	// Friend
	if ( isset( $_POST['mizuki_friend_nonce'] ) && wp_verify_nonce( $_POST['mizuki_friend_nonce'], 'mizuki_friend_save' ) ) {
		if ( isset( $_POST['mizuki_friend_url'] ) ) update_post_meta( $post_id, '_mizuki_friend_url', esc_url_raw( wp_unslash( $_POST['mizuki_friend_url'] ) ) );
		if ( isset( $_POST['mizuki_friend_desc'] ) ) update_post_meta( $post_id, '_mizuki_friend_desc', sanitize_textarea_field( wp_unslash( $_POST['mizuki_friend_desc'] ) ) );
	}
	// Diary
	if ( isset( $_POST['mizuki_diary_nonce'] ) && wp_verify_nonce( $_POST['mizuki_diary_nonce'], 'mizuki_diary_save' ) ) {
		if ( isset( $_POST['mizuki_diary_images'] ) ) update_post_meta( $post_id, '_mizuki_diary_images', sanitize_textarea_field( wp_unslash( $_POST['mizuki_diary_images'] ) ) );
	}
	// Album
	if ( isset( $_POST['mizuki_album_nonce'] ) && wp_verify_nonce( $_POST['mizuki_album_nonce'], 'mizuki_album_save' ) ) {
		if ( isset( $_POST['mizuki_album_images'] ) ) update_post_meta( $post_id, '_mizuki_album_images', sanitize_textarea_field( wp_unslash( $_POST['mizuki_album_images'] ) ) );
	}
	// Project
	if ( isset( $_POST['mizuki_project_nonce'] ) && wp_verify_nonce( $_POST['mizuki_project_nonce'], 'mizuki_project_save' ) ) {
		if ( isset( $_POST['mizuki_project_url'] ) ) update_post_meta( $post_id, '_mizuki_project_url', esc_url_raw( wp_unslash( $_POST['mizuki_project_url'] ) ) );
		if ( isset( $_POST['mizuki_project_source'] ) ) update_post_meta( $post_id, '_mizuki_project_source', esc_url_raw( wp_unslash( $_POST['mizuki_project_source'] ) ) );
		if ( isset( $_POST['mizuki_project_desc'] ) ) update_post_meta( $post_id, '_mizuki_project_desc', sanitize_textarea_field( wp_unslash( $_POST['mizuki_project_desc'] ) ) );
		if ( isset( $_POST['mizuki_project_tech'] ) ) update_post_meta( $post_id, '_mizuki_project_tech', sanitize_text_field( wp_unslash( $_POST['mizuki_project_tech'] ) ) );
		if ( isset( $_POST['mizuki_project_status'] ) ) update_post_meta( $post_id, '_mizuki_project_status', sanitize_text_field( wp_unslash( $_POST['mizuki_project_status'] ) ) );
	}
	// Skill
	if ( isset( $_POST['mizuki_skill_nonce'] ) && wp_verify_nonce( $_POST['mizuki_skill_nonce'], 'mizuki_skill_save' ) ) {
		if ( isset( $_POST['mizuki_skill_level'] ) ) update_post_meta( $post_id, '_mizuki_skill_level', absint( $_POST['mizuki_skill_level'] ) );
		if ( isset( $_POST['mizuki_skill_icon'] ) ) update_post_meta( $post_id, '_mizuki_skill_icon', sanitize_text_field( wp_unslash( $_POST['mizuki_skill_icon'] ) ) );
	}
}
add_action( 'save_post', 'mizuki_save_meta_fields' );

/**
 * 保存文章时清除相关缓存。
 */
function mizuki_clear_post_caches( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( 'post' !== get_post_type( $post_id ) ) return;
	delete_transient( 'mizuki_total_words' );
	delete_transient( 'mizuki_local_anime_data' );
}
add_action( 'save_post', 'mizuki_clear_post_caches' );

/**
 * 主题激活时创建默认分类。
 */
function mizuki_create_default_taxonomies() {
	// 检查 taxonomy 是否已注册且分类是否已存在
	$skill_cats = get_terms( array(
		'taxonomy'   => 'skill_category',
		'hide_empty' => false,
		'number'     => 1,
	) );
	if ( ! is_wp_error( $skill_cats ) && count( $skill_cats ) > 0 ) {
		// 分类已存在，更新版本号并返回
		update_option( 'mizuki_taxonomy_version', '2' );
		return;
	}

	// 技能分类 (同步原项目 skills.astro)
	$skill_cats = array(
		'frontend'  => '前端',
		'backend'   => '后端',
		'database'  => '数据库',
		'tools'     => '工具',
		'other'     => '其他',
	);
	foreach ( $skill_cats as $slug => $name ) {
		if ( ! term_exists( $slug, 'skill_category' ) ) {
			wp_insert_term( $name, 'skill_category', array( 'slug' => $slug ) );
		}
	}

	// 项目分类 (同步原项目 projects.astro)
	$project_cats = array(
		'web'     => 'Web',
		'mobile'  => '移动端',
		'desktop' => '桌面端',
		'other'   => '其他',
	);
	foreach ( $project_cats as $slug => $name ) {
		if ( ! term_exists( $slug, 'project_category' ) ) {
			wp_insert_term( $name, 'project_category', array( 'slug' => $slug ) );
		}
	}

	// 追番状态 (同步原项目 anime 数据)
	$anime_statuses = array(
		'watching'   => '在看',
		'completed'  => '看完',
		'planned'    => '想看',
		'onhold'     => '搁置',
		'dropped'    => '弃番',
	);
	foreach ( $anime_statuses as $slug => $name ) {
		if ( ! term_exists( $slug, 'anime_status' ) ) {
			wp_insert_term( $name, 'anime_status', array( 'slug' => $slug ) );
		}
	}

	// 时间线类型 (同步原项目 timeline.astro)
	$timeline_types = array(
		'education'   => '教育',
		'work'        => '工作',
		'project'     => '项目',
		'achievement' => '成就',
	);
	foreach ( $timeline_types as $slug => $name ) {
		if ( ! term_exists( $slug, 'timeline_type' ) ) {
			wp_insert_term( $name, 'timeline_type', array( 'slug' => $slug ) );
		}
	}

	// 友链标签 (同步原项目 friends.astro)
	$friend_tags = array( 'Framework', 'Docs', 'Hosting', 'CSS', 'Tool', 'Blog', 'Social', 'Cloud' );
	foreach ( $friend_tags as $tag ) {
		if ( ! term_exists( $tag, 'friend_tag' ) ) {
			wp_insert_term( $tag, 'friend_tag' );
		}
	}

	update_option( 'mizuki_taxonomy_version', '2' );
}
add_action( 'after_switch_theme', 'mizuki_create_default_taxonomies' );
add_action( 'admin_init', 'mizuki_create_default_taxonomies' );

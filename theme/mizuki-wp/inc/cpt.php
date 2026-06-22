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
	register_taxonomy( 'timeline_type', 'mizuki_timeline', array(
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
		'mizuki_timeline' => array(
			'label'    => '时间线',
			'singular' => '时间线条目',
			'icon'     => 'dashicons-clock',
			'supports' => array( 'title' ),
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
		} elseif ( 'mizuki_timeline' === $slug ) {
			$taxonomies = array( 'timeline_type' );
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
	// Timeline meta box
	add_meta_box( 'mizuki_timeline_fields', '时间线条目信息', 'mizuki_timeline_fields_cb', 'mizuki_timeline', 'normal', 'high' );
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

function mizuki_timeline_fields_cb( $post ) {
	wp_nonce_field( 'mizuki_timeline_save', 'mizuki_timeline_nonce' );

	// 获取所有 meta 值
	$description   = get_post_meta( $post->ID, '_mizuki_timeline_description', true );
	$start_date    = get_post_meta( $post->ID, '_mizuki_timeline_start_date', true );
	$end_date      = get_post_meta( $post->ID, '_mizuki_timeline_end_date', true );
	$location      = get_post_meta( $post->ID, '_mizuki_timeline_location', true );
	$organization  = get_post_meta( $post->ID, '_mizuki_timeline_organization', true );
	$position      = get_post_meta( $post->ID, '_mizuki_timeline_position', true );
	$skills        = get_post_meta( $post->ID, '_mizuki_timeline_skills', true );
	$achievements  = get_post_meta( $post->ID, '_mizuki_timeline_achievements', true );
	$links_json    = get_post_meta( $post->ID, '_mizuki_timeline_links', true );
	$icon          = get_post_meta( $post->ID, '_mizuki_timeline_icon', true );
	$color         = get_post_meta( $post->ID, '_mizuki_timeline_color', true );
	$featured      = get_post_meta( $post->ID, '_mizuki_timeline_featured', true );

	// 解析 links JSON
	$links = array();
	if ( ! empty( $links_json ) ) {
		$links = json_decode( $links_json, true );
		if ( ! is_array( $links ) ) {
			$links = array();
		}
	}

	// 确保至少有一个空链接输入框
	if ( empty( $links ) ) {
		$links = array( array( 'name' => '', 'url' => '', 'type' => 'website' ) );
	}
	?>
	<style>
	.mizuki-timeline-links { margin-top: 10px; }
	.mizuki-timeline-link-row { margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px; }
	.mizuki-timeline-link-row input, .mizuki-timeline-link-row select { margin-right: 10px; }
	.mizuki-add-link-btn { margin-top: 10px; }
	</style>

	<table class="form-table">
	<tr>
		<th><label for="mizuki_timeline_description">描述 <span style="color:red;">*</span></label></th>
		<td><textarea id="mizuki_timeline_description" name="mizuki_timeline_description" rows="4" class="large-text" required><?php echo esc_textarea( $description ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_start_date">开始日期 <span style="color:red;">*</span></label></th>
		<td><input type="date" id="mizuki_timeline_start_date" name="mizuki_timeline_start_date" value="<?php echo esc_attr( $start_date ); ?>" class="regular-text" required></td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_end_date">结束日期</label></th>
		<td>
			<input type="date" id="mizuki_timeline_end_date" name="mizuki_timeline_end_date" value="<?php echo esc_attr( $end_date ); ?>" class="regular-text">
			<p class="description">留空表示进行中</p>
		</td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_location">地点</label></th>
		<td><input type="text" id="mizuki_timeline_location" name="mizuki_timeline_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_organization">组织/公司/学校</label></th>
		<td><input type="text" id="mizuki_timeline_organization" name="mizuki_timeline_organization" value="<?php echo esc_attr( $organization ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_position">职位/角色</label></th>
		<td><input type="text" id="mizuki_timeline_position" name="mizuki_timeline_position" value="<?php echo esc_attr( $position ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_skills">技能标签</label></th>
		<td>
			<input type="text" id="mizuki_timeline_skills" name="mizuki_timeline_skills" value="<?php echo esc_attr( $skills ); ?>" class="large-text">
			<p class="description">多个技能用逗号分隔，如: React, TypeScript, Node.js</p>
		</td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_achievements">成就列表</label></th>
		<td>
			<textarea id="mizuki_timeline_achievements" name="mizuki_timeline_achievements" rows="5" class="large-text"><?php echo esc_textarea( $achievements ); ?></textarea>
			<p class="description">每行一条成就</p>
		</td>
	</tr>
	<tr>
		<th><label>相关链接</label></th>
		<td>
			<div class="mizuki-timeline-links" id="mizuki-timeline-links-container">
				<?php foreach ( $links as $idx => $link ) : ?>
				<div class="mizuki-timeline-link-row">
					<input type="text" name="mizuki_timeline_link_name[]" value="<?php echo esc_attr( $link['name'] ?? '' ); ?>" placeholder="链接名称" style="width: 200px;">
					<input type="url" name="mizuki_timeline_link_url[]" value="<?php echo esc_url( $link['url'] ?? '' ); ?>" placeholder="URL" style="width: 300px;">
					<select name="mizuki_timeline_link_type[]" style="width: 120px;">
						<option value="website" <?php selected( $link['type'] ?? 'website', 'website' ); ?>>网站</option>
						<option value="certificate" <?php selected( $link['type'] ?? 'website', 'certificate' ); ?>>证书</option>
						<option value="project" <?php selected( $link['type'] ?? 'website', 'project' ); ?>>项目</option>
						<option value="other" <?php selected( $link['type'] ?? 'website', 'other' ); ?>>其他</option>
					</select>
					<button type="button" class="button mizuki-remove-link-btn">删除</button>
				</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button mizuki-add-link-btn">+ 添加链接</button>
		</td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_icon">图标</label></th>
		<td>
			<input type="text" id="mizuki_timeline_icon" name="mizuki_timeline_icon" value="<?php echo esc_attr( $icon ); ?>" class="regular-text" placeholder="如: material-symbols:school">
			<p class="description">使用 Iconify 图标 class，留空则根据类型自动选择</p>
		</td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_color">节点颜色</label></th>
		<td>
			<input type="text" id="mizuki_timeline_color" name="mizuki_timeline_color" value="<?php echo esc_attr( $color ); ?>" class="mizuki-color-field" placeholder="#7C3AED">
			<p class="description">留空使用默认主题色</p>
		</td>
	</tr>
	<tr>
		<th><label for="mizuki_timeline_featured">标星显示</label></th>
		<td><label><input type="checkbox" id="mizuki_timeline_featured" name="mizuki_timeline_featured" value="1" <?php checked( $featured, '1' ); ?>> 在前端显示星标</label></td>
	</tr>
	</table>
	<p class="description"><strong>提示：</strong>类型请使用右侧「类型」面板设置（教育/工作/项目/成就）。</p>

	<script>
	jQuery(document).ready(function($) {
		// 添加链接
		$('.mizuki-add-link-btn').on('click', function() {
			var html = '<div class="mizuki-timeline-link-row">' +
				'<input type="text" name="mizuki_timeline_link_name[]" placeholder="链接名称" style="width: 200px;">' +
				'<input type="url" name="mizuki_timeline_link_url[]" placeholder="URL" style="width: 300px;">' +
				'<select name="mizuki_timeline_link_type[]" style="width: 120px;">' +
				'<option value="website">网站</option>' +
				'<option value="certificate">证书</option>' +
				'<option value="project">项目</option>' +
				'<option value="other">其他</option>' +
				'</select>' +
				'<button type="button" class="button mizuki-remove-link-btn">删除</button>' +
				'</div>';
			$('#mizuki-timeline-links-container').append(html);
		});

		// 删除链接
		$(document).on('click', '.mizuki-remove-link-btn', function() {
			$(this).closest('.mizuki-timeline-link-row').remove();
		});

		// 颜色选择器
		if ($.fn.wpColorPicker) {
			$('.mizuki-color-field').wpColorPicker();
		}
	});
	</script>
	<?php
}

/**
 * 保存元字段。
 */
function mizuki_save_meta_fields( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$mizuki_cpts = array( 'mizuki_anime', 'mizuki_friend', 'mizuki_diary', 'mizuki_album', 'mizuki_project', 'mizuki_skill', 'mizuki_timeline' );
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

	// Timeline
	if ( isset( $_POST['mizuki_timeline_nonce'] ) && wp_verify_nonce( $_POST['mizuki_timeline_nonce'], 'mizuki_timeline_save' ) ) {
		// 描述（必填）
		if ( isset( $_POST['mizuki_timeline_description'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_description', sanitize_textarea_field( wp_unslash( $_POST['mizuki_timeline_description'] ) ) );
		}

		// 开始日期（必填，验证格式）
		if ( isset( $_POST['mizuki_timeline_start_date'] ) ) {
			$start_date = sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_start_date'] ) );
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_date ) ) {
				update_post_meta( $post_id, '_mizuki_timeline_start_date', $start_date );
			}
		}

		// 结束日期（可选，验证格式）
		if ( isset( $_POST['mizuki_timeline_end_date'] ) ) {
			$end_date = sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_end_date'] ) );
			if ( empty( $end_date ) || preg_match( '/^\d{4}-\d{2}-\d{2}$/', $end_date ) ) {
				update_post_meta( $post_id, '_mizuki_timeline_end_date', $end_date );
			}
		}

		// 地点
		if ( isset( $_POST['mizuki_timeline_location'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_location', sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_location'] ) ) );
		}

		// 组织
		if ( isset( $_POST['mizuki_timeline_organization'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_organization', sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_organization'] ) ) );
		}

		// 职位
		if ( isset( $_POST['mizuki_timeline_position'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_position', sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_position'] ) ) );
		}

		// 技能标签
		if ( isset( $_POST['mizuki_timeline_skills'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_skills', sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_skills'] ) ) );
		}

		// 成就列表
		if ( isset( $_POST['mizuki_timeline_achievements'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_achievements', sanitize_textarea_field( wp_unslash( $_POST['mizuki_timeline_achievements'] ) ) );
		}

		// 链接（构建 JSON）
		$links = array();
		if ( isset( $_POST['mizuki_timeline_link_name'] ) && is_array( $_POST['mizuki_timeline_link_name'] ) ) {
			$names = array_map( 'sanitize_text_field', wp_unslash( $_POST['mizuki_timeline_link_name'] ) );
			$urls  = isset( $_POST['mizuki_timeline_link_url'] ) ? array_map( 'esc_url_raw', wp_unslash( $_POST['mizuki_timeline_link_url'] ) ) : array();
			$types = isset( $_POST['mizuki_timeline_link_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['mizuki_timeline_link_type'] ) ) : array();

			foreach ( $names as $idx => $name ) {
				if ( ! empty( $name ) && ! empty( $urls[ $idx ] ) ) {
					$links[] = array(
						'name' => $name,
						'url'  => $urls[ $idx ],
						'type' => isset( $types[ $idx ] ) ? $types[ $idx ] : 'website',
					);
				}
			}
		}
		update_post_meta( $post_id, '_mizuki_timeline_links', wp_json_encode( $links, JSON_UNESCAPED_UNICODE ) );

		// 图标
		if ( isset( $_POST['mizuki_timeline_icon'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_icon', sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_icon'] ) ) );
		}

		// 节点颜色
		if ( isset( $_POST['mizuki_timeline_color'] ) ) {
			$color = sanitize_text_field( wp_unslash( $_POST['mizuki_timeline_color'] ) );
			// 验证 hex 颜色格式
			if ( empty( $color ) || preg_match( '/^#[0-9A-Fa-f]{6}$/', $color ) ) {
				update_post_meta( $post_id, '_mizuki_timeline_color', $color );
			}
		}

		// 标星
		if ( isset( $_POST['mizuki_timeline_featured'] ) ) {
			update_post_meta( $post_id, '_mizuki_timeline_featured', '1' );
		} else {
			delete_post_meta( $post_id, '_mizuki_timeline_featured' );
		}
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

/**
 * 后台加载颜色选择器（用于时间线）
 */
function mizuki_enqueue_admin_scripts( $hook ) {
	// 只在编辑时间线页面加载
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'mizuki_timeline' !== $screen->post_type ) {
		return;
	}

	// 加载 WordPress 颜色选择器
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'mizuki_enqueue_admin_scripts' );


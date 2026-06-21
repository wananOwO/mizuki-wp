<?php
/**
 * 自定义文章类型 + 元字段。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册 6 个特色页 CPT(收纳到"Mizuki 主题"菜单下)。
 *
 * 注意: public=true + has_archive=false + publicly_queryable=false
 * 确保 CPT 在后台可管理，页面模板可通过 WP_Query 查询，
 * 但不生成独立前台存档页(所有前台展示走页面模板)。
 */
function mizuki_register_cpts() {
	$cpts = array(
		'mizuki_anime'   => array( 'label' => '追番', 'singular' => '追番', 'icon' => 'dashicons-video-alt3' ),
		'mizuki_friend'  => array( 'label' => '友链', 'singular' => '友链', 'icon' => 'dashicons-groups' ),
		'mizuki_diary'   => array( 'label' => '日记', 'singular' => '日记', 'icon' => 'dashicons-book' ),
		'mizuki_album'   => array( 'label' => '相册', 'singular' => '相册', 'icon' => 'dashicons-format-gallery' ),
		'mizuki_project' => array( 'label' => '项目', 'singular' => '项目', 'icon' => 'dashicons-portfolio' ),
		'mizuki_skill'   => array( 'label' => '技能', 'singular' => '技能', 'icon' => 'dashicons-awards' ),
	);

	foreach ( $cpts as $slug => $cfg ) {
		register_post_type( $slug, array(
			'labels'              => array(
				'name'          => $cfg['label'],
				'singular_name' => $cfg['singular'],
				'add_new_item'  => '添加' . $cfg['singular'],
				'edit_item'     => '编辑' . $cfg['singular'],
			),
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'mizuki-theme-settings', // 挂到"Mizuki 主题"菜单下作为子菜单
			'menu_icon'           => $cfg['icon'],
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'         => false,
			'exclude_from_search' => true,
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
	$status  = get_post_meta( $post->ID, '_mizuki_anime_status', true );
	$score   = get_post_meta( $post->ID, '_mizuki_anime_score', true );
	$url     = get_post_meta( $post->ID, '_mizuki_anime_url', true );
	$progress= get_post_meta( $post->ID, '_mizuki_anime_progress', true );
	?>
	<table class="form-table">
	<tr><th>状态</th><td>
		<select name="mizuki_anime_status">
			<option value="watching" <?php selected( $status, 'watching' ); ?>>在看</option>
			<option value="completed" <?php selected( $status, 'completed' ); ?>>看完</option>
			<option value="planned" <?php selected( $status, 'planned' ); ?>>想看</option>
		</select>
	</td></tr>
	<tr><th>评分 (0-10)</th><td><input type="number" name="mizuki_anime_score" value="<?php echo esc_attr( $score ); ?>" min="0" max="10" step="0.1" class="small-text"></td></tr>
	<tr><th>链接</th><td><input type="url" name="mizuki_anime_url" value="<?php echo esc_url( $url ); ?>" class="regular-text"></td></tr>
	<tr><th>进度</th><td><input type="text" name="mizuki_anime_progress" value="<?php echo esc_attr( $progress ); ?>" class="regular-text" placeholder="如: 12/24"></td></tr>
	</table>
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
	<?php
}

/**
 * 保存元字段。
 */
function mizuki_save_meta_fields( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	// Anime
	if ( isset( $_POST['mizuki_anime_nonce'] ) && wp_verify_nonce( $_POST['mizuki_anime_nonce'], 'mizuki_anime_save' ) ) {
		$fields = array( 'mizuki_anime_status', 'mizuki_anime_score', 'mizuki_anime_url', 'mizuki_anime_progress' );
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
function mizuki_clear_post_caches( \$post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( 'post' !== get_post_type( \$post_id ) ) return;
	// 清除站点统计中的总字数缓存
	delete_transient( 'mizuki_total_words' );
	// 清除本地追番数据缓存
	delete_transient( 'mizuki_local_anime_data' );
}
add_action( 'save_post', 'mizuki_clear_post_caches' );

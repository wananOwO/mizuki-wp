<?php
/**
 * 自定义文章类型 + 元字段。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册 6 个特色页 CPT。
 */
function mizuki_register_cpts() {
	$cpts = array(
		'mizuki_anime'   => array( 'label' => '追番', 'singular' => '追番', 'icon' => 'dashicons-video-alt3' ),
		'mizuki_friend'  => array( 'label' => '友链', 'singular' => '友链', 'icon' => 'dashicons-groups' ),
		'mizuki_diary'   => array( 'label' => '说说', 'singular' => '说说', 'icon' => 'dashicons-format-status' ),
		'mizuki_album'   => array( 'label' => '相册', 'singular' => '相册', 'icon' => 'dashicons-format-gallery' ),
		'mizuki_project' => array( 'label' => '项目', 'singular' => '项目', 'icon' => 'dashicons-portfolio' ),
		'mizuki_skill'   => array( 'label' => '技能', 'singular' => '技能', 'icon' => 'dashicons-awards' ),
	);

	foreach ( $cpts as $slug => $cfg ) {
		register_post_type( $slug, array(
			'labels'       => array(
				'name'          => $cfg['label'],
				'singular_name' => $cfg['singular'],
				'add_new_item'  => '添加' . $cfg['singular'],
				'edit_item'     => '编辑' . $cfg['singular'],
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => $cfg['icon'],
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'  => false,
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
	add_meta_box( 'mizuki_diary_fields', '说说信息', 'mizuki_diary_fields_cb', 'mizuki_diary', 'normal', 'high' );
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
}
add_action( 'save_post', 'mizuki_save_meta_fields' );

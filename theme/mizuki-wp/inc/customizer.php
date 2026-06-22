<?php
/**
 * Customizer 设置面板:Banner、主色 hue、个人资料、社交链接、Live2D。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

function mizuki_customize_register( $wp_customize ) {
	// === 单一 Mizuki 主题面板:所有配置收纳其中,点击展开子项(各 Section)===
	$wp_customize->add_panel( 'mizuki_panel', array(
		'title'       => __( 'Mizuki 主题设置', 'mizuki' ),
		'description' => __( 'Mizuki 主题的全部配置:Banner、主题色、个人资料、社交链接、公告、Live2D。', 'mizuki' ),
		'priority'    => 10,
	) );

	// === Banner ===
	$wp_customize->add_section( 'mizuki_banner', array(
		'title'    => __( 'Banner 设置', 'mizuki' ),
		'panel'    => 'mizuki_panel',
		'priority' => 10,
	) );
	$wp_customize->add_setting( 'mizuki_banner_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mizuki_banner_image', array(
		'label'   => __( 'Banner 图片', 'mizuki' ),
		'section' => 'mizuki_banner',
	) ) );
	$wp_customize->add_setting( 'mizuki_banner_height', array(
		'default'           => '60vh',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_banner_height', array(
		'label'   => __( 'Banner 高度', 'mizuki' ),
		'section' => 'mizuki_banner',
		'type'    => 'text',
	) );

	// === 主题色 ===
	$wp_customize->add_section( 'mizuki_color', array(
		'title'    => __( '主题色', 'mizuki' ),
		'panel'    => 'mizuki_panel',
		'priority' => 20,
	) );
	$wp_customize->add_setting( 'mizuki_hue', array(
		'default'           => 240,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'mizuki_hue', array(
		'label'       => __( '主题色相 (Hue)', 'mizuki' ),
		'description' => __( '0-360,默认 240(蓝色)。', 'mizuki' ),
		'section'     => 'mizuki_color',
		'type'        => 'range',
		'input_attrs' => array( 'min' => 0, 'max' => 360, 'step' => 1 ),
	) );
	$wp_customize->add_setting( 'mizuki_hue_fixed', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'mizuki_hue_fixed', array(
		'label'   => __( '锁定主题色(隐藏访客调色器)', 'mizuki' ),
		'section' => 'mizuki_color',
		'type'    => 'checkbox',
	) );

	// === 个人资料 ===
	$wp_customize->add_section( 'mizuki_profile', array(
		'title'    => __( '个人资料', 'mizuki' ),
		'panel'    => 'mizuki_panel',
		'priority' => 30,
	) );
	$fields = array(
		'mizuki_avatar'   => array( __( '头像 URL', 'mizuki' ), 'esc_url_raw', 'url' ),
		'mizuki_nickname' => array( __( '昵称', 'mizuki' ), 'sanitize_text_field', 'text' ),
		'mizuki_bio'      => array( __( '简介', 'mizuki' ), 'sanitize_text_field', 'textarea' ),
	);
	foreach ( $fields as $id => $cfg ) {
		$wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => $cfg[1] ) );
		$wp_customize->add_control( $id, array( 'label' => $cfg[0], 'section' => 'mizuki_profile', 'type' => $cfg[2] ) );
	}

	// 社交链接:改由后台「Mizuki 主题设置」中的自定义 repeater 管理(theme_mod 'mizuki_custom_links')。
	// 旧的固定四项 mizuki_social_{github,twitter,email,rss} 仅作为向后兼容的回退数据源,
	// 不再在 Customizer 暴露独立控件(避免与新 repeater 冲突/混淆)。

	// === 追番 API ===
	$wp_customize->add_section( 'mizuki_anime_api', array(
		'title'       => __( '追番 API 设置', 'mizuki' ),
		'description' => __( '配置 Bangumi 或 Bilibili API 来自动获取追番数据。', 'mizuki' ),
		'panel'       => 'mizuki_panel',
		'priority'    => 50,
	) );

	// 追番数据源模式
	$wp_customize->add_setting( 'mizuki_anime_mode', array(
		'default'           => 'local',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_anime_mode', array(
		'label'       => __( '追番数据源', 'mizuki' ),
		'description' => __( '选择追番数据来源。', 'mizuki' ),
		'section'     => 'mizuki_anime_api',
		'type'        => 'select',
		'choices'     => array(
			'local'    => __( '本地数据（自定义文章类型）', 'mizuki' ),
			'bangumi'  => __( 'Bangumi API', 'mizuki' ),
			'bilibili' => __( 'Bilibili API', 'mizuki' ),
		),
	) );

	// Bangumi 用户 ID
	$wp_customize->add_setting( 'mizuki_bangumi_user_id', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_bangumi_user_id', array(
		'label'       => __( 'Bangumi 用户 ID', 'mizuki' ),
		'description' => __( '你的 Bangumi 用户 ID（例如：sai）。', 'mizuki' ),
		'section'     => 'mizuki_anime_api',
		'type'        => 'text',
	) );

	// Bilibili VMID
	$wp_customize->add_setting( 'mizuki_bilibili_vmid', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_bilibili_vmid', array(
		'label'       => __( 'Bilibili 用户 ID (VMID)', 'mizuki' ),
		'description' => __( '你的 Bilibili 用户 ID（例如：1129280784）。', 'mizuki' ),
		'section'     => 'mizuki_anime_api',
		'type'        => 'text',
	) );

	// Bilibili WebP 选项
	$wp_customize->add_setting( 'mizuki_bilibili_use_webp', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'mizuki_bilibili_use_webp', array(
		'label'       => __( '使用 WebP 格式封面', 'mizuki' ),
		'description' => __( '启用后将使用 WebP 格式的封面图（Bilibili）。', 'mizuki' ),
		'section'     => 'mizuki_anime_api',
		'type'        => 'checkbox',
	) );

	// 缓存时间
	$wp_customize->add_setting( 'mizuki_anime_cache_hours', array(
		'default'           => 24,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'mizuki_anime_cache_hours', array(
		'label'       => __( '缓存时间（小时）', 'mizuki' ),
		'description' => __( '远程 API 数据的缓存时间，默认 24 小时。', 'mizuki' ),
		'section'     => 'mizuki_anime_api',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 168, 'step' => 1 ),
	) );

	// === Live2D ===
	$wp_customize->add_section( 'mizuki_live2d', array(
		'title'    => __( 'Live2D 看板娘', 'mizuki' ),
		'panel'    => 'mizuki_panel',
		'priority' => 60,
	) );
	$wp_customize->add_setting( 'mizuki_live2d_enabled', array( 'default' => false, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control( 'mizuki_live2d_enabled', array( 'label' => __( '启用 Live2D 看板娘', 'mizuki' ), 'section' => 'mizuki_live2d', 'type' => 'checkbox' ) );
}
add_action( 'customize_register', 'mizuki_customize_register' );

/**
 * Customizer 实时预览 JS。
 */
function mizuki_customize_preview_js() {
	wp_enqueue_script(
		'mizuki-customizer-preview',
		MIZUKI_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		MIZUKI_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'mizuki_customize_preview_js' );

/**
 * 输出侧边栏个人资料小工具(读取 Customizer 设置)。
 * 在 sidebar-1 小工具区域之前输出，保证即使没有添加小工具也能显示。
 */
function mizuki_sidebar_profile_widget() {
	$avatar   = get_theme_mod( 'mizuki_avatar', '' );
	$nickname = get_theme_mod( 'mizuki_nickname', '' );
	$bio      = get_theme_mod( 'mizuki_bio', '' );

	// 如果什么都没设置，使用 WordPress 默认值
	if ( ! $nickname ) {
		$nickname = get_bloginfo( 'name' );
	}
	if ( ! $avatar ) {
		// 使用自定义 logo 或默认头像
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$avatar = wp_get_attachment_image_url( $custom_logo_id, 'thumbnail' );
		}
	}

	if ( ! $avatar && ! $nickname && ! $bio ) {
		return;
	}
	?>
	<div class="widget card-base p-4 mb-4 text-center onload-animation">
		<?php if ( $avatar ) : ?>
		<div class="mb-3">
			<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $nickname ); ?>"
			     class="w-20 h-20 rounded-full mx-auto object-cover ring-2 ring-[var(--primary)]/20">
		</div>
		<?php endif; ?>
		<?php if ( $nickname ) : ?>
		<h3 class="font-bold text-90 text-lg mb-1"><?php echo esc_html( $nickname ); ?></h3>
		<?php endif; ?>
		<?php if ( $bio ) : ?>
		<p class="text-50 text-sm"><?php echo esc_html( $bio ); ?></p>
		<?php endif; ?>
		<?php
		// 社交/外链:来自后台「社交链接」自定义列表。
		$clinks = mizuki_get_custom_links();
		if ( ! empty( $clinks ) ) :
		?>
		<div class="flex items-center justify-center gap-3 mt-3">
			<?php foreach ( $clinks as $cl ) : ?>
			<a href="<?php echo esc_url( $cl['url'] ); ?>" target="_blank" rel="noopener"
			   class="btn-plain scale-animation rounded-lg w-9 h-9 text-50 hover:text-[var(--primary)] transition text-[1.1rem]"
			   aria-label="<?php echo esc_attr( $cl['name'] ); ?>">
				<?php mizuki_social_icon_svg( $cl['icon'] ); ?>
			</a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'mizuki_sidebar_before_widgets', 'mizuki_sidebar_profile_widget' );

/**
 * 输出 Live2D 看板娘 JS(如果启用)。
 */
function mizuki_live2d_script() {
	if ( ! get_theme_mod( 'mizuki_live2d_enabled', false ) ) {
		return;
	}
	?>
	<script src="https://cdn.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest/autoload.js" async></script>
	<?php
}
add_action( 'wp_footer', 'mizuki_live2d_script' );

/**
 * ========== 自定义社交/外链(替代旧的固定 github/twitter/email/rss 四项) ==========
 * 旧的「固定四样」改为用户可在后台自由增删的链接列表(theme_mod 'mizuki_custom_links',
 * JSON 数组,每项 {name,url,icon})。导航栏「链接」下拉与资料卡社交按钮都从这里取。
 */

if ( ! function_exists( 'mizuki_custom_link_icons' ) ) {
	/**
	 * 自定义链接图标 SVG(viewBox 24x24,尺寸由调用方 class 控制)。
	 *
	 * @return array<string,string> icon-key => SVG markup。
	 */
	function mizuki_custom_link_icons() {
		return array(
			'github'    => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33s1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2"/></svg>',
			'bilibili'  => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M18.223 3.086a1.25 1.25 0 0 1 0 1.768L17.08 5.996h1.17A3.75 3.75 0 0 1 22 9.747v7.5a3.75 3.75 0 0 1-3.75 3.75H5.75A3.75 3.75 0 0 1 2 17.247v-7.5a3.75 3.75 0 0 1 3.75-3.751h1.166L5.775 4.854a1.25 1.25 0 1 1 1.767-1.768l2.91 2.91h3.090l2.911-2.91a1.25 1.25 0 0 1 1.767 0zM18.25 8.496H5.75a1.25 1.25 0 0 0-1.247 1.157l-.003.094v7.5c0 .659.51 1.198 1.157 1.246l.093.004h12.5a1.25 1.25 0 0 0 1.247-1.157l.003-.093v-7.5c0-.69-.56-1.251-1.25-1.251m-10 2.5c.69 0 1.25.56 1.25 1.25v1.25a1.25 1.25 0 1 1-2.5 0v-1.25c0-.69.56-1.25 1.25-1.25m7.5 0c.69 0 1.25.56 1.25 1.25v1.25a1.25 1.25 0 1 1-2.5 0v-1.25c0-.69.56-1.25 1.25-1.25"/></svg>',
			'git'       => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M2.6 10.59L8.38 4.8l1.69 1.7c-.24.85.15 1.78.93 2.23v5.54c-.6.34-1 .99-1 1.73a2 2 0 0 0 2 2a2 2 0 0 0 2-2c0-.74-.4-1.39-1-1.73V9.41l2.07 2.09c-.07.15-.07.32-.07.5a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2c-.18 0-.35 0-.5.07L13.93 7.5a1.98 1.98 0 0 0-1.15-2.34c-.43-.16-.88-.2-1.28-.09L9.8 3.38l.79-.78c.78-.79 2.04-.79 2.82 0l7.99 7.99c.79.78.79 2.04 0 2.82l-7.99 7.99c-.78.79-2.04.79-2.82 0L2.6 13.41c-.79-.78-.79-2.04 0-2.82"/></svg>',
			'twitter'   => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
			'email'     => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 4l-8 5-8-5V6l8 5 8-5z"/></svg>',
			'rss'       => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M6.18 17.82a2.18 2.18 0 1 0 0-4.36 2.18 2.18 0 0 0 0 4.36M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56m0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9"/></svg>',
			'website'   => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m-1 17.93C7.05 19.44 4 16.08 4 12c0-.61.08-1.21.21-1.78L9 15v1c0 1.1.9 2 2 2zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41C17.93 5.78 20 8.65 20 12c0 2.08-.81 3.98-2.1 5.39"/></svg>',
			'telegram'  => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="m21.94 4.6l-3.2 15.07c-.24 1.06-.87 1.32-1.76.82l-4.87-3.59l-2.35 2.26c-.26.26-.48.48-.98.48l.35-4.96L17.4 5.3c.4-.35-.09-.55-.62-.2L6.89 11.62l-4.78-1.49c-1.04-.32-1.06-1.04.22-1.54l18.7-7.22c.86-.32 1.62.2 1.34 1.51z"/></svg>',
			'discord'   => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M19.27 5.33A17.4 17.4 0 0 0 15 4l-.2.41a13.2 13.2 0 0 1 3.7 1.2a16.6 16.6 0 0 0-13-.01a13.3 13.3 0 0 1 3.71-1.19L9.04 4a17.4 17.4 0 0 0-4.27 1.33C2.08 9.66 1.35 13.88 1.71 18.04A17.6 17.6 0 0 0 7.06 21l.46-.63a11.7 11.7 0 0 1-1.83-.87l.45-.34a12.5 12.5 0 0 0 9.72 0l.45.35c-.58.34-1.2.64-1.83.86l.46.63a17.5 17.5 0 0 0 5.35-2.95c.43-4.8-.73-8.99-3.06-12.71zM8.52 15.6c-1.05 0-1.92-.97-1.92-2.16c0-1.19.85-2.16 1.92-2.16c1.07 0 1.94.98 1.92 2.16c0 1.19-.85 2.16-1.92 2.16zm7.07 0c-1.05 0-1.92-.97-1.92-2.16c0-1.19.85-2.16 1.92-2.16c1.07 0 1.94.98 1.92 2.16c0 1.19-.85 2.16-1.92 2.16"/></svg>',
			'qq'        => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 2c3.3 0 6 2.9 6 6.5c0 1.6-.4 3-1 4.1c.5.3 1.3 1 1.8 2.1c.4 1 .8 2.3.8 2.3l-1.8.8s.6 1.1.6 2.2c0 .7-.4 1.5-1.3 1.5c-.8 0-1.3-.6-1.6-1.2c-.4.8-1.2 1.7-2.5 1.7c-1.4 0-2.2-1-2.6-1.8c-.3.7-.8 1.3-1.6 1.3c-.9 0-1.3-.8-1.3-1.5c0-1.1.6-2.2.6-2.2l-1.8-.8s.4-1.3.8-2.3c.5-1.1 1.3-1.8 1.8-2.1c-.6-1.1-1-2.5-1-4.1C6 4.9 8.7 2 12 2"/></svg>',
			'wechat'    => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M8.69 2C4.7 2 1.5 4.8 1.5 8.27c0 2 1.1 3.77 2.85 4.93L3.7 15.3l2.6-1.3c.74.2 1.5.3 2.3.3c.2 0 .4 0 .6-.02c-.13-.4-.2-.85-.2-1.3c0-2.9 2.7-5.27 6.07-5.27c.23 0 .45.02.67.04C15.96 4.66 12.6 2 8.69 2m-2.6 3.3a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1m5.2 0a1 1 0 0 1 1 1a1 1 0 0 1-1 1a1 1 0 0 1-1-1a1 1 0 0 1 1-1M23 13.27c0-2.9-2.82-5.27-6.3-5.27s-6.3 2.36-6.3 5.27s2.82 5.27 6.3 5.27c.67 0 1.3-.08 1.93-.23l2.27 1.14l-.6-1.84C21.9 16.4 23 14.96 23 13.27m-8.4-1.3a.85.85 0 0 1 .85.85a.85.85 0 0 1-.85.85a.85.85 0 0 1-.85-.85a.85.85 0 0 1 .85-.85m4.2 0a.85.85 0 0 1 .85.85a.85.85 0 0 1-.85.85a.85.85 0 0 1-.85-.85a.85.85 0 0 1 .85-.85"/></svg>',
			'weibo'      => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M9.31 8.17c-3.42.69-5.95 3.32-5.95 6.45C3.36 18.35 7.11 21 11.73 21s8.37-2.65 8.37-6.38c0-3.39-3.27-6.16-7.62-6.16c-.49 0-.97.04-1.44.11c.06-.21.1-.43.1-.66c0-1.5 1.21-2.71 2.71-2.71c.45 0 .87.11 1.25.3l.84-1.6A4.7 4.7 0 0 0 14 3.5c-2.6 0-4.71 2.11-4.71 4.71c0 .67.14 1.3.39 1.87zm2.42 4.83c1.74 0 3.15 1.18 3.15 2.63c0 1.46-1.41 2.64-3.15 2.64s-3.16-1.18-3.16-2.64c0-1.45 1.42-2.63 3.16-2.63m6.84-7.06c-.59-.59-1.4-.86-2.19-.75c-.36.05-.61.38-.56.74c.05.36.38.62.74.56c.4-.05.8.07 1.09.36c.29.29.41.69.36 1.09c-.05.36.21.69.56.74h.09c.33 0 .61-.24.66-.57c.11-.8-.16-1.61-.75-2.17"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M23 12s0-3.6-.46-5.33a2.78 2.78 0 0 0-1.94-1.94C18.88 4.27 12 4.27 12 4.27s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 1.94C1 8.4 1 12 1 12s0 3.6.46 5.33a2.78 2.78 0 0 0 1.94 1.94c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-1.94C23 15.6 23 12 23 12M9.75 15.5v-7l6 3.5z"/></svg>',
			'zhihu'     => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M13.7 3h-3v6.6H8.3V3H5.4v14.7h2.8v-5.3h2.4l1.7 5.3h2.9l-2.1-6c1.4-.7 2.2-2 2.2-3.8V6.2c0-2-1.3-3.2-3.6-3.2m6.8 5.9l-1.9.7V18l1.9-.3l2.6.6V8.6zm-9.8 5.6V8.4c0-.8.4-1.2 1.2-1.2h1.4c.8 0 1.2.4 1.2 1.2v3.6c0 .8-.4 1.2-1.2 1.2z"/></svg>',
			'music'     => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3z"/></svg>',
			'steam'     => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12c0 1.97.57 3.8 1.55 5.35l3.43-1.4a3.5 3.5 0 0 1 2.86-4.5l1.6-3.95a4 4 0 1 1 4.05 4.34l-3.3 2.9a3.5 3.5 0 0 1-3.2 4.65l-1.6 2.1C8.86 21.7 10.4 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2"/></svg>',
			'link'      => '<svg viewBox="0 0 24 24" width="1em" height="1em"><path fill="currentColor" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1M8 13h8v-2H8zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5"/></svg>',
		);
	}
}

if ( ! function_exists( 'mizuki_social_icon_svg' ) ) {
	/**
	 * 输出某个图标键的 SVG(未知键回退到 link)。
	 *
	 * @param string $icon 图标键。
	 */
	function mizuki_social_icon_svg( $icon ) {
		$icons = mizuki_custom_link_icons();
		$key   = sanitize_key( (string) $icon );
		if ( ! isset( $icons[ $key ] ) ) {
			$key = 'link';
		}
		echo $icons[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — 静态 SVG。
	}
}

if ( ! function_exists( 'mizuki_get_custom_links' ) ) {
	/**
	 * 读取自定义链接列表。
	 *
	 * 优先解析 theme_mod 'mizuki_custom_links'(JSON 数组,每项 {name,url,icon});
	 * 若未设置,回退旧的固定四项 mizuki_social_{github,twitter,email,rss}(向后兼容,
	 * 老用户在升级前填过的社交链接不会丢失)。
	 *
	 * @return array<int,array{name:string,url:string,icon:string}>
	 */
	function mizuki_get_custom_links() {
		$raw   = (string) mizuki_get_theme_mod( 'mizuki_custom_links', '' );
		$links = array();

		if ( '' !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				foreach ( $decoded as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					$url = isset( $item['url'] ) ? trim( (string) $item['url'] ) : '';
					if ( '' === $url ) {
						continue;
					}
					$name = isset( $item['name'] ) ? trim( (string) $item['name'] ) : '';
					$links[] = array(
						'name' => '' !== $name ? $name : $url,
						'url'  => mizuki_maybe_mailto( $url, isset( $item['icon'] ) ? $item['icon'] : 'link' ),
						'icon' => isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'link',
					);
				}
				return $links;
			}
		}

		// 回退:旧的固定四项(向后兼容)。
		foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
			$url = mizuki_get_theme_mod( "mizuki_social_{$p}", '' );
			if ( ! $url ) {
				continue;
			}
			$links[] = array(
				'name' => ucfirst( $p ),
				'url'  => mizuki_maybe_mailto( $url, $p ),
				'icon' => $p,
			);
		}
		return $links;
	}
}

if ( ! function_exists( 'mizuki_maybe_mailto' ) ) {
	/**
	 * 邮箱类链接(含 @ 且无协议)自动补 mailto:。
	 *
	 * @param string $url  原始 URL。
	 * @param string $icon 图标键(用于判断是否邮箱)。
	 * @return string
	 */
	function mizuki_maybe_mailto( $url, $icon = '' ) {
		if ( 'email' === $icon && false !== strpos( $url, '@' ) && 0 !== strpos( $url, 'mailto:' ) ) {
			return 'mailto:' . $url;
		}
		return $url;
	}
}

/**
 * ========== wp-admin 仪表台设置页面(统一折叠式管理) ==========
 * 用户反馈:希望在 wp-admin 仪表台看到"折叠统一管理"的美观配置页面，
 * 而非分散在多处。本页面镜像所有 Customizer 设置，以手风琴折叠面板呈现，
 * 存入同一套 theme_mod(与 Customizer 双向同步)。
 */

add_action( 'admin_menu', 'mizuki_admin_menu_page' );
function mizuki_admin_menu_page() {
	// 顶级菜单(作为所有 Mizuki 特色功能的父容器)
	add_menu_page(
		__( 'Mizuki 主题', 'mizuki' ), // 页面标题
		__( 'Mizuki 主题', 'mizuki' ), // 菜单标题
		'edit_theme_options',          // 权限
		'mizuki-theme-settings',       // slug(CPT 的 show_in_menu 指向此 slug)
		'mizuki_render_admin_page',    // 渲染回调(点击顶级菜单时显示设置页)
		'dashicons-admin-appearance',  // 图标
		59                            // 优先级(在"外观"下方)
	);

	// 第一个子菜单:主题设置(覆盖默认的顶级菜单名,改为"主题设置")
	// position 0 确保它排在 CPT 子菜单之前。
	add_submenu_page(
		'mizuki-theme-settings',       // 父 slug
		__( '主题设置', 'mizuki' ),    // 页面标题
		__( '主题设置', 'mizuki' ),    // 菜单标题
		'edit_theme_options',
		'mizuki-theme-settings',       // 同父 slug,覆盖顶级菜单显示为子菜单
		'mizuki_render_admin_page',
		0                              // position: 最前
	);

	// 其余 6 个 CPT(追番/友链/日记/相册/项目/技能)通过 register_post_type 的
	// show_in_menu => 'mizuki-theme-settings' 自动挂载为子菜单,无需手动 add_submenu_page。
}

// 强制"主题设置"排在第一位(WordPress 会把手动添加的子菜单放在 CPT 之后,需要重排序)。
add_action( 'admin_menu', 'mizuki_reorder_submenus', 999 );
function mizuki_reorder_submenus() {
	global $submenu;
	if ( ! isset( $submenu['mizuki-theme-settings'] ) ) {
		return;
	}
	// 把"主题设置"项提到最前。
	$items   = $submenu['mizuki-theme-settings'];
	$setting = null;
	$others  = array();
	foreach ( $items as $item ) {
		if ( isset( $item[2] ) && 'mizuki-theme-settings' === $item[2] ) {
			$setting = $item;
		} else {
			$others[] = $item;
		}
	}
	if ( $setting ) {
		$submenu['mizuki-theme-settings'] = array_merge( array( $setting ), $others );
	}
}

/**
 * 写入单个分类的显示名映射(theme_mod mizuki_{tax}_labels)。
 */
function mizuki_set_category_label( $taxonomy, $slug, $name ) {
	$map = get_theme_mod( 'mizuki_' . $taxonomy . '_labels', array() );
	if ( ! is_array( $map ) ) {
		$map = array();
	}
	$map[ $slug ] = $name;
	set_theme_mod( 'mizuki_' . $taxonomy . '_labels', $map );
}

/**
 * 写入/清除单个分类的图标 class 映射(theme_mod mizuki_{tax}_icons)。
 * 传入空字符串表示清除该 slug 的自定义图标(回退到默认 SVG)。
 */
function mizuki_set_category_icon( $taxonomy, $slug, $icon ) {
	$map = get_theme_mod( 'mizuki_' . $taxonomy . '_icons', array() );
	if ( ! is_array( $map ) ) {
		$map = array();
	}
	if ( '' === $icon ) {
		unset( $map[ $slug ] );
	} else {
		$map[ $slug ] = $icon;
	}
	set_theme_mod( 'mizuki_' . $taxonomy . '_icons', $map );
}

/**
 * 删除 term 后清理其 labels / icons 映射,避免脏数据。
 */
function mizuki_remove_category_mapping( $taxonomy, $slug ) {
	foreach ( array( 'labels', 'icons' ) as $kind ) {
		$key = 'mizuki_' . $taxonomy . '_' . $kind;
		$map = get_theme_mod( $key, array() );
		if ( is_array( $map ) && isset( $map[ $slug ] ) ) {
			unset( $map[ $slug ] );
			set_theme_mod( $key, $map );
		}
	}
}

/**
 * 处理「项目分类 / 技能分类」管理表单提交(新增 / 重命名 / 删除)。
 *
 * 使用独立 nonce(mizuki_cat_nonce / mizuki_cat_manage),与主设置表单互不干扰。
 *
 * @return array 消息列表,每项 array( 'success'|'error', 文本 )。
 */
function mizuki_handle_category_admin_actions() {
	$messages = array();
	if ( ! isset( $_POST['mizuki_cat_nonce'] ) ) {
		return $messages;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mizuki_cat_nonce'] ) ), 'mizuki_cat_manage' ) ) {
		return $messages;
	}
	// 与设置页注册时的权限(edit_theme_options)一致,避免有权访问页面却无法保存的静默失败。
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return $messages;
	}

	foreach ( array( 'project_category', 'skill_category' ) as $tax ) {
		// ── 新增 ──
		if ( isset( $_POST[ $tax . '_add' ] ) ) {
			$slug = isset( $_POST[ $tax . '_new_slug' ] ) ? sanitize_title( wp_unslash( $_POST[ $tax . '_new_slug' ] ) ) : '';
			$name = isset( $_POST[ $tax . '_new_name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $tax . '_new_name' ] ) ) : '';
			$icon = isset( $_POST[ $tax . '_new_icon' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $tax . '_new_icon' ] ) ) : '';
			if ( '' === $slug || '' === $name ) {
				$messages[] = array( 'error', __( 'slug 和显示名均不能为空。', 'mizuki' ) );
			} elseif ( term_exists( $slug, $tax ) ) {
				/* translators: %s: 分类 slug */
				$messages[] = array( 'error', sprintf( __( '分类「%s」已存在。', 'mizuki' ), $slug ) );
			} else {
				$res = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );
				if ( is_wp_error( $res ) ) {
					$messages[] = array( 'error', $res->get_error_message() );
				} else {
					mizuki_set_category_label( $tax, $slug, $name );
					if ( '' !== $icon ) {
						mizuki_set_category_icon( $tax, $slug, $icon );
					}
					/* translators: %s: 分类显示名 */
					$messages[] = array( 'success', sprintf( __( '已新增分类「%s」。', 'mizuki' ), $name ) );
				}
			}
		}

		// ── 重命名 / 删除(按 term_id 扫描)──
		$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
		if ( is_wp_error( $terms ) ) {
			continue;
		}
		foreach ( $terms as $term ) {
			$tid = (int) $term->term_id;

			if ( isset( $_POST[ $tax . '_update_' . $tid ] ) ) {
				$name = isset( $_POST[ $tax . '_name_' . $tid ] ) ? sanitize_text_field( wp_unslash( $_POST[ $tax . '_name_' . $tid ] ) ) : '';
				$icon = isset( $_POST[ $tax . '_icon_' . $tid ] ) ? sanitize_text_field( wp_unslash( $_POST[ $tax . '_icon_' . $tid ] ) ) : '';
				if ( '' !== $name ) {
					wp_update_term( $tid, $tax, array( 'name' => $name ) );
					mizuki_set_category_label( $tax, $term->slug, $name );
				}
				mizuki_set_category_icon( $tax, $term->slug, $icon ); // 空 = 清除自定义图标
				/* translators: %s: 分类 slug */
				$messages[] = array( 'success', sprintf( __( '已更新分类「%s」。', 'mizuki' ), $term->slug ) );

			} elseif ( isset( $_POST[ $tax . '_delete_' . $tid ] ) ) {
				if ( (int) $term->count > 0 ) {
					/* translators: 1: 分类 slug, 2: 内容数 */
					$messages[] = array( 'error', sprintf( __( '分类「%1$s」下仍有 %2$d 篇内容,不能删除。', 'mizuki' ), $term->slug, (int) $term->count ) );
				} else {
					wp_delete_term( $tid, $tax );
					mizuki_remove_category_mapping( $tax, $term->slug );
					/* translators: %s: 分类 slug */
					$messages[] = array( 'success', sprintf( __( '已删除分类「%s」。', 'mizuki' ), $term->slug ) );
				}
			}
		}
	}

	return $messages;
}

/**
 * 渲染单个 taxonomy 的分类管理折叠面板(表格 + 新增表单)。
 */
function mizuki_render_category_manager( $taxonomy, $title, $hint ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'orderby' => 'name' ) );
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}
	$icons_raw = get_theme_mod( 'mizuki_' . $taxonomy . '_icons', array() );
	if ( ! is_array( $icons_raw ) ) {
		$icons_raw = array();
	}
	?>
	<div class="mizuki-accordion-item">
		<div class="mizuki-accordion-header">
			<span><?php echo esc_html( $title ); ?></span>
			<span class="mizuki-accordion-arrow">▼</span>
		</div>
		<div class="mizuki-accordion-body">
			<p class="description"><?php echo esc_html( $hint ); ?></p>
			<table class="widefat striped" style="margin-bottom:16px;">
				<thead>
					<tr>
						<th style="width:18%;">Slug</th>
						<th style="width:28%;"><?php esc_html_e( '显示名', 'mizuki' ); ?></th>
						<th style="width:28%;"><?php esc_html_e( '图标 class(可选)', 'mizuki' ); ?></th>
						<th style="width:8%;"><?php esc_html_e( '内容数', 'mizuki' ); ?></th>
						<th style="width:18%;"><?php esc_html_e( '操作', 'mizuki' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $terms as $term ) :
					$tid      = (int) $term->term_id;
					$icon_val = isset( $icons_raw[ $term->slug ] ) ? $icons_raw[ $term->slug ] : '';
				?>
					<tr>
						<td><code><?php echo esc_html( $term->slug ); ?></code></td>
						<td><input type="text" name="<?php echo esc_attr( $taxonomy . '_name_' . $tid ); ?>" value="<?php echo esc_attr( $term->name ); ?>" class="regular-text"></td>
						<td><input type="text" name="<?php echo esc_attr( $taxonomy . '_icon_' . $tid ); ?>" value="<?php echo esc_attr( $icon_val ); ?>" placeholder="devicon-..." class="regular-text"></td>
						<td><?php echo (int) $term->count; ?></td>
						<td>
							<button type="submit" name="<?php echo esc_attr( $taxonomy . '_update_' . $tid ); ?>" value="1" class="button"><?php esc_html_e( '保存', 'mizuki' ); ?></button>
							<button type="submit" name="<?php echo esc_attr( $taxonomy . '_delete_' . $tid ); ?>" value="1" class="button button-link-delete" onclick="return confirm('<?php echo esc_js( __( '确定删除该分类?此操作不可撤销。', 'mizuki' ) ); ?>');"<?php disabled( (int) $term->count > 0 ); ?>><?php esc_html_e( '删除', 'mizuki' ); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if ( empty( $terms ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( '暂无分类。', 'mizuki' ); ?></td></tr>
				<?php endif; ?>
				</tbody>
			</table>
			<h4 style="margin:8px 0;"><?php esc_html_e( '新增分类', 'mizuki' ); ?></h4>
			<div class="mizuki-field" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
				<input type="text" name="<?php echo esc_attr( $taxonomy . '_new_slug' ); ?>" placeholder="slug(如 embedded)" class="regular-text" style="max-width:200px;">
				<input type="text" name="<?php echo esc_attr( $taxonomy . '_new_name' ); ?>" placeholder="<?php esc_attr_e( '显示名', 'mizuki' ); ?>" class="regular-text" style="max-width:160px;">
				<input type="text" name="<?php echo esc_attr( $taxonomy . '_new_icon' ); ?>" placeholder="devicon-...(可选)" class="regular-text" style="max-width:200px;">
				<button type="submit" name="<?php echo esc_attr( $taxonomy . '_add' ); ?>" value="1" class="button button-primary"><?php esc_html_e( '新增', 'mizuki' ); ?></button>
			</div>
			<p class="description"><?php esc_html_e( '图标 class 与「技能信息」里的图标字段一致(如 devicon-html5-plain),留空则使用默认图标。删除仅在该分类下没有内容时可用。', 'mizuki' ); ?></p>
		</div>
	</div>
	<?php
}

function mizuki_render_admin_page() {
	// 处理分类管理表单(独立 nonce,先于渲染执行以便表格反映最新状态)
	$cat_messages = mizuki_handle_category_admin_actions();
	foreach ( $cat_messages as $msg ) {
		$class = ( 'error' === $msg[0] ) ? 'notice-error' : 'notice-success';
		echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( $msg[1] ) . '</p></div>';
	}

	// 保存逻辑
	if ( isset( $_POST['mizuki_settings_nonce'] ) && wp_verify_nonce( $_POST['mizuki_settings_nonce'], 'mizuki_save_settings' ) ) {
		// Banner
		set_theme_mod( 'mizuki_banner_image', isset( $_POST['mizuki_banner_image'] ) ? esc_url_raw( $_POST['mizuki_banner_image'] ) : '' );
		set_theme_mod( 'mizuki_banner_height', isset( $_POST['mizuki_banner_height'] ) ? sanitize_text_field( $_POST['mizuki_banner_height'] ) : '60vh' );
		// 主题色
		set_theme_mod( 'mizuki_hue', isset( $_POST['mizuki_hue'] ) ? absint( $_POST['mizuki_hue'] ) : 240 );
		set_theme_mod( 'mizuki_hue_fixed', ! empty( $_POST['mizuki_hue_fixed'] ) );
		// 个人资料
		set_theme_mod( 'mizuki_avatar', isset( $_POST['mizuki_avatar'] ) ? esc_url_raw( $_POST['mizuki_avatar'] ) : '' );
		set_theme_mod( 'mizuki_nickname', isset( $_POST['mizuki_nickname'] ) ? sanitize_text_field( $_POST['mizuki_nickname'] ) : '' );
		set_theme_mod( 'mizuki_bio', isset( $_POST['mizuki_bio'] ) ? sanitize_text_field( $_POST['mizuki_bio'] ) : '' );
		// 社交/外链(自定义 repeater:名称/URL/图标)→ 存为 JSON 'mizuki_custom_links'。
		$cl_names = isset( $_POST['mizuki_cl_name'] ) ? (array) wp_unslash( $_POST['mizuki_cl_name'] ) : array();
		$cl_urls  = isset( $_POST['mizuki_cl_url'] ) ? (array) wp_unslash( $_POST['mizuki_cl_url'] ) : array();
		$cl_icons = isset( $_POST['mizuki_cl_icon'] ) ? (array) wp_unslash( $_POST['mizuki_cl_icon'] ) : array();
		$saved_links = array();
		foreach ( $cl_names as $i => $raw_name ) {
			$url = isset( $cl_urls[ $i ] ) ? trim( (string) $cl_urls[ $i ] ) : '';
			if ( '' === $url ) {
				continue;
			}
			$name = trim( wp_strip_all_tags( (string) $raw_name ) );
			$icon = isset( $cl_icons[ $i ] ) ? sanitize_key( (string) $cl_icons[ $i ] ) : 'link';
			$saved_links[] = array(
				'name' => '' !== $name ? $name : $url,
				'url'  => $url,
				'icon' => '' !== $icon ? $icon : 'link',
			);
		}
		set_theme_mod( 'mizuki_custom_links', wp_json_encode( $saved_links, JSON_UNESCAPED_UNICODE ) );
		// Live2D
		set_theme_mod( 'mizuki_live2d_enabled', ! empty( $_POST['mizuki_live2d_enabled'] ) );

		// 追番 API（修复:此前保存其它设置会清空追番配置）
		$anime_mode = isset( $_POST['mizuki_anime_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_anime_mode'] ) ) : 'local';
		if ( ! in_array( $anime_mode, array( 'local', 'bangumi', 'bilibili' ), true ) ) {
			$anime_mode = 'local';
		}
		set_theme_mod( 'mizuki_anime_mode', $anime_mode );
		set_theme_mod( 'mizuki_bangumi_user_id', isset( $_POST['mizuki_bangumi_user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_bangumi_user_id'] ) ) : '' );
		set_theme_mod( 'mizuki_bilibili_vmid', isset( $_POST['mizuki_bilibili_vmid'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_bilibili_vmid'] ) ) : '' );
		set_theme_mod( 'mizuki_bilibili_use_webp', ! empty( $_POST['mizuki_bilibili_use_webp'] ) );
		set_theme_mod( 'mizuki_anime_cache_hours', isset( $_POST['mizuki_anime_cache_hours'] ) ? absint( $_POST['mizuki_anime_cache_hours'] ) : 24 );

		// 追番数据源变更后清除旧缓存
		delete_transient( 'mizuki_local_anime_data' );
		delete_transient( 'mizuki_bangumi_data_' . get_theme_mod( 'mizuki_bangumi_user_id', '' ) );
		delete_transient( 'mizuki_bilibili_data_' . get_theme_mod( 'mizuki_bilibili_vmid', '' ) );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '设置已保存。', 'mizuki' ) . '</p></div>';
	}

	// 读取当前值
	$banner_image     = get_theme_mod( 'mizuki_banner_image', '' );
	$banner_height    = get_theme_mod( 'mizuki_banner_height', '60vh' );
	$hue              = get_theme_mod( 'mizuki_hue', 240 );
	$hue_fixed        = get_theme_mod( 'mizuki_hue_fixed', false );
	$avatar           = get_theme_mod( 'mizuki_avatar', '' );
	$nickname         = get_theme_mod( 'mizuki_nickname', '' );
	$bio              = get_theme_mod( 'mizuki_bio', '' );
	$custom_links     = mizuki_get_custom_links(); // 自定义社交/外链列表。
	$live2d_enabled   = get_theme_mod( 'mizuki_live2d_enabled', false );

	// 追番 API 当前值
	$anime_mode          = get_theme_mod( 'mizuki_anime_mode', 'local' );
	$bangumi_user_id     = get_theme_mod( 'mizuki_bangumi_user_id', '' );
	$bilibili_vmid       = get_theme_mod( 'mizuki_bilibili_vmid', '' );
	$bilibili_use_webp   = get_theme_mod( 'mizuki_bilibili_use_webp', false );
	$anime_cache_hours   = get_theme_mod( 'mizuki_anime_cache_hours', 24 );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Mizuki 主题设置', 'mizuki' ); ?></h1>
		<p class="description"><?php esc_html_e( '统一管理 Mizuki 主题的所有配置,与 外观→自定义→Mizuki 主题设置 双向同步。', 'mizuki' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'mizuki_save_settings', 'mizuki_settings_nonce' ); ?>

			<style>
				.mizuki-accordion { margin-top: 20px; max-width: 800px; }
				.mizuki-accordion-item { background: #fff; border: 1px solid #ccd0d4; margin-bottom: 10px; border-radius: 4px; }
				.mizuki-accordion-header { padding: 12px 16px; cursor: pointer; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: space-between; user-select: none; }
				.mizuki-accordion-header:hover { background: #f6f7f7; }
				.mizuki-accordion-arrow { transition: transform 0.2s; font-size: 16px; }
				.mizuki-accordion-item.open .mizuki-accordion-arrow { transform: rotate(180deg); }
				.mizuki-accordion-body { padding: 16px; border-top: 1px solid #f0f0f1; display: none; }
				.mizuki-accordion-item.open .mizuki-accordion-body { display: block; }
				.mizuki-field { margin-bottom: 16px; }
				.mizuki-field label { display: block; font-weight: 600; margin-bottom: 6px; }
				.mizuki-field input[type="text"], .mizuki-field input[type="url"], .mizuki-field input[type="number"], .mizuki-field textarea { width: 100%; max-width: 500px; }
				.mizuki-field input[type="range"] { width: 100%; max-width: 400px; }
				.mizuki-field .description { color: #646970; font-size: 13px; margin-top: 4px; }
			</style>

			<div class="mizuki-accordion">
				<!-- Banner -->
				<div class="mizuki-accordion-item open">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( 'Banner 设置', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label for="mizuki_banner_image"><?php esc_html_e( 'Banner 图片 URL', 'mizuki' ); ?></label>
							<input type="url" id="mizuki_banner_image" name="mizuki_banner_image" value="<?php echo esc_attr( $banner_image ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( '留空则使用默认 Banner。可在媒体库上传后复制 URL。', 'mizuki' ); ?></p>
						</div>
						<div class="mizuki-field">
							<label for="mizuki_banner_height"><?php esc_html_e( 'Banner 高度', 'mizuki' ); ?></label>
							<input type="text" id="mizuki_banner_height" name="mizuki_banner_height" value="<?php echo esc_attr( $banner_height ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( '如 60vh、400px,默认 60vh。', 'mizuki' ); ?></p>
						</div>
					</div>
				</div>

				<!-- 主题色 -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( '主题色', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label for="mizuki_hue"><?php esc_html_e( '主题色相 (Hue)', 'mizuki' ); ?> <span id="mizuki-hue-val"><?php echo esc_html( $hue ); ?></span></label>
							<input type="range" id="mizuki_hue" name="mizuki_hue" min="0" max="360" step="1" value="<?php echo esc_attr( $hue ); ?>">
							<p class="description"><?php esc_html_e( '0-360,默认 240(蓝色)。', 'mizuki' ); ?></p>
							<script>
								document.getElementById('mizuki_hue').addEventListener('input', function(e) {
									document.getElementById('mizuki-hue-val').textContent = e.target.value;
								});
							</script>
						</div>
						<div class="mizuki-field">
							<label><input type="checkbox" name="mizuki_hue_fixed" value="1" <?php checked( $hue_fixed ); ?>> <?php esc_html_e( '锁定主题色(隐藏访客调色器)', 'mizuki' ); ?></label>
						</div>
					</div>
				</div>

				<!-- 个人资料 -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( '个人资料', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label for="mizuki_avatar"><?php esc_html_e( '头像 URL', 'mizuki' ); ?></label>
							<input type="url" id="mizuki_avatar" name="mizuki_avatar" value="<?php echo esc_attr( $avatar ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_nickname"><?php esc_html_e( '昵称', 'mizuki' ); ?></label>
							<input type="text" id="mizuki_nickname" name="mizuki_nickname" value="<?php echo esc_attr( $nickname ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_bio"><?php esc_html_e( '简介', 'mizuki' ); ?></label>
							<textarea id="mizuki_bio" name="mizuki_bio" rows="3" class="large-text"><?php echo esc_textarea( $bio ); ?></textarea>
						</div>
					</div>
				</div>

				<!-- 社交链接(自定义:可增删,不限固定类型) -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( '社交链接', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<p class="description" style="margin-bottom:10px"><?php esc_html_e( '添加任意数量的链接(显示在导航栏「链接」下拉和资料卡)。图标可选;邮箱可直接填地址。', 'mizuki' ); ?></p>
						<style>
							.mizuki-cl-row{display:flex;gap:6px;align-items:center;margin-bottom:8px;flex-wrap:wrap}
							.mizuki-cl-row input[type=text],.mizuki-cl-row input[type=url]{flex:1 1 120px;min-width:120px}
							.mizuki-cl-row select{flex:0 0 110px}
							.mizuki-cl-row .mizuki-cl-del{flex:0 0 auto}
						</style>
						<div id="mizuki-cl-container">
							<?php
							$cl_icon_keys = array_keys( mizuki_custom_link_icons() );
							$cl_rows      = ! empty( $custom_links ) ? $custom_links : array( array( 'name' => '', 'url' => '', 'icon' => 'github' ) );
							foreach ( $cl_rows as $cl ) :
								$cl_name = isset( $cl['name'] ) ? $cl['name'] : '';
								$cl_url  = isset( $cl['url'] ) ? $cl['url'] : '';
								$cl_icon = isset( $cl['icon'] ) ? $cl['icon'] : 'github';
								?>
							<div class="mizuki-cl-row">
								<input type="text" name="mizuki_cl_name[]" value="<?php echo esc_attr( $cl_name ); ?>" placeholder="<?php esc_attr_e( '名称(如 GitHub)', 'mizuki' ); ?>" class="regular-text">
								<input type="text" name="mizuki_cl_url[]" value="<?php echo esc_attr( $cl_url ); ?>" placeholder="<?php esc_attr_e( 'URL 或邮箱', 'mizuki' ); ?>" class="regular-text">
								<select name="mizuki_cl_icon[]">
									<?php foreach ( $cl_icon_keys as $k ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $cl_icon, $k ); ?>><?php echo esc_html( $k ); ?></option>
									<?php endforeach; ?>
								</select>
								<button type="button" class="button mizuki-cl-del" aria-label="<?php esc_attr_e( '删除该链接', 'mizuki' ); ?>">✕</button>
							</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button" id="mizuki-cl-add">+ <?php esc_html_e( '添加链接', 'mizuki' ); ?></button>
						<script>
						(function(){
							var iconHtml = <?php echo wp_json_encode( '<option>' . implode( '</option><option>', $cl_icon_keys ) . '</option>' ); ?>;
							document.getElementById('mizuki-cl-add').addEventListener('click', function(){
								var row = document.createElement('div');
								row.className = 'mizuki-cl-row';
								row.innerHTML =
									'<input type="text" name="mizuki_cl_name[]" placeholder="<?php esc_attr_e( '名称(如 GitHub)', 'mizuki' ); ?>" class="regular-text">' +
									'<input type="text" name="mizuki_cl_url[]" placeholder="<?php esc_attr_e( 'URL 或邮箱', 'mizuki' ); ?>" class="regular-text">' +
									'<select name="mizuki_cl_icon[]">' + iconHtml + '</select>' +
									'<button type="button" class="button mizuki-cl-del" aria-label="<?php esc_attr_e( '删除该链接', 'mizuki' ); ?>">✕</button>';
								document.getElementById('mizuki-cl-container').appendChild(row);
							});
							document.getElementById('mizuki-cl-container').addEventListener('click', function(e){
								if (e.target && e.target.classList.contains('mizuki-cl-del')) {
									var rows = this.querySelectorAll('.mizuki-cl-row');
									if (rows.length > 1) {
										e.target.closest('.mizuki-cl-row').remove();
									} else {
										// 仅剩一行时清空而非删除,保证至少有一个输入行。
										e.target.closest('.mizuki-cl-row').querySelectorAll('input').forEach(function(i){ i.value=''; });
									}
								}
							});
						})();
						</script>
					</div>
				</div>

				<!-- Live2D -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( 'Live2D 看板娘', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label><input type="checkbox" name="mizuki_live2d_enabled" value="1" <?php checked( $live2d_enabled ); ?>> <?php esc_html_e( '启用 Live2D 看板娘', 'mizuki' ); ?></label>
						</div>
					</div>
				</div>

				<!-- 追番 API -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( '追番 API', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label for="mizuki_anime_mode"><?php esc_html_e( '追番数据源', 'mizuki' ); ?></label>
							<select id="mizuki_anime_mode" name="mizuki_anime_mode">
								<option value="local" <?php selected( $anime_mode, 'local' ); ?>><?php esc_html_e( '本地数据(自定义文章类型)', 'mizuki' ); ?></option>
								<option value="bangumi" <?php selected( $anime_mode, 'bangumi' ); ?>><?php esc_html_e( 'Bangumi', 'mizuki' ); ?></option>
								<option value="bilibili" <?php selected( $anime_mode, 'bilibili' ); ?>><?php esc_html_e( '哔哩哔哩', 'mizuki' ); ?></option>
							</select>
						</div>
						<div class="mizuki-field">
							<label for="mizuki_bangumi_user_id"><?php esc_html_e( 'Bangumi 用户 ID', 'mizuki' ); ?></label>
							<input type="text" id="mizuki_bangumi_user_id" name="mizuki_bangumi_user_id" value="<?php echo esc_attr( $bangumi_user_id ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_bilibili_vmid"><?php esc_html_e( '哔哩哔哩 UID', 'mizuki' ); ?></label>
							<input type="text" id="mizuki_bilibili_vmid" name="mizuki_bilibili_vmid" value="<?php echo esc_attr( $bilibili_vmid ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label><input type="checkbox" name="mizuki_bilibili_use_webp" value="1" <?php checked( $bilibili_use_webp ); ?>> <?php esc_html_e( '使用 WebP 格式封面(哔哩哔哩,体积更小)', 'mizuki' ); ?></label>
						</div>
						<div class="mizuki-field">
							<label for="mizuki_anime_cache_hours"><?php esc_html_e( '缓存时长(小时)', 'mizuki' ); ?></label>
							<input type="number" id="mizuki_anime_cache_hours" name="mizuki_anime_cache_hours" value="<?php echo esc_attr( $anime_cache_hours ); ?>" min="1" max="168" class="small-text">
						</div>
					</div>
				</div>
			</div>

			<?php submit_button( __( '保存所有设置', 'mizuki' ) ); ?>
		</form>

		<!-- 内容分类管理(项目 / 技能):独立表单,与主设置互不影响 -->
		<h2 style="margin-top:32px;"><?php esc_html_e( '内容分类管理', 'mizuki' ); ?></h2>
		<p class="description"><?php esc_html_e( '管理「项目」和「技能」页面的筛选分类(taxonomy term),增删改后前端筛选 Tab 会自动同步。', 'mizuki' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'mizuki_cat_manage', 'mizuki_cat_nonce' ); ?>
			<div class="mizuki-accordion">
				<?php
				mizuki_render_category_manager(
					'project_category',
					__( '项目分类管理', 'mizuki' ),
					__( '对应「项目」页面的筛选分类(默认:web / mobile / desktop / other)。', 'mizuki' )
				);
				mizuki_render_category_manager(
					'skill_category',
					__( '技能分类管理', 'mizuki' ),
					__( '对应「技能」页面的筛选分类(默认:frontend / backend / database / tools / other)。', 'mizuki' )
				);
				?>
			</div>
		</form>
	</div>

	<script>
	document.querySelectorAll('.mizuki-accordion-header').forEach(header => {
		header.addEventListener('click', () => {
			header.parentElement.classList.toggle('open');
		});
	});
	</script>
	<?php
}

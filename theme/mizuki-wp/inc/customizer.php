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

	// === 社交链接 ===
	$wp_customize->add_section( 'mizuki_social', array(
		'title'    => __( '社交链接', 'mizuki' ),
		'panel'    => 'mizuki_panel',
		'priority' => 40,
	) );
	foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
		if ( 'email' === $p ) {
			// 邮箱: 接受纯邮箱地址(渲染时自动加 mailto:),避免 esc_url 丢弃无协议地址。
			$wp_customize->add_setting( 'mizuki_social_email', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( 'mizuki_social_email', array( 'label' => __( 'Email(邮箱或 mailto: 链接)', 'mizuki' ), 'section' => 'mizuki_social', 'type' => 'text' ) );
			continue;
		}
		$wp_customize->add_setting( "mizuki_social_{$p}", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "mizuki_social_{$p}", array( 'label' => ucfirst( $p ) . ' URL', 'section' => 'mizuki_social', 'type' => 'url' ) );
	}

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
		// 社交链接
		$socials = array();
		foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
			$url = get_theme_mod( "mizuki_social_{$p}", '' );
			if ( ! $url ) {
				continue;
			}
			// 邮箱: 纯地址(含 @ 但无 mailto: 前缀)自动补 mailto:。
			if ( 'email' === $p && false !== strpos( $url, '@' ) && 0 !== strpos( $url, 'mailto:' ) ) {
				$url = 'mailto:' . $url;
			}
			$socials[ $p ] = $url;
		}
		if ( ! empty( $socials ) ) :
		?>
		<div class="flex items-center justify-center gap-3 mt-3">
			<?php foreach ( $socials as $name => $url ) : ?>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"
			   class="btn-plain scale-animation rounded-lg w-9 h-9 text-50 hover:text-[var(--primary)] transition"
			   aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>">
				<?php
				// 简单图标 SVG
				$icons = array(
					'github'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>',
					'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
					'email'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 4l-8 5-8-5V6l8 5 8-5z"/></svg>',
					'rss'     => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="6.18" cy="17.82" r="2.18"/><path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56m0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9"/></svg>',
				);
				echo isset( $icons[ $name ] ) ? $icons[ $name ] : '';
				?>
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
		// 社交链接
		foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
			$key = "mizuki_social_{$p}";
			set_theme_mod( $key, isset( $_POST[ $key ] ) ? ( 'email' === $p ? sanitize_text_field( $_POST[ $key ] ) : esc_url_raw( $_POST[ $key ] ) ) : '' );
		}
		// Live2D
		set_theme_mod( 'mizuki_live2d_enabled', ! empty( $_POST['mizuki_live2d_enabled'] ) );

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
	$social_github    = get_theme_mod( 'mizuki_social_github', '' );
	$social_twitter   = get_theme_mod( 'mizuki_social_twitter', '' );
	$social_email     = get_theme_mod( 'mizuki_social_email', '' );
	$social_rss       = get_theme_mod( 'mizuki_social_rss', '' );
	$live2d_enabled   = get_theme_mod( 'mizuki_live2d_enabled', false );
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

				<!-- 社交链接 -->
				<div class="mizuki-accordion-item">
					<div class="mizuki-accordion-header">
						<span><?php esc_html_e( '社交链接', 'mizuki' ); ?></span>
						<span class="mizuki-accordion-arrow">▼</span>
					</div>
					<div class="mizuki-accordion-body">
						<div class="mizuki-field">
							<label for="mizuki_social_github">GitHub URL</label>
							<input type="url" id="mizuki_social_github" name="mizuki_social_github" value="<?php echo esc_attr( $social_github ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_social_twitter">Twitter/X URL</label>
							<input type="url" id="mizuki_social_twitter" name="mizuki_social_twitter" value="<?php echo esc_attr( $social_twitter ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_social_email"><?php esc_html_e( 'Email(邮箱或 mailto: 链接)', 'mizuki' ); ?></label>
							<input type="text" id="mizuki_social_email" name="mizuki_social_email" value="<?php echo esc_attr( $social_email ); ?>" class="regular-text">
						</div>
						<div class="mizuki-field">
							<label for="mizuki_social_rss">RSS URL</label>
							<input type="url" id="mizuki_social_rss" name="mizuki_social_rss" value="<?php echo esc_attr( $social_rss ); ?>" class="regular-text">
						</div>
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

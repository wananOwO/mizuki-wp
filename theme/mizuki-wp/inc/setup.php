<?php
/**
 * 主题基础设置: 菜单、侧边栏、缩略图等。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 主题初始化:theme supports + 菜单。
 */
function mizuki_setup() {
	load_theme_textdomain( 'mizuki', MIZUKI_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
}
add_action( 'after_setup_theme', 'mizuki_setup' );

/**
 * 注册菜单位置。
 */
function mizuki_register_menus() {
	register_nav_menus(
		array(
			'primary' => esc_html__( '主导航菜单', 'mizuki' ),
		)
	);
}
add_action( 'after_setup_theme', 'mizuki_register_menus' );

/**
 * 注册侧边栏小工具区域。
 */
function mizuki_register_sidebars() {
	register_sidebar(
		array(
			'name'          => esc_html__( '左侧边栏', 'mizuki' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( '左侧边栏小工具区域(档案、分类、标签等)。', 'mizuki' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title font-bold text-lg mb-2">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'mizuki_register_sidebars' );

/**
 * 自定义评论输出(套用 Mizuki 样式 class)。
 */
function mizuki_comment_template( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'card-base p-4 mb-2' ); ?>>
		<div class="comment-author flex items-center gap-3 mb-2">
			<?php echo get_avatar( $comment, 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
			<span class="font-bold text-90"><?php comment_author(); ?></span>
			<span class="text-50 text-sm"><?php comment_date(); ?></span>
		</div>
		<div class="comment-content prose dark:prose-invert text-75">
			<?php comment_text(); ?>
		</div>
		<div class="comment-actions text-sm text-50 mt-2">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => isset( $args['max_depth'] ) ? $args['max_depth'] : 5 ) ) ); ?>
		</div>
	<?php
}

/**
 * 输出 Mizuki 原版无闪烁引导脚本(#03)。
 *
 * 直接复用 Mizuki 编译产物中的 <head> 脚本:在 CSS 之前设置
 * 明暗主题 class、--hue、--banner-height-extend,以及桌面端
 * 自动缩放(pageScaling,设置 documentElement.style.fontSize)。
 * 配置值由 WordPress(Customizer)注入。
 */
function mizuki_head_boot_script() {
	$default_theme = 'light';
	$config_hue    = (int) get_theme_mod( 'mizuki_hue', 240 );
	$hue_fixed     = get_theme_mod( 'mizuki_hue_fixed', false ) ? 'true' : 'false';
	// pageScaling: 与 Mizuki 默认一致(桌面端按 targetWidth 缩放)。
	$scaling_enable = get_theme_mod( 'mizuki_page_scaling', true ) ? 'true' : 'false';
	$target_width   = 2000;
	?>
<script>(function(){const DEFAULT_THEME = "<?php echo esc_js( $default_theme ); ?>";
const LIGHT_MODE = "light", DARK_MODE = "dark", AUTO_MODE = "auto";
const BANNER_HEIGHT_EXTEND = 30;
const configHue = <?php echo (int) $config_hue; ?>;
const themeColorFixed = <?php echo $hue_fixed; // phpcs:ignore ?>;
const pageScaling = {"enable":<?php echo $scaling_enable; // phpcs:ignore ?>,"targetWidth":<?php echo (int) $target_width; ?>};
	const theme = localStorage.getItem("theme") || DEFAULT_THEME;
	let isDark = false;
	switch (theme) {
		case DARK_MODE: document.documentElement.classList.add("dark"); isDark = true; break;
		case AUTO_MODE:
			isDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
			document.documentElement.classList.toggle("dark", isDark); break;
		default: document.documentElement.classList.remove("dark"); isDark = false;
	}
	const expressiveTheme = isDark ? "github-dark" : "github-light";
	if (document.documentElement.getAttribute("data-theme") !== expressiveTheme) {
		document.documentElement.setAttribute("data-theme", expressiveTheme);
	}
	const hue = themeColorFixed ? configHue : (localStorage.getItem("hue") || configHue);
	document.documentElement.style.setProperty("--hue", hue);
	let offset = Math.floor(window.innerHeight * (BANNER_HEIGHT_EXTEND / 100));
	offset = offset - (offset % 4);
	document.documentElement.style.setProperty("--banner-height-extend", `${offset}px`);
	(function () {
		if (pageScaling && pageScaling.enable) {
			function adjustPageScale() {
				const isTouch = (window.matchMedia && (window.matchMedia("(pointer:coarse)").matches || window.matchMedia("(hover: none)").matches)) || "ontouchstart" in window;
				const isPortrait = window.matchMedia && window.matchMedia("(orientation: portrait)").matches;
				if (isTouch || window.innerWidth <= 1280 || isPortrait) { document.documentElement.style.fontSize = ""; return; }
				const targetWidth = pageScaling.targetWidth || 2000;
				let scale = document.documentElement.clientWidth / targetWidth;
				if (scale > 1) scale = 1;
				if (scale < 0.85) scale = 0.85;
				document.documentElement.style.fontSize = `${scale * 100}%`;
			}
			adjustPageScale();
			window.addEventListener("resize", adjustPageScale);
			window.addEventListener("orientationchange", adjustPageScale);
		}
	})();
})();</script>
	<?php
}

/**
 * 把主题主色 hue 作为 CSS 变量注入文档头(无 JS 时的回退值)。
 * 运行时 --hue 由 mizuki_head_boot_script() 中的脚本从 localStorage 设定。
 */
function mizuki_output_hue() {
	$hue = (int) apply_filters( 'mizuki_theme_hue', (int) get_theme_mod( 'mizuki_hue', 240 ) );
	$hue = max( 0, min( 360, $hue ) );
	printf( "<style id=\"mizuki-hue-fallback\">:root{--hue:%d;}</style>\n", absint( $hue ) );
}
add_action( 'wp_head', 'mizuki_output_hue', 1 );

/**
 * 向 body 添加 Mizuki 功能性 class。
 * 始终启用 banner(与 Mizuki 默认 wallpaperMode="banner" 一致,内置默认 banner 图)。
 */
function mizuki_body_classes( $classes ) {
	$classes[] = 'enable-banner';
	$classes[] = 'enable-card-border';
	return $classes;
}
add_filter( 'body_class', 'mizuki_body_classes' );

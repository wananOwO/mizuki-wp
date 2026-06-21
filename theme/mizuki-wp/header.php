<?php
/**
 * Mizuki 主题页头模板。
 *
 * 输出 Mizuki MainGridLayout + Navbar + Banner 的精确 DOM 结构,
 * 与编译后的 Mizuki CSS 选择器完全匹配。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<?php
// 与 Mizuki 一致的布局 CSS 变量(置于 <html> 内联样式)。
$mz_hue          = (int) mizuki_get_theme_mod( 'mizuki_hue', 240 );
$mz_banner_home  = mizuki_get_theme_mod( 'mizuki_banner_height_home', '65vh' );
$mz_banner_inner = mizuki_get_theme_mod( 'mizuki_banner_height', '35vh' );
?>
<html <?php language_attributes(); ?> class="bg-[var(--page-bg)] text-[14px] md:text-[16px]" style="--configHue:<?php echo esc_attr( $mz_hue ); ?>;--page-width:90rem;--bannerOffset:15vh;--banner-height-home:<?php echo esc_attr( $mz_banner_home ); ?>;--banner-height:<?php echo esc_attr( $mz_banner_inner ); ?>;">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php mizuki_head_boot_script(); // Mizuki 原版无闪烁主题/缩放引导脚本(必须在 CSS 之前) ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php mizuki_render_navbar(); ?>

<?php mizuki_render_banner(); ?>

<!-- 主内容网格 -->
<div class="absolute w-full z-30 pointer-events-none<?php echo ( ! is_front_page() ) ? ' mobile-main-no-banner' : ''; ?>" style="top: 35vh" data-astro-cid-haiuh7kc>
	<div class="relative max-w-(--page-width) mx-auto pointer-events-auto" data-astro-cid-haiuh7kc>
		<div id="main-grid" class="transition duration-700 w-full left-0 right-0 grid grid-cols-1 md:grid-cols-[17.5rem_1fr] lg:grid-cols-[17.5rem_1fr_17.5rem] grid-rows-none md:grid-rows-[auto] mx-auto gap-4 px-2 md:px-4 mobile-both-sidebar" data-layout-mode="list">
			<?php mizuki_render_left_sidebar(); ?>

			<main id="swup-container" class="transition-main">
				<div id="content-wrapper" class="onload-animation transition-leaving">
					<!-- 各页面内容插入点 -->

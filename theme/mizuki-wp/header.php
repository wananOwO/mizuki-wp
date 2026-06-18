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
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 顶部导航 -->
<div id="top-row" class="z-50 pointer-events-none relative transition-all duration-700 max-w-(--page-width) px-0 md:px-4 mx-auto">
	<div id="navbar-wrapper" class="pointer-events-auto sticky top-0 transition-all">
		<div id="navbar" class="z-50 onload-animation group" data-transparent-mode="semi" data-is-home="<?php echo is_front_page() ? 'true' : 'false'; ?>">
			<div class="absolute h-8 left-0 right-0 -top-8 bg-[var(--card-bg)] transition"></div>
			<div class="!overflow-visible max-w-[var(--page-width)] h-[4.5rem] mx-auto flex items-center justify-between px-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-plain scale-animation rounded-lg h-[2.5rem] md:h-[3.25rem] px-5 font-bold active:scale-95 shrink-0 transition-all duration-300">
					<div class="flex flex-row items-center text-md">
						<span class="dark:text-white text-black"><?php bloginfo( 'name' ); ?></span>
					</div>
				</a>
				<div id="navbar-links-container" class="hidden md:flex items-center space-x-1 transition-opacity duration-300">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
					?>
				</div>
				<div class="flex items-center gap-1">
					<button id="theme-toggle" class="btn-plain scale-animation rounded-lg h-11 w-11" aria-label="<?php esc_attr_e( '切换明暗', 'mizuki' ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
					</button>
					<button id="display-settings-switch" class="btn-plain scale-animation rounded-lg h-11 w-11 active:scale-90" aria-label="<?php esc_attr_e( 'Display Settings', 'mizuki' ); ?>">
						<svg class="text-[1.25rem]" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 22C6.49 22 2 17.51 2 12S6.49 2 12 2s10 4.04 10 9c0 3.31-2.69 6-6 6h-1.77c-.28 0-.5.22-.5.5c0 .12.05.23.13.33c.41.47.64 1.06.64 1.67A2.5 2.5 0 0 1 12 22m0-18c-4.41 0-8 3.59-8 8s3.59 8 8 8c.28 0 .5-.22.5-.5a.54.54 0 0 0-.14-.35c-.41-.46-.63-1.05-.63-1.65a2.5 2.5 0 0 1 2.5-2.5H16c2.21 0 4-1.79 4-4c0-3.86-3.59-7-8-7m-5.5 9a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3m3-4a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3m5 0a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3m3 4a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3"/></svg>
					</button>
					<button id="nav-menu-switch" class="btn-plain scale-animation rounded-lg w-11 h-11 active:scale-90 md:!hidden" aria-label="<?php esc_attr_e( 'Menu', 'mizuki' ); ?>">
						<svg class="text-[1.25rem]" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 3c-.55 0-1 .45-1 1v7H4c-.55 0-1 .45-1 1s.45 1 1 1h7v7c0 .55.45 1 1 1s1-.45 1-1v-7h7c.55 0 1-.45 1-1s-.45-1-1-1h-7V4c0-.55-.45-1-1-1"/></svg>
					</button>
				</div>
				<!-- 移动端导航菜单面板 -->
				<div id="nav-menu-panel" class="float-panel float-panel-closed absolute transition-all fixed right-4 px-2 py-2 max-h-[80vh] overflow-y-auto">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'fallback_cb'    => false,
							'depth'          => 2,
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Banner 大图区域 -->
<?php
$banner_img    = get_theme_mod( 'mizuki_banner_image', '' );
$banner_height = get_theme_mod( 'mizuki_banner_height', '60vh' );
$banner_style  = '';
if ( $banner_img ) {
	$banner_style = sprintf(
		'style="background-image: url(%s); background-size: cover; background-position: center; min-height: %s;"',
		esc_url( $banner_img ),
		esc_attr( $banner_height )
	);
}
?>
<div id="banner-wrapper" <?php echo $banner_style; ?> class="absolute z-10 w-full transition duration-700 overflow-hidden">
	<div id="banner-carousel" class="relative h-full w-full">
		<div class="banner-image-slot-mobile absolute inset-0 block md:hidden"></div>
		<div class="banner-image-slot-desktop absolute inset-0 hidden md:block">
			<div id="banner"></div>
		</div>
	</div>
	<div id="banner-single-container" class="relative h-full w-full">
		<?php if ( is_front_page() ) : ?>
			<div class="banner-text-overlay absolute inset-0 z-20 flex items-center justify-center">
				<div class="w-4/5 lg:w-3/4 text-center mb-0">
					<div class="flex flex-col">
						<h1 class="banner-title text-6xl lg:text-8xl text-white drop-shadow-lg mb-2 lg:mb-4"><?php bloginfo( 'name' ); ?></h1>
						<h2 class="banner-subtitle text-xl lg:text-3xl text-white/90 drop-shadow-md">
							<span class="inline-block min-h-[1.2em]"><?php bloginfo( 'description' ); ?></span>
						</h2>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div id="banner-credit"></div>
</div>

<!-- 主内容网格 -->
<div class="absolute w-full z-30 pointer-events-none">
	<div class="relative max-w-(--page-width) mx-auto pointer-events-auto">
		<div id="main-grid" class="transition duration-700 w-full left-0 right-0 grid grid-cols-1 2xl:grid-cols-[1fr_min(var(--page-width),100%)_1fr] grid-rows-none md:grid-rows-[auto] mx-auto gap-4 px-2 md:px-4" data-layout-mode="list">
			<div class="contents">
				<aside id="sidebar" class="w-full sidebar-root">
					<div id="sidebar-sticky" class="transition-all duration-700 flex flex-col w-full gap-4 sticky top-4">
						<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
							<div class="sidebar-content hidden lg:flex flex-col w-full gap-4">
								<?php dynamic_sidebar( 'sidebar-1' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</aside>
			</div>

			<main id="swup-container" class="transition-main">
				<div id="content-wrapper" class="onload-animation transition-leaving">
					<!-- 各页面内容插入点 -->

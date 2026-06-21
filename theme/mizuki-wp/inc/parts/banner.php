<?php
/**
 * Banner 大图区域渲染 — 与 Mizuki dist 的 #banner-wrapper 1:1。
 *
 * 结构:#banner-wrapper(top:-30vh)> #banner-carousel(桌面/移动图片 + 轮播模板)
 *   + Ken Burns 动画 + 首页标题/打字副标题 overlay + 内页 page-overlay
 *   + #header-waves(SVG 动画波浪)。
 * 常量:BANNER_HEIGHT_EXTEND=30vh(banner-wrapper top),内容区 top=35vh(见 header.php)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mizuki_banner_images' ) ) {
	/**
	 * 返回 banner 图片 URL 列表(桌面/移动)。自定义图优先,否则用内置 4 张。
	 *
	 * @return array{desktop:string[],mobile:string[]}
	 */
	function mizuki_banner_images() {
		$custom = get_theme_mod( 'mizuki_banner_image', '' );
		$base   = get_template_directory_uri();
		if ( $custom ) {
			return array(
				'desktop' => array( $custom ),
				'mobile'  => array( $custom ),
			);
		}
		$d = array();
		$m = array();
		for ( $i = 1; $i <= 4; $i++ ) {
			$d[] = $base . '/assets/desktop-banner/' . $i . '.webp';
			$m[] = $base . '/assets/mobile-banner/' . $i . '.webp';
		}
		return array(
			'desktop' => $d,
			'mobile'  => $m,
		);
	}
}

if ( ! function_exists( 'mizuki_banner_slide' ) ) {
	/**
	 * 输出单张 banner 图片的内层结构(与 dist 一致)。
	 *
	 * @param string $src     图片 URL。
	 * @param string $alt     替代文本。
	 * @param string $wrap_id 外层 id(首图为 #banner,其余为空)。
	 */
	function mizuki_banner_slide( $src, $alt, $wrap_id = '' ) {
		$id = $wrap_id ? ' id="' . esc_attr( $wrap_id ) . '"' : '';
		?><div<?php echo $id; // phpcs:ignore ?> class="object-cover h-full w-full overflow-hidden relative"><div class="transition absolute inset-0 dark:bg-black/10 bg-opacity-50 pointer-events-none"></div><img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="w-full h-full object-cover" style="object-position: center" loading="eager" decoding="async" fetchpriority="high"></div><?php
	}
}

if ( ! function_exists( 'mizuki_render_banner' ) ) {
	/**
	 * 输出 Mizuki Banner。
	 */
	function mizuki_render_banner() {
		$imgs        = mizuki_banner_images();
		$desktop     = $imgs['desktop'];
		$mobile      = $imgs['mobile'];
		$mobile_hide = ( ! is_front_page() ) ? ' mobile-hide-banner' : '';
		?>
<!-- Banner 大图区域 -->
<div id="banner-wrapper" class="absolute z-10 w-full transition duration-700 overflow-hidden<?php echo esc_attr( $mobile_hide ); ?>" style="top: -30vh" data-astro-cid-3tcy46xc>
	<div id="banner-carousel" class="relative h-full w-full" data-mobile-count="<?php echo count( $mobile ); ?>" data-desktop-count="<?php echo count( $desktop ); ?>" data-carousel-interval="3000" data-fade-ms="1200" data-astro-cid-3tcy46xc>
		<div class="banner-image-slot-mobile absolute inset-0 block md:hidden" data-astro-cid-3tcy46xc>
			<?php mizuki_banner_slide( $mobile[0], 'Mobile banner image 1' ); ?>
		</div>
		<?php
		foreach ( array_slice( $mobile, 1 ) as $i => $src ) :
			?>
			<template class="banner-tpl-mobile" data-astro-cid-3tcy46xc><?php mizuki_banner_slide( $src, 'Mobile banner image ' . ( $i + 2 ) ); ?></template>
		<?php endforeach; ?>
		<div class="banner-image-slot-desktop absolute inset-0 hidden md:block" data-astro-cid-3tcy46xc>
			<?php mizuki_banner_slide( $desktop[0], 'Desktop banner image 1', 'banner' ); ?>
		</div>
		<?php
		foreach ( array_slice( $desktop, 1 ) as $i => $src ) :
			?>
			<template class="banner-tpl-desktop" data-astro-cid-3tcy46xc><?php mizuki_banner_slide( $src, 'Desktop banner image ' . ( $i + 2 ) ); ?></template>
		<?php endforeach; ?>
	</div>

	<style>
		@keyframes bannerKenBurns { 0% { transform: scale(1.03); } 100% { transform: scale(1.13); } }
		.banner-kb { animation: bannerKenBurns 4200ms ease-out forwards; }
	</style>

	<?php if ( is_front_page() ) : ?>
	<!-- 首页文字覆盖层 -->
	<div class="banner-text-overlay absolute inset-0 z-20 flex items-center justify-center" data-astro-cid-3tcy46xc>
		<div class="w-4/5 lg:w-3/4 text-center mb-0" data-astro-cid-3tcy46xc>
			<div class="flex flex-col" data-astro-cid-3tcy46xc>
				<h1 class="banner-title text-6xl lg:text-8xl text-white drop-shadow-lg mb-2 lg:mb-4" data-astro-cid-3tcy46xc><?php bloginfo( 'name' ); ?></h1>
				<h2 class="banner-subtitle text-xl lg:text-3xl text-white/90 drop-shadow-md" data-astro-cid-3tcy46xc>
					<span class="inline-block min-h-[1.2em]" data-astro-cid-3tcy46xc><?php bloginfo( 'description' ); ?></span>
				</h2>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- 非首页页面标题覆盖层 -->
	<div id="banner-page-overlay" class="banner-page-overlay <?php echo is_front_page() ? 'hidden' : 'hidden lg:flex'; ?> absolute inset-0 z-20 pointer-events-none" data-astro-cid-3tcy46xc>
		<div class="flex flex-col items-center justify-center w-full text-center px-4" data-astro-cid-3tcy46xc>
			<h2 id="page-overlay-title" class="page-overlay-title text-3xl lg:text-5xl text-white font-bold drop-shadow-lg px-4" data-astro-cid-3tcy46xc><?php echo ( ! is_front_page() ) ? esc_html( wp_get_document_title() ) : ''; ?></h2>
		</div>
	</div>

	<!-- 水波纹效果 -->
	<div class="waves absolute -bottom-[1px] h-[10vh] max-h-[9.375rem] min-h-[3.125rem] w-full md:h-[15vh]" id="header-waves" style="transform: translateZ(0); will-change: fill;" data-astro-cid-3tcy46xc>
		<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 20 150 32" preserveAspectRatio="none" shape-rendering="auto" style="transform: translateZ(0); backface-visibility: hidden;" data-astro-cid-3tcy46xc>
			<defs data-astro-cid-3tcy46xc>
				<path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v48h-352z" data-astro-cid-3tcy46xc></path>
			</defs>
			<g class="parallax" style="transform: translateZ(0);" data-astro-cid-3tcy46xc>
				<use xlink:href="#gentle-wave" x="48" y="0" class="opacity-25 fill-[var(--page-bg)]" style="animation-delay: -2s; animation-duration: 7s; will-change: transform;" data-astro-cid-3tcy46xc></use>
				<use xlink:href="#gentle-wave" x="48" y="3" class="opacity-50 fill-[var(--page-bg)]" style="animation-delay: -3s; animation-duration: 10s; will-change: transform;" data-astro-cid-3tcy46xc></use>
				<use xlink:href="#gentle-wave" x="48" y="5" class="opacity-75 fill-[var(--page-bg)]" style="animation-delay: -4s; animation-duration: 13s; will-change: transform;" data-astro-cid-3tcy46xc></use>
				<use xlink:href="#gentle-wave" x="48" y="7" class="fill-[var(--page-bg)]" style="animation-delay: -5s; animation-duration: 20s; will-change: transform;" data-astro-cid-3tcy46xc></use>
			</g>
		</svg>
	</div>
</div>
		<?php
	}
}

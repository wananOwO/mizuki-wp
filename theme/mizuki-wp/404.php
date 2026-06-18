<?php
/**
 * 404 页面。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="error-404 onload-animation">
	<div class="card-base px-6 py-12 text-center">
		<h1 class="text-6xl font-bold text-90 mb-4"><?php esc_html_e( '404', 'mizuki' ); ?></h1>
		<p class="text-xl text-50 mb-8"><?php esc_html_e( '你访问的页面不存在或已移动。', 'mizuki' ); ?></p>
		<div class="max-w-md mx-auto">
			<?php get_search_form(); ?>
		</div>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
		   class="inline-block mt-6 btn-regular px-6 py-3 rounded-xl bg-[var(--primary)] text-white hover:opacity-80 transition">
			<?php esc_html_e( '返回首页', 'mizuki' ); ?>
		</a>
	</div>
</main>
<?php
get_footer();

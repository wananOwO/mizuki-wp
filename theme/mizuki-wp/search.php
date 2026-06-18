<?php
/**
 * 搜索结果(WordPress 原生搜索)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="search-results onload-animation">
	<header class="archive__header mb-6">
		<h1 class="text-3xl font-bold text-90 mb-2">
			<?php echo esc_html( sprintf( __( '搜索: %s', 'mizuki' ), get_search_query() ) ); ?>
		</h1>
		<p class="text-50">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %d: number of results */
					_n( '找到 %d 个结果', '找到 %d 个结果', $wp_query->found_posts, 'mizuki' ),
					$wp_query->found_posts
				)
			);
			?>
		</p>
	</header>

	<div class="post-list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative transition mb-4">
				<div class="pl-6 md:pl-9 pr-6 md:pr-2 pt-6 md:pt-7 pb-6 relative w-full">
					<a href="<?php the_permalink(); ?>"
					   class="transition group w-full block font-bold mb-3 text-2xl text-90 hover:text-[var(--primary)]">
						<?php the_title(); ?>
					</a>
					<div class="transition text-75 mb-3.5 pr-4 line-clamp-2">
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</div>
				</div>
			</div>
		<?php endwhile; the_posts_pagination( array( 'mid_size' => 1 ) ); else : ?>
			<p class="text-50 text-center py-12"><?php esc_html_e( '没有匹配的结果。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

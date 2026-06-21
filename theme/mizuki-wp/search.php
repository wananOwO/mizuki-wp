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

	<div id="post-list-container" class="post-list-container list-mode flex flex-col gap-2">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
			$has_cover = has_post_thumbnail();
		?>
			<div class="post-card-item card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative transition">
				<!-- 文字区 -->
				<div class="pl-6 md:pl-9 pr-6 md:pr-2 pt-6 md:pt-7 pb-6 relative w-full <?php echo $has_cover ? 'md:w-[72%]' : 'md:w-[calc(100%-3.25rem-0.75rem)]'; ?>">
					<a href="<?php the_permalink(); ?>"
					   class="transition group w-full block font-bold mb-3 text-2xl text-90 hover:text-[var(--primary)]">
						<?php the_title(); ?>
					</a>
					<!-- 元信息 -->
					<div class="flex flex-wrap text-neutral-500 dark:text-neutral-400 items-center gap-4 mb-3">
						<div class="flex items-center">
							<div class="meta-icon transition">
								<svg class="text-xl" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 16H5V10h14zM9 14H7v-2h2zm4 0h-2v-2h2zm4 0h-2v-2h2zm-8 4H7v-2h2zm4 0h-2v-2h2zm4 0h-2v-2h2z"/></svg>
							</div>
							<time class="text-50 text-sm font-medium" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
						<div class="flex items-center">
							<div class="meta-icon transition">
								<svg class="text-xl" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2M12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8s8 3.58 8 8s-3.58 8-8 8m.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
							</div>
							<span class="text-50 text-sm font-medium"><?php echo esc_html( sprintf( __( '%d 分钟', 'mizuki' ), mizuki_reading_time() ) ); ?></span>
						</div>
					</div>
					<div class="transition text-75 mb-3.5 pr-4 line-clamp-2">
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</div>
				</div>
				<!-- 封面图 -->
				<?php if ( $has_cover ) : ?>
					<a href="<?php the_permalink(); ?>"
					   class="group max-h-[20vh] md:max-h-none mx-4 mt-4 -mb-2 md:mb-0 md:mx-0 md:mt-0 md:w-[28%] relative md:absolute md:top-3 md:bottom-3 md:right-3 rounded-xl overflow-hidden active:scale-95"
					   aria-label="<?php the_title_attribute(); ?>">
						<div class="absolute pointer-events-none z-10 w-full h-full group-hover:bg-black/30 group-active:bg-black/50 transition"></div>
						<div class="absolute pointer-events-none z-20 w-full h-full flex items-center justify-center">
							<svg class="transition opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 text-white text-5xl" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
						</div>
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
					</a>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>"
					   aria-label="<?php the_title_attribute(); ?>"
					   class="!hidden md:!flex btn-regular w-[3.25rem] absolute right-3 top-3 bottom-3 rounded-xl bg-[var(--enter-btn-bg)] hover:bg-[var(--enter-btn-bg-hover)] active:bg-[var(--enter-btn-bg-active)] active:scale-95">
						<svg class="transition text-[var(--primary)] text-4xl mx-auto" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
					</a>
				<?php endif; ?>
			</div>
		<?php endwhile; the_posts_pagination( array( 'mid_size' => 1 ) ); else : ?>
			<p class="text-50 text-center py-12"><?php esc_html_e( '没有匹配的结果。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

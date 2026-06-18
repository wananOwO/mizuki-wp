<?php
/**
 * Template Name: 追番
 *
 * 追番页面模板 - 以网格卡片形式展示所有追番条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="anime-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '追番', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$anime_query = new WP_Query( array(
			'post_type'      => 'mizuki_anime',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$status_labels = array(
			'watching'  => '在看',
			'completed' => '看完',
			'planned'   => '想看',
		);

		if ( $anime_query->have_posts() ) :
		?>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
			<?php while ( $anime_query->have_posts() ) : $anime_query->the_post();
				$status   = get_post_meta( get_the_ID(), '_mizuki_anime_status', true );
				$score    = get_post_meta( get_the_ID(), '_mizuki_anime_score', true );
				$url      = get_post_meta( get_the_ID(), '_mizuki_anime_url', true );
				$progress = get_post_meta( get_the_ID(), '_mizuki_anime_progress', true );
			?>
			<div class="card-base overflow-hidden transition hover:ring-2 hover:ring-[var(--primary)] group">
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="relative overflow-hidden aspect-video">
					<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover transition group-hover:scale-105' ) ); ?>
					<?php if ( $status && isset( $status_labels[ $status ] ) ) : ?>
					<span class="absolute top-2 right-2 text-xs px-2 py-1 rounded-lg <?php
						echo $status === 'watching' ? 'bg-green-500/80 text-white' :
							( $status === 'completed' ? 'bg-[var(--primary)]/80 text-white' : 'bg-gray-500/80 text-white' );
					?>">
						<?php echo esc_html( $status_labels[ $status ] ); ?>
					</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="p-4">
					<h3 class="font-bold text-lg text-90 mb-2 transition">
						<?php if ( $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="hover:text-[var(--primary)] transition">
								<?php the_title(); ?>
							</a>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</h3>
					<div class="flex flex-wrap gap-3 text-sm text-50 transition">
						<?php if ( $score ) : ?>
						<span class="flex items-center gap-1">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
							<?php echo esc_html( $score ); ?>/10
						</span>
						<?php endif; ?>
						<?php if ( $progress ) : ?>
						<span><?php echo esc_html( $progress ); ?></span>
						<?php endif; ?>
						<?php if ( ! has_post_thumbnail() && $status && isset( $status_labels[ $status ] ) ) : ?>
						<span class="<?php
							echo $status === 'watching' ? 'text-green-500' :
								( $status === 'completed' ? 'text-[var(--primary)]' : 'text-gray-500' );
						?>">
							<?php echo esc_html( $status_labels[ $status ] ); ?>
						</span>
						<?php endif; ?>
					</div>
					<?php if ( has_excerpt() || get_the_content() ) : ?>
					<div class="mt-2 text-sm text-75 line-clamp-2 transition">
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无追番内容。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

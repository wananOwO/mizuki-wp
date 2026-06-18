<?php
/**
 * Template Name: 说说
 *
 * 说说页面模板 - 以时间线形式展示所有说说条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="diary-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '说说', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$diary_query = new WP_Query( array(
			'post_type'      => 'mizuki_diary',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		if ( $diary_query->have_posts() ) :
		?>
		<div class="relative">
			<?php
			$current_date = '';
			while ( $diary_query->have_posts() ) : $diary_query->the_post();
				$entry_date = get_the_date( 'Y-m-d' );
				$entry_time = get_the_time( 'H:i' );
				$images     = get_post_meta( get_the_ID(), '_mizuki_diary_images', true );

				if ( $entry_date !== $current_date ) {
					if ( $current_date ) {
						echo '</div>'; // close date group
					}
					$current_date = $entry_date;
					$day_count    = $diary_query->found_posts;
				?>
				<!-- 日期行 -->
				<div class="flex flex-row w-full items-center h-[3rem] mb-2">
					<div class="w-[15%] md:w-[10%] transition text-lg font-bold text-right text-75">
						<?php echo esc_html( get_the_date( 'm-d' ) ); ?>
					</div>
					<div class="w-[15%] md:w-[10%]">
						<div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto"></div>
					</div>
					<div class="w-[70%] md:w-[80%] transition text-sm text-50">
						<?php echo esc_html( get_the_date( 'Y年n月j日 l' ) ); ?>
					</div>
				</div>
				<div class="date-group mb-4">
				<?php } ?>

				<!-- 说说条目 -->
				<div class="group btn-plain !block w-full rounded-lg transition mb-3">
					<div class="flex flex-row justify-start items-start">
						<div class="w-[15%] md:w-[10%] transition text-sm text-right text-50 pt-1">
							<?php echo esc_html( $entry_time ); ?>
						</div>
						<div class="w-[15%] md:w-[10%] relative dash-line min-h-[2rem] flex items-start pt-2">
							<div class="transition-all mx-auto w-1 h-1 rounded group-hover:h-5 group-hover:bg-[var(--primary)]"></div>
						</div>
						<div class="w-[70%] md:w-[80%] text-left">
							<div class="card-base p-4 transition">
								<div class="prose dark:prose-invert prose-sm !max-w-none custom-md markdown-content">
									<?php the_content(); ?>
								</div>
								<?php if ( $images ) :
									$img_list = array_filter( array_map( 'trim', explode( "\n", $images ) ) );
									if ( ! empty( $img_list ) ) : ?>
									<div class="flex flex-wrap gap-2 mt-3">
										<?php foreach ( $img_list as $img_url ) : ?>
										<a href="<?php echo esc_url( $img_url ); ?>" class="block w-20 h-20 rounded-lg overflow-hidden" data-fancybox="diary-<?php echo esc_attr( get_the_ID() ); ?>">
											<img src="<?php echo esc_url( $img_url ); ?>" alt="" class="w-full h-full object-cover" loading="lazy">
										</a>
										<?php endforeach; ?>
									</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
			<?php if ( $current_date ) : ?>
			</div><!-- close last date group -->
			<?php endif; ?>
		</div>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无说说内容。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

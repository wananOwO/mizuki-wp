<?php
/**
 * Template Name: 时间线
 *
 * 时间线页面模板 - 以时间线形式展示所有文章(复用归档布局)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="timeline-page onload-animation">
	<div class="card-base px-8 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '时间线', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$timeline_query = new WP_Query( array(
			'post_type'      => 'post',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		if ( $timeline_query->have_posts() ) :
		?>
		<div>
			<?php
			$current_year = '';
			while ( $timeline_query->have_posts() ) : $timeline_query->the_post();
				$year = get_the_date( 'Y' );
				if ( $year !== $current_year ) {
					if ( $current_year ) {
						echo '</div>'; // close year group
					}
					$current_year = $year;
					?>
					<!-- 年份行 -->
					<div class="flex flex-row w-full items-center h-[3.75rem]">
						<div class="w-[15%] md:w-[10%] transition text-2xl font-bold text-right text-75"><?php echo esc_html( $year ); ?></div>
						<div class="w-[15%] md:w-[10%]">
							<div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto"></div>
						</div>
						<div class="w-[70%] md:w-[80%] transition text-left text-50">
							<?php
							$year_count = count( get_posts( array(
								'year'         => (int) $year,
								'fields'       => 'ids',
								'posts_per_page' => -1,
							) ) );
							echo esc_html(
								sprintf(
									/* translators: %d: number of posts */
									_n( '%d 篇文章', '%d 篇文章', $year_count, 'mizuki' ),
									$year_count
								)
							);
							?>
						</div>
					</div>
					<div class="year-group">
					<?php
				}
				?>
				<!-- 文章行 -->
				<a href="<?php the_permalink(); ?>"
				   class="group btn-plain !block h-10 w-full rounded-lg hover:text-[initial] transition">
					<div class="flex flex-row justify-start items-center h-full">
						<div class="w-[15%] md:w-[10%] transition text-sm text-right text-50"><?php echo esc_html( get_the_date( 'm-d' ) ); ?></div>
						<div class="w-[15%] md:w-[10%] relative dash-line h-full flex items-center">
							<div class="transition-all mx-auto w-1 h-1 rounded group-hover:h-5 group-hover:bg-[var(--primary)]"></div>
						</div>
						<div class="w-[70%] md:max-w-[65%] md:w-[65%] text-left font-bold truncate"><?php the_title(); ?></div>
						<div class="hidden md:block md:w-[15%] text-left text-sm transition text-50 truncate">
							<?php
							$tags = get_the_tags();
							if ( $tags ) {
								echo esc_html( $tags[0]->name );
							}
							?>
						</div>
					</div>
				</a>
			<?php endwhile; ?>
			</div><!-- close last year group -->
		</div>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无文章。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

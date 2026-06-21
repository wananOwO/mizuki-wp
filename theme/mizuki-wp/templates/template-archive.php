<?php
/**
 * Template Name: 归档 (Archive)
 *
 * 归档页面 — 文章按年份分组,每条显示日期 + 标题。与 Mizuki dist/archive 一致:
 * 顶部分类筛选条 + card-base 年份分组列表。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

$mz_archive = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);
?>
<?php mizuki_category_bar(); ?>
<div class="card-base px-4 md:px-8 py-6">
	<?php
	if ( $mz_archive->have_posts() ) :
		$current_year = '';
		while ( $mz_archive->have_posts() ) :
			$mz_archive->the_post();
			$year = get_the_date( 'Y' );
			if ( $year !== $current_year ) :
				if ( '' !== $current_year ) :
					echo '</div>'; // close previous year group.
				endif;
				$current_year = $year;
				?>
				<div class="year-group">
					<div class="flex flex-row w-full items-center h-[3.75rem]">
						<div class="w-[15%] md:w-[10%] transition text-2xl font-bold text-right text-75"><?php echo esc_html( $year ); ?></div>
						<div class="w-[15%] md:w-[10%]">
							<div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto -outline-offset-[2px] z-50 outline-3"></div>
						</div>
						<div class="w-[70%] md:w-[80%] transition text-left text-50"></div>
					</div>
			<?php endif; ?>
				<a href="<?php the_permalink(); ?>" class="group btn-plain block h-10 w-full rounded-lg hover:text-[var(--primary)] active:scale-[0.98]">
					<div class="flex flex-row justify-start items-center h-full">
						<div class="w-[15%] md:w-[10%] transition text-sm text-right text-50"><?php echo esc_html( get_the_date( 'm-d' ) ); ?></div>
						<div class="w-[15%] md:w-[10%] relative dash-line h-full flex items-center">
							<div class="transition-all mx-auto w-1 h-1 rounded group-hover:h-5 bg-[oklch(0.5_0.05_var(--hue))] group-hover:bg-[var(--primary)] outline outline-4 z-50 outline-[var(--card-bg)] group-hover:outline-[var(--btn-plain-bg-hover)]"></div>
						</div>
						<div class="w-[70%] md:max-w-[65%] md:w-[65%] text-left font-bold group-hover:translate-x-1 transition-all text-75 group-hover:text-[var(--primary)] text-lg truncate"><?php the_title(); ?></div>
					</div>
				</a>
			<?php
		endwhile;
		if ( '' !== $current_year ) :
			echo '</div>'; // close last year group.
		endif;
		wp_reset_postdata();
	else :
		?>
		<p class="text-50 text-center py-8"><?php esc_html_e( '暂无文章。', 'mizuki' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();

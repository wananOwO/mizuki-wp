<?php
/**
 * 归档(分类/标签/日期)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="archive-page onload-animation">
	<!-- 分类筛选条 -->
	<div id="category-bar" class="card-base category-bar p-3 onload-animation mb-4">
		<div class="category-bar-inner flex gap-2 overflow-x-auto">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
			   class="category-pill text-sm px-2 py-1 rounded-lg transition <?php echo is_home() ? 'bg-[var(--primary)] text-white' : 'text-50 hover:bg-[var(--btn-plain-bg-hover)]'; ?>">
				<?php esc_html_e( '首页', 'mizuki' ); ?>
			</a>
			<?php
			// "归档"链接: 优先指向"文章页"(page_for_posts),否则回退到站点首页。
			$posts_page_id   = (int) get_option( 'page_for_posts' );
			$archive_pill_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
			?>
			<a href="<?php echo esc_url( $archive_pill_url ); ?>"
			   class="category-pill text-sm px-2 py-1 rounded-lg transition <?php echo ( is_archive() && ! is_category() && ! is_tag() && ! is_date() ) || is_home() ? 'bg-[var(--primary)] text-white' : 'text-50 hover:bg-[var(--btn-plain-bg-hover)]'; ?>">
				<?php esc_html_e( '归档', 'mizuki' ); ?>
			</a>
			<?php
			$categories = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10 ) );
			foreach ( $categories as $cat ) {
				$active = is_category( $cat->term_id );
				echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" ';
				echo 'class="category-pill text-sm px-2 py-1 rounded-lg transition ';
				echo $active ? 'bg-[var(--primary)] text-white' : 'text-50 hover:bg-[var(--btn-plain-bg-hover)]';
				echo '">' . esc_html( $cat->name ) . ' <span class="text-xs opacity-60 ml-1">' . esc_html( $cat->count ) . '</span></a>';
			}
			?>
		</div>
	</div>

	<!-- 时间线 -->
	<div class="card-base px-8 py-6">
		<?php if ( have_posts() ) : ?>
			<?php
			// 高效计算每年文章数: 使用当前查询的年份分组
			// 而非查询所有文章(避免 N+1 和内存问题)
			global $wp_query;
			$year_counts = array();
			// 从当前查询的所有文章中统计
			$all_posts = $wp_query->posts;
			foreach ( $all_posts as $p ) {
				$y = get_the_date( 'Y', $p->ID );
				if ( ! isset( $year_counts[ $y ] ) ) {
					$year_counts[ $y ] = 0;
				}
				$year_counts[ $y ]++;
			}

			$current_year = '';
			while ( have_posts() ) : the_post();
				$year = get_the_date( 'Y' );
				if ( $year !== $current_year ) {
					if ( $current_year ) {
						echo '</div>'; // close year group
					}
					$current_year = $year;
					$year_count   = isset( $year_counts[ $year ] ) ? $year_counts[ $year ] : 0;
					?>
					<!-- 年份行 -->
					<div class="flex flex-row w-full items-center h-[3.75rem]">
						<div class="w-[15%] md:w-[10%] transition text-2xl font-bold text-right text-75"><?php echo esc_html( $year ); ?></div>
						<div class="w-[15%] md:w-[10%]">
							<div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto"></div>
						</div>
						<div class="w-[70%] md:w-[80%] transition text-left text-50">
							<?php
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
						<div class="w-[15%] md:w-[10%] transition text-sm text-right text-50"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></div>
						<div class="w-[15%] md:w-[10%] relative dash-line h-full flex items-center">
							<div class="transition-all mx-auto w-1 h-1 rounded group-hover:h-5 group-hover:bg-[var(--primary)]"></div>
						</div>
						<div class="w-[70%] md:max-w-[65%] md:w-[65%] text-left font-bold truncate"><?php the_title(); ?></div>
						<div class="hidden md:block md:w-[15%] text-left text-sm transition text-50 truncate">
							<?php
							$tags = get_the_tags();
							if ( $tags ) {
								echo '# ' . esc_html( $tags[0]->name );
							}
							?>
						</div>
					</div>
				</a>
			<?php endwhile; ?>
			</div><!-- close last year group -->
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p class="text-50 text-center py-12"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

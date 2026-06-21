<?php
/**
 * Template Name: 时间线
 *
 * 时间线页面模板 - 以垂直时间线形式展示所有文章(按年份分组)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="timeline-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
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
			// 收集所有标签
			$all_tags = array();
			foreach ( $timeline_query->posts as $timeline_post ) {
				$post_tags = get_the_tags( $timeline_post->ID );
				if ( $post_tags ) {
					foreach ( $post_tags as $tag ) {
						if ( ! isset( $all_tags[ $tag->term_id ] ) ) {
							$all_tags[ $tag->term_id ] = array(
								'name'  => $tag->name,
								'slug'  => $tag->slug,
								'count' => 0,
							);
						}
						$all_tags[ $tag->term_id ]['count']++;
					}
				}
			}
			// 按名称排序
			uasort( $all_tags, function( $a, $b ) {
				return strcmp( $a['name'], $b['name'] );
			} );
		?>
		<!-- 标签过滤 -->
		<?php if ( ! empty( $all_tags ) ) : ?>
		<div class="filter-tabs flex flex-wrap gap-2 mb-6">
			<button class="filter-tabs-item active" data-filter-attr="timeline-tags" data-filter-value="all">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo count( $timeline_query->posts ); ?>)</span>
			</button>
			<?php foreach ( $all_tags as $tag ) : ?>
			<button class="filter-tabs-item" data-filter-attr="timeline-tags" data-filter-value="<?php echo esc_attr( $tag['slug'] ); ?>">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
				</svg>
				<span><?php echo esc_html( $tag['name'] ); ?></span>
				<span class="filter-tabs-count">(<?php echo $tag['count']; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div id="timeline-wrapper" class="timeline-wrapper">
		<?php
			// 统计每年文章数。
			$year_counts = array();
			foreach ( $timeline_query->posts as $p ) {
				$y = get_the_date( 'Y', $p->ID );
				if ( ! isset( $year_counts[ $y ] ) ) {
					$year_counts[ $y ] = 0;
				}
				$year_counts[ $y ]++;
			}
		?>
		<div class="timeline-wrapper">
			<?php
			$current_year = '';
			while ( $timeline_query->have_posts() ) : $timeline_query->the_post();
				$year = get_the_date( 'Y' );
				if ( $year !== $current_year ) {
					if ( '' !== $current_year ) {
						echo '</div>'; // close .timeline-list
					}
					$current_year = $year;
					$year_count   = isset( $year_counts[ $year ] ) ? $year_counts[ $year ] : 0;
					?>
					<!-- 年份标题 -->
					<div class="flex items-center gap-3 mb-4 mt-8 first:mt-0">
						<span class="text-2xl font-bold text-90"><?php echo esc_html( $year ); ?></span>
						<span class="px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %d: number of posts */
									_n( '%d 篇文章', '%d 篇文章', $year_count, 'mizuki' ),
									$year_count
								)
							);
							?>
						</span>
					</div>
					<div class="timeline-list relative">
					<?php
				}

				$cats = get_the_category();
				$tags = get_the_tags();
				$tag_slugs = $tags ? implode( ',', wp_list_pluck( $tags, 'slug' ) ) : '';
			?>
				<div class="timeline-entry" data-timeline-tags="<?php echo esc_attr( $tag_slugs ); ?>">
					<div class="timeline-node" style="background-color: var(--primary);">
						<svg class="w-2.5 h-2.5" fill="white" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 2 4 4h-4V4zM8 13h8v2H8v-2zm0 4h5v2H8v-2z"></path></svg>
					</div>
					<a href="<?php the_permalink(); ?>" class="timeline-card group relative block rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-lg">
						<div class="p-5">
							<div class="flex items-start justify-between gap-3 mb-1">
								<h3 class="text-lg font-bold text-black/90 dark:text-white/90 group-hover:text-[var(--primary)] transition-colors duration-200">
									<?php the_title(); ?>
								</h3>
								<?php if ( $cats ) : ?>
								<span class="shrink-0 px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
									<?php echo esc_html( $cats[0]->name ); ?>
								</span>
								<?php endif; ?>
							</div>

							<?php $excerpt = get_the_excerpt(); ?>
							<?php if ( $excerpt ) : ?>
							<p class="text-sm text-black/70 dark:text-white/70 mb-4 leading-relaxed line-clamp-2">
								<?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?>
							</p>
							<?php endif; ?>

							<?php if ( $tags ) : ?>
							<div class="flex flex-wrap gap-2 mb-4">
								<?php foreach ( array_slice( $tags, 0, 4 ) as $tag ) : ?>
								<span class="px-2 py-1 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">#<?php echo esc_html( $tag->name ); ?></span>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>

							<div class="flex items-center gap-4 text-xs text-black/50 dark:text-white/50 pt-3 border-t border-black/5 dark:border-white/5">
								<span class="flex items-center gap-1">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
									<?php echo esc_html( get_the_date() ); ?>
								</span>
							</div>
						</div>
						<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-xl"></div>
					</a>
				</div>
			<?php endwhile; ?>
			</div><!-- close last .timeline-list -->
		</div>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-12">
			<p class="text-50"><?php esc_html_e( '没有找到匹配的文章。', 'mizuki' ); ?></p>
		</div>

		<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p class="text-50 text-center py-12"><?php esc_html_e( '暂无文章。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>

	<style>
		.timeline-page .timeline-entry {
			position: relative;
			padding-left: 2.5rem;
			padding-bottom: 1.5rem;
		}
		.timeline-page .timeline-list::before {
			content: "";
			position: absolute;
			left: 0.625rem;
			top: 0.5rem;
			bottom: 0.5rem;
			width: 2px;
			background-color: var(--line-divider);
		}
		.timeline-page .timeline-node {
			position: absolute;
			left: 0;
			top: 1.5rem;
			width: 1.25rem;
			height: 1.25rem;
			border-radius: 50%;
			border: 2.5px solid var(--card-bg, #fff);
			box-shadow: 0 0 0 2px var(--line-divider);
			display: flex;
			align-items: center;
			justify-content: center;
			z-index: 2;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}
		.timeline-page .timeline-entry:hover .timeline-node {
			transform: scale(1.15);
			box-shadow: 0 0 0 2px var(--primary);
		}
	</style>
</main>
<?php
get_footer();

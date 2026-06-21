<?php
/**
 * Template Name: 项目
 *
 * 项目页面模板 - 以网格卡片形式展示所有项目条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="projects-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '项目', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$project_query = new WP_Query( array(
			'post_type'              => 'mizuki_project',
			'posts_per_page'         => 200,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		) );

		$status_labels = array(
			'active'    => __( '进行中', 'mizuki' ),
			'completed' => __( '已完成', 'mizuki' ),
			'paused'    => __( '暂停', 'mizuki' ),
		);

		if ( $project_query->have_posts() ) :
			// 收集所有标签
			$all_tags = array();
			foreach ( $project_query->posts as $project_post ) {
				$post_tags = get_the_tags( $project_post->ID );
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
			<button class="filter-tabs-item active" data-filter-attr="project-tags" data-filter-value="all">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo count( $project_query->posts ); ?>)</span>
			</button>
			<?php foreach ( $all_tags as $tag ) : ?>
			<button class="filter-tabs-item" data-filter-attr="project-tags" data-filter-value="<?php echo esc_attr( $tag['slug'] ); ?>">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
				</svg>
				<span><?php echo esc_html( $tag['name'] ); ?></span>
				<span class="filter-tabs-count">(<?php echo $tag['count']; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div id="projects-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
		<?php
		endif;

		if ( $project_query->have_posts() ) :
		?>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<?php while ( $project_query->have_posts() ) : $project_query->the_post();
				$url    = get_post_meta( get_the_ID(), '_mizuki_project_url', true );
				$source = get_post_meta( get_the_ID(), '_mizuki_project_source', true );
				$desc   = get_post_meta( get_the_ID(), '_mizuki_project_desc', true );
				$status = get_post_meta( get_the_ID(), '_mizuki_project_status', true );
				$tech   = get_post_meta( get_the_ID(), '_mizuki_project_tech', true );
				$tech_list = $tech ? array_filter( array_map( 'trim', explode( ',', $tech ) ) ) : array();
				if ( ! $desc ) {
					$desc = wp_strip_all_tags( get_the_excerpt() );
				}
				$project_tags = get_the_tags();
				$tag_slugs    = $project_tags ? implode( ',', wp_list_pluck( $project_tags, 'slug' ) ) : '';
			?>
			<div class="project-card group relative rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-project-tags="<?php echo esc_attr( $tag_slugs ); ?>">
				<div class="aspect-video overflow-hidden relative bg-gradient-to-br from-[var(--primary)]/5 to-[var(--primary)]/10">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500', 'loading' => 'lazy' ) ); ?>
					<?php else : ?>
						<div class="w-full h-full flex items-center justify-center">
							<span class="text-4xl font-bold text-[var(--primary)]/15 select-none tracking-wide px-4 text-center"><?php the_title(); ?></span>
						</div>
					<?php endif; ?>
					<div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
				</div>

				<div class="p-5">
					<div class="flex items-center justify-between mb-3">
						<h3 class="text-lg font-bold text-black/90 dark:text-white/90 truncate group-hover:text-[var(--primary)] transition-colors duration-200">
							<?php the_title(); ?>
						</h3>
						<?php if ( $status && isset( $status_labels[ $status ] ) ) : ?>
						<span class="shrink-0 ml-3 px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
							<?php echo esc_html( $status_labels[ $status ] ); ?>
						</span>
						<?php endif; ?>
					</div>

					<?php if ( $desc ) : ?>
					<p class="text-sm text-black/60 dark:text-white/60 mb-4 line-clamp-2 min-h-[2.5rem]">
						<?php echo esc_html( $desc ); ?>
					</p>
					<?php endif; ?>

					<?php if ( ! empty( $tech_list ) ) : ?>
					<div class="flex flex-wrap gap-2 mb-4">
						<?php foreach ( array_slice( $tech_list, 0, 4 ) as $t ) : ?>
						<span class="px-2 py-1 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium"><?php echo esc_html( $t ); ?></span>
						<?php endforeach; ?>
						<?php if ( count( $tech_list ) > 4 ) : ?>
						<span class="px-2 py-1 text-xs rounded-md bg-[var(--btn-regular-bg)] text-black/50 dark:text-white/50 font-medium">+<?php echo esc_html( count( $tech_list ) - 4 ); ?></span>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<?php if ( $url || $source ) : ?>
					<div class="flex gap-2">
						<?php if ( $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"
						   class="btn-regular flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
							<?php esc_html_e( '访问', 'mizuki' ); ?>
						</a>
						<?php endif; ?>
						<?php if ( $source ) : ?>
						<a href="<?php echo esc_url( $source ); ?>" target="_blank" rel="noopener noreferrer"
						   class="btn-regular flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium" <?php echo $url ? '' : 'style="flex: 1;"'; ?>>
							<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49 0-.24-.01-.88-.01-1.73-2.78.62-3.37-1.37-3.37-1.37-.45-1.18-1.11-1.49-1.11-1.49-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.89 1.56 2.34 1.11 2.91.85.09-.66.35-1.11.63-1.37-2.22-.26-4.55-1.14-4.55-5.07 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05A9.36 9.36 0 0 1 12 6.84c.85 0 1.71.12 2.51.34 1.91-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.63 1.03 2.75 0 3.94-2.34 4.81-4.57 5.06.36.32.68.94.68 1.9 0 1.37-.01 2.48-.01 2.82 0 .27.18.6.69.49A10.02 10.02 0 0 0 22 12.25C22 6.58 17.52 2 12 2z"></path></svg>
							<?php echo $url ? '' : esc_html__( '源码', 'mizuki' ); ?>
						</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-12">
			<p class="text-50"><?php esc_html_e( '没有找到匹配的项目。', 'mizuki' ); ?></p>
		</div>

		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无项目。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

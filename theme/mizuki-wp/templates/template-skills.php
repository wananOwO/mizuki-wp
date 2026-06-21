<?php
/**
 * Template Name: 技能
 *
 * 技能页面模板 - 以网格卡片形式展示所有技能条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

/**
 * 根据熟练度数值返回等级文案。
 *
 * @param int $level 0-100 的熟练度。
 * @return string
 */
function mizuki_skill_level_text( $level ) {
	$level = (int) $level;
	if ( $level >= 90 ) {
		return __( '专家', 'mizuki' );
	}
	if ( $level >= 70 ) {
		return __( '熟练', 'mizuki' );
	}
	if ( $level >= 40 ) {
		return __( '中等', 'mizuki' );
	}
	return __( '入门', 'mizuki' );
}
?>
<main id="main" class="skills-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '技能', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$skill_query = new WP_Query( array(
			'post_type'              => 'mizuki_skill',
			'posts_per_page'         => 200,
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		) );

		if ( $skill_query->have_posts() ) :
			// 收集所有标签
			$all_tags = array();
			foreach ( $skill_query->posts as $skill_post ) {
				$post_tags = get_the_tags( $skill_post->ID );
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
			<button class="filter-tabs-item active" data-filter-attr="skill-tags" data-filter-value="all">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo count( $skill_query->posts ); ?>)</span>
			</button>
			<?php foreach ( $all_tags as $tag ) : ?>
			<button class="filter-tabs-item" data-filter-attr="skill-tags" data-filter-value="<?php echo esc_attr( $tag['slug'] ); ?>">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
				</svg>
				<span><?php echo esc_html( $tag['name'] ); ?></span>
				<span class="filter-tabs-count">(<?php echo $tag['count']; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div id="skills-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
		<?php
		endif;

		if ( $skill_query->have_posts() ) :
		?>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php while ( $skill_query->have_posts() ) : $skill_query->the_post();
				$level = (int) get_post_meta( get_the_ID(), '_mizuki_skill_level', true );
				$level = max( 0, min( 100, $level ) );
				$icon  = get_post_meta( get_the_ID(), '_mizuki_skill_icon', true );
				$desc  = wp_strip_all_tags( get_the_excerpt() );
				$skill_tags = get_the_tags();
				$tag_slugs  = $skill_tags ? implode( ',', wp_list_pluck( $skill_tags, 'slug' ) ) : '';
			?>
			<div class="skill-card group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-skill-tags="<?php echo esc_attr( $tag_slugs ); ?>">
				<div class="p-5">
					<div class="flex items-start gap-4 mb-3">
						<div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center bg-[var(--primary)]/10">
							<?php if ( $icon ) : ?>
								<i class="<?php echo esc_attr( $icon ); ?> text-xl text-[var(--primary)]"></i>
							<?php else : ?>
								<span class="text-xl font-bold text-[var(--primary)]"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
							<?php endif; ?>
						</div>

						<div class="flex-1 min-w-0">
							<div class="flex items-center justify-between mb-1">
								<h3 class="text-lg font-bold text-black/90 dark:text-white/90 truncate group-hover:text-[var(--primary)] transition-colors duration-200">
									<?php the_title(); ?>
								</h3>
								<span class="shrink-0 ml-2 px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
									<?php echo esc_html( mizuki_skill_level_text( $level ) ); ?>
								</span>
							</div>
							<p class="text-xs text-black/50 dark:text-white/50"><?php echo esc_html( $level ); ?>%</p>
						</div>
					</div>

					<?php if ( $desc ) : ?>
					<p class="text-sm text-black/60 dark:text-white/60 line-clamp-2 min-h-[2.5rem]">
						<?php echo esc_html( $desc ); ?>
					</p>
					<?php endif; ?>

					<div class="mt-3 w-full bg-[var(--btn-regular-bg)] rounded-full h-1.5">
						<div class="h-1.5 rounded-full transition-all duration-500 bg-[var(--primary)]" style="width: <?php echo esc_attr( $level ); ?>%"></div>
					</div>
				</div>

				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-12">
			<p class="text-50"><?php esc_html_e( '没有找到匹配的技能。', 'mizuki' ); ?></p>
		</div>

		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无技能。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

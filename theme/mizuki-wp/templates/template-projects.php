<?php
/**
 * Template Name: 项目
 *
 * 项目页面模板 — 完全同步 Mizuki 原项目 projects.astro。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

$project_categories_cache_key = 'mizuki_terms_project_category';
$project_categories = get_transient( $project_categories_cache_key );
if ( false === $project_categories ) {
	$project_categories = get_terms( array(
		'taxonomy'   => 'project_category',
		'hide_empty' => true,
		'orderby'    => 'name',
	) );
	if ( ! is_wp_error( $project_categories ) ) {
		set_transient( $project_categories_cache_key, $project_categories, 12 * HOUR_IN_SECONDS );
	}
}

// 显示名 + 图标映射:theme_mod 覆盖叠加在默认值之上(控制台「项目分类管理」可编辑)。
$category_names = mizuki_get_category_labels( 'project_category' );
$category_icons = mizuki_get_category_icons( 'project_category' );

$status_names = array(
	'active'    => '进行中',
	'completed' => '已完成',
	'paused'    => '暂停',
);

$project_query = new WP_Query( array(
	'post_type'              => 'mizuki_project',
	'posts_per_page'         => 200,
	'orderby'                => 'date',
	'order'                  => 'DESC',
	'no_found_rows'          => true,
	'update_post_term_cache' => true,
	'update_post_meta_cache' => true,
) );

$total_count = $project_query->post_count;
?>


<main id="main" class="projects-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-2 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '项目', 'mizuki' ); ?>
		</h1>
		<p class="text-black/50 dark:text-white/50 mb-6"><?php esc_html_e( '我的项目作品', 'mizuki' ); ?></p>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-8"></div>

		<?php if ( ! is_wp_error( $project_categories ) && ! empty( $project_categories ) ) : ?>
		<div class="filter-tabs mb-8">
			<button class="filter-tabs-item active" data-filter-value="all" data-filter-attr="category">
				<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $total_count; ?>)</span>
			</button>
			<?php foreach ( $project_categories as $cat ) : ?>
			<button class="filter-tabs-item" data-filter-value="<?php echo esc_attr( $cat->slug ); ?>" data-filter-attr="category">
				<?php echo isset( $category_icons[ $cat->slug ] ) ? $category_icons[ $cat->slug ] : $category_icons['other']; ?>
				<span><?php echo esc_html( isset( $category_names[ $cat->slug ] ) ? $category_names[ $cat->slug ] : $cat->name ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $cat->count; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( $project_query->have_posts() ) : ?>
		<div id="projects-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
			<?php while ( $project_query->have_posts() ) : $project_query->the_post();
				$url    = get_post_meta( get_the_ID(), '_mizuki_project_url', true );
				$source = get_post_meta( get_the_ID(), '_mizuki_project_source', true );
				$desc   = get_post_meta( get_the_ID(), '_mizuki_project_desc', true );
				$status = get_post_meta( get_the_ID(), '_mizuki_project_status', true );
				$tech   = get_post_meta( get_the_ID(), '_mizuki_project_tech', true );
				$proj_cats = get_the_terms( get_the_ID(), 'project_category' );
				if ( $proj_cats && ! is_wp_error( $proj_cats ) && ! empty( $proj_cats ) ) {
					$cat_slug = implode( ',', wp_list_pluck( $proj_cats, 'slug' ) );
				} else {
					$cat_slug = '__none__';
				}
				$has_cover = has_post_thumbnail();
			?>
			<div class="project-card mizuki-stagger group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-category="<?php echo esc_attr( $cat_slug ); ?>">
				<?php if ( $has_cover ) : ?>
				<div class="aspect-video overflow-hidden">
					<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ) ); ?>
				</div>
				<?php endif; ?>
				<div class="p-5">
					<div class="flex items-center justify-between mb-2">
						<h3 class="text-lg font-bold text-black/90 dark:text-white/90 group-hover:text-[var(--primary)] transition-colors duration-200">
							<?php the_title(); ?>
						</h3>
						<?php if ( $status && isset( $status_names[ $status ] ) ) : ?>
						<span class="shrink-0 ml-2 px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
							<?php echo esc_html( $status_names[ $status ] ); ?>
						</span>
						<?php endif; ?>
					</div>
					<?php if ( $desc ) : ?>
					<p class="text-sm text-black/60 dark:text-white/60 mb-3 line-clamp-2">
						<?php echo esc_html( $desc ); ?>
					</p>
					<?php endif; ?>
					<?php if ( $tech ) : ?>
					<div class="flex flex-wrap gap-1.5 mb-3">
						<?php foreach ( array_map( 'trim', explode( ',', $tech ) ) as $t ) : ?>
						<span class="px-2 py-0.5 text-xs rounded-md bg-black/5 dark:bg-white/10 text-black/60 dark:text-white/60"><?php echo esc_html( $t ); ?></span>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<div class="flex gap-2">
						<?php if ( $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="text-sm text-[var(--primary)] hover:underline"><?php esc_html_e( '访问', 'mizuki' ); ?></a>
						<?php endif; ?>
						<?php if ( $source ) : ?>
						<a href="<?php echo esc_url( $source ); ?>" target="_blank" rel="noopener" class="text-sm text-[var(--primary)] hover:underline"><?php esc_html_e( '源码', 'mizuki' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<?php mizuki_empty_state( __( '暂无项目。', 'mizuki' ) ); ?>
		<?php endif; ?>

		<div id="no-results" class="hidden text-center py-16">
			<p class="text-black/40 dark:text-white/40 text-lg"><?php esc_html_e( '没有找到匹配的项目。', 'mizuki' ); ?></p>
		</div>
	</div>
</main>

<style>
	.line-clamp-2 {
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>

<?php get_footer(); ?>

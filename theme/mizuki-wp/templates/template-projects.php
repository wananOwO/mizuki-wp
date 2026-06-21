<?php
/**
 * Template Name: 项目
 *
 * 项目页面模板 — 完全同步 Mizuki 原项目 projects.astro。
 * 使用 project_category taxonomy + filter-tabs-handler.js 筛选。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

$project_categories = get_terms( array(
	'taxonomy'   => 'project_category',
	'hide_empty' => true,
	'orderby'    => 'name',
) );

$category_names = array(
	'web'     => 'Web',
	'mobile'  => '移动端',
	'desktop' => '桌面端',
	'other'   => '其他',
);

$category_icons = array(
	'web'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
	'mobile'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>',
	'desktop' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/></svg>',
	'other'   => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>',
);

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
) );

$total_count = $project_query->post_count;
?>

<link rel="stylesheet" href="<?php echo esc_url( MIZUKI_URI . '/assets/css/mizuki-filter-tabs.css' ); ?>">
<script is:inline src="<?php echo esc_url( MIZUKI_URI . '/assets/js/filter-tabs-handler.js' ); ?>"></script>

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
				$cat_slug = ( $proj_cats && ! is_wp_error( $proj_cats ) ) ? $proj_cats[0]->slug : 'other';
				$has_cover = has_post_thumbnail();
			?>
			<div class="project-card group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-category="<?php echo esc_attr( $cat_slug ); ?>">
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
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无项目。', 'mizuki' ); ?></p>
		<?php endif; ?>

		<div id="no-results" class="hidden text-center py-16">
			<p class="text-black/40 dark:text-white/40 text-lg"><?php esc_html_e( '没有找到匹配的项目。', 'mizuki' ); ?></p>
		</div>
	</div>
</main>

<style>
	.project-card {
		animation: fadeInUp 0.5s ease-out forwards;
		opacity: 0;
	}
	@keyframes fadeInUp {
		from { opacity: 0; transform: translateY(20px); }
		to { opacity: 1; transform: translateY(0); }
	}
	.project-card:nth-child(1) { animation-delay: 0.05s; }
	.project-card:nth-child(2) { animation-delay: 0.10s; }
	.project-card:nth-child(3) { animation-delay: 0.15s; }
	.project-card:nth-child(4) { animation-delay: 0.20s; }
	.line-clamp-2 {
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>

<?php get_footer(); ?>

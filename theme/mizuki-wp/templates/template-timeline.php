<?php
/**
 * Template Name: 时间线
 *
 * 时间线页面模板 — 完全同步 Mizuki 原项目 timeline.astro。
 * 使用 timeline_type taxonomy + filter-tabs-handler.js 筛选。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

// 获取所有时间线类型 (同步原项目 timeline.astro)
$timeline_types = get_terms( array(
	'taxonomy'   => 'timeline_type',
	'hide_empty' => true,
	'orderby'    => 'name',
) );

// 类型显示名称映射 (同步原项目)
$type_names = array(
	'education'   => '教育',
	'work'        => '工作',
	'project'     => '项目',
	'achievement' => '成就',
);

$type_icons = array(
	'education'   => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>',
	'work'        => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>',
	'project'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>',
	'achievement' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>',
);

$timeline_query = new WP_Query( array(
	'post_type'              => 'post',
	'posts_per_page'         => 500,
	'orderby'                => 'date',
	'order'                  => 'DESC',
	'no_found_rows'          => true,
	'update_post_term_cache' => true,
) );

$total_count = $timeline_query->post_count;
?>

<link rel="stylesheet" href="<?php echo esc_url( MIZUKI_URI . '/assets/css/mizuki-filter-tabs.css' ); ?>">
<script is:inline src="<?php echo esc_url( MIZUKI_URI . '/assets/js/filter-tabs-handler.js' ); ?>"></script>

<main id="main" class="timeline-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-2 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '时间线', 'mizuki' ); ?>
		</h1>
		<p class="text-black/50 dark:text-white/50 mb-6"><?php esc_html_e( '我的历程', 'mizuki' ); ?></p>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-8"></div>

		<!-- FilterTabs 筛选栏 -->
		<?php if ( ! is_wp_error( $timeline_types ) && ! empty( $timeline_types ) ) : ?>
		<div class="filter-tabs mb-8">
			<button class="filter-tabs-item active" data-filter-value="all" data-filter-attr="type">
				<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $total_count; ?>)</span>
			</button>
			<?php foreach ( $timeline_types as $type ) : ?>
			<button class="filter-tabs-item" data-filter-value="<?php echo esc_attr( $type->slug ); ?>" data-filter-attr="type">
				<?php echo isset( $type_icons[ $type->slug ] ) ? $type_icons[ $type->slug ] : $type_icons['achievement']; ?>
				<span><?php echo esc_html( isset( $type_names[ $type->slug ] ) ? $type_names[ $type->slug ] : $type->name ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $type->count; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<!-- 时间线列表 -->
		<?php if ( $timeline_query->have_posts() ) :
			$current_year = '';
		?>
		<div id="timeline-list" class="timeline-list relative">
			<?php while ( $timeline_query->have_posts() ) : $timeline_query->the_post();
				$year = get_the_date( 'Y' );
				$type_terms = get_the_terms( get_the_ID(), 'timeline_type' );
				$type_slug = ( $type_terms && ! is_wp_error( $type_terms ) ) ? $type_terms[0]->slug : 'achievement';
			?>
			<?php if ( $year !== $current_year ) :
				if ( $current_year ) echo '</div>'; // close year group
				$current_year = $year;
			?>
			<!-- 年份行 -->
			<div class="flex flex-row w-full items-center h-[3.75rem] year-header">
				<div class="w-[15%] md:w-[10%] transition text-2xl font-bold text-right text-75"><?php echo esc_html( $year ); ?></div>
				<div class="w-[15%] md:w-[10%]">
					<div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto"></div>
				</div>
				<div class="w-[70%] md:w-[80%]"></div>
			</div>
			<div class="year-group">
			<?php endif; ?>
			<!-- 文章行 -->
			<a href="<?php the_permalink(); ?>"
			   class="timeline-item group btn-plain !block w-full rounded-lg hover:text-[initial] transition"
			   data-type="<?php echo esc_attr( $type_slug ); ?>">
				<div class="flex flex-row justify-start items-center h-10">
					<div class="w-[15%] md:w-[10%] transition text-sm text-right text-50"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></div>
					<div class="w-[15%] md:w-[10%] relative h-full flex items-center">
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
		</div>
		<?php wp_reset_postdata(); ?>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
		<?php endif; ?>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-16">
			<p class="text-black/40 dark:text-white/40 text-lg"><?php esc_html_e( '没有找到匹配的内容。', 'mizuki' ); ?></p>
		</div>
	</div>
</main>

<style>
	.timeline-list::before {
		content: "";
		position: absolute;
		left: 9px;
		top: 0;
		bottom: 0;
		width: 2px;
		background: var(--line-divider);
		border-radius: 1px;
	}
	.timeline-item {
		animation: fadeInUp 0.5s ease-out forwards;
		opacity: 0;
	}
	@keyframes fadeInUp {
		from { opacity: 0; transform: translateY(20px); }
		to { opacity: 1; transform: translateY(0); }
	}
	.timeline-item:nth-child(1) { animation-delay: 0.03s; }
	.timeline-item:nth-child(2) { animation-delay: 0.06s; }
	.timeline-item:nth-child(3) { animation-delay: 0.09s; }
	.timeline-item:nth-child(4) { animation-delay: 0.12s; }
	.timeline-item:nth-child(5) { animation-delay: 0.15s; }
</style>

<?php get_footer(); ?>

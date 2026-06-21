<?php
/**
 * Template Name: 技能
 *
 * 技能页面模板 — 完全同步 Mizuki 原项目 skills.astro。
 * 使用 skill_category taxonomy + filter-tabs-handler.js 筛选。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

// 获取所有技能分类
$skill_categories = get_terms( array(
	'taxonomy'   => 'skill_category',
	'hide_empty' => true,
	'orderby'    => 'name',
) );

// 分类显示名称映射 (同步原项目)
$category_names = array(
	'frontend'  => '前端',
	'backend'   => '后端',
	'database'  => '数据库',
	'tools'     => '工具',
	'other'     => '其他',
);

$category_icons = array(
	'frontend'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
	'backend'   => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zM7 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>',
	'database'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z"/></svg>',
	'tools'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>',
	'other'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>',
);

// 等级文本映射 (同步原项目 SkillCard)
function mizuki_skill_level_text( $level ) {
	$level = (int) $level;
	if ( $level >= 90 ) return '专家';
	if ( $level >= 70 ) return '熟练';
	if ( $level >= 40 ) return '中等';
	return '入门';
}

function mizuki_skill_level_width( $level ) {
	$level = (int) $level;
	if ( $level >= 90 ) return '100%';
	if ( $level >= 70 ) return '80%';
	if ( $level >= 40 ) return '60%';
	return '40%';
}

$skill_query = new WP_Query( array(
	'post_type'              => 'mizuki_skill',
	'posts_per_page'         => 200,
	'orderby'                => 'date',
	'order'                  => 'ASC',
	'no_found_rows'          => true,
	'update_post_term_cache' => true,
) );

$total_count = $skill_query->post_count;
?>

<!-- 加载 filter-tabs CSS 和 JS -->
<link rel="stylesheet" href="<?php echo esc_url( MIZUKI_URI . '/assets/css/mizuki-filter-tabs.css' ); ?>">
<script is:inline src="<?php echo esc_url( MIZUKI_URI . '/assets/js/filter-tabs-handler.js' ); ?>"></script>

<main id="main" class="skills-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-2 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '技能', 'mizuki' ); ?>
		</h1>
		<p class="text-black/50 dark:text-white/50 mb-6"><?php esc_html_e( '我的技能栈', 'mizuki' ); ?></p>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-8"></div>

		<!-- FilterTabs 筛选栏 (同步原项目 FilterTabs.astro) -->
		<?php if ( ! is_wp_error( $skill_categories ) && ! empty( $skill_categories ) ) : ?>
		<div class="filter-tabs mb-8">
			<button class="filter-tabs-item active" data-filter-value="all" data-filter-attr="category">
				<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $total_count; ?>)</span>
			</button>
			<?php foreach ( $skill_categories as $cat ) : ?>
			<button class="filter-tabs-item" data-filter-value="<?php echo esc_attr( $cat->slug ); ?>" data-filter-attr="category">
				<?php echo isset( $category_icons[ $cat->slug ] ) ? $category_icons[ $cat->slug ] : $category_icons['other']; ?>
				<span><?php echo esc_html( isset( $category_names[ $cat->slug ] ) ? $category_names[ $cat->slug ] : $cat->name ); ?></span>
				<span class="filter-tabs-count">(<?php echo (int) $cat->count; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<!-- 技能卡片网格 (同步原项目 SkillCard.astro) -->
		<?php if ( $skill_query->have_posts() ) : ?>
		<div id="skills-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
			<?php while ( $skill_query->have_posts() ) : $skill_query->the_post();
				$level = (int) get_post_meta( get_the_ID(), '_mizuki_skill_level', true );
				$level = max( 0, min( 100, $level ) );
				$icon  = get_post_meta( get_the_ID(), '_mizuki_skill_icon', true );
				$desc  = wp_strip_all_tags( get_the_excerpt() );
				$skill_cats = get_the_terms( get_the_ID(), 'skill_category' );
				$cat_slug = ( $skill_cats && ! is_wp_error( $skill_cats ) ) ? $skill_cats[0]->slug : 'other';
			?>
			<div class="skill-card group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-category="<?php echo esc_attr( $cat_slug ); ?>">
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
						<div class="h-1.5 rounded-full transition-all duration-500 bg-[var(--primary)]" style="width: <?php echo esc_attr( mizuki_skill_level_width( $level ) ); ?>%"></div>
					</div>
				</div>
				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无技能。', 'mizuki' ); ?></p>
		<?php endif; ?>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-16">
			<p class="text-black/40 dark:text-white/40 text-lg"><?php esc_html_e( '没有找到匹配的技能。', 'mizuki' ); ?></p>
		</div>
	</div>
</main>

<style>
	.skill-card {
		animation: fadeInUp 0.5s ease-out forwards;
		opacity: 0;
	}
	@keyframes fadeInUp {
		from { opacity: 0; transform: translateY(20px); }
		to { opacity: 1; transform: translateY(0); }
	}
	.skill-card:nth-child(1) { animation-delay: 0.03s; }
	.skill-card:nth-child(2) { animation-delay: 0.06s; }
	.skill-card:nth-child(3) { animation-delay: 0.09s; }
	.skill-card:nth-child(4) { animation-delay: 0.12s; }
	.skill-card:nth-child(5) { animation-delay: 0.15s; }
	.skill-card:nth-child(6) { animation-delay: 0.18s; }
	.skill-card:nth-child(7) { animation-delay: 0.21s; }
	.skill-card:nth-child(8) { animation-delay: 0.24s; }
	.skill-card:nth-child(9) { animation-delay: 0.27s; }
	.skill-card:nth-child(10) { animation-delay: 0.3s; }
	.skill-card:nth-child(11) { animation-delay: 0.33s; }
	.skill-card:nth-child(12) { animation-delay: 0.36s; }
	.line-clamp-2 {
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>

<?php get_footer(); ?>

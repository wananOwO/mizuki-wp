<?php
/**
 * Template Name: 技能
 *
 * 技能页面模板 — 完全同步 Mizuki 原项目 skills.astro。
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

// 显示名 + 图标映射:theme_mod 覆盖叠加在默认值之上(控制台「技能分类管理」可编辑)。
$category_names = mizuki_get_category_labels( 'skill_category' );
$category_icons = mizuki_get_category_icons( 'skill_category' );

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
				if ( $skill_cats && ! is_wp_error( $skill_cats ) && ! empty( $skill_cats ) ) {
					$cat_slug = implode( ',', wp_list_pluck( $skill_cats, 'slug' ) );
				} else {
					$cat_slug = '__none__';
				}
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

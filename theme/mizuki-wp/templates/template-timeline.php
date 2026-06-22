<?php
/**
 * Template Name: 时间线
 *
 * 时间线页面模板 - 独立的成长记录系统
 * 参照 Mizuki 原版 Astro 设计，完全重做
 *
 * @package Mizuki
 */
get_header();

// 获取所有时间线类型分类
$types = get_terms( array(
	'taxonomy'   => 'timeline_type',
	'hide_empty' => false,
) );

// 获取类型的显示名和图标映射
$type_labels = mizuki_get_category_labels( 'timeline_type' );
$type_icons  = mizuki_get_category_icons( 'timeline_type' );

// 查询所有时间线条目，按开始日期降序
$timeline_query = new WP_Query( array(
	'post_type'      => 'mizuki_timeline',
	'posts_per_page' => 200,
	'post_status'    => 'publish',
	'meta_key'       => '_mizuki_timeline_start_date',
	'orderby'        => 'meta_value',
	'order'          => 'DESC',
	'update_post_term_cache' => true,
	'update_post_meta_cache' => true,
) );

// 构建筛选 Tab 数据
$filter_tabs = array();
$filter_tabs[] = array(
	'value' => 'all',
	'label' => '全部',
	'icon'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>',
	'count' => $timeline_query->found_posts,
);

if ( ! empty( $types ) && ! is_wp_error( $types ) ) {
	foreach ( $types as $term ) {
		$count = $term->count;
		$label = isset( $type_labels[ $term->slug ] ) ? $type_labels[ $term->slug ] : $term->name;
		$icon_html = isset( $type_icons[ $term->slug ] ) ? $type_icons[ $term->slug ] : '';

		// 如果没有自定义图标，使用默认图标
		if ( empty( $icon_html ) ) {
			$default_icon = mizuki_get_default_type_icon( $term->slug );
			$icon_html = $default_icon;
		}

		$filter_tabs[] = array(
			'value' => $term->slug,
			'label' => $label,
			'icon'  => $icon_html,
			'count' => $count,
		);
	}
}
?>

<div class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative min-h-32">
	<div class="card-base z-10 px-6 sm:px-9 py-6 relative w-full">
		<!-- 页头 -->
		<div class="mb-8">
			<h1 class="text-3xl sm:text-4xl font-bold text-black/90 dark:text-white/90 mb-2">时间线</h1>
			<p class="text-black/60 dark:text-white/60">记录成长的每一步</p>
		</div>

		<!-- 筛选 Tab -->
		<div class="filter-tabs mb-8">
			<?php foreach ( $filter_tabs as $tab ) : ?>
				<button class="filter-tabs-item <?php echo $tab['value'] === 'all' ? 'active' : ''; ?>"
				        data-filter-value="<?php echo esc_attr( $tab['value'] ); ?>"
				        data-filter-attr="type">
					<?php echo $tab['icon']; ?>
					<span><?php echo esc_html( $tab['label'] ); ?></span>
					<span class="filter-tabs-count">(<?php echo esc_html( $tab['count'] ); ?>)</span>
				</button>
			<?php endforeach; ?>
		</div>

		<!-- 时间线列表 -->
		<div id="timeline-list" class="timeline-list relative">
			<?php
			if ( $timeline_query->have_posts() ) :
				while ( $timeline_query->have_posts() ) :
					$timeline_query->the_post();
					$post_id = get_the_ID();

					// 获取所有 meta 数据
					$description  = get_post_meta( $post_id, '_mizuki_timeline_description', true );
					$start_date   = get_post_meta( $post_id, '_mizuki_timeline_start_date', true );
					$end_date     = get_post_meta( $post_id, '_mizuki_timeline_end_date', true );
					$location     = get_post_meta( $post_id, '_mizuki_timeline_location', true );
					$organization = get_post_meta( $post_id, '_mizuki_timeline_organization', true );
					$position     = get_post_meta( $post_id, '_mizuki_timeline_position', true );
					$skills       = get_post_meta( $post_id, '_mizuki_timeline_skills', true );
					$achievements = get_post_meta( $post_id, '_mizuki_timeline_achievements', true );
					$links_json   = get_post_meta( $post_id, '_mizuki_timeline_links', true );
					$icon         = get_post_meta( $post_id, '_mizuki_timeline_icon', true );
					$color        = get_post_meta( $post_id, '_mizuki_timeline_color', true );
					$featured     = get_post_meta( $post_id, '_mizuki_timeline_featured', true );

					// 获取类型
					$type_terms = get_the_terms( $post_id, 'timeline_type' );
					$type_slug  = 'achievement'; // 默认
					$type_label = '成就';
					if ( ! empty( $type_terms ) && ! is_wp_error( $type_terms ) ) {
						$first_term = reset( $type_terms );
						$type_slug  = $first_term->slug;
						$type_label = isset( $type_labels[ $type_slug ] ) ? $type_labels[ $type_slug ] : $first_term->name;
					}

					// 判断是否进行中
					$is_current = empty( $end_date );

					// 解析技能标签
					$skills_array = array();
					if ( ! empty( $skills ) ) {
						$skills_array = array_map( 'trim', explode( ',', $skills ) );
					}

					// 解析成就列表
					$achievements_array = array();
					if ( ! empty( $achievements ) ) {
						$achievements_array = array_filter( array_map( 'trim', explode( "\n", $achievements ) ) );
					}

					// 解析链接
					$links = array();
					if ( ! empty( $links_json ) ) {
						$decoded = json_decode( $links_json, true );
						if ( is_array( $decoded ) ) {
							$links = $decoded;
						}
					}

					// 图标：优先使用自定义，否则根据类型自动选择
					if ( empty( $icon ) ) {
						$icon = mizuki_get_default_type_icon_class( $type_slug );
					}

					// 颜色：优先使用自定义，否则根据类型/进行中状态选择
					if ( empty( $color ) ) {
						if ( $is_current ) {
							$color = '#22c55e'; // 进行中用绿色
						} else {
							$color = mizuki_get_default_type_color( $type_slug );
						}
					}

					// 格式化日期和时长
					$date_range = mizuki_format_date_range( $start_date, $end_date );
					$duration   = mizuki_calculate_duration( $start_date, $end_date );
					?>

					<div class="timeline-entry <?php echo $is_current ? 'is-current' : ''; ?>"
					     data-type="<?php echo esc_attr( $type_slug ); ?>">

						<!-- 时间轴节点 -->
						<div class="timeline-node" style="background-color: <?php echo esc_attr( $color ); ?>;">
							<iconify-icon icon="<?php echo esc_attr( $icon ); ?>" class="w-2.5 h-2.5" style="color: white;"></iconify-icon>
						</div>

						<!-- 卡片内容 -->
						<div class="timeline-card group relative rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-lg">
							<div class="p-5 sm:p-6">
								<!-- 标题行 -->
								<div class="flex items-start gap-3 mb-3">
									<!-- 图标 -->
									<div class="w-10 h-10 flex-shrink-0 rounded-lg flex items-center justify-center bg-[var(--btn-regular-bg)]">
										<iconify-icon icon="<?php echo esc_attr( $icon ); ?>" class="text-lg" style="color: <?php echo esc_attr( $color ); ?>;"></iconify-icon>
									</div>

									<!-- 标题 + 组织 -->
									<div class="flex-1 min-w-0">
										<div class="flex items-center gap-2 mb-0.5">
											<h3 class="text-lg font-bold text-black/90 dark:text-white/90 group-hover:text-[var(--primary)] transition-colors duration-200">
												<?php the_title(); ?>
											</h3>
											<?php if ( $featured ) : ?>
												<svg viewBox="0 0 24 24" width="16" height="16" class="flex-shrink-0 text-[var(--primary)]">
													<path fill="currentColor" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
												</svg>
											<?php endif; ?>
										</div>
										<?php if ( $organization ) : ?>
											<p class="text-sm text-black/60 dark:text-white/60">
												<?php echo esc_html( $organization ); ?>
												<?php if ( $position ) : ?>
													· <?php echo esc_html( $position ); ?>
												<?php endif; ?>
											</p>
										<?php endif; ?>
									</div>

									<!-- 标签组 -->
									<div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
										<?php if ( $is_current ) : ?>
											<span class="px-2 py-0.5 text-xs rounded-md bg-green-500/10 text-green-600 dark:text-green-400 font-medium">
												进行中
											</span>
										<?php endif; ?>
										<span class="px-2 py-0.5 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
											<?php echo esc_html( $type_label ); ?>
										</span>
									</div>
								</div>

								<!-- 描述 -->
								<p class="text-sm text-black/70 dark:text-white/70 mb-4 leading-relaxed">
									<?php echo esc_html( $description ); ?>
								</p>

								<!-- 技能标签 -->
								<?php if ( ! empty( $skills_array ) ) : ?>
									<div class="flex flex-wrap gap-2 mb-4">
										<?php foreach ( $skills_array as $skill ) : ?>
											<span class="px-2 py-1 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
												<?php echo esc_html( $skill ); ?>
											</span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<!-- 成就列表 -->
								<?php if ( ! empty( $achievements_array ) ) : ?>
									<div class="mb-4">
										<h4 class="text-xs font-semibold text-black/50 dark:text-white/50 uppercase tracking-wider mb-2">
											成就
										</h4>
										<ul class="space-y-1.5">
											<?php foreach ( $achievements_array as $achievement ) : ?>
												<li class="text-sm text-black/70 dark:text-white/70 flex items-start gap-2">
													<svg viewBox="0 0 24 24" width="16" height="16" class="flex-shrink-0 mt-0.5 text-green-500">
														<path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2m-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
													</svg>
													<span><?php echo esc_html( $achievement ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

								<!-- 日期范围 + 时长 + 地点 -->
								<div class="flex items-center gap-4 text-xs text-black/50 dark:text-white/50 mb-1 pt-3 border-t border-black/5 dark:border-white/5">
									<span class="flex items-center gap-1">
										<svg viewBox="0 0 24 24" width="14" height="14">
											<path fill="currentColor" d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5zm2 4h10v2H7v-2z"/>
										</svg>
										<?php echo esc_html( $date_range ); ?>
									</span>
									<span class="flex items-center gap-1">
										<svg viewBox="0 0 24 24" width="14" height="14">
											<path fill="currentColor" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
										</svg>
										<?php echo esc_html( $duration ); ?>
									</span>
									<?php if ( $location ) : ?>
										<span class="flex items-center gap-1">
											<svg viewBox="0 0 24 24" width="14" height="14">
												<path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
											</svg>
											<?php echo esc_html( $location ); ?>
										</span>
									<?php endif; ?>
								</div>

								<!-- 链接按钮 -->
								<?php if ( ! empty( $links ) ) : ?>
									<div class="flex flex-wrap gap-2 mt-3">
										<?php foreach ( $links as $link ) : ?>
											<a href="<?php echo esc_url( $link['url'] ); ?>"
											   target="_blank"
											   rel="noopener noreferrer"
											   class="btn-regular flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium">
												<?php echo mizuki_get_link_type_icon( $link['type'] ?? 'website' ); ?>
												<?php echo esc_html( $link['name'] ); ?>
											</a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>

							<!-- Hover 渐变层 -->
							<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-xl"></div>
						</div>
					</div>

				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<!-- 空状态 -->
				<div class="text-center py-16">
					<svg viewBox="0 0 24 24" width="64" height="64" class="mx-auto text-black/15 dark:text-white/15 mb-4">
						<path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
					</svg>
					<p class="text-black/40 dark:text-white/40 text-lg">暂无时间线记录</p>
				</div>
			<?php endif; ?>
		</div>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-16">
			<svg viewBox="0 0 24 24" width="64" height="64" class="mx-auto text-black/15 dark:text-white/15 mb-4">
				<path fill="currentColor" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
			</svg>
			<p class="text-black/40 dark:text-white/40 text-lg">没有匹配的记录</p>
		</div>
	</div>
</div>

<style>
/* 时间轴线 */
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

/* 时间线条目 */
.timeline-entry {
	position: relative;
	padding-left: 2.5rem;
	padding-bottom: 2rem;
	animation: fadeInUp 0.5s ease-out forwards;
	opacity: 0;
}

.timeline-entry:last-child {
	padding-bottom: 0;
}

.timeline-entry.filtered-out {
	display: none;
}

/* 时间轴节点 */
.timeline-node {
	position: absolute;
	left: 0;
	top: 1.5rem;
	width: 1.25rem;
	height: 1.25rem;
	border-radius: 50%;
	border: 2.5px solid white;
	box-shadow: 0 0 0 2px var(--line-divider);
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 2;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.timeline-entry:hover .timeline-node {
	transform: scale(1.15);
	box-shadow: 0 0 0 2px var(--primary), 0 0 12px rgba(0, 0, 0, 0.1);
}

/* 进行中项的脉冲动画 */
.timeline-entry.is-current .timeline-node {
	animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
	0%, 100% {
		box-shadow: 0 0 0 2px var(--line-divider);
	}
	50% {
		box-shadow: 0 0 0 2px #22c55e, 0 0 8px rgba(34, 197, 94, 0.3);
	}
}

/* 渐入动画 */
@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(16px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* 错开动画时间 */
.timeline-entry:nth-child(1) { animation-delay: 0.05s; }
.timeline-entry:nth-child(2) { animation-delay: 0.1s; }
.timeline-entry:nth-child(3) { animation-delay: 0.15s; }
.timeline-entry:nth-child(4) { animation-delay: 0.2s; }
.timeline-entry:nth-child(5) { animation-delay: 0.25s; }
.timeline-entry:nth-child(6) { animation-delay: 0.3s; }
.timeline-entry:nth-child(7) { animation-delay: 0.35s; }
.timeline-entry:nth-child(8) { animation-delay: 0.4s; }
.timeline-entry:nth-child(9) { animation-delay: 0.45s; }

/* 暗色模式适配 */
.dark .timeline-node {
	border-color: #1a1a1a;
}
</style>

<?php get_footer(); ?>

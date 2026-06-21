<?php
/**
 * Template Name: 友链
 *
 * 友链页面模板 — 完全同步 Mizuki 原项目 friends.astro。
 * 使用 friend_tag taxonomy + filter-tag 按钮筛选。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

// 获取所有友链标签
$friend_tags = get_terms( array(
	'taxonomy'   => 'friend_tag',
	'hide_empty' => true,
	'orderby'    => 'name',
) );

$friend_query = new WP_Query( array(
	'post_type'              => 'mizuki_friend',
	'posts_per_page'         => 200,
	'orderby'                => 'date',
	'order'                  => 'ASC',
	'no_found_rows'          => true,
	'update_post_term_cache' => true,
) );

$total_count = $friend_query->post_count;
?>

<script is:inline src="<?php echo esc_url( MIZUKI_URI . '/assets/js/filter-tabs-handler.js' ); ?>"></script>

<main id="main" class="friends-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-2 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '友链', 'mizuki' ); ?>
		</h1>
		<p class="text-black/50 dark:text-white/50 mb-6"><?php esc_html_e( '我的朋友们', 'mizuki' ); ?></p>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<!-- 搜索和筛选栏 (同步原项目 friends.astro) -->
		<div class="mb-6 space-y-3">
			<!-- 搜索框 -->
			<div class="w-full">
				<div class="relative">
					<input
						type="text"
						id="friend-search"
						placeholder="<?php esc_attr_e( '搜索友链...', 'mizuki' ); ?>"
						class="w-full px-4 py-2 pl-9 rounded-lg bg-[var(--btn-regular-bg)]
								text-black/90 dark:text-white/90
								border border-black/10 dark:border-white/10
								focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/50
								transition-all duration-200"
					/>
					<svg
						class="absolute left-2 top-1/2 -translate-y-1/2 w-5 h-5 text-black/40 dark:text-white/40"
						fill="none" stroke="currentColor" viewBox="0 0 24 24"
					>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
					</svg>
				</div>
			</div>

			<!-- 标签筛选 (同步原项目 filter-tag 按钮) -->
			<?php if ( ! is_wp_error( $friend_tags ) && ! empty( $friend_tags ) ) : ?>
			<div class="filter-container flex flex-wrap gap-2">
				<button class="filter-tag active" data-tag="all">
					<?php esc_html_e( '全部', 'mizuki' ); ?>
				</button>
				<?php foreach ( $friend_tags as $tag ) : ?>
				<button class="filter-tag" data-tag="<?php echo esc_attr( $tag->name ); ?>">
					<?php echo esc_html( $tag->name ); ?>
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<!-- 友链卡片网格 -->
		<?php if ( $friend_query->have_posts() ) : ?>
		<div id="friends-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
			<?php while ( $friend_query->have_posts() ) : $friend_query->the_post();
				$furl = get_post_meta( get_the_ID(), '_mizuki_friend_url', true );
				$desc = get_post_meta( get_the_ID(), '_mizuki_friend_desc', true );
				$tags = get_the_terms( get_the_ID(), 'friend_tag' );
				$tag_names = array();
				if ( $tags && ! is_wp_error( $tags ) ) {
					$tag_names = wp_list_pluck( $tags, 'name' );
				}
				$tag_str = implode( ',', $tag_names );
			?>
			<a href="<?php echo esc_url( $furl ?: '#' ); ?>" target="_blank" rel="noopener noreferrer"
			   class="friend-card group block bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
			   data-tag="<?php echo esc_attr( $tag_str ); ?>">
				<div class="p-5 flex items-start gap-4">
					<div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'w-full h-full object-cover', 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<div class="w-full h-full bg-[var(--primary)]/10 flex items-center justify-center text-2xl font-bold text-[var(--primary)]">
								<?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="flex-1 min-w-0">
						<h3 class="text-lg font-bold text-black/90 dark:text-white/90 truncate group-hover:text-[var(--primary)] transition-colors duration-200">
							<?php the_title(); ?>
						</h3>
						<?php if ( $desc ) : ?>
						<p class="text-sm text-black/60 dark:text-white/60 mt-1 line-clamp-2">
							<?php echo esc_html( $desc ); ?>
						</p>
						<?php endif; ?>
						<?php if ( ! empty( $tag_names ) ) : ?>
						<div class="flex flex-wrap gap-1.5 mt-2">
							<?php foreach ( $tag_names as $tn ) : ?>
							<span class="px-2 py-0.5 text-xs rounded-md bg-black/5 dark:bg-white/10 text-black/60 dark:text-white/60"><?php echo esc_html( $tn ); ?></span>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无友链。', 'mizuki' ); ?></p>
		<?php endif; ?>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-12">
			<p class="text-black/50 dark:text-white/50 text-lg"><?php esc_html_e( '没有找到匹配的友链。', 'mizuki' ); ?></p>
		</div>
	</div>
</main>

<!-- 友链筛选由 filter-handler.js 统一处理 -->

<style>
	.friend-card {
		animation: fadeInUp 0.5s ease-out forwards;
		opacity: 0;
	}
	@keyframes fadeInUp {
		from { opacity: 0; transform: translateY(20px); }
		to { opacity: 1; transform: translateY(0); }
	}
	.friend-card:nth-child(1) { animation-delay: 0.03s; }
	.friend-card:nth-child(2) { animation-delay: 0.06s; }
	.friend-card:nth-child(3) { animation-delay: 0.09s; }
	.friend-card:nth-child(4) { animation-delay: 0.12s; }
	.friend-card:nth-child(5) { animation-delay: 0.15s; }
	.friend-card:nth-child(6) { animation-delay: 0.18s; }
	.line-clamp-2 {
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>

<?php get_footer(); ?>

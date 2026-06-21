<?php
/**
 * Template Name: 友链
 *
 * 友链页面模板 - 以网格卡片形式展示所有友链条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="friends-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '友链', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$friend_query = new WP_Query( array(
			'post_type'      => 'mizuki_friend',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		) );

		if ( $friend_query->have_posts() ) :
			// 收集所有标签
			$all_tags = array();
			foreach ( $friend_query->posts as $friend_post ) {
				$post_tags = get_the_tags( $friend_post->ID );
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
			<button class="filter-tabs-item active" data-filter-attr="friend-tags" data-filter-value="all">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<span><?php esc_html_e( '全部', 'mizuki' ); ?></span>
				<span class="filter-tabs-count">(<?php echo count( $friend_query->posts ); ?>)</span>
			</button>
			<?php foreach ( $all_tags as $tag ) : ?>
			<button class="filter-tabs-item" data-filter-attr="friend-tags" data-filter-value="<?php echo esc_attr( $tag['slug'] ); ?>">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
				</svg>
				<span><?php echo esc_html( $tag['name'] ); ?></span>
				<span class="filter-tabs-count">(<?php echo $tag['count']; ?>)</span>
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div id="friends-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
		<?php
		endif;

		if ( $friend_query->have_posts() ) :
		?>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php while ( $friend_query->have_posts() ) : $friend_query->the_post();
				$friend_url  = get_post_meta( get_the_ID(), '_mizuki_friend_url', true );
				$friend_desc = get_post_meta( get_the_ID(), '_mizuki_friend_desc', true );
				$friend_host = $friend_url ? wp_parse_url( $friend_url, PHP_URL_HOST ) : '';
				$friend_tags = get_the_tags();
				$tag_slugs   = $friend_tags ? implode( ',', wp_list_pluck( $friend_tags, 'slug' ) ) : '';
			?>
			<div class="group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1" data-friend-tags="<?php echo esc_attr( $tag_slugs ); ?>">
				<div class="p-6">
					<!-- 头像和标题区 -->
					<div class="flex items-start gap-4 mb-4">
						<div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden bg-[var(--primary)]/10 flex items-center justify-center ring-2 ring-transparent transition-all duration-300">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300', 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="text-2xl font-bold text-[var(--primary)] group-hover:scale-110 transition-transform duration-300"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
							<?php endif; ?>
						</div>
						<div class="flex-1 min-w-0">
							<h3 class="text-xl font-bold text-black/90 dark:text-white/90 mb-1 truncate group-hover:text-[var(--primary)] transition-colors duration-200">
								<?php the_title(); ?>
							</h3>
							<?php if ( $friend_host ) : ?>
							<a href="<?php echo esc_url( $friend_url ); ?>" target="_blank" rel="noopener noreferrer"
							   class="text-xs text-black/50 dark:text-white/50 hover:text-[var(--primary)] truncate block transition-colors duration-200">
								<?php echo esc_html( $friend_host ); ?>
							</a>
							<?php endif; ?>
						</div>
					</div>

					<!-- 描述 -->
					<?php if ( $friend_desc ) : ?>
					<p class="text-sm text-black/60 dark:text-white/60 mb-4 line-clamp-2 min-h-[2.5rem]">
						<?php echo esc_html( $friend_desc ); ?>
					</p>
					<?php endif; ?>

					<!-- 标签 -->
					<?php if ( $friend_tags ) : ?>
					<div class="flex flex-wrap gap-2 mb-4">
						<?php foreach ( $friend_tags as $friend_tag ) : ?>
						<span class="px-2 py-1 text-xs rounded-md bg-[var(--primary)]/10 text-[var(--primary)] font-medium">
							<?php echo esc_html( $friend_tag->name ); ?>
						</span>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<!-- 操作按钮 -->
					<?php if ( $friend_url ) : ?>
					<div class="flex gap-2">
						<a href="<?php echo esc_url( $friend_url ); ?>" target="_blank" rel="noopener noreferrer"
						   class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[var(--primary)] text-white hover:bg-[var(--primary)]/90 active:scale-95 transition-all duration-200 font-medium text-sm">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
							<?php esc_html_e( '访问', 'mizuki' ); ?>
						</a>
					</div>
					<?php endif; ?>
				</div>

				<!-- 悬停装饰效果 -->
				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-xl"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

		<!-- 无结果提示 -->
		<div id="no-results" class="hidden text-center py-12">
			<p class="text-50"><?php esc_html_e( '没有找到匹配的友链。', 'mizuki' ); ?></p>
		</div>

		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无友链。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

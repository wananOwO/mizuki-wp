<?php
/**
 * Template Name: 日记
 *
 * 日记页面模板 - 以单列信息流形式展示所有日记条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

/**
 * 将发布时间格式化为相对时间。
 *
 * @param int $timestamp 发布时间戳。
 * @return string
 */
function mizuki_diary_relative_time( $timestamp ) {
	$diff = time() - $timestamp;
	if ( $diff < 60 ) {
		return __( '刚刚', 'mizuki' );
	}
	if ( $diff < 3600 ) {
		$m = (int) floor( $diff / 60 );
		/* translators: %d: minutes */
		return sprintf( _n( '%d 分钟前', '%d 分钟前', $m, 'mizuki' ), $m );
	}
	if ( $diff < 86400 ) {
		$h = (int) floor( $diff / 3600 );
		/* translators: %d: hours */
		return sprintf( _n( '%d 小时前', '%d 小时前', $h, 'mizuki' ), $h );
	}
	if ( $diff < 2592000 ) {
		$d = (int) floor( $diff / 86400 );
		/* translators: %d: days */
		return sprintf( _n( '%d 天前', '%d 天前', $d, 'mizuki' ), $d );
	}
	return date_i18n( get_option( 'date_format' ), $timestamp );
}
?>
<main id="main" class="diary-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '日记', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$diary_query = new WP_Query( array(
			'post_type'              => 'mizuki_diary',
			'posts_per_page'         => 200,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
	'update_post_meta_cache' => true,
		) );

		if ( $diary_query->have_posts() ) :
		?>
		<div class="max-w-[600px] mx-auto flex flex-col gap-4">
			<?php while ( $diary_query->have_posts() ) : $diary_query->the_post();
				$images   = get_post_meta( get_the_ID(), '_mizuki_diary_images', true );
				$img_list = $images ? array_filter( array_map( 'trim', explode( "\n", $images ) ) ) : array();
				$img_count = count( $img_list );
				$diary_tags = get_the_tags();
			?>
			<div class="moment-card group relative bg-transparent rounded-xl border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
				<div class="p-5">
					<!-- 内容 -->
					<div class="prose dark:prose-invert prose-sm !max-w-none custom-md markdown-content text-sm md:text-base text-black/90 dark:text-white/90 leading-relaxed mb-3">
						<?php the_content(); ?>
					</div>

					<!-- 配图 -->
					<?php if ( $img_count > 0 ) :
						$grid_cols = ( 1 === $img_count ) ? 'grid-cols-1 max-w-[400px]' : ( ( 2 === $img_count ) ? 'grid-cols-2 max-w-[500px]' : 'grid-cols-3 max-w-[600px]' );
					?>
					<div class="grid gap-2 mb-3 <?php echo esc_attr( $grid_cols ); ?>">
						<?php foreach ( $img_list as $img_index => $img_url ) : ?>
						<div class="relative rounded-lg overflow-hidden aspect-square cursor-pointer">
							<a href="<?php echo esc_url( $img_url ); ?>" data-src="<?php echo esc_url( $img_url ); ?>" data-fancybox="diary-<?php echo esc_attr( get_the_ID() ); ?>" class="block w-full h-full">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php esc_attr_e( '日记配图', 'mizuki' ); ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" loading="lazy" decoding="async">
							</a>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<!-- 标签 -->
					<?php if ( $diary_tags ) : ?>
					<div class="flex flex-wrap gap-1.5 mb-3">
						<?php foreach ( $diary_tags as $diary_tag ) : ?>
						<span class="btn-regular h-6 text-xs px-2 rounded-lg flex items-center"><?php echo esc_html( $diary_tag->name ); ?></span>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<!-- 分隔线 -->
					<hr class="border-t border-black/5 dark:border-white/5 my-3">

					<!-- 底部 -->
					<div class="flex items-center justify-between text-xs text-black/50 dark:text-white/50 flex-wrap gap-2">
						<div class="flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( mizuki_diary_relative_time( get_the_time( 'U' ) ) ); ?></time>
						</div>
					</div>
				</div>

				<!-- 悬停渐变遮罩 -->
				<div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-xl"></div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<?php mizuki_empty_state( __( '暂无日记内容。', 'mizuki' ) ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

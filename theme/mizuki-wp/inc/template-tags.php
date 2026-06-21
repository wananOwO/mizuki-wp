<?php
/**
 * 模板辅助函数。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 估算当前文章阅读时间(分钟,按 ~300 中文字/分钟)。
 *
 * @param int|null $post_id 文章 ID,默认当前文章。
 * @return int 阅读分钟数(至少 1)。
 */
function mizuki_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$text    = wp_strip_all_tags( $content );
	$count   = mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
	return max( 1, (int) ceil( $count / 300 ) );
}

/**
 * 统计当前文章字数(中文)。
 *
 * @param int|null $post_id 文章 ID,默认当前文章。
 * @return int 字数。
 */
function mizuki_word_count( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$text    = wp_strip_all_tags( $content );
	return mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
}

/**
 * 输出分类筛选条(category-bar):Home + 归档 + 各分类胶囊。
 * 与原版 Mizuki 首页主内容区上方的 #category-bar 一致。
 */
function mizuki_category_bar() {
	$cats = get_categories( array( 'hide_empty' => true ) );
	if ( ! $cats ) {
		return;
	}
	$archive_url   = get_permalink( get_option( 'page_for_posts' ) );
	if ( ! $archive_url ) {
		$archive_url = home_url( '/' );
	}
	$total         = (int) wp_count_posts()->publish;
	$current_cat   = is_category() ? (int) get_queried_object_id() : 0;
	$home_active   = ( is_home() || is_front_page() ) ? ' data-active' : '';
	$home_icon     = '<svg width="1em" height="1em" viewBox="0 0 24 24" class="text-lg"><path fill="currentColor" d="M4 21V9l8-6l8 6v12h-6v-7h-4v7z"/></svg>';
	?>
	<div class="card-base category-bar p-3 onload-animation" id="category-bar">
		<div class="category-bar-inner flex gap-2">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="category-pill text-sm px-2 py-1 rounded-lg flex-shrink-0 transition-colors duration-200 flex items-center justify-center" aria-label="<?php esc_attr_e( '首页', 'mizuki' ); ?>"<?php echo $home_active; // phpcs:ignore ?>><?php echo $home_icon; // phpcs:ignore ?></a>
			<a href="<?php echo esc_url( $archive_url ); ?>" class="category-pill text-sm px-3 py-1 rounded-lg whitespace-nowrap flex-shrink-0 transition-colors duration-200 flex items-center justify-center"><?php esc_html_e( '归档', 'mizuki' ); ?> <span class="text-xs opacity-60 ml-1"><?php echo (int) $total; ?></span></a>
			<div class="category-divider flex-shrink-0"></div>
			<div class="category-scroll flex gap-2 overflow-x-auto min-w-0 flex-1">
				<?php foreach ( $cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="category-pill text-sm px-3 py-1 rounded-lg whitespace-nowrap transition-colors duration-200 flex items-center justify-center"<?php echo ( $current_cat === (int) $cat->term_id ) ? ' data-active' : ''; ?>><?php echo esc_html( $cat->name ); ?> <span class="text-xs opacity-60 ml-1"><?php echo (int) $cat->count; ?></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

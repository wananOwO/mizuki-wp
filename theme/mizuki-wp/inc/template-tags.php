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

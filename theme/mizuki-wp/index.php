<?php
/**
 * 临时兜底模板,Task 1.4 用真实 Mizuki 卡片结构替换。
 *
 * @package Mizuki
 */
get_header();
?>
<main id="main" class="site-main">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_title( '<h2><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
		}
	} else {
		esc_html_e( '暂无内容。', 'mizuki' );
	}
	?>
</main>
<?php
get_footer();

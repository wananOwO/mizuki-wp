<?php
/**
 * 主题基础设置: 菜单、侧边栏、缩略图等。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册菜单位置。
 */
function mizuki_register_menus() {
	register_nav_menus(
		array(
			'primary' => esc_html__( '主导航菜单', 'mizuki' ),
		)
	);
}
add_action( 'after_setup_theme', 'mizuki_register_menus' );

/**
 * 注册侧边栏小工具区域。
 */
function mizuki_register_sidebars() {
	register_sidebar(
		array(
			'name'          => esc_html__( '左侧边栏', 'mizuki' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( '左侧边栏小工具区域(档案、分类、标签等)。', 'mizuki' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title font-bold text-lg mb-2">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'mizuki_register_sidebars' );

/**
 * 自定义评论输出(套用 Mizuki 样式 class)。
 */
function mizuki_comment_template( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'card-base p-4 mb-2' ); ?>>
		<div class="comment-author flex items-center gap-3 mb-2">
			<?php echo get_avatar( $comment, 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
			<span class="font-bold text-90"><?php comment_author(); ?></span>
			<span class="text-50 text-sm"><?php comment_date(); ?></span>
		</div>
		<div class="comment-content prose dark:prose-invert text-75">
			<?php comment_text(); ?>
		</div>
		<div class="comment-actions text-sm text-50 mt-2">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	<?php
}

<?php
/**
 * 侧栏模板。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
<aside id="sidebar" class="sidebar">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<div class="widget card-base p-4 mb-4">
			<h3 class="widget__title font-bold mb-2"><?php esc_html_e( '侧栏', 'mizuki' ); ?></h3>
			<p class="text-50 text-sm"><?php esc_html_e( '请在 外观 > 小工具 中添加内容。', 'mizuki' ); ?></p>
		</div>
	<?php endif; ?>
</aside>

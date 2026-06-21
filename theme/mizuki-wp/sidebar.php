<?php
/**
 * 侧栏模板(备用)。
 *
 * 注意: 当前主题的侧栏通过 header.php 中的 dynamic_sidebar() 直接渲染，
 * 此文件保留作为备选，可通过 get_sidebar() 在需要时调用。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
<aside id="sidebar-alt" class="w-full sidebar-root">
	<div id="sidebar-sticky" class="transition-all duration-700 flex flex-col w-full gap-4 sticky top-4">
		<?php
		/**
		 * 输出侧边栏个人资料小工具(读取 Customizer 设置)。
		 */
		do_action( 'mizuki_sidebar_before_widgets' );
		?>
		<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
			<div class="sidebar-content flex flex-col w-full gap-4">
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			</div>
		<?php else : ?>
			<div class="widget card-base p-4 mb-4">
				<h3 class="widget__title font-bold mb-2"><?php esc_html_e( '侧栏', 'mizuki' ); ?></h3>
				<p class="text-50 text-sm"><?php esc_html_e( '请在 外观 > 小工具 中添加内容。', 'mizuki' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</aside>

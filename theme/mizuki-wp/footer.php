<?php
/**
 * Mizuki 主题页脚模板。
 *
 * 输出 Mizuki footer + TOC 容器的精确 DOM 结构,
 * 与编译后的 Mizuki CSS 选择器完全匹配。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
						<!-- 页脚 -->
						<div class="footer col-span-full onload-animation">
							<div class="transition border-t border-black/10 dark:border-white/15 my-10 border-dashed mx-32"></div>
							<div class="transition border-dashed border-[oklch(85%_0.01_var(--hue))] dark:border-white/15 rounded-2xl mb-12 flex flex-col items-center justify-center px-6">
								<div class="transition text-50 text-sm text-center">
									<div class="mb-2">
										&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved. /
										<a class="transition link text-[var(--primary)] font-medium" href="<?php echo esc_url( get_feed_link() ); ?>">RSS</a> /
										<a class="transition link text-[var(--primary)] font-medium" href="<?php echo esc_url( get_feed_link( 'atom' ) ); ?>">Atom</a>
									</div>
									<div>
										Powered by <a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://wordpress.org">WordPress</a> &
										<a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://github.com/LyraVoid/Mizuki">Mizuki</a>
									</div>
								</div>
							</div>
						</div>

					</div><!-- #content-wrapper -->
				</main><!-- #swup-container -->

				<?php mizuki_render_right_sidebar(); ?>
			</div><!-- #main-grid -->

			<!-- 右侧 TOC 容器(2xl 可见，文章页用) -->
			<div class="absolute w-full z-0 hidden 2xl:block">
				<div class="relative max-w-(--page-width) mx-auto">
					<div id="toc-wrapper" class="hidden lg:block transition absolute top-0 w-(--toc-width) items-center -right-(--toc-width)">
						<div id="toc-inner-wrapper" class="fixed top-14 w-(--toc-width) h-[calc(100vh-6rem)] max-h-[calc(100vh-6rem)] overflow-x-hidden overflow-y-auto">
							<div id="toc-container" class="w-full h-full transition-swup-fade"></div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- max-w wrapper -->
	</div><!-- absolute z-30 wrapper -->

	<?php
	/**
	 * 主题色(hue)调节仅保留在顶部导航的"显示设置"面板中(#panel-hue-slider)。
	 * 此处不再渲染页脚浮动滑块,避免底部出现多余的 UI 条。
	 */
	?>

	<!-- 右下角浮动控制(回到顶部) -->
	<div class="floating-controls-container" data-astro-cid-l4lcuy54>
		<div class="floating-control-item" data-control-key="top" data-astro-cid-l4lcuy54>
			<div id="back-to-top-btn" class="floating-btn hide btn-card flex items-center rounded-2xl overflow-hidden transition" data-astro-cid-eoir7dmc>
				<button aria-label="<?php esc_attr_e( '回到顶部', 'mizuki' ); ?>" class="h-full w-full rounded-2xl" data-astro-cid-eoir7dmc>
					<svg viewBox="0 0 24 24" width="1em" height="1em" class="mx-auto"><path fill="currentColor" d="m12 10.8l-3.9 3.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.6-4.6q.3-.3.7-.3t.7.3l4.6 4.6q.275.275.275.7t-.275.7t-.7.275t-.7-.275z"/></svg>
				</button>
			</div>
		</div>
	</div>

	<?php wp_footer(); ?>
	</body>
	</html>

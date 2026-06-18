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
					<!-- 页脚(桌面端,lg+ 可见) -->
					<div class="footer col-span-full onload-animation hidden lg:block">
						<div class="transition border-t border-black/10 dark:border-white/15 my-10 border-dashed mx-32"></div>
						<div class="transition border-dashed border-[oklch(85%_0.01_var(--hue))] dark:border-white/15 rounded-2xl mb-12 flex flex-col items-center justify-center px-6">
							<div class="transition text-50 text-sm text-center">
								<div class="mb-2">
									&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved.
								</div>
								<div>
									Powered by <a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://wordpress.org">WordPress</a> &amp;
									<a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://github.com/LyraVoid/Mizuki">Mizuki</a>
								</div>
							</div>
						</div>
					</div>

				</div><!-- #content-wrapper -->
			</main><!-- #swup-container -->

			<!-- 页脚(移动端/平板端,block lg:hidden) -->
			<div class="footer col-span-full onload-animation block lg:hidden">
				<div class="transition border-t border-black/10 dark:border-white/15 my-10 border-dashed mx-32"></div>
				<div class="transition border-dashed border-[oklch(85%_0.01_var(--hue))] dark:border-white/15 rounded-2xl mb-12 flex flex-col items-center justify-center px-6">
					<div class="transition text-50 text-sm text-center">
						<div class="mb-2">
							&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved.
						</div>
						<div>
							Powered by <a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://wordpress.org">WordPress</a> &amp;
							<a class="transition link text-[var(--primary)] font-medium" target="_blank" href="https://github.com/LyraVoid/Mizuki">Mizuki</a>
						</div>
					</div>
				</div>
			</div>
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
		<!-- 保留此元素以确保Swup正常工作 -->
		<div id="toc-container" class="hidden"></div>
	</div><!-- max-w wrapper -->
</div><!-- absolute z-30 wrapper -->

<?php wp_footer(); ?>
</body>
</html>

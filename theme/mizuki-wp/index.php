<?php
/**
 * 博客首页 / 文章列表。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="post-list onload-animation">
	<?php if ( have_posts() ) : ?>
	<?php mizuki_category_bar(); ?>
	<div id="post-list-container" class="post-list-container transition-all duration-500 ease-in-out rounded-[var(--radius-large)] bg-[var(--card-bg)] md:bg-transparent mb-4 list-mode" data-both-sidebars="true" style="--coverWidth: 28%;">
		<?php while ( have_posts() ) : the_post();
			$has_cover = has_post_thumbnail();
			$wc        = str_word_count( wp_strip_all_tags( get_the_content() ) );
		?>
			<div class="post-card-item card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative transition onload-animation">
				<!-- 文字区 -->
				<div class="pl-6 md:pl-9 pr-6 md:pr-2 pt-6 md:pt-7 pb-6 relative w-full <?php echo $has_cover ? 'md:w-[72%]' : 'md:w-[calc(100%-3.25rem-0.75rem)]'; ?>">
					<!-- 标题 -->
					<a href="<?php the_permalink(); ?>"
					   class="transition group w-full block font-bold mb-3 text-3xl text-90
					          hover:text-[var(--primary)] dark:hover:text-[var(--primary)]
					          before:w-1 before:h-5 before:rounded-md before:bg-[var(--primary)]
					          before:absolute before:top-[2.1875rem] before:left-[1.125rem] before:hidden md:before:block">
						<?php the_title(); ?>
						<!-- 移动端箭头 -->
						<svg class="inline text-[2rem] text-[var(--primary)] md:hidden translate-y-0.5 absolute" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
						<!-- 桌面端悬停箭头 -->
						<svg class="text-[var(--primary)] text-[2rem] transition hidden md:inline absolute translate-y-0.5 opacity-0 group-hover:opacity-100 -translate-x-1 group-hover:translate-x-0" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
					</a>
					<!-- 元信息 -->
					<div class="flex flex-wrap text-neutral-500 dark:text-neutral-400 items-center gap-4 gap-x-4 gap-y-2 mb-4">
						<div class="flex items-center">
							<div class="meta-icon">
								<svg class="text-xl" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 22q-.825 0-1.412-.587T3 20V6q0-.825.588-1.412T5 4h1V3q0-.425.288-.712T7 2t.713.288T8 3v1h8V3q0-.425.288-.712T17 2t.713.288T18 3v1h1q.825 0 1.413.588T21 6v14q0 .825-.587 1.413T19 22zm0-2h14V10H5zM5 8h14V6H5zm0 0V6z"/></svg>
							</div>
							<span class="text-50 text-sm font-medium"><?php echo esc_html( get_the_date() ); ?></span>
						</div>
						<?php
						$post_cats = get_the_category();
						if ( $post_cats ) :
							$primary_cat = $post_cats[0];
							?>
						<div class="flex items-center">
							<div class="meta-icon">
								<svg class="text-xl" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M6 15.325q.35-.175.725-.25T7.5 15H8V4h-.5q-.625 0-1.062.438T6 5.5zM10 15h8V4h-8zm-4 .325V4zM7.5 22q-1.45 0-2.475-1.025T4 18.5v-13q0-1.45 1.025-2.475T7.5 2H18q.825 0 1.413.587T20 4v12.525q0 .2-.162.363t-.588.362q-.35.175-.55.5t-.2.75t.2.763t.55.487t.55.413t.2.562v.25q0 .425-.288.725T19 22zm0-2h9.325q-.15-.35-.237-.712T16.5 18.5q0-.4.075-.775t.25-.725H7.5q-.65 0-1.075.438T6 18.5q0 .65.425 1.075T7.5 20"/></svg>
							</div>
							<div class="flex flex-row flex-nowrap items-center">
								<a href="<?php echo esc_url( get_category_link( $primary_cat ) ); ?>" class="link-lg transition text-50 text-sm font-medium hover:text-[var(--primary)] dark:hover:text-[var(--primary)] whitespace-nowrap"><?php echo esc_html( $primary_cat->name ); ?></a>
							</div>
						</div>
						<?php endif; ?>
						<div class="flex items-center">
							<div class="meta-icon">
								<svg class="text-xl" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5zM5 5v14zm3 12h5q.425 0 .713-.288T14 16t-.288-.712T13 15H8q-.425 0-.712.288T7 16t.288.713T8 17m0-4h8q.425 0 .713-.288T17 12t-.288-.712T16 11H8q-.425 0-.712.288T7 12t.288.713T8 13m0-4h8q.425 0 .713-.288T17 8t-.288-.712T16 7H8q-.425 0-.712.288T7 8t.288.713T8 9"/></svg>
							</div>
							<span class="text-50 text-sm font-medium"><?php echo esc_html( number_format_i18n( $wc ) . ' ' . _n( 'word', 'words', $wc, 'mizuki' ) ); ?></span>
						</div>
					</div>
					<!-- 摘要 -->
					<div class="transition text-75 mb-3.5 pr-4 line-clamp-2 md:line-clamp-1">
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</div>
					<!-- 标签 -->
					<div class="flex flex-wrap gap-2 mt-2">
						<?php
						$tags = get_the_tags();
						if ( $tags ) {
							foreach ( $tags as $tag ) {
								echo '<a href="' . esc_url( get_tag_link( $tag ) ) . '" class="link-lg transition text-50 text-xs font-medium px-2 py-1 rounded-lg hover:text-[var(--primary)] dark:hover:text-[var(--primary)] whitespace-nowrap">';
								echo '<span class="transition-transform"># ' . esc_html( $tag->name ) . '</span></a>';
							}
						}
						?>
					</div>
				</div>
				<!-- 封面图 -->
				<?php if ( $has_cover ) : ?>
					<a href="<?php the_permalink(); ?>"
					   class="group max-h-[20vh] md:max-h-none mx-4 mt-4 -mb-2 md:mb-0 md:mx-0 md:mt-0 md:w-[28%] relative md:absolute md:top-3 md:bottom-3 md:right-3 rounded-xl overflow-hidden active:scale-95"
					   aria-label="<?php the_title_attribute(); ?>">
						<div class="absolute pointer-events-none z-10 w-full h-full group-hover:bg-black/30 group-active:bg-black/50 transition"></div>
						<div class="absolute pointer-events-none z-20 w-full h-full flex items-center justify-center">
							<svg class="transition opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 text-white text-5xl" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
						</div>
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
					</a>
				<?php else : ?>
					<!-- 无封面时的进入按钮 -->
					<a href="<?php the_permalink(); ?>"
					   aria-label="<?php the_title_attribute(); ?>"
					   class="!hidden md:!flex btn-regular w-[3.25rem] absolute right-3 top-3 bottom-3 rounded-xl bg-[var(--enter-btn-bg)] hover:bg-[var(--enter-btn-bg-hover)] active:bg-[var(--enter-btn-bg-active)] active:scale-95">
						<svg class="transition text-[var(--primary)] text-4xl mx-auto" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
					</a>
				<?php endif; ?>
			</div>
			<!-- 移动端分隔线 -->
			<div class="transition border-t-[1px] border-dashed mx-6 border-black/10 dark:border-white/[0.15] last:border-t-0 md:hidden"></div>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p class="post-list__empty text-50 text-center py-12"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();

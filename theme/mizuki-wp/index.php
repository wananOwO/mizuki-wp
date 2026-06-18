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
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative transition">
				<!-- 文字区 -->
				<div class="pl-6 md:pl-9 pr-6 md:pr-2 pt-6 md:pt-7 pb-6 relative w-full md:w-[calc(100%_-_var(--coverWidth)_-_0.75rem)]">
					<!-- 标题 -->
					<a href="<?php the_permalink(); ?>"
					   class="transition group w-full block font-bold mb-3 text-3xl text-90 hover:text-[var(--primary)]
					          before:w-1 before:h-5 before:rounded-md before:bg-[var(--primary)] before:absolute">
						<?php the_title(); ?>
					</a>
					<!-- 元信息 -->
					<div class="flex flex-wrap gap-4 mb-4 text-50 text-sm transition">
						<div class="flex flex-row items-center">
							<div class="meta-icon transition"><!-- 日历 SVG --></div>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
						<div class="flex flex-row items-center">
							<div class="meta-icon transition"><!-- 时钟 SVG --></div>
							<span><?php echo esc_html( sprintf( __( '%d 分钟', 'mizuki' ), mizuki_reading_time() ) ); ?></span>
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
								echo '<a href="' . esc_url( get_tag_link( $tag ) ) . '" class="link-lg transition text-50 text-xs font-medium px-2 py-1 rounded-lg">';
								echo '<span class="transition-transform"># ' . esc_html( $tag->name ) . '</span></a>';
							}
						}
						?>
					</div>
				</div>
				<!-- 封面图 -->
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>"
					   class="group max-h-[20vh] md:max-h-none mx-4 mt-4 -mb-2 md:mb-0 md:mx-0 md:mt-0 md:w-[var(--coverWidth)] relative md:absolute md:top-3 md:bottom-3 md:right-3 rounded-xl overflow-hidden active:scale-95"
					   aria-label="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
			<!-- 移动端分隔线 -->
			<div class="transition border-t-[1px] border-dashed mx-6 border-black/10 dark:border-white/[0.15] last:border-t-0 md:hidden"></div>
		<?php endwhile; ?>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p class="post-list__empty text-50 text-center py-12"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();

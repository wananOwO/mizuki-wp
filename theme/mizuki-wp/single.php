<?php
/**
 * 单篇文章。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="post-single onload-animation">
	<?php while ( have_posts() ) : the_post(); ?>
	<div class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative mb-4">
		<div id="post-container" class="card-base z-10 px-6 md:px-9 pt-6">
			<!-- 字数 + 阅读时间 -->
			<div class="flex flex-row text-black/30 dark:text-white/30 gap-5 mb-3 transition onload-animation">
				<div class="flex flex-row items-center">
					<span class="text-sm"><?php echo esc_html( sprintf( __( '%d 字', 'mizuki' ), mizuki_word_count() ) ); ?></span>
				</div>
				<div class="flex flex-row items-center">
					<span class="text-sm"><?php echo esc_html( sprintf( __( '%d 分钟阅读', 'mizuki' ), mizuki_reading_time() ) ); ?></span>
				</div>
			</div>

			<!-- 标题 -->
			<div class="relative onload-animation">
				<div data-pagefind-meta="title" class="transition w-full block font-bold mb-3 text-3xl md:text-4xl">
					<?php the_title(); ?>
				</div>
			</div>

			<!-- 元信息(发布日期、标签、分类) -->
			<div class="onload-animation">
				<div class="flex flex-wrap gap-4 mb-4 text-50 text-sm transition">
					<div class="flex flex-row items-center">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</div>
					<?php
					$cats = get_the_category();
					if ( $cats ) {
						echo '<div class="flex flex-row items-center">';
						echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '" class="text-50 hover:text-[var(--primary)] transition">' . esc_html( $cats[0]->name ) . '</a>';
						echo '</div>';
					}
					?>
				</div>
				<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-5"></div>
			</div>

			<!-- 封面(如有) -->
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="mb-8 rounded-xl overflow-hidden onload-animation">
					<?php the_post_thumbnail( 'full', array( 'id' => 'post-cover', 'class' => 'w-full' ) ); ?>
				</div>
			<?php endif; ?>

			<!-- 正文(必须用这些精确的 class) -->
			<div data-pagefind-body
			     class="prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation">
				<?php the_content(); ?>
			</div>

			<!-- 标签 -->
			<?php the_tags( '<div class="flex flex-wrap gap-2 mb-6">', '', '</div>' ); ?>

			<!-- 上下篇导航 -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
				<?php
				$prev = get_previous_post();
				$next = get_next_post();
				if ( $prev ) {
					echo '<a href="' . esc_url( get_permalink( $prev ) ) . '" class="card-base p-4 transition hover:ring-2 hover:ring-[var(--primary)]">';
					echo '<div class="text-50 text-xs mb-1">' . esc_html__( '上一篇', 'mizuki' ) . '</div>';
					echo '<div class="font-bold text-90">' . esc_html( get_the_title( $prev ) ) . '</div></a>';
				}
				if ( $next ) {
					echo '<a href="' . esc_url( get_permalink( $next ) ) . '" class="card-base p-4 transition hover:ring-2 hover:ring-[var(--primary)]">';
					echo '<div class="text-50 text-xs mb-1">' . esc_html__( '下一篇', 'mizuki' ) . '</div>';
					echo '<div class="font-bold text-90">' . esc_html( get_the_title( $next ) ) . '</div></a>';
				}
				?>
			</div>

			<!-- 评论 -->
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div><!-- #post-container -->
	</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();

<?php
/**
 * Template Name: 友链
 *
 * 友链页面模板 - 以网格卡片形式展示所有友链条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="friends-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '友链', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		$friend_query = new WP_Query( array(
			'post_type'      => 'mizuki_friend',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		) );

		if ( $friend_query->have_posts() ) :
		?>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
			<?php while ( $friend_query->have_posts() ) : $friend_query->the_post();
				$friend_url  = get_post_meta( get_the_ID(), '_mizuki_friend_url', true );
				$friend_desc = get_post_meta( get_the_ID(), '_mizuki_friend_desc', true );
			?>
			<a href="<?php echo esc_url( $friend_url ? $friend_url : '#' ); ?>"
			   class="card-base flex items-center gap-4 p-4 transition hover:ring-2 hover:ring-[var(--primary)] group"
			   <?php echo $friend_url ? 'target="_blank" rel="noopener"' : ''; ?>>
				<div class="shrink-0 w-14 h-14 rounded-full overflow-hidden bg-[var(--primary)]/10 flex items-center justify-center">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'w-full h-full object-cover' ) ); ?>
					<?php else : ?>
						<span class="text-2xl font-bold text-[var(--primary)]"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="min-w-0 flex-1">
					<h3 class="font-bold text-90 truncate transition group-hover:text-[var(--primary)]">
						<?php the_title(); ?>
					</h3>
					<?php if ( $friend_desc ) : ?>
					<p class="text-sm text-50 truncate transition mt-1">
						<?php echo esc_html( $friend_desc ); ?>
					</p>
					<?php endif; ?>
				</div>
			</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无友链。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();

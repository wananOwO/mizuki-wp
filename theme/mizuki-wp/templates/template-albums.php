<?php
/**
 * Template Name: 相册 (Albums)
 *
 * 相册页面 — 与 Mizuki dist/albums 一致:card-base + 标题 + 相册封面卡片网格。
 * 每个相册(mizuki_album CPT)= 封面(特色图)+ 多张照片(_mizuki_album_images,每行一个 URL)。
 * 点击封面用 Fancybox 打开该相册的照片画廊。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();

$mz_albums = new WP_Query(
	array(
		'post_type'      => 'mizuki_album',
		'post_status'    => 'publish',
		'posts_per_page'         => 200,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	'update_post_meta_cache' => true,
	)
);
?>
<div class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative min-h-32">
	<div class="card-base z-10 px-6 sm:px-9 py-6 relative w-full">
		<div class="flex flex-col items-start justify-center mb-8">
			<h1 class="text-4xl font-bold text-black/90 dark:text-white/90 mb-2 relative before:w-1 before:h-8 before:rounded-md before:bg-[var(--primary)] before:absolute before:top-1/2 before:-translate-y-1/2 before:-left-4"><?php the_title(); ?></h1>
			<p class="text-lg text-black/60 dark:text-white/60"><?php esc_html_e( '记录生活中的美好瞬间', 'mizuki' ); ?></p>
		</div>

		<?php if ( $mz_albums->have_posts() ) : ?>
		<div class="albums-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
			<?php
			while ( $mz_albums->have_posts() ) :
				$mz_albums->the_post();
				$album_id = get_the_ID();
				$raw      = (string) get_post_meta( $album_id, '_mizuki_album_images', true );
				$photos   = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n]+/', $raw ) ) ) );
				$cover    = has_post_thumbnail() ? get_the_post_thumbnail_url( $album_id, 'large' ) : ( $photos ? $photos[0] : '' );
				$count    = count( $photos );
				$group    = 'album-' . $album_id;
				if ( ! $cover ) {
					continue;
				}
				?>
				<a href="<?php echo esc_url( $cover ); ?>" data-fancybox="<?php echo esc_attr( $group ); ?>" data-caption="<?php echo esc_attr( get_the_title() ); ?>" class="album-card group relative block overflow-hidden rounded-xl transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
					<div class="aspect-[4/3] relative overflow-hidden">
						<img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-full h-full object-cover pointer-events-none transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async">
						<?php if ( $count > 0 ) : ?>
						<div class="absolute top-2 right-2 flex items-center gap-1.5">
							<div class="px-2 py-1 rounded-full text-xs text-white font-medium bg-black/50 backdrop-blur-sm"><?php echo esc_html( sprintf( _n( '%d 张', '%d 张', $count, 'mizuki' ), $count ) ); ?></div>
						</div>
						<?php endif; ?>
						<div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
						<div class="absolute bottom-0 left-0 right-0 p-4">
							<h3 class="font-bold text-base text-white line-clamp-1 drop-shadow-lg"><?php the_title(); ?></h3>
							<?php if ( has_excerpt() ) : ?>
							<p class="text-xs text-white/80 line-clamp-1 mt-0.5 drop-shadow"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</a>
				<?php
				// 该相册其余照片(隐藏的 Fancybox 链接,与封面同组,点击封面即可翻看)。
				if ( $count > 1 ) {
					echo '<div class="hidden">';
					foreach ( array_slice( $photos, ( has_post_thumbnail() ? 0 : 1 ) ) as $ph ) {
						printf(
							'<a href="%1$s" data-fancybox="%2$s" data-caption="%3$s"></a>',
							esc_url( $ph ),
							esc_attr( $group ),
							esc_attr( get_the_title() )
						);
					}
					echo '</div>';
				}
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php else : ?>
			<p class="text-50 text-center py-8"><?php esc_html_e( '暂无相册。在后台「相册」中添加,设置特色图作为封面,并在「相册图片」中每行填写一个照片 URL。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();

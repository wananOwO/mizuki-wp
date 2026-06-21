<?php
/**
 * 单个页面。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="page-single onload-animation">
	<?php while ( have_posts() ) : the_post(); ?>
	<div <?php post_class( 'flex w-full rounded-[var(--radius-large)] overflow-hidden relative mb-4' ); ?>>
		<div class="card-base z-10 px-6 md:px-9 pt-6 pb-6">
			<h1 class="transition w-full block font-bold mb-3 text-3xl md:text-4xl text-90">
				<?php the_title(); ?>
			</h1>
			<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-5"></div>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="mb-8 rounded-xl overflow-hidden">
					<?php the_post_thumbnail( 'full', array( 'class' => 'w-full' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation">
				<?php the_content(); ?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();

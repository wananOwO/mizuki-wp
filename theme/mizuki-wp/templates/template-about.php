<?php
/**
 * Template Name: 关于 (About)
 *
 * 关于页面 — 与 Mizuki dist/about 一致:card-base + 强调条标题 + 正文 prose。
 * 正文取自页面内容(在后台「页面」编辑器中撰写)。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative min-h-32">
	<div class="card-base z-10 px-9 py-6 relative w-full">
		<div class="flex flex-col items-start justify-center mb-8">
			<h1 class="text-4xl font-bold text-black/90 dark:text-white/90 mb-2 relative before:w-1 before:h-8 before:rounded-md before:bg-[var(--primary)] before:absolute before:top-1/2 before:-translate-y-1/2 before:-left-4"><?php the_title(); ?></h1>
		</div>
		<div data-pagefind-body class="prose dark:prose-invert prose-base !max-w-none custom-md markdown-content mt-2">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</div>
<?php
get_footer();

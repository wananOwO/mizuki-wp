<?php
/**
 * 原生评论模板。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area card-base p-6 mt-4 mb-4">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title font-bold text-xl mb-4 text-90">
			<?php
			echo esc_html(
				sprintf(
					_n( '%s 条评论', '%s 条评论', get_comments_number(), 'mizuki' ),
					number_format_i18n( get_comments_number() )
				)
			);
			?>
		</h2>
		<ol class="comment-list space-y-4">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
					'callback'    => 'mizuki_comment_template',
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments text-50 text-center py-4"><?php esc_html_e( '评论已关闭。', 'mizuki' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'    => 'comment-form card-base p-6',
			'title_reply'   => __( '发表评论', 'mizuki' ),
			'label_submit'  => __( '提交', 'mizuki' ),
			'comment_field' => '<div class="comment-form-field mb-4"><textarea id="comment" name="comment" class="w-full p-3 rounded-lg bg-[var(--card-bg)] border border-[var(--line-divider)]" rows="5" required></textarea></div>',
		)
	);
	?>
</section>

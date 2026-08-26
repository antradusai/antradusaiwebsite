<?php
/**
 * Comments.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div class="ant-comments" id="comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="ant-comments-title">
			<?php
			$antradus_count = (int) get_comments_number();
			printf(
				/* translators: %d: comment count. */
				esc_html( _n( '%d comment', '%d comments', $antradus_count, 'antradus' ) ),
				$antradus_count
			);
			?>
		</h2>

		<ol class="ant-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 44,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'class'     => 'ant-pagination',
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="ant-empty-note"><?php esc_html_e( 'Comments are closed on this one.', 'antradus' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => __( 'Leave a comment', 'antradus' ),
			'class_submit'       => 'ant-btn ant-btn--primary',
			'comment_notes_before' => '',
		)
	);
	?>
</div>

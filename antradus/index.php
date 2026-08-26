<?php
/**
 * The fallback template: the posts index, archives and anything else without a
 * more specific file.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$antradus_title = '';
$antradus_sub   = '';

if ( is_home() ) {
	$antradus_title = antradus_opt( 'blog_title', get_bloginfo( 'name' ) );
	$antradus_sub   = antradus_opt( 'blog_sub', '' );
} elseif ( is_category() || is_tag() || is_tax() ) {
	$antradus_title = single_term_title( '', false );
	$antradus_sub   = wp_strip_all_tags( (string) term_description() );
} elseif ( is_author() ) {
	$antradus_title = get_the_author();
} elseif ( is_date() ) {
	$antradus_title = get_the_archive_title();
} else {
	$antradus_title = get_the_archive_title();
}
?>

<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<?php
		antradus_section_head(
			is_home() ? antradus_opt( 'blog_eyebrow', '' ) : __( 'Archive', 'antradus' ),
			$antradus_title,
			$antradus_sub
		);
		?>
	</div>
</section>

<section class="ant-section ant-section--top">
	<div class="ant-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="ant-grid ant-grid--cards">
				<?php
				while ( have_posts() ) {
					the_post();
					antradus_post_card();
				}
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'class'     => 'ant-pagination',
					'mid_size'  => 1,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				)
			);
			?>
		<?php else : ?>
			<div class="ant-empty">
				<p class="ant-empty-title"><?php esc_html_e( 'Nothing here yet', 'antradus' ); ?></p>
				<p><?php echo esc_html( antradus_opt( 'blog_empty', '' ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();

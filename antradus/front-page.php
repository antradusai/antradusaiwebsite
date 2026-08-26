<?php
/**
 * The front page.
 *
 * When the front page is set to show your latest posts, this hands over to the
 * article index so the site still makes sense; otherwise it is the designed
 * home page.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() ) {
	?>
	<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
		<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
		<span class="ant-mesh" aria-hidden="true"></span>
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'blog_eyebrow', '' ),
				antradus_opt( 'blog_title', get_bloginfo( 'name' ) ),
				antradus_opt( 'blog_sub', '' )
			);
			?>
		</div>
	</section>
	<section class="ant-section ant-section--top">
		<div class="ant-wrap"><?php antradus_render_blog_index(); ?></div>
	</section>
	<?php
} else {
	get_template_part( 'template-parts/home' );
}

get_footer();

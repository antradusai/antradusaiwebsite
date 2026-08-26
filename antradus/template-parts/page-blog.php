<?php
/**
 * The article index.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<?php
		antradus_section_head(
			antradus_opt( 'blog_eyebrow', '' ),
			antradus_opt( 'blog_title', get_the_title() ),
			antradus_opt( 'blog_sub', '' )
		);
		?>
	</div>
</section>

<section class="ant-section ant-section--top">
	<div class="ant-wrap">
		<?php antradus_render_blog_index(); ?>
	</div>
</section>

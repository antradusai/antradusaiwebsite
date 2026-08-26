<?php
/**
 * Home - the questions.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_faq_title', '' );
if ( '' === trim( $antradus_title ) || ! antradus_rows( 'home_faq_items' ) ) {
	return;
}
?>
<section class="ant-section ant-band" id="faq">
	<div class="ant-wrap ant-wrap--narrow">
		<?php
		antradus_section_head(
			antradus_opt( 'home_faq_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_faq_sub', '' )
		);
		antradus_render_faq( 'home_faq_items' );
		?>
	</div>
</section>

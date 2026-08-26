<?php
/**
 * The Docs hub.
 *
 * Three ways this page can be filled, in order of preference:
 *
 *   1. the theme renders the guides itself, designed and searchable - this is
 *      what happens whenever the Antradus AI plugin has published any;
 *   2. otherwise, whatever is in the editor, in case the page was written by
 *      hand or carries the plugin's own shortcode;
 *   3. otherwise, a note saying what to add - rather than a blank band.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_own = antradus_docs_available();

$antradus_content = '';
if ( ! $antradus_own && have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$antradus_content = apply_filters( 'the_content', get_the_content() );
	}
	rewind_posts();
}
$antradus_has_content = '' !== trim( wp_strip_all_tags( $antradus_content ) );
?>
<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<?php
		antradus_section_head(
			antradus_opt( 'docs_eyebrow', '' ),
			antradus_opt( 'docs_title', get_the_title() ),
			antradus_opt( 'docs_sub', '' )
		);
		?>
	</div>
</section>

<section class="ant-section ant-section--top">
	<div class="ant-wrap">
		<?php
		if ( $antradus_own ) {
			antradus_render_docs_hub();
		} elseif ( $antradus_has_content ) {
			echo '<div class="ant-glass ant-docs-panel"><div class="ant-prose ant-docs-index">';
			echo $antradus_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already filtered by the_content; kses here would strip embeds and forms.
			echo '</div></div>';
		} else {
			echo '<div class="ant-glass ant-docs-panel">';
			echo '<p class="ant-empty-note">' . esc_html( antradus_opt( 'docs_note', '' ) ) . '</p>';
			echo '</div>';
		}
		?>
	</div>
</section>

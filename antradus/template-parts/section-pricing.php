<?php
/**
 * Home - the pricing preview. The same plans the pricing page renders.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

if ( ! antradus_on( 'home_price_show', true ) ) {
	return;
}
$antradus_title = antradus_opt( 'home_price_title', '' );
if ( '' === trim( $antradus_title ) || ! antradus_plans() ) {
	return;
}
$antradus_more = antradus_page_url( 'pricing' );
?>
<section class="ant-section" id="pricing">
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_price_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_price_sub', '' )
		);
		?>

		<?php antradus_render_plans(); ?>

		<?php if ( '' !== $antradus_more ) : ?>
			<p class="ant-center ant-more">
				<a href="<?php echo esc_url( $antradus_more ); ?>">
					<?php esc_html_e( 'Compare every feature, plan by plan', 'antradus' ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</p>
		<?php endif; ?>

	</div>
</section>

<?php
/**
 * Home - the product demo: one screenshot, and the list of what comes out.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_demo_title', '' );
if ( '' === trim( $antradus_title ) ) {
	return;
}
$antradus_cards = antradus_rows( 'home_demo_cards' );
?>
<section class="ant-section ant-band" id="demo">
	<span class="ant-orb ant-orb--c" aria-hidden="true"></span>
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_demo_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_demo_sub', '' )
		);
		?>

		<figure class="ant-glass ant-demo-frame">
			<?php
			antradus_image(
				'home_demo_image',
				array(
					'ratio' => '16 / 9',
					'label' => __( 'Product demo screenshot', 'antradus' ),
					'alt'   => '',
				)
			);
			?>
			<?php if ( antradus_opt( 'home_demo_caption', '' ) ) : ?>
				<figcaption class="ant-demo-caption">
					<span class="ant-demo-live" aria-hidden="true"></span>
					<?php echo esc_html( antradus_opt( 'home_demo_caption', '' ) ); ?>
				</figcaption>
			<?php endif; ?>
		</figure>

		<?php if ( $antradus_cards ) : ?>
			<ul class="ant-grid ant-grid--3 ant-outputs">
				<?php foreach ( $antradus_cards as $antradus_card ) : ?>
					<li class="ant-glass ant-output">
						<span class="ant-output-ic">
							<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'spark' ), 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<b><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></b>
						<span><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>

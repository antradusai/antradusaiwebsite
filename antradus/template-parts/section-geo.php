<?php
/**
 * Home - the GEO / SEO / AIO band.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_geo_title', '' );
$antradus_cards = antradus_rows( 'home_geo_cards' );
if ( '' === trim( $antradus_title ) ) {
	return;
}
?>
<section class="ant-section ant-band ant-geo" id="geo">
	<span class="ant-orb ant-orb--e" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-geo-grid">

			<div class="ant-geo-copy">
				<?php
				antradus_section_head(
					antradus_opt( 'home_geo_eyebrow', '' ),
					$antradus_title,
					antradus_opt( 'home_geo_sub', '' ),
					'left'
				);
				?>

				<?php if ( $antradus_cards ) : ?>
					<ul class="ant-geo-list">
						<?php foreach ( $antradus_cards as $antradus_card ) : ?>
							<li>
								<span class="ant-geo-ic">
									<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'spark' ), 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
								</span>
								<span>
									<b><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></b>
									<span><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></span>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="ant-geo-art">
				<div class="ant-glass ant-geo-frame">
					<?php
					antradus_image(
						'home_geo_image',
						array(
							'ratio' => '4 / 3',
							'label' => __( 'GEO section illustration', 'antradus' ),
							'alt'   => '',
						)
					);
					?>
				</div>
			</div>

		</div>
	</div>
</section>

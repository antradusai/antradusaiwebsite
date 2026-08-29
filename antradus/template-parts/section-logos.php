<?php
/**
 * Home - the logo strip.
 *
 * A logo with no image is drawn as a labelled tile rather than a gap, so the
 * row still reads as a row while you are collecting the real artwork.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_logos_title', '' );
$antradus_logos = antradus_rows( 'home_logos' );
if ( '' === trim( $antradus_title ) || ! $antradus_logos ) {
	return;
}
?>
<section class="ant-section ant-section--tight ant-logos">
	<div class="ant-wrap">
		<p class="ant-logos-title"><?php echo esc_html( $antradus_title ); ?></p>
		<ul class="ant-logos-row">
			<?php
			foreach ( $antradus_logos as $antradus_logo ) :
				$antradus_name = antradus_cell( $antradus_logo, 'name', __( 'Logo', 'antradus' ) );
				$antradus_url  = antradus_image_full_url( antradus_cell( $antradus_logo, 'image' ) );
				?>
				<li class="ant-logo">
					<?php if ( $antradus_url ) : ?>
						<img src="<?php echo esc_url( $antradus_url ); ?>" alt="<?php echo esc_attr( $antradus_name ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="ant-logo-slot"><?php echo esc_html( $antradus_name ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

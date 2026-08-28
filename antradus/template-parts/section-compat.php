<?php
/**
 * Compatibility: what goes in on the left, what comes out on the right.
 *
 * Written once and rendered wherever the diagram belongs. The home page shows
 * every source and destination the plugin has; an audience page shows the
 * subset that audience actually uses, under its own keys - so the studio page
 * can list the podcast hosts and leave out the keyword sources without the two
 * diagrams drifting apart in layout.
 *
 * Which set of keys to read arrives as `prefix`: '' for the home page's
 * `home_compat_*`, 'std_' for the studio page's `std_compat_*`.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_p = isset( $args['prefix'] ) ? (string) $args['prefix'] : '';
$antradus_k = ( '' === $antradus_p ) ? 'home_compat_' : $antradus_p . 'compat_';

$antradus_title = antradus_opt( $antradus_k . 'title', '' );
$antradus_in    = antradus_rows( $antradus_k . 'in' );
$antradus_out   = antradus_rows( $antradus_k . 'out' );
if ( '' === trim( $antradus_title ) || ( ! $antradus_in && ! $antradus_out ) ) {
	return;
}

$antradus_centre = antradus_image_url( antradus_opt( $antradus_k . 'center', '' ), 'medium' );
if ( '' === $antradus_centre ) {
	$antradus_centre = antradus_image_url( antradus_opt( 'brand_logo', '' ), 'medium' );
}

?>
<section class="ant-section">
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( $antradus_k . 'eyebrow', '' ),
			$antradus_title,
			antradus_opt( $antradus_k . 'sub', '' )
		);
		?>

		<?php
		/*
		 * The lines between the two columns and the hub are drawn at runtime
		 * from where the pills actually landed, not hard-coded: the number of
		 * sources and destinations is a setting, the pill heights depend on
		 * the words in them, and the whole thing reflows at every width. An
		 * SVG measured after layout is the only version that stays correct.
		 *
		 * It is decorative, so it is aria-hidden and it simply does not appear
		 * on the narrow layout where the columns stack.
		 */
		?>
		<div class="ant-glass ant-compat" data-compat>
			<svg class="ant-compat-flow" data-compat-flow aria-hidden="true" focusable="false" preserveAspectRatio="none"></svg>

			<?php antradus_compat_column( $antradus_in, __( 'Bring it in from', 'antradus' ), 'in' ); ?>

			<div class="ant-compat-hub">
				<span class="ant-compat-ring" aria-hidden="true"></span>
				<span class="ant-compat-mark">
					<?php if ( $antradus_centre ) : ?>
						<img src="<?php echo esc_url( $antradus_centre ); ?>" alt="<?php echo esc_attr( antradus_opt( 'brand_name', '' ) ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<?php echo antradus_icon( 'wave', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
					<?php endif; ?>
				</span>
				<span class="ant-compat-hub-name"><?php echo esc_html( antradus_opt( 'brand_name', '' ) ); ?></span>
			</div>

			<?php antradus_compat_column( $antradus_out, __( 'Send it out to', 'antradus' ), 'out' ); ?>
		</div>

	</div>
</section>

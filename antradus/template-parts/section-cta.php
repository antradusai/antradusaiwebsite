<?php
/**
 * Home - the closing call to action.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_cta_title', '' );
if ( '' === trim( $antradus_title ) ) {
	return;
}
$antradus_note = antradus_lines( antradus_opt( 'home_cta_note', '' ) );
?>
<section class="ant-section">
	<div class="ant-wrap">
		<div class="ant-glass ant-cta">
			<span class="ant-orb ant-orb--f" aria-hidden="true"></span>
			<div class="ant-cta-inner">
				<h2 class="ant-h2"><?php echo antradus_headline( $antradus_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></h2>
				<?php if ( antradus_opt( 'home_cta_sub', '' ) ) : ?>
					<p class="ant-sub"><?php echo esc_html( antradus_opt( 'home_cta_sub', '' ) ); ?></p>
				<?php endif; ?>
				<div class="ant-actions ant-actions--center">
					<?php
					antradus_button( antradus_opt( 'home_cta_btn1', '' ), antradus_opt( 'home_cta_btn1_url', '' ), 'primary' );
					antradus_button( antradus_opt( 'home_cta_btn2', '' ), antradus_opt( 'home_cta_btn2_url', '' ), 'ghost' );
					?>
				</div>
				<?php if ( $antradus_note ) : ?>
					<p class="ant-note ant-center"><?php echo esc_html( implode( ' · ', $antradus_note ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

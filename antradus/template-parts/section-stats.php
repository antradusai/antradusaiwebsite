<?php
/**
 * Home - the numbers strip.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_stats = antradus_rows( 'home_stats' );
if ( ! $antradus_stats ) {
	return;
}
?>
<section class="ant-section ant-section--tight">
	<div class="ant-wrap">
		<div class="ant-glass ant-stats">
			<?php foreach ( $antradus_stats as $antradus_stat ) : ?>
				<div class="ant-stat">
					<b><?php echo esc_html( antradus_cell( $antradus_stat, 'value' ) ); ?></b>
					<span><?php echo esc_html( antradus_cell( $antradus_stat, 'label' ) ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
/**
 * Home - the economics table.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_cost_title', '' );
$antradus_rows  = antradus_rows( 'home_cost_rows' );
if ( '' === trim( $antradus_title ) || ! $antradus_rows ) {
	return;
}
?>
<section class="ant-section ant-band">
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_cost_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_cost_sub', '' )
		);
		?>

		<div class="ant-glass ant-cost">
			<div class="ant-cost-scroll">
				<table class="ant-cost-table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'What it takes', 'antradus' ); ?></th>
							<th scope="col"><?php esc_html_e( 'The usual way', 'antradus' ); ?></th>
							<th scope="col" class="ant-cost-us"><?php echo esc_html( antradus_opt( 'brand_name', 'Antradus AI' ) ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $antradus_rows as $antradus_row ) : ?>
							<tr>
								<td class="ant-cost-label"><?php echo esc_html( antradus_cell( $antradus_row, 'label' ) ); ?></td>
								<td class="ant-cost-them"><?php echo esc_html( antradus_cell( $antradus_row, 'them' ) ); ?></td>
								<td class="ant-cost-us">
									<?php echo antradus_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
									<span><?php echo esc_html( antradus_cell( $antradus_row, 'us' ) ); ?></span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php if ( antradus_opt( 'home_cost_note', '' ) ) : ?>
				<p class="ant-cost-note"><?php echo esc_html( antradus_opt( 'home_cost_note', '' ) ); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>

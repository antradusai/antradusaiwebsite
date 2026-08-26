<?php
/**
 * The Pricing page.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_trust = antradus_lines( antradus_opt( 'price_trust', '' ) );
$antradus_sales = antradus_link( antradus_opt( 'price_sales_url', '' ) );
?>

<section class="ant-section ant-page-hero ant-page-hero--center">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<?php
		antradus_section_head(
			antradus_opt( 'price_eyebrow', '' ),
			antradus_opt( 'price_title', get_the_title() ),
			antradus_opt( 'price_sub', '' )
		);
		?>

		<?php if ( antradus_opt( 'price_note', '' ) ) : ?>
			<p class="ant-price-note"><?php echo esc_html( antradus_opt( 'price_note', '' ) ); ?></p>
		<?php endif; ?>

		<?php antradus_render_plans(); ?>
	</div>
</section>

<?php if ( antradus_on( 'price_cmp_show', true ) && antradus_rows( 'price_cmp_rows' ) ) : ?>
	<section class="ant-section ant-band" id="compare">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				'',
				antradus_opt( 'price_cmp_title', '' ),
				antradus_opt( 'price_cmp_sub', '' )
			);
			antradus_render_compare();
			?>

			<?php if ( antradus_opt( 'price_sales_text', '' ) && '' !== $antradus_sales ) : ?>
				<div class="ant-glass ant-sales">
					<p><?php echo esc_html( antradus_opt( 'price_sales_text', '' ) ); ?></p>
					<?php antradus_button( antradus_opt( 'price_sales_btn', '' ), antradus_opt( 'price_sales_url', '' ), 'soft' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_rows( 'price_faq_items' ) ) : ?>
	<section class="ant-section">
		<div class="ant-wrap ant-wrap--narrow">
			<?php
			antradus_section_head( '', antradus_opt( 'price_faq_title', '' ), '' );
			antradus_render_faq( 'price_faq_items' );
			?>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_trust ) : ?>
	<div class="ant-wrap">
		<ul class="ant-trustbar ant-trustbar--strong">
			<?php foreach ( $antradus_trust as $antradus_line ) : ?>
				<li class="ant-trustbar-item">
					<span class="ant-trustbar-mark" aria-hidden="true">
						<?php echo antradus_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
					</span>
					<span><?php echo esc_html( $antradus_line ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

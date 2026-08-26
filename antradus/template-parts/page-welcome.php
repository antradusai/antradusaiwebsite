<?php
/**
 * The Welcome / newsletter page.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_bullets = antradus_lines( antradus_opt( 'welcome_bullets', '' ) );

$antradus_form_html = antradus_form_html( antradus_opt( 'welcome_form', '' ) );
?>
<section class="ant-section ant-page-hero">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-orb ant-orb--b" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>

	<div class="ant-wrap">
		<div class="ant-welcome-grid">

			<div class="ant-welcome-copy">
				<?php
				antradus_section_head(
					antradus_opt( 'welcome_eyebrow', '' ),
					antradus_opt( 'welcome_title', get_the_title() ),
					antradus_opt( 'welcome_sub', '' ),
					'left'
				);
				?>

				<?php if ( $antradus_bullets ) : ?>
					<ul class="ant-ticks ant-ticks--lg">
						<?php foreach ( $antradus_bullets as $antradus_line ) : ?>
							<li>
								<?php echo antradus_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
								<span><?php echo esc_html( $antradus_line ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="ant-welcome-art">
					<?php
					antradus_image(
						'welcome_image',
						array(
							'ratio' => '16 / 9',
							'label' => __( 'Newsletter page image', 'antradus' ),
							'alt'   => '',
						)
					);
					?>
				</div>
			</div>

			<div class="ant-glass ant-form-panel ant-welcome-panel">
				<div class="ant-form-body">
					<?php if ( '' !== $antradus_form_html ) : ?>
						<?php echo $antradus_form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the form plugin's own output; kses would strip every input. ?>
					<?php else : ?>
						<p class="ant-empty-note">
							<?php esc_html_e( 'No form is showing here. Either the shortcode in Antradus Content points at a form plugin that is not active, or there is no form on this page yet.', 'antradus' ); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php if ( antradus_opt( 'welcome_note', '' ) ) : ?>
					<p class="ant-form-hint ant-center"><?php echo esc_html( antradus_opt( 'welcome_note', '' ) ); ?></p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>

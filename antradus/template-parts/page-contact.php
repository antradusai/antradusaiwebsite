<?php
/**
 * The Contact page, and the affiliate programme below it.
 *
 * Both forms are whichever shortcode you configured. Leave the contact
 * shortcode empty and the page's own content is used instead, which is how a
 * form block placed in the editor keeps working.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_cards = antradus_rows( 'contact_cards' );
$antradus_steps = antradus_rows( 'aff_steps' );
$antradus_trust = antradus_lines( antradus_opt( 'contact_trust', '' ) );

$antradus_form_html = antradus_form_html( antradus_opt( 'contact_form', '' ) );
$antradus_aff_html  = antradus_form_html( antradus_opt( 'aff_form', '' ), false );
?>

<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<?php
		antradus_section_head(
			antradus_opt( 'contact_eyebrow', '' ),
			antradus_opt( 'contact_title', get_the_title() ),
			antradus_opt( 'contact_sub', '' )
		);
		?>
	</div>
</section>

<section class="ant-section ant-section--top">
	<div class="ant-wrap">
		<div class="ant-contact-grid">

			<div class="ant-contact-cards">
				<?php
				foreach ( $antradus_cards as $antradus_card ) :
					$antradus_url = antradus_link( antradus_cell( $antradus_card, 'link' ) );
					if ( '' === $antradus_url && 0 === strpos( antradus_cell( $antradus_card, 'link' ), 'mailto:' ) ) {
						$antradus_url = antradus_cell( $antradus_card, 'link' );
					}
					?>
					<?php
					/*
					 * Icon and title on one row. Previously the icon tile was a
					 * bare <span> among the card's other bare <span>s, so the
					 * rule styling the card's body text also hit the tile and
					 * knocked the glyph out of its centre. Giving the head its
					 * own element makes that class of collision impossible
					 * rather than merely fixed.
					 */
					?>
					<article class="ant-glass ant-contact-card">
						<div class="ant-contact-head">
							<span class="ant-contact-ic" aria-hidden="true">
								<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'mail' ), 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
							</span>
							<b><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></b>
						</div>
						<span><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></span>
						<?php if ( '' !== $antradus_url && antradus_cell( $antradus_card, 'label' ) ) : ?>
							<a class="ant-contact-link" href="<?php echo esc_url( $antradus_url ); ?>">
								<?php echo esc_html( antradus_cell( $antradus_card, 'label' ) ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="ant-glass ant-form-panel">
				<?php if ( antradus_opt( 'contact_form_head', '' ) ) : ?>
					<h2 class="ant-form-title"><?php echo esc_html( antradus_opt( 'contact_form_head', '' ) ); ?></h2>
				<?php endif; ?>
				<?php if ( antradus_opt( 'contact_form_hint', '' ) ) : ?>
					<p class="ant-form-hint"><?php echo esc_html( antradus_opt( 'contact_form_hint', '' ) ); ?></p>
				<?php endif; ?>

				<div class="ant-form-body">
					<?php if ( '' !== $antradus_form_html ) : ?>
						<?php echo $antradus_form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the form plugin's own output; kses would strip every input. ?>
					<?php else : ?>
						<p class="ant-empty-note">
							<?php esc_html_e( 'No form is showing here. Either the shortcode in Antradus Content points at a form plugin that is not active, or there is no form on this page yet.', 'antradus' ); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
</section>

<?php if ( antradus_on( 'aff_show', true ) && antradus_opt( 'aff_title', '' ) ) : ?>
	<section class="ant-section ant-band" id="affiliate">
		<span class="ant-orb ant-orb--c" aria-hidden="true"></span>
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'aff_eyebrow', '' ),
				antradus_opt( 'aff_title', '' ),
				antradus_opt( 'aff_sub', '' )
			);
			?>

			<?php if ( $antradus_steps ) : ?>
				<div class="ant-grid ant-grid--3 ant-steps">
					<?php foreach ( $antradus_steps as $antradus_step ) : ?>
						<article class="ant-glass ant-step">
							<span class="ant-step-num"><?php echo esc_html( antradus_cell( $antradus_step, 'num' ) ); ?></span>
							<b><?php echo esc_html( antradus_cell( $antradus_step, 'title' ) ); ?></b>
							<span><?php echo esc_html( antradus_cell( $antradus_step, 'text' ) ); ?></span>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $antradus_aff_html ) : ?>
				<div class="ant-glass ant-form-panel ant-form-panel--wide">
					<?php if ( antradus_opt( 'aff_form_head', '' ) ) : ?>
						<h3 class="ant-form-title"><?php echo esc_html( antradus_opt( 'aff_form_head', '' ) ); ?></h3>
					<?php endif; ?>
					<?php if ( antradus_opt( 'aff_form_hint', '' ) ) : ?>
						<p class="ant-form-hint"><?php echo esc_html( antradus_opt( 'aff_form_hint', '' ) ); ?></p>
					<?php endif; ?>
					<div class="ant-form-body"><?php echo $antradus_aff_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the form plugin's own output; kses would strip every input. ?></div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_trust ) : ?>
	<div class="ant-wrap">
		<ul class="ant-trustbar">
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

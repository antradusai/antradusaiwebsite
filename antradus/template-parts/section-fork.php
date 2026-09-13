<?php
/**
 * Home - the two audiences, side by side.
 *
 * Antradus is one product sold to two kinds of buyer, and which one a visitor
 * is decides both the feature list they care about and the plan they end up
 * on. The hero says so in two links; this says so in full, with the price of
 * each path and its own call to action, and sends each reader to the page
 * written for them.
 *
 * Cards are a repeater, so a third audience is a card rather than a template.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_fork_title', '' );
$antradus_cards = antradus_rows( 'home_fork_cards' );
if ( '' === trim( $antradus_title ) || ! $antradus_cards ) {
	return;
}
?>
<section class="ant-section" id="who">
	<span class="ant-orb ant-orb--c" aria-hidden="true"></span>
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_fork_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_fork_sub', '' )
		);
		?>

		<div class="ant-forks">
			<?php
			foreach ( $antradus_cards as $antradus_card ) :
				$antradus_items = antradus_lines( antradus_cell( $antradus_card, 'items' ) );
				$antradus_alt   = antradus_link( antradus_cell( $antradus_card, 'alt_url' ) );
				?>
				<article class="ant-glass ant-fork">
					<header class="ant-fork-head">
						<?php if ( antradus_cell( $antradus_card, 'name' ) ) : ?>
							<span class="ant-fork-plan"><?php echo esc_html( antradus_cell( $antradus_card, 'name' ) ); ?></span>
						<?php endif; ?>
					</header>

					<h3 class="ant-fork-title"><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></h3>

					<?php if ( antradus_cell( $antradus_card, 'text' ) ) : ?>
						<p class="ant-fork-text"><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></p>
					<?php endif; ?>

					<?php if ( $antradus_items ) : ?>
						<ul class="ant-ticks ant-fork-list">
							<?php foreach ( $antradus_items as $antradus_item ) : ?>
								<li>
									<?php echo antradus_icon( 'check', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
									<span><?php echo esc_html( $antradus_item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( antradus_cell( $antradus_card, 'price' ) ) : ?>
						<p class="ant-fork-price"><?php echo esc_html( antradus_cell( $antradus_card, 'price' ) ); ?></p>
					<?php endif; ?>

					<div class="ant-fork-actions">
						<?php antradus_button( antradus_cell( $antradus_card, 'cta' ), antradus_cell( $antradus_card, 'cta_url' ), 'primary' ); ?>
						<?php if ( antradus_cell( $antradus_card, 'alt' ) && '' !== $antradus_alt ) : ?>
							<a class="ant-fork-alt" href="<?php echo esc_url( $antradus_alt ); ?>">
								<?php echo esc_html( antradus_cell( $antradus_card, 'alt' ) ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( antradus_opt( 'home_fork_note', '' ) ) : ?>
			<p class="ant-note ant-center ant-fork-note"><?php echo esc_html( antradus_opt( 'home_fork_note', '' ) ); ?></p>
		<?php endif; ?>

	</div>
</section>

<?php
/**
 * Home - where it runs: editors, queue, keys.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_run_title', '' );
$antradus_cards = antradus_rows( 'home_run_cards' );
if ( '' === trim( $antradus_title ) || ! $antradus_cards ) {
	return;
}
?>
<section class="ant-section ant-band ant-band--deep">
	<span class="ant-orb ant-orb--d" aria-hidden="true"></span>
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_run_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_run_sub', '' )
		);
		?>

		<div class="ant-grid ant-grid--3">
			<?php foreach ( $antradus_cards as $antradus_card ) : ?>
				<article class="ant-glass ant-runcard">
					<span class="ant-runcard-ic">
						<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'spark' ), 22 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
					</span>
					<h3><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></h3>
					<ul class="ant-ticks">
						<?php foreach ( antradus_lines( antradus_cell( $antradus_card, 'text' ) ) as $antradus_line ) : ?>
							<li>
								<?php echo antradus_icon( 'check', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
								<span><?php echo esc_html( $antradus_line ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

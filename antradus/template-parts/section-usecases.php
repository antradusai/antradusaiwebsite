<?php
/**
 * Home - who it is for.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_use_title', '' );
$antradus_cards = antradus_rows( 'home_use_cards' );
if ( '' === trim( $antradus_title ) || ! $antradus_cards ) {
	return;
}
?>
<section class="ant-section">
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_use_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_use_sub', '' )
		);
		?>

		<div class="ant-grid ant-grid--3 ant-uses">
			<?php foreach ( $antradus_cards as $antradus_card ) : ?>
				<article class="ant-glass ant-use">
					<span class="ant-use-ic">
						<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'users' ), 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
					</span>
					<h3><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></h3>
					<p><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

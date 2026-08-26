<?php
/**
 * Home - the four core features, each with room for an illustration.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_feat_title', '' );
$antradus_cards = antradus_rows( 'home_feat_cards' );
if ( '' === trim( $antradus_title ) || ! $antradus_cards ) {
	return;
}
?>
<section class="ant-section" id="features">
	<div class="ant-wrap">

		<?php
		antradus_section_head(
			antradus_opt( 'home_feat_eyebrow', '' ),
			$antradus_title,
			antradus_opt( 'home_feat_sub', '' )
		);
		?>

		<div class="ant-grid ant-grid--2 ant-features">
			<?php
			foreach ( $antradus_cards as $antradus_card ) :
				$antradus_img = antradus_image_url( antradus_cell( $antradus_card, 'image' ), 'large' );
				?>
				<article class="ant-glass ant-feature">
					<div class="ant-feature-copy">
						<span class="ant-feature-ic">
							<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'spark' ), 22 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<h3><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></h3>
						<p><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></p>
					</div>
					<div class="ant-feature-art">
						<?php if ( $antradus_img ) : ?>
							<img class="ant-media" src="<?php echo esc_url( $antradus_img ); ?>" alt="" loading="lazy" decoding="async" style="aspect-ratio:16 / 10">
						<?php else : ?>
							<?php
							antradus_placeholder(
								sprintf(
									/* translators: %s: feature name. */
									__( '%s illustration', 'antradus' ),
									antradus_cell( $antradus_card, 'title', __( 'Feature', 'antradus' ) )
								),
								'16 / 10'
							);
							?>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

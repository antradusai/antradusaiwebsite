<?php
/**
 * The trends band: what is being searched today, checked before it is written.
 *
 * Written for the audience pages and read under a prefix, the way the
 * compatibility diagram is, so a second audience can carry it later without a
 * second copy of this file. Today only the publisher page fills these keys in.
 *
 * The section has two halves because the feature does. The list on the left is
 * what the plugin finds; the numbered strip below it is the part that matters
 * commercially - a trend is verified against a real article published today,
 * in the site's own timezone, before it is allowed into the queue. Selling the
 * list alone would be selling the half that every competitor also has.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_p = isset( $args['prefix'] ) ? (string) $args['prefix'] : '';
$antradus_k = $antradus_p . 'trends_';

$antradus_title = antradus_opt( $antradus_k . 'title', '' );
if ( '' === trim( $antradus_title ) ) {
	return;
}

$antradus_points = antradus_rows( $antradus_k . 'points' );
$antradus_steps  = antradus_rows( $antradus_k . 'steps' );
?>
<section class="ant-section ant-band ant-geo" id="trends">
	<span class="ant-orb ant-orb--c" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-geo-grid">

			<div class="ant-geo-copy">
				<?php
				antradus_section_head(
					antradus_opt( $antradus_k . 'eyebrow', '' ),
					$antradus_title,
					antradus_opt( $antradus_k . 'sub', '' ),
					'left'
				);
				?>

				<?php if ( $antradus_points ) : ?>
					<ul class="ant-geo-list">
						<?php foreach ( $antradus_points as $antradus_point ) : ?>
							<li>
								<span class="ant-geo-ic">
									<?php echo antradus_icon( antradus_cell( $antradus_point, 'icon', 'spark' ), 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
								</span>
								<span>
									<b><?php echo esc_html( antradus_cell( $antradus_point, 'title' ) ); ?></b>
									<span><?php echo esc_html( antradus_cell( $antradus_point, 'text' ) ); ?></span>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="ant-geo-art">
				<div class="ant-glass ant-geo-frame">
					<?php
					antradus_image(
						$antradus_k . 'image',
						array(
							'ratio' => '4 / 3',
							'label' => __( 'Trends section illustration', 'antradus' ),
							'alt'   => '',
						)
					);
					?>
				</div>
			</div>

		</div>

		<?php if ( $antradus_steps ) : ?>
			<?php
			/*
			 * Numbered by position, like every other numbered strip in this
			 * theme: the editor may add or reorder a step, and a typed number
			 * would be wrong the moment they did.
			 */
			?>
			<ol class="ant-grid ant-grid--4 ant-steps ant-flow ant-trend-steps">
				<?php foreach ( $antradus_steps as $antradus_i => $antradus_step ) : ?>
					<li class="ant-glass ant-step">
						<span class="ant-step-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $antradus_i + 1 ) ); ?></span>
						<b><?php echo esc_html( antradus_cell( $antradus_step, 'title' ) ); ?></b>
						<span><?php echo esc_html( antradus_cell( $antradus_step, 'text' ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php if ( antradus_opt( $antradus_k . 'note', '' ) ) : ?>
			<p class="ant-note ant-center ant-trend-note"><?php echo esc_html( antradus_opt( $antradus_k . 'note', '' ) ); ?></p>
		<?php endif; ?>

	</div>
</section>

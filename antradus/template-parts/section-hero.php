<?php
/**
 * Home - the hero.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_title = antradus_opt( 'home_hero_title', '' );
if ( '' === trim( $antradus_title ) ) {
	return;
}
$antradus_chips = array_slice( antradus_lines( antradus_opt( 'home_hero_chips', '' ) ), 0, 5 );
?>
<section class="ant-section ant-hero">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-orb ant-orb--b" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>

	<div class="ant-wrap">
		<div class="ant-hero-grid">

			<div class="ant-hero-copy">
				<?php if ( antradus_opt( 'home_hero_badge', '' ) ) : ?>
					<p class="ant-badge">
						<span class="ant-badge-dot" aria-hidden="true"></span>
						<?php echo esc_html( antradus_opt( 'home_hero_badge', '' ) ); ?>
					</p>
				<?php endif; ?>

				<h1 class="ant-h1">
					<?php
					foreach ( antradus_lines( $antradus_title ) as $antradus_line ) {
						echo '<span class="ant-h1-line">' . antradus_headline( $antradus_line ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside.
					}
					?>
				</h1>

				<?php if ( antradus_opt( 'home_hero_sub', '' ) ) : ?>
					<p class="ant-lede"><?php echo esc_html( antradus_opt( 'home_hero_sub', '' ) ); ?></p>
				<?php endif; ?>

				<div class="ant-actions">
					<?php
					antradus_button( antradus_opt( 'home_hero_cta1', '' ), antradus_opt( 'home_hero_cta1_url', '' ), 'primary' );
					antradus_button( antradus_opt( 'home_hero_cta2', '' ), antradus_opt( 'home_hero_cta2_url', '' ), 'ghost' );
					?>
				</div>

				<?php if ( antradus_opt( 'home_hero_note', '' ) ) : ?>
					<p class="ant-note"><?php echo esc_html( antradus_opt( 'home_hero_note', '' ) ); ?></p>
				<?php endif; ?>

				<?php if ( antradus_opt( 'home_hero_proof', '' ) ) : ?>
					<p class="ant-proof">
						<span class="ant-proof-mark" aria-hidden="true">
							<?php echo antradus_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<?php echo esc_html( antradus_opt( 'home_hero_proof', '' ) ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="ant-hero-visual">
				<div class="ant-glass ant-hero-frame">
					<?php
					antradus_image(
						'home_hero_image',
						array(
							'ratio' => '4 / 3',
							'label' => __( 'Home hero image', 'antradus' ),
							'alt'   => '',
							'eager' => true,
						)
					);
					?>
				</div>

				<?php if ( $antradus_chips ) : ?>
					<ul class="ant-hero-chips" aria-hidden="true">
						<?php foreach ( $antradus_chips as $antradus_i => $antradus_chip ) : ?>
							<li class="ant-hero-chip ant-hero-chip--<?php echo (int) $antradus_i + 1; ?>">
								<span class="ant-hero-chip-dot"></span>
								<?php echo esc_html( $antradus_chip ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>

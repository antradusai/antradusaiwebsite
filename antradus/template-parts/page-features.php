<?php
/**
 * The Plugin features page.
 *
 * Whatever the page itself contains - screenshots, a gallery, ordinary
 * paragraphs - is rendered inside the design between the hero and the feature
 * groups, so the editor stays useful instead of being bypassed.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_groups  = antradus_rows( 'feat_groups' );
$antradus_content = '';

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$antradus_content = apply_filters( 'the_content', get_the_content() );
	}
	rewind_posts();
}
?>

<section class="ant-section ant-page-hero">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-page-hero-grid">
			<div>
				<?php
				antradus_section_head(
					antradus_opt( 'feat_eyebrow', '' ),
					antradus_opt( 'feat_title', get_the_title() ),
					antradus_opt( 'feat_sub', '' ),
					'left'
				);
				?>
			</div>
			<div class="ant-glass ant-page-hero-art">
				<?php
				antradus_image(
					'feat_hero_image',
					array(
						'ratio' => '4 / 3',
						'label' => __( 'Features page hero image', 'antradus' ),
						'alt'   => '',
						'eager' => true,
					)
				);
				?>
			</div>
		</div>
	</div>
</section>

<?php if ( '' !== trim( wp_strip_all_tags( $antradus_content ) ) || false !== strpos( $antradus_content, '<img' ) ) : ?>
	<section class="ant-section ant-band">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				'',
				antradus_opt( 'feat_content_top', '' ),
				antradus_opt( 'feat_content_sub', '' )
			);
			?>
			<div class="ant-glass ant-showcase">
				<div class="ant-prose">
					<?php echo $antradus_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already filtered by the_content; kses here would strip embeds and forms. ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_groups ) : ?>
	<section class="ant-section">
		<div class="ant-wrap">
			<div class="ant-grid ant-grid--2 ant-groups">
				<?php foreach ( $antradus_groups as $antradus_group ) : ?>
					<article class="ant-glass ant-group">
						<header class="ant-group-head">
							<span class="ant-group-ic">
								<?php echo antradus_icon( antradus_cell( $antradus_group, 'icon', 'spark' ), 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
							</span>
							<h2><?php echo esc_html( antradus_cell( $antradus_group, 'title' ) ); ?></h2>
						</header>
						<ul class="ant-ticks">
							<?php foreach ( antradus_lines( antradus_cell( $antradus_group, 'items' ) ) as $antradus_item ) : ?>
								<li>
									<?php echo antradus_icon( 'check', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
									<span><?php echo esc_html( $antradus_item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_opt( 'feat_cta_title', '' ) ) : ?>
	<section class="ant-section ant-band">
		<div class="ant-wrap">
			<div class="ant-glass ant-cta">
				<div class="ant-cta-inner">
					<h2 class="ant-h2"><?php echo antradus_headline( antradus_opt( 'feat_cta_title', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></h2>
					<?php if ( antradus_opt( 'feat_cta_sub', '' ) ) : ?>
						<p class="ant-sub"><?php echo esc_html( antradus_opt( 'feat_cta_sub', '' ) ); ?></p>
					<?php endif; ?>
					<div class="ant-actions ant-actions--center">
						<?php antradus_button( antradus_opt( 'feat_cta_btn', '' ), antradus_opt( 'feat_cta_btn_url', '' ), 'primary' ); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
/**
 * The two audience pages: "for publishers" and "for studios".
 *
 * One design, two vocabularies. Every field this reads is prefixed - `pub_`
 * for the publisher page, `std_` for the studio one - and the prefix arrives
 * as an argument from the two-line template that names it. That is deliberate:
 * the pages differ in every word and in not one line of layout, so a second
 * copy of this file would be a second place to fix a spacing bug.
 *
 * The plan section is the point of the whole page. It does not restate a
 * price: it names a plan from the Pricing tab and renders that plan's real
 * card, buy button, free trial and all. There is one price in this theme and
 * it lives on the Pricing tab.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_p = isset( $args['prefix'] ) ? (string) $args['prefix'] : '';
if ( '' === $antradus_p ) {
	return;
}

/**
 * Read one of this page's fields.
 *
 * @param string $suffix  Key after the prefix.
 * @param mixed  $default Fallback.
 * @return mixed
 */
$antradus_f = static function ( $suffix, $default = '' ) use ( $antradus_p ) {
	return antradus_opt( $antradus_p . $suffix, $default );
};

$antradus_signals = antradus_lines( $antradus_f( 'signals' ) );
$antradus_groups  = antradus_rows( $antradus_p . 'groups' );
$antradus_flow    = antradus_rows( $antradus_p . 'flow' );
$antradus_plans   = antradus_plans_named( $antradus_f( 'plan_names' ) );
$antradus_more    = antradus_page_url( 'pricing' );
$antradus_switch  = antradus_link( $antradus_f( 'switch_btn_url' ) );
$antradus_content = '';

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$antradus_content = apply_filters( 'the_content', get_the_content() );
	}
	rewind_posts();
}
?>

<section class="ant-section ant-page-hero ant-aud-hero">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-orb ant-orb--b" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-page-hero-grid">
			<div>
				<?php
				antradus_section_head(
					$antradus_f( 'eyebrow' ),
					$antradus_f( 'title', get_the_title() ),
					$antradus_f( 'sub' ),
					'left'
				);
				?>

				<div class="ant-actions">
					<?php
					antradus_button( $antradus_f( 'cta1' ), $antradus_f( 'cta1_url' ), 'primary' );
					antradus_button( $antradus_f( 'cta2' ), $antradus_f( 'cta2_url' ), 'ghost' );
					?>
				</div>

				<?php if ( $antradus_f( 'note' ) ) : ?>
					<p class="ant-note"><?php echo esc_html( $antradus_f( 'note' ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="ant-glass ant-page-hero-art">
				<?php
				antradus_image(
					$antradus_p . 'hero_image',
					array(
						'ratio' => '4 / 3',
						'label' => __( 'Audience page hero image', 'antradus' ),
						'alt'   => '',
						'eager' => true,
					)
				);
				?>
			</div>
		</div>
	</div>
</section>

<?php if ( $antradus_signals ) : ?>
	<section class="ant-section ant-section--tight">
		<div class="ant-wrap">
			<div class="ant-glass ant-signals">
				<?php if ( $antradus_f( 'signals_title' ) ) : ?>
					<p class="ant-signals-title"><?php echo esc_html( $antradus_f( 'signals_title' ) ); ?></p>
				<?php endif; ?>
				<ul class="ant-ticks ant-signals-list">
					<?php foreach ( $antradus_signals as $antradus_line ) : ?>
						<li>
							<?php echo antradus_icon( 'check', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
							<span><?php echo esc_html( $antradus_line ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * Whatever the page itself holds - a screenshot, a gallery, three paragraphs
 * written for this audience - renders here, in the design, so the editor is
 * still worth opening. An empty page prints nothing at all.
 */
?>
<?php if ( '' !== trim( wp_strip_all_tags( $antradus_content ) ) || false !== strpos( $antradus_content, '<img' ) ) : ?>
	<section class="ant-section">
		<div class="ant-wrap">
			<div class="ant-glass ant-showcase">
				<div class="ant-prose">
					<?php echo $antradus_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already filtered by the_content; kses here would strip embeds and forms. ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_groups && $antradus_f( 'groups_title' ) ) : ?>
	<section class="ant-section ant-band" id="features">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				$antradus_f( 'groups_eyebrow' ),
				$antradus_f( 'groups_title' ),
				$antradus_f( 'groups_sub' )
			);
			?>
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

<?php if ( $antradus_flow && $antradus_f( 'flow_title' ) ) : ?>
	<section class="ant-section">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				'',
				$antradus_f( 'flow_title' ),
				$antradus_f( 'flow_sub' )
			);
			?>
			<?php
			/*
			 * Numbered by position, not by hand. The steps on the contact page
			 * carry their own "01", which is right there - three steps that
			 * never change. Here the editor can add or reorder a step, and a
			 * typed number would be wrong the moment they did.
			 */
			?>
			<ol class="ant-grid ant-grid--4 ant-steps ant-flow">
				<?php foreach ( $antradus_flow as $antradus_i => $antradus_step ) : ?>
					<li class="ant-glass ant-step">
						<span class="ant-step-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $antradus_i + 1 ) ); ?></span>
						<b><?php echo esc_html( antradus_cell( $antradus_step, 'title' ) ); ?></b>
						<span><?php echo esc_html( antradus_cell( $antradus_step, 'text' ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_plans && $antradus_f( 'plan_title' ) ) : ?>
	<section class="ant-section ant-band ant-band--deep" id="plan">
		<span class="ant-orb ant-orb--e" aria-hidden="true"></span>
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				$antradus_f( 'plan_eyebrow' ),
				$antradus_f( 'plan_title' ),
				$antradus_f( 'plan_sub' )
			);

			antradus_render_plans( array( 'only' => $antradus_f( 'plan_names' ) ) );
			?>

			<?php if ( $antradus_f( 'plan_note' ) ) : ?>
				<p class="ant-note ant-center ant-aud-plan-note"><?php echo esc_html( $antradus_f( 'plan_note' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $antradus_f( 'plan_more' ) && '' !== $antradus_more ) : ?>
				<p class="ant-center ant-more">
					<a href="<?php echo esc_url( $antradus_more ); ?>">
						<?php echo esc_html( $antradus_f( 'plan_more' ) ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * The way back out. A page that asks somebody to identify themselves has to
 * let the ones who guessed wrong leave without using the back button, so the
 * other audience page is always one click away from the bottom of this one.
 */
?>
<?php if ( $antradus_f( 'switch_title' ) && '' !== $antradus_switch ) : ?>
	<section class="ant-section">
		<div class="ant-wrap">
			<div class="ant-glass ant-switchcard">
				<div class="ant-switchcard-copy">
					<h2><?php echo antradus_headline( $antradus_f( 'switch_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></h2>
					<?php if ( $antradus_f( 'switch_text' ) ) : ?>
						<p><?php echo esc_html( $antradus_f( 'switch_text' ) ); ?></p>
					<?php endif; ?>
				</div>
				<?php antradus_button( $antradus_f( 'switch_btn' ), $antradus_f( 'switch_btn_url' ), 'soft' ); ?>
			</div>
		</div>
	</section>
<?php endif; ?>

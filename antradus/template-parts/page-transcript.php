<?php
/**
 * The Transcript Extractor page.
 *
 * A second product on the same site: the Chrome extension that reads Spotify
 * and YouTube transcripts, sold on its own through Freemius. Every word here is
 * a `tx_` field on the Transcript Extractor tab, edited in both languages like
 * every other page - only the product is different.
 *
 * Two things are deliberate rather than habit.
 *
 * The plan buttons go to the Chrome Web Store, never to a checkout. A plan is
 * bought from inside the extension, where Freemius opens with the buyer's
 * Google email already filled in and locked, and that email is how the
 * purchase finds the account that signed in. A checkout opened from this page
 * with any other address would take the money and unlock nothing.
 *
 * And the hero and the speaker section each draw an illustration of the side
 * panel until a picture is chosen for them, so the page reads as finished on
 * the day it is published. It is the same job the dashed placeholder does on
 * the other pages, done in a form a visitor can actually look at.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_steps  = antradus_rows( 'tx_steps' );
$antradus_points = antradus_rows( 'tx_why_points' );
$antradus_cards  = antradus_rows( 'tx_get_cards' );
$antradus_who    = antradus_rows( 'tx_who_cards' );
$antradus_plans  = antradus_rows( 'tx_plans' );
$antradus_trust  = antradus_lines( antradus_opt( 'tx_trust', '' ) );
$antradus_more   = antradus_link( antradus_opt( 'tx_more_btn_url', '' ) );
$antradus_legal  = antradus_link_list( antradus_opt( 'tx_legal_links', '' ) );
$antradus_shots  = antradus_image_list( antradus_opt( 'tx_hero_image', '' ) );
$antradus_why    = antradus_image_full_url( antradus_opt( 'tx_why_image', '' ) );
$antradus_title  = trim( (string) antradus_opt( 'tx_title', '' ) );
$antradus_title  = '' !== $antradus_title ? $antradus_title : get_the_title();
?>

<section class="ant-section ant-page-hero ant-tx-hero">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-orb ant-orb--b" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-page-hero-grid">
			<div>
				<?php
				/*
				 * Written out rather than handed to antradus_section_head(), which
				 * prints an h2. This is the page's one h1, and it carries the
				 * phrase the page is meant to be found for.
				 */
				?>
				<div class="ant-head ant-head--left">
					<?php if ( antradus_opt( 'tx_eyebrow', '' ) ) : ?>
						<p class="ant-eyebrow"><?php echo esc_html( antradus_opt( 'tx_eyebrow', '' ) ); ?></p>
					<?php endif; ?>
					<h1 class="ant-h1 ant-h1--page"><?php echo antradus_headline( $antradus_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></h1>
					<?php if ( antradus_opt( 'tx_sub', '' ) ) : ?>
						<p class="ant-sub"><?php echo esc_html( antradus_opt( 'tx_sub', '' ) ); ?></p>
					<?php endif; ?>
				</div>

				<div class="ant-actions">
					<?php
					antradus_button( antradus_opt( 'tx_cta1', '' ), antradus_opt( 'tx_cta1_url', '' ), 'primary' );
					antradus_button( antradus_opt( 'tx_cta2', '' ), antradus_opt( 'tx_cta2_url', '' ), 'ghost' );
					?>
				</div>

				<?php if ( antradus_opt( 'tx_note', '' ) ) : ?>
					<p class="ant-note"><?php echo esc_html( antradus_opt( 'tx_note', '' ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="ant-glass ant-page-hero-art">
				<?php if ( $antradus_shots ) : ?>
					<?php
					antradus_slider(
						'tx_hero_image',
						array(
							'ratio' => '16 / 10',
							'label' => __( 'Transcript Extractor hero pictures', 'antradus' ),
							'alt'   => '',
							'eager' => true,
						)
					);
					?>
				<?php else : ?>
					<div class="ant-tx-panel" role="img" aria-label="<?php esc_attr_e( 'The side panel beside a Spotify episode: two named speakers, their lines and the export formats', 'antradus' ); ?>">
						<div class="ant-tx-panel-bar" aria-hidden="true">
							<span class="ant-tx-dots"><i></i><i></i><i></i></span>
							<b><?php esc_html_e( 'Transcript', 'antradus' ); ?></b>
							<span class="ant-tx-pill">Spotify</span>
						</div>
						<div class="ant-tx-panel-body" aria-hidden="true">
							<p class="ant-tx-label">
								<?php esc_html_e( 'Who is speaking', 'antradus' ); ?>
								<span class="ant-tx-ai"><?php esc_html_e( 'AI guess', 'antradus' ); ?></span>
							</p>
							<ul class="ant-tx-voices">
								<li>
									<span class="ant-tx-avatar">M</span>
									<span class="ant-tx-voice">
										<s><?php esc_html_e( 'Speaker 1', 'antradus' ); ?></s>
										<b>Maya Chen</b>
										<em><?php esc_html_e( 'Host', 'antradus' ); ?></em>
									</span>
									<span class="ant-tx-share" style="--ant-tx-share:58%"><i></i></span>
								</li>
								<li>
									<span class="ant-tx-avatar">D</span>
									<span class="ant-tx-voice">
										<s><?php esc_html_e( 'Speaker 2', 'antradus' ); ?></s>
										<b>Daniel Ortiz</b>
										<em><?php esc_html_e( 'Guest', 'antradus' ); ?></em>
									</span>
									<span class="ant-tx-share" style="--ant-tx-share:42%"><i></i></span>
								</li>
							</ul>
							<p class="ant-tx-label"><?php esc_html_e( 'Transcript', 'antradus' ); ?></p>
							<ol class="ant-tx-lines">
								<li><time>00:42</time><span><b>Maya Chen</b> <?php esc_html_e( 'So where did the idea actually start?', 'antradus' ); ?></span></li>
								<li><time>00:46</time><span><b>Daniel Ortiz</b> <?php esc_html_e( 'With an interview nobody could quote, because it only said Speaker 2.', 'antradus' ); ?></span></li>
								<li><time>00:53</time><span><b>Maya Chen</b> <?php esc_html_e( 'And now it says who said it.', 'antradus' ); ?></span></li>
							</ol>
							<div class="ant-tx-exports"><span>TXT</span><span>MD</span><span>DOCX</span><span>SRT</span><span>VTT</span></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php if ( $antradus_steps && antradus_opt( 'tx_steps_title', '' ) ) : ?>
	<section class="ant-section" id="how-it-works">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'tx_steps_eyebrow', '' ),
				antradus_opt( 'tx_steps_title', '' ),
				antradus_opt( 'tx_steps_sub', '' )
			);
			?>
			<ol class="ant-grid ant-grid--4 ant-steps ant-flow">
				<?php foreach ( array_values( $antradus_steps ) as $antradus_i => $antradus_step ) : ?>
					<li class="ant-glass ant-step">
						<span class="ant-step-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $antradus_i + 1 ) ); ?></span>
						<b><?php echo esc_html( antradus_cell( $antradus_step, 'title' ) ); ?></b>
						<span><?php echo esc_html( antradus_cell( $antradus_step, 'text' ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
			<?php if ( antradus_opt( 'tx_steps_note', '' ) ) : ?>
				<p class="ant-note ant-center ant-trend-note"><?php echo esc_html( antradus_opt( 'tx_steps_note', '' ) ); ?></p>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_opt( 'tx_why_title', '' ) ) : ?>
	<section class="ant-section ant-band ant-geo" id="speakers">
		<span class="ant-orb ant-orb--c" aria-hidden="true"></span>
		<div class="ant-wrap">
			<div class="ant-geo-grid">

				<div class="ant-geo-copy">
					<?php
					antradus_section_head(
						antradus_opt( 'tx_why_eyebrow', '' ),
						antradus_opt( 'tx_why_title', '' ),
						antradus_opt( 'tx_why_sub', '' ),
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

					<?php if ( antradus_opt( 'tx_why_note', '' ) ) : ?>
						<?php // Bold, because it is the line that says what YouTube does not get. ?>
						<p class="ant-note ant-tx-why-note"><strong><?php echo esc_html( antradus_opt( 'tx_why_note', '' ) ); ?></strong></p>
					<?php endif; ?>
				</div>

				<div class="ant-geo-art">
					<div class="ant-glass ant-geo-frame">
						<?php if ( '' !== $antradus_why ) : ?>
							<?php
							antradus_image(
								'tx_why_image',
								array(
									'ratio' => '4 / 3',
									'label' => __( 'Speaker section picture', 'antradus' ),
									'alt'   => '',
								)
							);
							?>
						<?php else : ?>
							<div class="ant-tx-compare" role="img" aria-label="<?php esc_attr_e( 'A Spotify transcript with Speaker 1 and Speaker 2, and the same lines with the names on them', 'antradus' ); ?>">
								<div class="ant-tx-side" aria-hidden="true">
									<p class="ant-tx-label"><?php esc_html_e( 'Spotify transcript', 'antradus' ); ?></p>
									<ol class="ant-tx-lines">
										<li><b><?php esc_html_e( 'Speaker 1', 'antradus' ); ?></b><span><?php esc_html_e( 'Thanks for coming on the show.', 'antradus' ); ?></span></li>
										<li><b><?php esc_html_e( 'Speaker 2', 'antradus' ); ?></b><span><?php esc_html_e( 'Thanks for having me.', 'antradus' ); ?></span></li>
									</ol>
								</div>
								<span class="ant-tx-arrow" aria-hidden="true">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</span>
								<div class="ant-tx-side is-named" aria-hidden="true">
									<p class="ant-tx-label"><?php esc_html_e( 'With names', 'antradus' ); ?></p>
									<ol class="ant-tx-lines">
										<li><b>Maya Chen &middot; <?php esc_html_e( 'Host', 'antradus' ); ?></b><span><?php esc_html_e( 'Thanks for coming on the show.', 'antradus' ); ?></span></li>
										<li><b>Daniel Ortiz &middot; <?php esc_html_e( 'Guest', 'antradus' ); ?></b><span><?php esc_html_e( 'Thanks for having me.', 'antradus' ); ?></span></li>
									</ol>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_cards && antradus_opt( 'tx_get_title', '' ) ) : ?>
	<section class="ant-section" id="features">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'tx_get_eyebrow', '' ),
				antradus_opt( 'tx_get_title', '' ),
				antradus_opt( 'tx_get_sub', '' )
			);
			?>
			<div class="ant-grid ant-grid--3 ant-uses">
				<?php foreach ( $antradus_cards as $antradus_card ) : ?>
					<article class="ant-glass ant-use">
						<span class="ant-use-ic">
							<?php echo antradus_icon( antradus_cell( $antradus_card, 'icon', 'spark' ), 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<h3><?php echo esc_html( antradus_cell( $antradus_card, 'title' ) ); ?></h3>
						<p><?php echo esc_html( antradus_cell( $antradus_card, 'text' ) ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $antradus_who && antradus_opt( 'tx_who_title', '' ) ) : ?>
	<section class="ant-section ant-section--tight">
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'tx_who_eyebrow', '' ),
				antradus_opt( 'tx_who_title', '' ),
				antradus_opt( 'tx_who_sub', '' )
			);
			?>
			<div class="ant-grid ant-grid--4 ant-uses">
				<?php foreach ( $antradus_who as $antradus_card ) : ?>
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
<?php endif; ?>

<?php if ( $antradus_plans && antradus_opt( 'tx_plans_title', '' ) ) : ?>
	<section class="ant-section ant-band ant-band--deep" id="plans">
		<span class="ant-orb ant-orb--e" aria-hidden="true"></span>
		<div class="ant-wrap">
			<?php
			antradus_section_head(
				antradus_opt( 'tx_plans_eyebrow', '' ),
				antradus_opt( 'tx_plans_title', '' ),
				antradus_opt( 'tx_plans_sub', '' )
			);

			// The Pricing page's card design, with this tab's rows and no checkout.
			antradus_render_plans( array( 'rows' => $antradus_plans ) );
			?>

			<?php if ( antradus_opt( 'tx_plans_note', '' ) ) : ?>
				<p class="ant-note ant-center ant-aud-plan-note"><?php echo esc_html( antradus_opt( 'tx_plans_note', '' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $antradus_trust ) : ?>
				<ul class="ant-trustbar ant-trustbar--strong ant-tx-trust">
					<?php foreach ( $antradus_trust as $antradus_line ) : ?>
						<li class="ant-trustbar-item">
							<span class="ant-trustbar-mark" aria-hidden="true">
								<?php echo antradus_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
							</span>
							<span><?php echo esc_html( $antradus_line ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_rows( 'tx_faq_items' ) && antradus_opt( 'tx_faq_title', '' ) ) : ?>
	<section class="ant-section" id="faq">
		<div class="ant-wrap ant-wrap--narrow">
			<?php
			antradus_section_head( '', antradus_opt( 'tx_faq_title', '' ), antradus_opt( 'tx_faq_sub', '' ) );
			antradus_render_faq( 'tx_faq_items' );
			?>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_opt( 'tx_more_title', '' ) && '' !== $antradus_more ) : ?>
	<section class="ant-section ant-section--tight">
		<div class="ant-wrap">
			<div class="ant-glass ant-switchcard">
				<div class="ant-switchcard-copy">
					<h2><?php echo antradus_headline( antradus_opt( 'tx_more_title', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></h2>
					<?php if ( antradus_opt( 'tx_more_text', '' ) ) : ?>
						<p><?php echo esc_html( antradus_opt( 'tx_more_text', '' ) ); ?></p>
					<?php endif; ?>
				</div>
				<?php antradus_button( antradus_opt( 'tx_more_btn', '' ), antradus_opt( 'tx_more_btn_url', '' ), 'soft' ); ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( antradus_opt( 'tx_legal', '' ) || $antradus_legal ) : ?>
	<div class="ant-wrap">
		<div class="ant-tx-legal">
			<?php if ( antradus_opt( 'tx_legal', '' ) ) : ?>
				<p><?php echo esc_html( antradus_opt( 'tx_legal', '' ) ); ?></p>
			<?php endif; ?>
			<?php if ( $antradus_legal ) : ?>
				<ul class="ant-tx-legal-links">
					<?php foreach ( $antradus_legal as $antradus_item ) : ?>
						<li><a href="<?php echo esc_url( $antradus_item['url'] ); ?>"><?php echo esc_html( $antradus_item['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

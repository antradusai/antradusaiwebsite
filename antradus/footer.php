<?php
/**
 * The closing call to action and the site footer.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

$antradus_cta_title = antradus_opt( 'footer_cta_title', '' );
$antradus_cta_url   = antradus_link( antradus_opt( 'footer_cta_url', '' ) );
$antradus_logo      = antradus_image_url( antradus_opt( 'brand_logo', '' ), 'medium' );
$antradus_cols      = antradus_rows( 'footer_cols' );
$antradus_social    = antradus_link_list( antradus_opt( 'footer_social', '' ) );
$antradus_trust     = antradus_lines( antradus_opt( 'footer_trust', '' ) );
?>
</main><!-- /.ant-main -->

<?php if ( '' !== trim( $antradus_cta_title ) && '' !== $antradus_cta_url ) : ?>
	<section class="ant-footer-cta">
		<div class="ant-wrap">
			<div class="ant-footer-cta-card">
				<p class="ant-footer-cta-title"><?php echo antradus_headline( $antradus_cta_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></p>
				<?php antradus_button( antradus_opt( 'footer_cta_btn', '' ), antradus_opt( 'footer_cta_url', '' ), 'primary' ); ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<footer class="ant-footer">
	<div class="ant-wrap">

		<div class="ant-footer-top">
			<div class="ant-footer-brand">
				<a class="ant-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php if ( $antradus_logo ) : ?>
						<img class="ant-brand-logo" src="<?php echo esc_url( $antradus_logo ); ?>" alt="" width="36" height="36">
					<?php else : ?>
						<span class="ant-brand-logo ant-brand-logo--mark" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="20" height="20" fill="none">
								<path d="M12 3v10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
								<path d="M7.5 7h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
								<circle cx="12" cy="4.4" r="1.9" stroke="currentColor" stroke-width="1.6"/>
								<path d="M5 13a7 7 0 0014 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
							</svg>
						</span>
					<?php endif; ?>
					<span class="ant-brand-name"><?php echo esc_html( antradus_opt( 'brand_name', get_bloginfo( 'name' ) ) ); ?></span>
				</a>
				<?php if ( antradus_opt( 'footer_tagline', '' ) ) : ?>
					<p class="ant-footer-tagline"><?php echo esc_html( antradus_opt( 'footer_tagline', '' ) ); ?></p>
				<?php endif; ?>

				<?php if ( $antradus_social ) : ?>
					<ul class="ant-footer-social">
						<?php foreach ( $antradus_social as $item ) : ?>
							<li><a href="<?php echo esc_url( $item['url'] ); ?>" rel="noopener"><?php echo esc_html( $item['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $antradus_cols ) : ?>
				<div class="ant-footer-cols">
					<?php
					foreach ( $antradus_cols as $col ) :
						$links = antradus_link_list( antradus_cell( $col, 'links' ) );
						if ( ! $links ) {
							continue;
						}
						?>
						<nav class="ant-footer-col" aria-label="<?php echo esc_attr( antradus_cell( $col, 'title' ) ); ?>">
							<p class="ant-footer-col-title"><?php echo esc_html( antradus_cell( $col, 'title' ) ); ?></p>
							<ul>
								<?php foreach ( $links as $link ) : ?>
									<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		/*
		 * The trust row used to be small grey text with a diamond between the
		 * items - the visual weight of a disclaimer. These are the promises
		 * that answer the last objection before somebody buys, so they are
		 * stated the way a promise is stated: a tick, a solid card, and type
		 * you can read without leaning in.
		 */
		?>
		<?php if ( $antradus_trust ) : ?>
			<ul class="ant-trustbar ant-trustbar--footer">
				<?php foreach ( $antradus_trust as $line ) : ?>
					<li class="ant-trustbar-item">
						<span class="ant-trustbar-mark" aria-hidden="true">
							<?php echo antradus_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
						</span>
						<span><?php echo esc_html( $line ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="ant-footer-base">
			<p class="ant-footer-legal">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( antradus_opt( 'brand_name', get_bloginfo( 'name' ) ) ); ?></a>
				<?php echo esc_html( antradus_opt( 'footer_legal', '' ) ); ?>
			</p>
			<?php antradus_lang_switch( 'ant-langs--footer' ); ?>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

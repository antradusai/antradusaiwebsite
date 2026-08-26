<?php
/**
 * Not found.
 *
 * A missing page and an unpublished page look the same from out here, so the
 * wording covers both without guessing which it was.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="ant-section ant-page-hero ant-page-hero--center">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-orb ant-orb--b" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>

	<div class="ant-wrap ant-wrap--narrow">
		<div class="ant-head ant-head--center">
			<p class="ant-eyebrow">404</p>
			<h1 class="ant-h1 ant-h1--page"><?php esc_html_e( 'That page is not here', 'antradus' ); ?></h1>
			<p class="ant-sub"><?php esc_html_e( 'It may have moved, or it may not be published yet. Either way, here is the way back.', 'antradus' ); ?></p>
		</div>

		<div class="ant-actions ant-actions--center">
			<a class="ant-btn ant-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'antradus' ); ?></a>
			<?php
			$antradus_blog = antradus_page_url( 'blog' );
			if ( '' !== $antradus_blog ) :
				?>
				<a class="ant-btn ant-btn--ghost" href="<?php echo esc_url( $antradus_blog ); ?>"><?php esc_html_e( 'Read the blog', 'antradus' ); ?></a>
			<?php endif; ?>
		</div>

		<form class="ant-search ant-search--center" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
			<?php echo antradus_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
			<input type="search" name="s" aria-label="<?php esc_attr_e( 'Search', 'antradus' ); ?>"
				placeholder="<?php esc_attr_e( 'Search the site', 'antradus' ); ?>">
		</form>
	</div>
</section>

<?php
get_footer();

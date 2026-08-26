<?php
/**
 * Search results.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$antradus_term  = get_search_query();
$antradus_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
	<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
	<span class="ant-mesh" aria-hidden="true"></span>
	<div class="ant-wrap">
		<div class="ant-head ant-head--center">
			<p class="ant-eyebrow"><?php esc_html_e( 'Search', 'antradus' ); ?></p>
			<h1 class="ant-h1 ant-h1--page">
				<?php
				printf(
					/* translators: %s: the search term. */
					esc_html__( 'Results for %s', 'antradus' ),
					'<em class="ant-ital">' . esc_html( $antradus_term ) . '</em>'
				);
				?>
			</h1>
			<p class="ant-sub">
				<?php
				printf(
					/* translators: %d: number of results. */
					esc_html( _n( '%d match', '%d matches', $antradus_found, 'antradus' ) ),
					(int) $antradus_found
				);
				?>
			</p>
			<form class="ant-search ant-search--center" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
				<?php echo antradus_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup. ?>
				<input type="search" name="s" value="<?php echo esc_attr( $antradus_term ); ?>"
					aria-label="<?php esc_attr_e( 'Search', 'antradus' ); ?>"
					placeholder="<?php esc_attr_e( 'Search again', 'antradus' ); ?>">
			</form>
		</div>
	</div>
</section>

<section class="ant-section ant-section--top">
	<div class="ant-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="ant-grid ant-grid--cards">
				<?php
				while ( have_posts() ) {
					the_post();
					antradus_post_card();
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'ant-pagination',
					'mid_size'  => 1,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				)
			);
			?>
		<?php else : ?>
			<div class="ant-empty">
				<p class="ant-empty-title"><?php esc_html_e( 'No matches', 'antradus' ); ?></p>
				<p><?php esc_html_e( 'Try a shorter phrase, or browse the articles instead.', 'antradus' ); ?></p>
				<?php
				$antradus_blog = antradus_page_url( 'blog' );
				if ( '' !== $antradus_blog ) :
					?>
					<p><a class="ant-btn ant-btn--soft" href="<?php echo esc_url( $antradus_blog ); ?>"><?php esc_html_e( 'Browse articles', 'antradus' ); ?></a></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();

<?php
/**
 * Pages.
 *
 * One of the seven designed pages gets its design; anything else gets the plain
 * article treatment, so a Privacy Policy page looks like it belongs here too.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$antradus_key = antradus_current_page_key();

switch ( $antradus_key ) {
	case 'home':
		get_template_part( 'template-parts/home' );
		break;

	case 'features':
	case 'pricing':
	case 'blog':
	case 'contact':
	case 'welcome':
	case 'docs':
		get_template_part( 'template-parts/page', $antradus_key );
		break;

	default:
		?>
		<section class="ant-section ant-page-hero ant-page-hero--center ant-page-hero--short">
			<span class="ant-orb ant-orb--a" aria-hidden="true"></span>
			<span class="ant-mesh" aria-hidden="true"></span>
			<div class="ant-wrap">
				<div class="ant-head ant-head--center">
					<h1 class="ant-h1 ant-h1--page"><?php the_title(); ?></h1>
				</div>
			</div>
		</section>

		<section class="ant-section ant-section--top">
			<div class="ant-wrap ant-wrap--narrow">
				<article class="ant-paper">
					<div class="ant-prose">
						<?php
						while ( have_posts() ) {
							the_post();
							the_content();
							wp_link_pages(
								array(
									'before' => '<nav class="ant-pagelinks">',
									'after'  => '</nav>',
								)
							);
						}
						?>
					</div>
				</article>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</section>
		<?php
}

get_footer();

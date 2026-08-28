<?php
/**
 * The front page, section by section.
 *
 * Each part reads its own settings and renders nothing at all when its heading
 * is empty - so clearing a heading is how you remove a section.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/section', 'hero' );
get_template_part( 'template-parts/section', 'stats' );
get_template_part( 'template-parts/section', 'fork' );
get_template_part( 'template-parts/section', 'logos' );
get_template_part( 'template-parts/section', 'demo' );
get_template_part( 'template-parts/section', 'compat' );
get_template_part( 'template-parts/section', 'run' );
get_template_part( 'template-parts/section', 'features' );
get_template_part( 'template-parts/section', 'geo' );
get_template_part( 'template-parts/section', 'usecases' );
get_template_part( 'template-parts/section', 'cost' );
get_template_part( 'template-parts/section', 'pricing' );
get_template_part( 'template-parts/section', 'faq' );
get_template_part( 'template-parts/section', 'cta' );

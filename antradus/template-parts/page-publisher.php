<?php
/**
 * The "for publishers" page.
 *
 * The design is in template-parts/page-audience.php; this names the vocabulary
 * it should be rendered in. Everything the page says lives under `pub_` on the
 * Publishers tab in Antradus Content.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/page', 'audience', array( 'prefix' => 'pub_' ) );

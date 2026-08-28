<?php
/**
 * The "for studios" page.
 *
 * The design is in template-parts/page-audience.php; this names the vocabulary
 * it should be rendered in. Everything the page says lives under `std_` on the
 * Studios tab in Antradus Content.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/page', 'audience', array( 'prefix' => 'std_' ) );

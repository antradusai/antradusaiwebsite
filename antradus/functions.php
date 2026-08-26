<?php
/**
 * Antradus theme - bootstrap.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

define( 'ANTRADUS_VERSION', '2.4.0' );
define( 'ANTRADUS_OPTION', 'antradus_theme_options' );

/*
 * No one edits theme or plugin files from inside wp-admin on this site. The
 * editors are the single most useful thing an attacker can reach with a stolen
 * administrator session, and turning them off costs the owner nothing - the
 * theme is deployed as a zip, not typed into a browser.
 *
 * Defined here rather than in wp-config.php so it travels with the theme.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

require_once get_template_directory() . '/inc/defaults.php';
require_once get_template_directory() . '/inc/defaults-ar.php';
require_once get_template_directory() . '/inc/i18n.php';
require_once get_template_directory() . '/inc/strings-ar.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/settings-schema.php';
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/transfer.php';
require_once get_template_directory() . '/inc/comments.php';
require_once get_template_directory() . '/inc/gallery.php';
require_once get_template_directory() . '/inc/pricing.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/docs.php';

/* ===========================================================================
 * 1. Theme support
 * ========================================================================= */

add_action( 'after_setup_theme', 'antradus_setup' );
/**
 * Declare what the theme can do.
 */
function antradus_setup() {
	load_theme_textdomain( 'antradus', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 64,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu (optional - the theme builds one from your pages when this is empty)', 'antradus' ),
			'footer'  => __( 'Footer menu (optional)', 'antradus' ),
		)
	);

	// The reading measure the article template is designed around.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 780;
	}
}

/* ===========================================================================
 * 2. Assets
 * ========================================================================= */

add_action( 'wp_enqueue_scripts', 'antradus_assets' );
/**
 * Fonts, stylesheet, script - and the palette derived from the accent colour.
 */
function antradus_assets() {
	/*
	 * Arabic needs a face that actually has Arabic glyphs. IBM Plex Sans Arabic
	 * is loaded only when the page is Arabic, so an English reader never pays
	 * for a font they cannot read - and Plus Jakarta Sans stays the Latin face
	 * in both, which keeps numbers and product names identical either way.
	 */
	$fonts = 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Instrument+Serif:ital@0;1&display=swap';
	if ( antradus_is_rtl() ) {
		$fonts = 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap';
	}

	wp_enqueue_style(
		'antradus-fonts',
		$fonts,
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts is versioned by its own URL.
	);

	wp_enqueue_style(
		'antradus',
		get_template_directory_uri() . '/assets/css/theme.css',
		array( 'antradus-fonts' ),
		ANTRADUS_VERSION
	);
	wp_add_inline_style( 'antradus', antradus_inline_palette() );

	if ( antradus_is_rtl() ) {
		wp_enqueue_style(
			'antradus-rtl',
			get_template_directory_uri() . '/assets/css/rtl.css',
			array( 'antradus' ),
			ANTRADUS_VERSION
		);
	}

	wp_enqueue_script(
		'antradus',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		ANTRADUS_VERSION,
		true
	);
	wp_localize_script(
		'antradus',
		'antradusI18n',
		array(
			'rtl'      => antradus_is_rtl() ? 1 : 0,
			'lang'     => antradus_lang(),
			'noResult' => antradus_opt( 'docs_empty', __( 'No guide matches that.', 'antradus' ) ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_head', 'antradus_head_preconnect', 1 );
/**
 * Warm the font connection before the stylesheet asks for it.
 */
function antradus_head_preconnect() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/**
 * The palette, derived from the one accent colour the settings expose.
 *
 * Everything else in the design is a tint, a shade or a translucency of that
 * one value, which is why there is a single colour control and not fifteen.
 *
 * @return string
 */
function antradus_inline_palette() {
	$accent = antradus_opt( 'brand_accent', '#1f6feb' );
	$accent = sanitize_hex_color( $accent );
	if ( ! $accent ) {
		$accent = '#1f6feb';
	}
	$rgb = antradus_hex_to_rgb( $accent );

	/*
	 * The content measure. "full" is a real choice, not a very large number:
	 * it hands the wrap the viewport minus its gutters, so the design keeps
	 * breathing room on a 4K monitor instead of running into the bezel.
	 */
	$stored = (string) antradus_opt( 'brand_width', '1560' );
	if ( 'full' === $stored ) {
		$wide = 'calc(100vw - clamp(32px, 6vw, 120px))';
	} else {
		$width = (int) $stored;
		$width = ( $width >= 1000 && $width <= 2400 ) ? $width : 1560;
		$wide  = $width . 'px';
	}

	// The reading measure for articles, widened along with the site.
	$narrow = ( 'full' === $stored || (int) $stored >= 1560 ) ? '900px' : '820px';

	return sprintf(
		':root{--ant-accent:%1$s;--ant-accent-rgb:%2$d,%3$d,%4$d;--ant-accent-deep:%5$s;--ant-accent-lift:%6$s;--ant-wide:%7$s;--ant-narrow:%8$s}',
		$accent,
		$rgb[0],
		$rgb[1],
		$rgb[2],
		antradus_shade( $accent, -0.28 ),
		antradus_shade( $accent, 0.34 ),
		$wide,
		$narrow
	);
}

/**
 * #rrggbb to an [r, g, b] triple.
 *
 * @param string $hex Hex colour.
 * @return int[]
 */
function antradus_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * Move a colour towards white (positive) or black (negative).
 *
 * @param string $hex    Hex colour.
 * @param float  $amount -1 to 1.
 * @return string
 */
function antradus_shade( $hex, $amount ) {
	$rgb = antradus_hex_to_rgb( $hex );
	$out = '#';
	foreach ( $rgb as $channel ) {
		if ( $amount >= 0 ) {
			$value = $channel + ( 255 - $channel ) * $amount;
		} else {
			$value = $channel * ( 1 + $amount );
		}
		$out .= str_pad( dechex( (int) round( max( 0, min( 255, $value ) ) ) ), 2, '0', STR_PAD_LEFT );
	}
	return $out;
}

/* ===========================================================================
 * 3. Page templates
 * ========================================================================= */

add_filter( 'theme_page_templates', 'antradus_page_templates' );
/**
 * Offer a template per designed page, so a page at an unusual slug can still
 * be told which design it wants.
 *
 * @param array $templates Existing templates.
 * @return array
 */
function antradus_page_templates( $templates ) {
	foreach ( antradus_pages() as $key => $def ) {
		$templates[ 'antradus-' . $key . '.php' ] = sprintf(
			/* translators: %s: page name. */
			__( 'Antradus: %s', 'antradus' ),
			$def['label']
		);
	}
	return $templates;
}

add_filter( 'template_include', 'antradus_template_include', 20 );
/**
 * The templates above are virtual - page.php renders all of them - so make sure
 * WordPress does not go looking for files that are not there.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function antradus_template_include( $template ) {
	if ( ! is_page() ) {
		return $template;
	}
	if ( ! $template || ! file_exists( $template ) ) {
		return get_template_directory() . '/page.php';
	}
	return $template;
}

/* ===========================================================================
 * 4. Navigation
 * ========================================================================= */

/**
 * The header and footer menu.
 *
 * If a menu is assigned to the Primary location it wins, because that is an
 * explicit choice. Otherwise the theme builds the menu itself out of the seven
 * designed pages - and a page that is not published is simply not in the list.
 * That is the whole "draft pages disappear" behaviour, in one function.
 *
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function antradus_nav_items() {
	$items = array();
	$here  = antradus_current_page_key();

	foreach ( antradus_pages() as $key => $def ) {
		if ( 'home' === $key ) {
			continue; // The logo is the way home.
		}
		$url = antradus_page_url( $key );
		if ( '' === $url ) {
			continue;
		}
		$items[] = array(
			'label'   => $def['nav'],
			'url'     => $url,
			'current' => ( $key === $here ),
		);
	}

	foreach ( antradus_lines( antradus_opt( 'nav_extra', '' ) ) as $line ) {
		$pair  = array_map( 'trim', explode( '|', $line, 2 ) );
		$label = isset( $pair[0] ) ? $pair[0] : '';
		$url   = isset( $pair[1] ) ? antradus_link( $pair[1] ) : '';
		if ( '' !== $label && '' !== $url ) {
			$items[] = array(
				'label'   => $label,
				'url'     => $url,
				'current' => false,
			);
		}
	}

	return $items;
}

/**
 * Parse a "Label | target" list into renderable links, dropping the ones whose
 * target is a page that is not published.
 *
 * @param string $text Raw list.
 * @return array<int,array{label:string,url:string}>
 */
function antradus_link_list( $text ) {
	$out = array();
	foreach ( antradus_lines( $text ) as $line ) {
		$pair  = array_map( 'trim', explode( '|', $line, 2 ) );
		$label = isset( $pair[0] ) ? $pair[0] : '';
		$url   = isset( $pair[1] ) ? antradus_link( $pair[1] ) : '';
		if ( '' !== $label && '' !== $url ) {
			$out[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}
	}
	return $out;
}

/* ===========================================================================
 * 5. Odds and ends
 * ========================================================================= */

add_filter( 'body_class', 'antradus_body_class' );
/**
 * A class naming the designed page, so CSS can adjust per page without slugs.
 *
 * @param array $classes Existing classes.
 * @return array
 */
function antradus_body_class( $classes ) {
	$key = antradus_current_page_key();
	if ( $key ) {
		$classes[] = 'ant-page-' . $key;
	}
	if ( is_singular( 'post' ) || is_singular( 'antradus_doc' ) ) {
		$classes[] = 'ant-reading';
	}
	$classes[] = 'ant-site';
	return $classes;
}

add_filter( 'excerpt_more', 'antradus_excerpt_more' );
/**
 * @param string $more Default suffix.
 * @return string
 */
function antradus_excerpt_more( $more ) {
	return '&hellip;';
}

add_filter( 'excerpt_length', 'antradus_excerpt_length', 20 );
/**
 * @param int $length Default length.
 * @return int
 */
function antradus_excerpt_length( $length ) {
	return 26;
}

/**
 * Reading time for a post, in minutes.
 *
 * Counts CJK characters as words, which the naive word count does not - the
 * same fix the plugin needed in its own counter.
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function antradus_reading_minutes( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 1;
	}
	$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$cjk  = preg_match_all( '/[\x{4E00}-\x{9FFF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]/u', $text );
	$rest = preg_replace( '/[\x{4E00}-\x{9FFF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]/u', ' ', $text );
	$words = (int) $cjk + (int) str_word_count( (string) $rest );
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * The post's main category: the SEO plugin's primary one if set, else the first.
 *
 * @param int $post_id Post id.
 * @return WP_Term|null
 */
function antradus_main_category( $post_id ) {
	foreach ( array( '_yoast_wpseo_primary_category', 'rank_math_primary_category' ) as $key ) {
		$term_id = (int) get_post_meta( $post_id, $key, true );
		if ( $term_id ) {
			$term = get_term( $term_id, 'category' );
			if ( $term && ! is_wp_error( $term ) ) {
				return $term;
			}
		}
	}
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

add_action( 'after_switch_theme', 'antradus_on_activate' );
/**
 * On activation, make sure the front page is a real page so Home has somewhere
 * to live. Nothing is published that was not already published.
 */
function antradus_on_activate() {
	if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) ) {
		return;
	}
	$home = antradus_page_object( 'home' );
	if ( $home && 'publish' === $home->post_status ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
	}
}

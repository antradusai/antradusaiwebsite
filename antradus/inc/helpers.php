<?php
/**
 * Antradus theme - shared helpers.
 *
 * Everything the templates need to read content, resolve the known pages and
 * render an image (or an obvious placeholder when there is no image yet).
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 * 1. Options
 * ========================================================================= */

/*
 * antradus_options() lives in inc/i18n.php, because which words you get back
 * depends on which language is being rendered. Everything below reads through
 * it and never touches get_option() itself.
 */

/**
 * Read one setting.
 *
 * @param string $key     Field key, e.g. 'home_hero_title'.
 * @param mixed  $default Returned when the key is missing or empty.
 * @return mixed
 */
function antradus_opt( $key, $default = '' ) {
	$all = antradus_options();
	if ( ! array_key_exists( $key, $all ) ) {
		return $default;
	}
	$val = $all[ $key ];
	if ( '' === $val || null === $val ) {
		return $default;
	}
	return $val;
}

/**
 * Read a repeater setting and always hand back a list of rows.
 *
 * Rows the editor blanked out entirely are dropped, so clearing every field in
 * a row is how you delete it even without touching the Remove button.
 *
 * A language can be named to read the rows as that language has them, rather
 * than as the page is being rendered. Only one caller wants that - matching a
 * plan by the English name an editor typed into a field both languages share -
 * and it is the reason this takes a second argument at all.
 *
 * @param string      $key  Field key.
 * @param string|null $lang Language, or null for the one being rendered.
 * @return array
 */
function antradus_rows( $key, $lang = null ) {
	if ( null === $lang ) {
		$val = antradus_opt( $key, array() );
	} else {
		$all = antradus_options( $lang );
		$val = array_key_exists( $key, $all ) ? $all[ $key ] : array();
	}
	if ( ! is_array( $val ) ) {
		return array();
	}
	$out = array();
	foreach ( $val as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$has = false;
		foreach ( $row as $v ) {
			if ( is_string( $v ) && '' !== trim( $v ) ) {
				$has = true;
				break;
			}
		}
		if ( $has ) {
			$out[] = $row;
		}
	}
	return $out;
}

/**
 * One cell out of a repeater row.
 *
 * @param array  $row     Row.
 * @param string $key     Cell key.
 * @param string $default Fallback.
 * @return string
 */
function antradus_cell( $row, $key, $default = '' ) {
	if ( ! is_array( $row ) || ! isset( $row[ $key ] ) ) {
		return $default;
	}
	$v = $row[ $key ];
	return ( is_scalar( $v ) && '' !== $v ) ? (string) $v : $default;
}

/**
 * Is a switch on? Checkboxes are stored as '1' / ''.
 *
 * @param string $key     Field key.
 * @param bool   $default Default when never saved.
 * @return bool
 */
function antradus_on( $key, $default = true ) {
	$all = antradus_options();
	if ( ! array_key_exists( $key, $all ) ) {
		return $default;
	}
	return '1' === (string) $all[ $key ];
}

/* ===========================================================================
 * 2. The known pages
 * ========================================================================= */

/**
 * The ten pages this theme designs.
 *
 * A page is "live" only when a published page with that slug exists. Every
 * navigation link, footer link and in-page CTA runs through antradus_page_url(),
 * so a page left in Draft never appears anywhere on the site - which is exactly
 * what you want while one is still being written.
 *
 * The two audience pages sit directly after Home on purpose. They are the fork
 * the site turns on - somebody who runs a website and somebody who runs a show
 * want different feature lists and end up on different plans - so they come
 * before the pages that answer "what does it do" and "what does it cost". The
 * order of this array is the order of the menu.
 *
 * One of them is not about the plugin at all. The Transcript Extractor is a
 * separate product - a Chrome extension, sold on its own - so its entry says
 * 'menu' => false: the page is designed, linkable with page:transcript and
 * given its own search metadata, but it stays out of the main menu, which
 * belongs to the plugin.
 *
 * @return array<string,array<string,mixed>>
 */
function antradus_pages() {
	return array(
		'home'      => array(
			'label' => __( 'Home', 'antradus' ),
			'slug'  => 'home',
			'nav'   => __( 'Home', 'antradus' ),
		),
		'publisher' => array(
			'label' => __( 'For publishers', 'antradus' ),
			'slug'  => 'for-publishers',
			'nav'   => __( 'Publishers', 'antradus' ),
		),
		'studio'    => array(
			'label' => __( 'For studios', 'antradus' ),
			'slug'  => 'for-studios',
			'nav'   => __( 'Studios', 'antradus' ),
		),
		'features'  => array(
			'label' => __( 'Plugin features', 'antradus' ),
			'slug'  => 'plugin-features',
			'nav'   => __( 'Features', 'antradus' ),
		),
		'pricing'   => array(
			'label' => __( 'Pricing', 'antradus' ),
			'slug'  => 'pricing',
			'nav'   => __( 'Pricing', 'antradus' ),
		),
		'docs'      => array(
			'label' => __( 'Docs', 'antradus' ),
			'slug'  => 'docs',
			'nav'   => __( 'Docs', 'antradus' ),
		),
		'blog'      => array(
			'label' => __( 'Blog', 'antradus' ),
			'slug'  => 'blog',
			'nav'   => __( 'Blog', 'antradus' ),
		),
		'contact'   => array(
			'label' => __( 'Contact', 'antradus' ),
			'slug'  => 'contact',
			'nav'   => __( 'Contact', 'antradus' ),
		),
		'welcome'   => array(
			'label' => __( 'Welcome', 'antradus' ),
			'slug'  => 'welcome',
			'nav'   => __( 'Newsletter', 'antradus' ),
		),
		'transcript' => array(
			'label' => __( 'Transcript Extractor', 'antradus' ),
			'slug'  => 'transcript-extractor',
			'nav'   => __( 'Transcript Extractor', 'antradus' ),
			'menu'  => false,
		),
	);
}

/**
 * The page object for a key, whatever its status.
 *
 * The slug can be overridden per key in the settings, so a site that already
 * calls its contact page "contact-us" keeps working.
 *
 * @param string $key Page key.
 * @return WP_Post|null
 */
function antradus_page_object( $key ) {
	static $cache = array();
	if ( array_key_exists( $key, $cache ) ) {
		return $cache[ $key ];
	}
	$pages = antradus_pages();
	if ( ! isset( $pages[ $key ] ) ) {
		$cache[ $key ] = null;
		return null;
	}

	$slug = trim( (string) antradus_opt( 'slug_' . $key, $pages[ $key ]['slug'] ), '/ ' );
	$page = $slug ? get_page_by_path( $slug ) : null;

	/*
	 * A page that has never been published often has no stored post_name, so
	 * the lookup above cannot see it and the settings screen would report a
	 * page you are actively writing as "Not created". Fall back to matching the
	 * title, across every status a page can be in before it goes live.
	 */
	if ( ! $page && $slug ) {
		$found = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => array( 'draft', 'pending', 'future', 'private' ),
				'title'            => $pages[ $key ]['label'],
				'posts_per_page'   => 1,
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);
		if ( $found ) {
			$page = $found[0];
		}
	}

	// The front page counts as Home even when its slug is something else.
	if ( 'home' === $key && ! $page ) {
		$front = (int) get_option( 'page_on_front' );
		if ( $front ) {
			$page = get_post( $front );
		}
	}

	$cache[ $key ] = ( $page instanceof WP_Post ) ? $page : null;
	return $cache[ $key ];
}

/**
 * Is this page published and therefore allowed to be linked to?
 *
 * @param string $key Page key.
 * @return bool
 */
function antradus_page_is_live( $key ) {
	$page = antradus_page_object( $key );
	if ( $page && 'publish' === $page->post_status ) {
		return true;
	}

	// Docs is rendered by the plugin, which can serve it without a hub page.
	if ( 'docs' === $key && post_type_exists( 'antradus_doc' ) ) {
		$has = get_posts(
			array(
				'post_type'      => 'antradus_doc',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		return ! empty( $has );
	}
	return false;
}

/**
 * A linkable URL for a known page, or '' when it is not published.
 *
 * Templates must treat '' as "do not render this link at all".
 *
 * @param string $key Page key.
 * @return string
 */
function antradus_page_url( $key ) {
	if ( ! antradus_page_is_live( $key ) ) {
		return '';
	}
	$page = antradus_page_object( $key );
	// A draft page can still be "live" for Docs, where the plugin serves the
	// guides itself - but its own permalink is not a public address.
	if ( $page && 'publish' === $page->post_status ) {
		return antradus_localize_url( (string) get_permalink( $page ) );
	}
	if ( 'docs' === $key ) {
		$archive = get_post_type_archive_link( 'antradus_doc' );
		if ( $archive ) {
			return antradus_localize_url( (string) $archive );
		}
	}
	return '';
}

/**
 * Resolve a CTA target written in the settings.
 *
 * Accepts a full URL, an in-page #anchor, or "page:pricing" to point at a known
 * page - the last form is what keeps a button from surviving its page going
 * back to Draft.
 *
 * @param string $target Stored value.
 * @return string URL, or '' when the target is not available.
 */
function antradus_link( $target ) {
	$target = trim( (string) $target );
	if ( '' === $target ) {
		return '';
	}
	if ( 0 === strpos( $target, 'page:' ) ) {
		return antradus_page_url( substr( $target, 5 ) );
	}
	if ( 0 === strpos( $target, '#' ) ) {
		return $target;
	}
	// mailto: and tel: survive esc_url_raw; a bare "javascript:" does not.
	return antradus_localize_url( esc_url_raw( $target ) );
}

/**
 * Which of the known pages are we rendering right now?
 *
 * Resolution order: an explicitly assigned page template, then the slug, then
 * the front page. That way the design follows the page even if you rename it.
 *
 * @return string Page key, or '' when this is not one of them.
 */
function antradus_current_page_key() {
	if ( ! is_page() ) {
		return '';
	}
	$post_id = get_queried_object_id();

	$template = get_page_template_slug( $post_id );
	if ( $template && preg_match( '~^antradus-([a-z]+)\.php$~', $template, $m ) ) {
		$pages = antradus_pages();
		if ( isset( $pages[ $m[1] ] ) ) {
			return $m[1];
		}
	}

	foreach ( antradus_pages() as $key => $unused ) {
		$page = antradus_page_object( $key );
		if ( $page && (int) $page->ID === (int) $post_id ) {
			return $key;
		}
	}

	if ( is_front_page() ) {
		return 'home';
	}
	return '';
}

/* ===========================================================================
 * 3. Images
 * ========================================================================= */

/**
 * Turn a stored image value into a URL, at a named size.
 *
 * Stored values are either an attachment ID (what the media picker writes) or
 * a plain URL (what a paste writes). Both are accepted.
 *
 * The size only means anything in wp-admin, where the settings screen wants a
 * small copy for a preview. On the front end `inc/images.php` answers every
 * size with the uploaded file, so front-end code should say what it means and
 * call antradus_image_full_url() instead.
 *
 * @param string $value Stored value.
 * @param string $size  Image size for attachment IDs.
 * @return string
 */
function antradus_image_url( $value, $size = 'full' ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( ctype_digit( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, $size );
		return $url ? $url : '';
	}
	return esc_url_raw( $value );
}

/**
 * Split a stored image-list value into single image values.
 *
 * A slider field stores its pictures as one comma-separated list of attachment
 * IDs, which is why it is a string rather than an array: it goes through the
 * same option row, the same sanitizer and the same translation merge as every
 * other field, instead of needing a second shape for one setting.
 *
 * @param string $value Stored value.
 * @return string[]
 */
function antradus_image_list( $value ) {
	$out = array();
	foreach ( explode( ',', (string) $value ) as $one ) {
		$one = trim( $one );
		if ( '' !== $one ) {
			$out[] = $one;
		}
	}
	return $out;
}

/**
 * The picture behind a stored value: its address, and its size when that can
 * be known.
 *
 * WordPress does not hand back the file you gave it. Anything wider than the
 * "big image" threshold - 2560px unless a site changes it - is quietly resized
 * on upload, the resized copy is stored as `-scaled`, and that copy is what the
 * `full` size returns. For a photograph that is a kindness. For a screenshot of
 * an interface it is the difference between reading the text in it and not, so
 * every picture on this site asks for the file itself. `inc/images.php` is
 * where that rule is kept, and this reads it rather than repeating it.
 *
 * An attachment can be measured. A pasted URL is already the file and points at
 * somebody else's server, so there is nothing here to measure and its size
 * comes back as zero - a caller leaves the attributes off rather than guessing.
 *
 * @param string $value Stored value: an attachment ID or a URL.
 * @return array{url:string,width:int,height:int}|null Null when there is no picture.
 */
function antradus_image_file( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return null;
	}

	if ( ! ctype_digit( $value ) ) {
		return array(
			'url'    => esc_url_raw( $value ),
			'width'  => 0,
			'height' => 0,
		);
	}

	$file = antradus_original_image( (int) $value );
	if ( $file ) {
		return $file;
	}

	// Not an image - an ID that was deleted, or a file with no picture in it.
	$url = wp_get_attachment_url( (int) $value );
	if ( ! $url ) {
		return null;
	}
	return array(
		'url'    => $url,
		'width'  => 0,
		'height' => 0,
	);
}

/**
 * The address of the picture as it was uploaded, for a caller that wants only
 * the address.
 *
 * @param string $value Stored value: an attachment ID or a URL.
 * @return string
 */
function antradus_image_full_url( $value ) {
	$file = antradus_image_file( $value );
	return $file ? $file['url'] : '';
}

/**
 * The caption written on a picture in the media library.
 *
 * Only an attachment has one. A pasted URL points at a file on somebody else's
 * server and there is nothing here to read a caption from, so it has none - and
 * a slot mixing the two simply shows captions for the slides that have them.
 *
 * The caption is the file's, not the page's, which means it is the same in both
 * languages. That is the right trade for a screenshot label, and it is the same
 * rule every other image on this site already follows.
 *
 * @param string $value Stored value: an attachment ID or a URL.
 * @return string
 */
function antradus_image_caption( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value || ! ctype_digit( $value ) ) {
		return '';
	}
	return trim( (string) wp_get_attachment_caption( (int) $value ) );
}

/**
 * Render one picture from a stored value, or the placeholder when it is empty.
 *
 * The value, not the key: a slider hands this one slide at a time out of a
 * list, and a plain image slot hands it the whole setting.
 *
 * A picture standing on its own is drawn whole, at its own shape. The slot's
 * ratio is what the design expects, not a promise about the file, and cropping
 * a screenshot to it cuts the ends off the very sentences the picture is there
 * to show. A slider is the exception and sets the ratio itself, because its
 * slides are stacked in one box and that box needs a height before the second
 * picture is ever loaded; so is a card in a grid, where the pictures line up
 * with each other. Neither is this.
 *
 * The width and height attributes take the ratio's place: they let the browser
 * keep the right amount of room while the file is on its way, which is what the
 * ratio was really buying. An attachment can be measured, a pasted URL cannot,
 * and for that one the attributes are left off rather than guessed at.
 *
 * @param string $value Stored value: an attachment ID or a URL.
 * @param array  $args  ratio, label, class, alt, eager.
 */
function antradus_image_tag( $value, $args ) {
	$file  = antradus_image_file( $value );
	$class = trim( 'ant-media ' . $args['class'] );

	if ( ! $file ) {
		antradus_placeholder( $args['label'], $args['ratio'], $args['class'] );
		return;
	}

	$size = '';
	if ( $file['width'] > 0 && $file['height'] > 0 ) {
		$size = sprintf( ' width="%d" height="%d"', (int) $file['width'], (int) $file['height'] );
	}

	printf(
		'<img class="%1$s" src="%2$s" alt="%3$s"%4$s %5$s decoding="async">',
		esc_attr( $class ),
		esc_url( $file['url'] ),
		esc_attr( $args['alt'] ),
		$size, // phpcs:ignore WordPress.Security.EscapeOutput -- two integers, printed above.
		$args['eager'] ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'
	);
}

/**
 * Render an image field, or a labelled placeholder when it is still empty.
 *
 * The placeholder is deliberately loud: it names the slot it fills, so an
 * unfinished page tells you what to do instead of showing a broken frame.
 *
 * @param string $key  Option key holding the image.
 * @param array  $args ratio, label, class, alt, eager.
 */
function antradus_image( $key, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'ratio' => '16 / 10',
			'label' => '',
			'class' => '',
			'alt'   => '',
			'eager' => false,
		)
	);

	$args['label'] = $args['label'] ? $args['label'] : $key;

	antradus_image_tag( antradus_opt( $key, '' ), $args );
}

/**
 * Render an image slot that holds more than one picture, as a slider.
 *
 * The count decides the markup, not a setting. One picture is a picture - it
 * gets the same single tag it always had, with no arrows to press and no
 * script to load. The slider only exists from the second picture onwards, so
 * an editor turns it on by adding a second image and off by removing one, and
 * a page that has never been touched keeps the design it shipped with.
 *
 * Slides cross-fade in place rather than sliding along a track. That is a
 * deliberate choice for a right-to-left site: a fade has no direction to
 * mirror, so the Arabic hero behaves identically to the English one without a
 * second code path deciding which way "next" points.
 *
 * @param string $key  Option key holding the list.
 * @param array  $args ratio, label, class, alt, eager, delay.
 */
function antradus_slider( $key, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'ratio' => '16 / 9',
			'label' => '',
			'class' => '',
			'alt'   => '',
			'eager' => false,
			'delay' => 6000,
		)
	);

	$args['label'] = $args['label'] ? $args['label'] : $key;

	// A value that no longer resolves - a deleted attachment - is not a slide.
	$slides = array();
	foreach ( antradus_image_list( antradus_opt( $key, '' ) ) as $one ) {
		if ( '' !== antradus_image_full_url( $one ) ) {
			$slides[] = $one;
		}
	}

	if ( count( $slides ) < 2 ) {
		antradus_image_tag( $slides ? $slides[0] : '', $args );
		return;
	}

	$total = count( $slides );

	/*
	 * Captions come from the media library, not from a settings field: it is
	 * the picture's own caption, written once where the picture is, and it
	 * follows the file if the same shot is used on a second page. The row is
	 * printed only when at least one slide has one, so a site that never writes
	 * captions gets exactly the slider it had before.
	 */
	$captions = array();
	$has_caption = false;
	foreach ( $slides as $slide ) {
		$caption = antradus_image_caption( $slide );
		$captions[] = $caption;
		$has_caption = $has_caption || '' !== $caption;
	}

	printf(
		'<div class="ant-slider" data-slider data-slider-delay="%1$d" role="group" aria-roledescription="%2$s" aria-label="%3$s">',
		(int) $args['delay'],
		esc_attr__( 'image slider', 'antradus' ),
		esc_attr( $args['label'] )
	);

	printf( '<div class="ant-slider-stage" style="aspect-ratio:%s">', esc_attr( $args['ratio'] ) );
	echo '<ul class="ant-slider-track">';

	/*
	 * Every slide is the uploaded file at its own resolution, with no srcset -
	 * which is the rule for every picture on this site, and `inc/images.php` is
	 * where it is argued for.
	 *
	 * The weight that costs is paid back here by loading them one at a time.
	 * `loading="lazy"` is no help - the slides are stacked in the same box, so
	 * every one of them is in the viewport from the first frame and a browser
	 * fetches the lot. Only the first slide carries a `src`; the rest carry the
	 * address in `data-src` and the script fills it in just before the slide is
	 * needed. Turn the script off and the first slide is the only one that can
	 * be reached anyway.
	 */
	foreach ( $slides as $index => $slide ) {
		$url     = antradus_image_full_url( $slide );
		$first   = ( 0 === $index );
		$caption = $captions[ $index ];

		printf(
			'<li class="ant-slide%1$s" data-slide %2$s>',
			$first ? ' is-current' : '',
			$first ? '' : 'aria-hidden="true"'
		);

		/*
		 * The picture is inside a button, because opening it full size is
		 * something you do to it - and a button is the one control that a
		 * keyboard, a screen reader and a thumb all already know how to use.
		 * Only the current slide's button is reachable; the script moves that
		 * along with the slide.
		 */
		printf(
			'<button type="button" class="ant-slide-open" data-slide-open data-full="%1$s" data-caption="%2$s"%3$s aria-label="%4$s">',
			esc_url( $url ),
			esc_attr( $caption ),
			$first ? '' : ' tabindex="-1"',
			esc_attr(
				$caption
					/* translators: %s: the picture's caption. */
					? sprintf( __( 'View full size: %s', 'antradus' ), $caption )
					: __( 'View this picture full size', 'antradus' )
			)
		);

		printf(
			'<img class="ant-media" %1$s="%2$s" alt="%3$s" style="aspect-ratio:%4$s" %5$s decoding="async">',
			$first ? 'src' : 'data-src',
			esc_url( $url ),
			esc_attr( $args['alt'] ),
			esc_attr( $args['ratio'] ),
			$first && $args['eager'] ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'
		);

		echo '</button></li>';
	}

	echo '</ul>';

	printf(
		'<button type="button" class="ant-slider-nav ant-slider-prev" data-slider-prev aria-label="%s">'
		. '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true">'
		. '<path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
		. '</svg></button>',
		esc_attr__( 'Previous image', 'antradus' )
	);
	printf(
		'<button type="button" class="ant-slider-nav ant-slider-next" data-slider-next aria-label="%s">'
		. '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true">'
		. '<path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
		. '</svg></button>',
		esc_attr__( 'Next image', 'antradus' )
	);

	echo '<div class="ant-slider-dots">';
	foreach ( $slides as $index => $slide ) {
		printf(
			'<button type="button" class="ant-slider-dot%1$s" data-slider-dot %2$s aria-label="%3$s"><span></span></button>',
			0 === $index ? ' is-current' : '',
			0 === $index ? 'aria-current="true"' : '',
			/* translators: 1: number of this image, 2: how many there are. */
			esc_attr( sprintf( __( 'Image %1$d of %2$d', 'antradus' ), $index + 1, $total ) )
		);
	}
	echo '</div>';

	echo '</div>';

	/*
	 * Below the picture, outside the stage - a caption printed over a photo is
	 * a label, and this is a line of text about it.
	 *
	 * Every caption is printed at once, stacked in one grid cell, with only the
	 * current one visible. That is what keeps the row exactly as tall as the
	 * longest caption from the first frame onwards: swapping the text instead
	 * would make the hero grow or shrink by a line every few seconds, and drag
	 * the rest of the page with it.
	 */
	if ( $has_caption ) {
		echo '<div class="ant-slider-captions">';
		foreach ( $captions as $index => $caption ) {
			printf(
				'<p class="ant-slide-caption%1$s" data-slide-caption %2$s>%3$s</p>',
				0 === $index ? ' is-current' : '',
				0 === $index ? '' : 'aria-hidden="true"',
				esc_html( $caption )
			);
		}
		echo '</div>';
	}

	echo '</div>';
}

/**
 * The "put a picture here" block.
 *
 * @param string $label Human name of the slot.
 * @param string $ratio CSS aspect-ratio value.
 * @param string $class Extra classes.
 */
function antradus_placeholder( $label, $ratio = '16 / 10', $class = '' ) {
	printf(
		'<div class="ant-media ant-ph %1$s" style="--ant-ratio:%2$s" role="img" aria-label="%3$s">'
		. '<span class="ant-ph-mark" aria-hidden="true"><svg viewBox="0 0 24 24" width="26" height="26" fill="none">'
		. '<rect x="3" y="4.5" width="18" height="15" rx="3" stroke="currentColor" stroke-width="1.5"/>'
		. '<circle cx="8.6" cy="10" r="1.7" fill="currentColor"/>'
		. '<path d="M4 17.5l4.6-4.6 3.4 3.4 3-3 5 5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>'
		. '</svg></span>'
		. '<span class="ant-ph-label">%4$s</span>'
		. '<span class="ant-ph-hint">%5$s</span></div>',
		esc_attr( $class ),
		esc_attr( $ratio ),
		/* translators: %s: name of the image slot. */
		esc_attr( sprintf( __( 'Image placeholder: %s', 'antradus' ), $label ) ),
		esc_html( $label ),
		esc_html__( 'Add this image in Antradus Content', 'antradus' )
	);
}

/* ===========================================================================
 * 4. Small rendering helpers
 * ========================================================================= */

/**
 * A heading where one phrase is set in the accent serif italic.
 *
 * Authors mark the accent with *asterisks*, the way they already write emphasis
 * everywhere else: "See what Antradus *creates* from one episode".
 *
 * @param string $text Heading text with optional *accent*.
 * @return string Escaped HTML.
 */
function antradus_headline( $text ) {
	$text  = (string) $text;
	$parts = preg_split( '/(\*[^*]+\*)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$out   = '';
	foreach ( (array) $parts as $part ) {
		if ( '' === $part ) {
			continue;
		}
		if ( '*' === substr( $part, 0, 1 ) && '*' === substr( $part, -1 ) && strlen( $part ) > 2 ) {
			$out .= '<em class="ant-ital">' . esc_html( trim( $part, '*' ) ) . '</em>';
		} else {
			$out .= esc_html( $part );
		}
	}
	return $out;
}

/**
 * Split a textarea into trimmed, non-empty lines.
 *
 * @param string $text Raw textarea value.
 * @return string[]
 */
function antradus_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$out   = array();
	foreach ( (array) $lines as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return $out;
}

/**
 * The audience pages' movable sections, in the order they ship in.
 *
 * One list, read by the template that renders them and by the settings screen
 * that reorders them - so a section cannot appear in the chooser without
 * appearing on the page, or the other way round. The hero is deliberately not
 * here: it is the page's opening, and nothing good comes of moving it.
 *
 * @return array<string,string> Key => the name an editor sees.
 */
function antradus_audience_sections_list() {
	return array(
		'signals' => __( 'Is this you?', 'antradus' ),
		'content' => __( 'Whatever the page itself holds', 'antradus' ),
		'trends'  => __( 'Writing from what is trending', 'antradus' ),
		'groups'  => __( 'What it does for them', 'antradus' ),
		'flow'    => __( 'How it actually goes', 'antradus' ),
		'compat'  => __( 'What it plugs into', 'antradus' ),
		'plan'    => __( 'The plan this maps to', 'antradus' ),
		'switch'  => __( 'The way back out', 'antradus' ),
	);
}

/**
 * The order a page's sections should be rendered in.
 *
 * The stored value is a comma-separated list of section keys. It is never
 * trusted to be complete: a key that no longer exists is dropped and a section
 * added to the theme after the order was saved is appended rather than
 * silently disappearing from the page. That is the whole reason this is a
 * function and not a bare explode - a new section must show up on every site
 * that already saved an order, without anyone reopening the settings screen.
 *
 * @param string   $key   Option key holding the order.
 * @param string[] $known Every section key, in their shipped order.
 * @return string[]
 */
function antradus_section_order( $key, $known ) {
	$stored = antradus_opt( $key, '' );
	$wanted = array_filter( array_map( 'trim', explode( ',', (string) $stored ) ) );

	$out = array();
	foreach ( $wanted as $one ) {
		if ( in_array( $one, $known, true ) && ! in_array( $one, $out, true ) ) {
			$out[] = $one;
		}
	}
	foreach ( $known as $one ) {
		if ( ! in_array( $one, $out, true ) ) {
			$out[] = $one;
		}
	}
	return $out;
}

/**
 * Render a button, but only if its destination actually exists.
 *
 * @param string $label   Button text.
 * @param string $target  URL or "page:key".
 * @param string $variant primary|ghost|soft.
 * @param array  $attrs   Extra HTML attributes.
 */
function antradus_button( $label, $target, $variant = 'primary', $attrs = array() ) {
	$label = trim( (string) $label );
	$url   = antradus_link( $target );
	if ( '' === $label || '' === $url ) {
		return;
	}
	$extra = '';
	foreach ( $attrs as $name => $value ) {
		$extra .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}
	printf(
		'<a class="ant-btn ant-btn--%1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr( $variant ),
		esc_url( $url ),
		$extra, // phpcs:ignore WordPress.Security.EscapeOutput -- assembled from esc_attr() above.
		esc_html( $label )
	);
}

/**
 * The "which one are you?" chooser.
 *
 * Two links that send a reader to the page written for them. It is printed in
 * the home hero and again under the pricing cards, which is the whole reason
 * it is a function: the same fork asked in the two places a visitor is most
 * likely to be undecided, worded once.
 *
 * A row whose destination is a draft page resolves to '' and is skipped, and
 * if that leaves nothing the block is not printed at all - the same rule every
 * other link in the theme follows.
 *
 * @param string $rows_key  Repeater option key: icon, label, text, cta_url.
 * @param string $label_key Option key of the small line above the links.
 * @param string $class     Extra classes on the wrapper.
 */
function antradus_render_paths( $rows_key, $label_key = '', $class = '' ) {
	$rows  = antradus_rows( $rows_key );
	$links = array();

	foreach ( $rows as $row ) {
		$url   = antradus_link( antradus_cell( $row, 'cta_url' ) );
		$label = antradus_cell( $row, 'label' );
		if ( '' === $url || '' === $label ) {
			continue;
		}
		$links[] = array(
			'url'   => $url,
			'label' => $label,
			'text'  => antradus_cell( $row, 'text' ),
			'icon'  => antradus_cell( $row, 'icon', 'spark' ),
		);
	}

	if ( ! $links ) {
		return;
	}

	$label = $label_key ? trim( (string) antradus_opt( $label_key, '' ) ) : '';

	echo '<div class="ant-paths ' . esc_attr( $class ) . '">';
	if ( '' !== $label ) {
		echo '<p class="ant-paths-label">' . esc_html( $label ) . '</p>';
	}
	echo '<ul class="ant-paths-list">';
	foreach ( $links as $link ) {
		echo '<li><a class="ant-path" href="' . esc_url( $link['url'] ) . '">';
		echo '<span class="ant-path-ic">' . antradus_icon( $link['icon'], 19 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup.
		echo '<span class="ant-path-copy"><b>' . esc_html( $link['label'] ) . '</b>';
		if ( '' !== $link['text'] ) {
			echo '<span>' . esc_html( $link['text'] ) . '</span>';
		}
		echo '</span>';
		echo '<span class="ant-path-go" aria-hidden="true">&rarr;</span>';
		echo '</a></li>';
	}
	echo '</ul></div>';
}

/**
 * Section heading block: eyebrow, headline, sub-line.
 *
 * @param string $eyebrow Small label above the heading.
 * @param string $title   Heading, with an optional *accent*.
 * @param string $sub     Supporting sentence.
 * @param string $align   center|left.
 */
function antradus_section_head( $eyebrow, $title, $sub = '', $align = 'center' ) {
	if ( '' === trim( (string) $title ) && '' === trim( (string) $eyebrow ) ) {
		return;
	}
	echo '<div class="ant-head ant-head--' . esc_attr( $align ) . '">';
	if ( trim( (string) $eyebrow ) ) {
		echo '<p class="ant-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	if ( trim( (string) $title ) ) {
		echo '<h2 class="ant-h2">' . antradus_headline( $title ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside.
	}
	if ( trim( (string) $sub ) ) {
		echo '<p class="ant-sub">' . esc_html( $sub ) . '</p>';
	}
	echo '</div>';
}

/**
 * Resolve a form into markup.
 *
 * Accepts a shortcode from the settings, and falls back to the page's own
 * content when none is configured - which is how a form block dropped into the
 * editor keeps working.
 *
 * The subtle case is a shortcode whose plugin is not active: WordPress leaves
 * the text exactly as typed, so the page would print [forminator_form id="9"]
 * at the reader. That is worse than an empty panel, so an unresolved shortcode
 * is reported as no form at all.
 *
 * @param string $shortcode    Configured shortcode, or ''.
 * @param bool   $use_content  Fall back to the page content.
 * @return string Markup, or '' when there is no usable form.
 */
function antradus_form_html( $shortcode, $use_content = true ) {
	$shortcode = trim( (string) $shortcode );
	$html      = '';

	if ( '' !== $shortcode ) {
		$html = do_shortcode( $shortcode );
		if ( preg_match( '/^\s*\[[a-z0-9_\-]+[^\]]*\]\s*$/i', $html ) ) {
			return ''; // The shortcode came back untouched: its plugin is off.
		}
	} elseif ( $use_content && have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$html = apply_filters( 'the_content', get_the_content() );
		}
		rewind_posts();
	}

	$looks_empty = '' === trim( wp_strip_all_tags( $html ) )
		&& false === strpos( $html, '<form' )
		&& false === strpos( $html, '<input' );

	return $looks_empty ? '' : $html;
}

/**
 * One column of the compatibility diagram.
 *
 * @param array  $rows  Repeater rows of name + image.
 * @param string $title Column heading.
 * @param string $side  in|out.
 */
function antradus_compat_column( $rows, $title, $side ) {
	if ( ! $rows ) {
		return;
	}
	echo '<div class="ant-compat-col ant-compat-col--' . esc_attr( $side ) . '">';
	echo '<p class="ant-compat-label">' . esc_html( $title ) . '</p>';
	echo '<ul>';
	foreach ( $rows as $row ) {
		$name = antradus_cell( $row, 'name' );
		$url  = antradus_image_full_url( antradus_cell( $row, 'image' ) );
		echo '<li class="ant-glass ant-compat-item">';
		if ( $url ) {
			printf( '<img src="%s" alt="" loading="lazy" decoding="async">', esc_url( $url ) );
		} else {
			echo '<span class="ant-compat-dot" aria-hidden="true"></span>';
		}
		echo '<span>' . esc_html( $name ) . '</span>';
		echo '</li>';
	}
	echo '</ul></div>';
}

/**
 * One of the theme's line icons.
 *
 * Keeping the set in one place means a card in the settings screen can pick an
 * icon by name and never produce a broken glyph.
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 * @return string SVG markup.
 */
function antradus_icon( $name, $size = 22 ) {
	$icons = antradus_icon_set();
	$name  = isset( $icons[ $name ] ) ? $name : 'spark';
	return sprintf(
		'<svg class="ant-ic" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">%2$s</svg>',
		(int) $size,
		$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput -- static markup from antradus_icon_set().
	);
}

/**
 * The icon paths. Stroke colour is inherited, so one set serves every surface.
 *
 * @return array<string,string>
 */
function antradus_icon_set() {
	$s = 'stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'mic'      => '<path d="M12 3a3 3 0 013 3v6a3 3 0 01-6 0V6a3 3 0 013-3z" ' . $s . '/><path d="M5.5 11.5a6.5 6.5 0 0013 0M12 18v3" ' . $s . '/>',
		'pen'      => '<path d="M4 20l4-1L19 8a2 2 0 000-3l-1-1a2 2 0 00-3 0L4 15l-1 4z" ' . $s . '/>',
		'globe'    => '<circle cx="12" cy="12" r="8.5" ' . $s . '/><path d="M3.5 12h17M12 3.5c2.6 2.4 2.6 14.6 0 17M12 3.5c-2.6 2.4-2.6 14.6 0 17" ' . $s . '/>',
		'stack'    => '<path d="M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5M3 16.5l9 5 9-5" ' . $s . '/>',
		'play'     => '<rect x="3" y="5" width="18" height="14" rx="3" ' . $s . '/><path d="M11 9l4 3-4 3V9z" fill="currentColor"/>',
		'cart'     => '<path d="M4 5h2l2 11h9l2-8H7" ' . $s . '/><circle cx="10" cy="20" r="1.4" fill="currentColor"/><circle cx="17" cy="20" r="1.4" fill="currentColor"/>',
		'spark'    => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z" fill="currentColor"/>',
		'tag'      => '<path d="M4 4h7l9 9-7 7-9-9V4z" ' . $s . '/><circle cx="8" cy="8" r="1.4" fill="currentColor"/>',
		'image'    => '<rect x="3" y="4" width="18" height="16" rx="3" ' . $s . '/><circle cx="8.5" cy="9.5" r="1.7" fill="currentColor"/><path d="M4 18l5-5 4 4 3-3 4 4" ' . $s . '/>',
		'bulb'     => '<path d="M9 18h6M10 21h4M12 3a6 6 0 00-4 10c.7.7 1 1.3 1 2h6c0-.7.3-1.3 1-2a6 6 0 00-4-10z" ' . $s . '/>',
		'chart'    => '<path d="M4 20h16M7 20v-6M12 20V8M17 20v-9" ' . $s . '/>',
		'merge'    => '<path d="M5 4v3c0 3 3 4 7 6s0 5 0 7M19 4v3c0 3-3 4-6 5.5" ' . $s . '/>',
		'palette'  => '<path d="M12 3a9 9 0 000 18c1.4 0 2-1 2-2s-.6-1.5-.6-2 .6-1 1.6-1H17a4 4 0 004-4c0-4.4-4-9-9-9z" ' . $s . '/><circle cx="7.5" cy="11" r="1" fill="currentColor"/><circle cx="9.5" cy="7" r="1" fill="currentColor"/><circle cx="14.5" cy="7" r="1" fill="currentColor"/>',
		'lang'     => '<path d="M3 6h9M7.5 4v2c0 4-2 6.5-4.5 8M5 10c1.4 2 3.5 3.3 5.5 4" ' . $s . '/><path d="M12.5 20l4-9 4 9M14 17h5" ' . $s . '/>',
		'faq'      => '<rect x="3" y="4" width="18" height="13" rx="3" ' . $s . '/><path d="M8 17l-1 4 5-4" ' . $s . '/><path d="M9.6 8.5a2.4 2.4 0 013.7-2c1.3.8 1.1 2.5-.3 3.3-.7.4-1 .8-1 1.6" ' . $s . '/><circle cx="12" cy="14" r=".9" fill="currentColor"/>',
		'shield'   => '<path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" ' . $s . '/><path d="M9 12l2 2 4-4" ' . $s . '/>',
		'code'     => '<path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13 5l-2 14" ' . $s . '/>',
		'doc'      => '<path d="M6 3.5h7L18.5 9v11.5a1 1 0 01-1 1h-11a1 1 0 01-1-1v-16a1 1 0 011-1z" ' . $s . '/><path d="M13 3.5V9h5.5M8.5 13h7M8.5 16.5h4.5" ' . $s . '/>',
		'check'    => '<path d="M4 12l5 5 11-11" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
		'x'        => '<path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
		'link'     => '<path d="M10 13.5a4 4 0 006 .5l2-2a4 4 0 00-5.7-5.7l-1 1" ' . $s . '/><path d="M14 10.5a4 4 0 00-6-.5l-2 2a4 4 0 005.7 5.7l1-1" ' . $s . '/>',
		'search'   => '<circle cx="11" cy="11" r="6.5" ' . $s . '/><path d="M16 16l4.5 4.5" ' . $s . '/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="3" ' . $s . '/><path d="M4 7l8 6 8-6" ' . $s . '/>',
		'users'    => '<circle cx="9" cy="9" r="3.2" ' . $s . '/><path d="M3.5 19c.6-3 2.9-4.6 5.5-4.6S13.9 16 14.5 19" ' . $s . '/><path d="M16 6.4a3 3 0 010 5.6M17.5 19c-.2-1.6-.7-2.9-1.6-3.9" ' . $s . '/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5" ' . $s . '/><path d="M12 7.5V12l3 2" ' . $s . '/>',
		'quote'    => '<path d="M9.5 6.5C7 7.6 5.5 9.8 5.5 12.6c0 2.5 1.5 4.4 3.7 4.4 1.9 0 3.3-1.4 3.3-3.2 0-1.8-1.3-3.1-3-3.1-.3 0-.6 0-.8.1.3-1.3 1.2-2.4 2.5-3.1z" fill="currentColor"/><path d="M18 6.5c-2.5 1.1-4 3.3-4 6.1 0 2.5 1.5 4.4 3.7 4.4 1.9 0 3.3-1.4 3.3-3.2 0-1.8-1.3-3.1-3-3.1-.3 0-.6 0-.8.1.3-1.3 1.2-2.4 2.5-3.1z" fill="currentColor"/>',
		'wave'     => '<path d="M3 12h2.2M8 6.5v11M12 4v16M16 8v8M20.8 12H21" ' . $s . '/>',
		'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="3" ' . $s . '/><path d="M3.5 10h17M8 3.5v3M16 3.5v3" ' . $s . '/>',
		'download' => '<path d="M12 4v11M8 11.5l4 4 4-4M5 19.5h14" ' . $s . '/>',
		'rocket'   => '<path d="M14 4.5c3.5 0 5.5 2 5.5 5.5 0 4-4 8-7 9.5l-3.5-3.5C10.5 13 14.5 4.5 14 4.5z" ' . $s . '/><circle cx="14.5" cy="9.5" r="1.6" ' . $s . '/><path d="M8.5 15.5c-1.5.5-2.4 1.7-2.8 4 2.3-.4 3.5-1.3 4-2.8" ' . $s . '/>',
		'building' => '<path d="M4 20V6.5L12 4l8 2.5V20" ' . $s . '/><path d="M4 20h16M9.5 20v-4.5h5V20M8.5 9.5h2M13.5 9.5h2M8.5 13h2M13.5 13h2" ' . $s . '/>',
		'brain'    => '<path d="M12 5.5a2.5 2.5 0 00-4.8-1A2.6 2.6 0 005 7.2 2.6 2.6 0 004.2 11 2.7 2.7 0 006 15.4a2.6 2.6 0 006 1.1z" ' . $s . '/><path d="M12 5.5a2.5 2.5 0 014.8-1A2.6 2.6 0 0119 7.2a2.6 2.6 0 01.8 3.8A2.7 2.7 0 0118 15.4a2.6 2.6 0 01-6 1.1z" ' . $s . '/>',
	);
}

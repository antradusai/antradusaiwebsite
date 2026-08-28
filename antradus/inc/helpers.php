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
 * The nine pages this theme designs.
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
 * @return array<string,array<string,string>>
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
 * Turn a stored image value into a URL.
 *
 * Stored values are either an attachment ID (what the media picker writes) or
 * a plain URL (what a paste writes). Both are accepted.
 *
 * @param string $value Stored value.
 * @param string $size  Image size for attachment IDs.
 * @return string
 */
function antradus_image_url( $value, $size = 'large' ) {
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

	$value = antradus_opt( $key, '' );
	$url   = antradus_image_url( $value, 'full' );
	$class = trim( 'ant-media ' . $args['class'] );

	if ( '' === $url ) {
		antradus_placeholder( $args['label'] ? $args['label'] : $key, $args['ratio'], $args['class'] );
		return;
	}

	if ( ctype_digit( trim( (string) $value ) ) ) {
		echo wp_get_attachment_image(
			(int) $value,
			'full',
			false,
			array(
				'class'   => $class,
				'alt'     => $args['alt'],
				'style'   => 'aspect-ratio:' . $args['ratio'],
				'loading' => $args['eager'] ? 'eager' : 'lazy',
			)
		);
		return;
	}

	printf(
		'<img class="%1$s" src="%2$s" alt="%3$s" style="aspect-ratio:%4$s" loading="%5$s" decoding="async">',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $args['alt'] ),
		esc_attr( $args['ratio'] ),
		$args['eager'] ? 'eager' : 'lazy'
	);
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
		$url  = antradus_image_url( antradus_cell( $row, 'image' ), 'thumbnail' );
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

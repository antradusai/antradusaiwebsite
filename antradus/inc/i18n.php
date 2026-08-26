<?php
/**
 * Antradus theme - two languages, one set of settings.
 *
 * The site is published in English and Arabic. Rather than duplicating every
 * page, the theme keeps ONE structure and TWO vocabularies:
 *
 *   antradus_theme_options       the whole site: structure, links, images,
 *                                colours, plan IDs - and the English words.
 *   antradus_theme_options_ar    nothing but the Arabic words.
 *
 * A field is translatable when it is words a reader sees. A field is global
 * when it is a URL, an image, a colour, a shortcode, a Freemius ID or a slug -
 * those are the same in both languages by definition, so there is exactly one
 * place to edit them and no way for the two languages to drift apart.
 *
 * Repeaters (plans, cards, table rows) follow the same rule row by row: the
 * English side owns how many rows there are and what each one links to; the
 * Arabic side only carries the words for row 1, row 2, row 3. Add a plan once
 * and it exists in both languages immediately - untranslated at first, which
 * is visible and fixable, rather than missing.
 *
 * The reader chooses a language with ?lang=ar. That is deliberate: no rewrite
 * rules to flush, no second page tree to keep in sync, nothing stored in a
 * cookie (which would poison every page cache), and it works identically on
 * pages, posts and the plugin's docs. Every link the theme prints carries the
 * language forward, and <link rel="alternate" hreflang> tells search engines
 * the two addresses are the same page.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * The languages the site publishes in.
 *
 * @return array<string,array<string,string>>
 */
function antradus_languages() {
	/*
	 * Deliberately not wrapped in __(). Resolving a translation calls the
	 * gettext filter, the gettext filter asks which language this is, and
	 * asking that comes straight back here - a loop with no bottom. These two
	 * labels are the site's own names for its languages and are the same in
	 * both of them, so there is nothing to translate anyway.
	 */
	return array(
		'en' => array(
			'label'  => 'English',
			'native' => 'English',
			'short'  => 'EN',
			'html'   => 'en',
			'dir'    => 'ltr',
			'locale' => 'en_US',
		),
		'ar' => array(
			'label'  => 'Arabic',
			'native' => "\xD8\xA7\xD9\x84\xD8\xB9\xD8\xB1\xD8\xA8\xD9\x8A\xD8\xA9",
			'short'  => "\xD8\xB9",
			'html'   => 'ar',
			'dir'    => 'rtl',
			'locale' => 'ar',
		),
	);
}

/**
 * The language everything falls back to.
 *
 * @return string
 */
function antradus_default_lang() {
	return 'en';
}

/**
 * Is this a language we publish in?
 *
 * @param string $lang Candidate.
 * @return bool
 */
function antradus_is_lang( $lang ) {
	$langs = antradus_languages();
	return is_string( $lang ) && isset( $langs[ $lang ] );
}

/**
 * The language of this request.
 *
 * On the front end that is ?lang=. In wp-admin it is whichever language tab
 * the settings screen is showing, which is what makes one field renderer serve
 * both vocabularies.
 *
 * @return string
 */
function antradus_lang() {
	if ( isset( $GLOBALS['antradus_lang_context'] ) && antradus_is_lang( $GLOBALS['antradus_lang_context'] ) ) {
		return $GLOBALS['antradus_lang_context'];
	}

	static $lang = null;
	if ( null !== $lang ) {
		return $lang;
	}

	$lang = antradus_default_lang();

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public display preference, not an action.
	$asked = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
	if ( antradus_is_lang( $asked ) ) {
		$lang = $asked;
	}

	return $lang;
}

/**
 * Force a language for the duration of a render. Pass null to let go again.
 *
 * @param string|null $lang Language, or null.
 */
function antradus_set_lang_context( $lang ) {
	if ( null === $lang ) {
		unset( $GLOBALS['antradus_lang_context'] );
		return;
	}
	if ( antradus_is_lang( $lang ) ) {
		$GLOBALS['antradus_lang_context'] = $lang;
	}
}

/**
 * Are we rendering right to left?
 *
 * @return bool
 */
function antradus_is_rtl() {
	$langs = antradus_languages();
	$lang  = antradus_lang();
	return isset( $langs[ $lang ] ) && 'rtl' === $langs[ $lang ]['dir'];
}

/**
 * Where a language's words are stored.
 *
 * English lives in the main option, because English is also where every global
 * value lives. Any other language gets its own small row of nothing but words.
 *
 * @param string $lang Language.
 * @return string
 */
function antradus_option_name( $lang ) {
	return ( antradus_default_lang() === $lang ) ? ANTRADUS_OPTION : ANTRADUS_OPTION . '_' . $lang;
}

/**
 * The shipped content for a language.
 *
 * @param string $lang Language.
 * @return array
 */
function antradus_default_options_for( $lang ) {
	if ( 'ar' === $lang && function_exists( 'antradus_default_options_ar' ) ) {
		return antradus_default_options_ar();
	}
	return antradus_default_options();
}

/* ===========================================================================
 * Which fields carry words, and which carry wiring
 * ========================================================================= */

/**
 * Field keys that are wiring, not words, even though they are text boxes.
 *
 * Kept as one list rather than a flag scattered through the schema, because
 * the question "is this translated?" has to have the same answer in four
 * places - the editor, the sanitizer, the merge and the progress count - and
 * one list cannot disagree with itself.
 *
 * Suffixes cover the CTA targets, which is most of them: anything ending in
 * _url, _target or _link points somewhere rather than saying something.
 *
 * @return array{keys:string[],suffixes:string[],prefixes:string[]}
 */
function antradus_global_field_keys() {
	return array(
		'keys'     => array(
			'brand_logo',
			'brand_accent',
			'brand_width',
			'fs_product_id',
			'fs_public_key',
			'fs_logo',
			'fs_snippet',
			'contact_form',
			'aff_form',
			'welcome_form',
			'blog_per_page',
			// Repeater sub-keys.
			'link',
			'cta_url',
			'plan_id',
			'icon',
			'image',
		),
		'suffixes' => array( '_url', '_target', '_link' ),
		'prefixes' => array( 'slug_' ),
	);
}

/**
 * Is this field one a translator should see?
 *
 * Types decide most of it - an image, a colour or a checkbox is never
 * translated. Text fields that hold a URL, an ID or a shortcode are named in
 * antradus_global_field_keys(), and a schema entry can always settle it
 * outright with 'i18n' => false.
 *
 * @param array $field Field definition.
 * @return bool
 */
function antradus_field_is_translatable( $field ) {
	if ( isset( $field['i18n'] ) ) {
		return (bool) $field['i18n'];
	}

	$type = isset( $field['type'] ) ? $field['type'] : 'text';
	if ( ! in_array( $type, array( 'text', 'textarea', 'repeater' ), true ) ) {
		return false;
	}

	$key   = isset( $field['key'] ) ? (string) $field['key'] : '';
	$rules = antradus_global_field_keys();

	if ( in_array( $key, $rules['keys'], true ) ) {
		return false;
	}
	foreach ( $rules['suffixes'] as $suffix ) {
		if ( strlen( $key ) > strlen( $suffix ) && substr( $key, -strlen( $suffix ) ) === $suffix ) {
			return false;
		}
	}
	foreach ( $rules['prefixes'] as $prefix ) {
		if ( 0 === strpos( $key, $prefix ) ) {
			return false;
		}
	}

	// A repeater is worth translating only if something inside it is.
	if ( 'repeater' === $type ) {
		return (bool) antradus_repeater_translatable_fields( $field );
	}

	return true;
}

/**
 * The translatable sub-fields of a repeater, keyed by sub-key.
 *
 * @param array $field Repeater definition.
 * @return array<string,array>
 */
function antradus_repeater_translatable_fields( $field ) {
	$out = array();
	if ( empty( $field['fields'] ) ) {
		return $out;
	}
	foreach ( $field['fields'] as $sub ) {
		if ( antradus_field_is_translatable( $sub ) ) {
			$out[ $sub['key'] ] = $sub;
		}
	}
	return $out;
}

/* ===========================================================================
 * Reading
 * ========================================================================= */

/**
 * The raw stored words for one language, over that language's shipped content.
 *
 * @param string $lang Language.
 * @return array
 */
function antradus_stored_options( $lang ) {
	static $cache = array();
	if ( isset( $cache[ $lang ] ) ) {
		return $cache[ $lang ];
	}
	$saved          = get_option( antradus_option_name( $lang ), array() );
	$saved          = is_array( $saved ) ? $saved : array();
	$cache[ $lang ] = array_merge( antradus_default_options_for( $lang ), $saved );
	return $cache[ $lang ];
}

/**
 * Everything the templates read, resolved for one language.
 *
 * Global fields always come from the base option. Translatable fields come
 * from the language when it has something to say, and from the base when it
 * does not - so a half-translated site is readable rather than half-empty.
 *
 * @param string|null $lang Language, or null for the current one.
 * @return array
 */
function antradus_options( $lang = null ) {
	$lang = ( null === $lang ) ? antradus_lang() : $lang;
	$lang = antradus_is_lang( $lang ) ? $lang : antradus_default_lang();

	static $cache = array();
	if ( isset( $cache[ $lang ] ) ) {
		return $cache[ $lang ];
	}

	$base = antradus_stored_options( antradus_default_lang() );

	if ( antradus_default_lang() === $lang ) {
		$cache[ $lang ] = $base;
		return $base;
	}

	$words  = antradus_stored_options( $lang );
	$schema = antradus_schema_fields();
	$out    = $base;

	foreach ( $schema as $key => $field ) {
		if ( ! antradus_field_is_translatable( $field ) ) {
			continue;
		}

		if ( 'repeater' === ( isset( $field['type'] ) ? $field['type'] : 'text' ) ) {
			$out[ $key ] = antradus_merge_repeater_words(
				$field,
				isset( $base[ $key ] ) ? $base[ $key ] : array(),
				isset( $words[ $key ] ) ? $words[ $key ] : array()
			);
			continue;
		}

		if ( isset( $words[ $key ] ) && is_string( $words[ $key ] ) && '' !== trim( $words[ $key ] ) ) {
			$out[ $key ] = $words[ $key ];
		}
	}

	$cache[ $lang ] = $out;
	return $out;
}

/**
 * Lay a language's words over the structural rows.
 *
 * The base decides how many rows there are; the translation only replaces the
 * words inside them. A translation row that is missing, or a cell left blank,
 * simply keeps the base wording.
 *
 * @param array $field Repeater definition.
 * @param mixed $base  Structural rows.
 * @param mixed $words Translated rows.
 * @return array
 */
function antradus_merge_repeater_words( $field, $base, $words ) {
	$base  = is_array( $base ) ? array_values( $base ) : array();
	$words = is_array( $words ) ? array_values( $words ) : array();
	if ( ! $words ) {
		return $base;
	}
	$translatable = antradus_repeater_translatable_fields( $field );
	if ( ! $translatable ) {
		return $base;
	}

	foreach ( $base as $index => $row ) {
		if ( ! is_array( $row ) || ! isset( $words[ $index ] ) || ! is_array( $words[ $index ] ) ) {
			continue;
		}
		foreach ( $translatable as $sub_key => $unused ) {
			if ( isset( $words[ $index ][ $sub_key ] ) && is_string( $words[ $index ][ $sub_key ] ) && '' !== trim( $words[ $index ][ $sub_key ] ) ) {
				$base[ $index ][ $sub_key ] = $words[ $index ][ $sub_key ];
			}
		}
	}
	return $base;
}

/**
 * How much of a language has actually been written.
 *
 * @param string $lang Language.
 * @return array{done:int,total:int}
 */
function antradus_translation_progress( $lang ) {
	$done  = 0;
	$total = 0;
	if ( antradus_default_lang() === $lang ) {
		return array(
			'done'  => 1,
			'total' => 1,
		);
	}
	$words = antradus_stored_options( $lang );
	$base  = antradus_stored_options( antradus_default_lang() );

	foreach ( antradus_schema_fields() as $key => $field ) {
		if ( ! antradus_field_is_translatable( $field ) ) {
			continue;
		}

		// A field the English side leaves empty has nothing to translate, so
		// counting it would make a finished translation read as 124 / 127 and
		// send somebody hunting for three fields that do not want words.
		$source = isset( $base[ $key ] ) ? $base[ $key ] : '';
		if ( is_array( $source ) ? empty( $source ) : '' === trim( (string) $source ) ) {
			continue;
		}

		++$total;
		$value = isset( $words[ $key ] ) ? $words[ $key ] : '';
		if ( is_array( $value ) ? ! empty( $value ) : '' !== trim( (string) $value ) ) {
			++$done;
		}
	}
	return array(
		'done'  => $done,
		'total' => max( 1, $total ),
	);
}

/* ===========================================================================
 * Carrying the language through the site
 * ========================================================================= */

/**
 * Add ?lang= to a URL, unless it is already the default language or off-site.
 *
 * @param string      $url  URL.
 * @param string|null $lang Language, or null for the current one.
 * @return string
 */
function antradus_localize_url( $url, $lang = null ) {
	$url  = (string) $url;
	$lang = ( null === $lang ) ? antradus_lang() : $lang;

	if ( '' === $url || ! antradus_is_lang( $lang ) ) {
		return $url;
	}
	if ( antradus_default_lang() === $lang ) {
		return remove_query_arg( 'lang', $url );
	}
	if ( '#' === substr( $url, 0, 1 ) ) {
		return $url;
	}
	if ( 0 === strpos( $url, 'mailto:' ) || 0 === strpos( $url, 'tel:' ) ) {
		return $url;
	}
	// Only our own pages. An outbound link is not ours to annotate.
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( $host && $host !== wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) {
		return $url;
	}

	return add_query_arg( 'lang', $lang, $url );
}

/**
 * The address of the page being read, in another language.
 *
 * @param string $lang Language.
 * @return string
 */
function antradus_lang_switch_url( $lang ) {
	global $wp;
	$request = ( isset( $wp->request ) && '' !== $wp->request ) ? $wp->request : '';
	$current = $request ? home_url( user_trailingslashit( $request ) ) : home_url( '/' );

	// Keep whatever the reader was doing - a search, a page number, a filter.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- rebuilding the current address.
	$query = isset( $_GET ) ? (array) $_GET : array();
	unset( $query['lang'] );
	$keep = array();
	foreach ( $query as $key => $value ) {
		if ( is_scalar( $value ) ) {
			$keep[ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( (string) $value ) );
		}
	}
	if ( $keep ) {
		$current = add_query_arg( $keep, $current );
	}

	return antradus_localize_url( $current, $lang );
}

add_filter( 'language_attributes', 'antradus_language_attributes' );
/**
 * Say which language and which direction the document is in.
 *
 * @param string $output Existing attributes.
 * @return string
 */
function antradus_language_attributes( $output ) {
	if ( is_admin() ) {
		return $output;
	}
	$langs = antradus_languages();
	$lang  = antradus_lang();
	if ( ! isset( $langs[ $lang ] ) ) {
		return $output;
	}
	$def = $langs[ $lang ];

	// Replace rather than append: two lang attributes is not better than one.
	$output = preg_replace( '/\s*lang="[^"]*"/', '', (string) $output );
	$output = preg_replace( '/\s*dir="[^"]*"/', '', (string) $output );

	return trim( $output . ' lang="' . esc_attr( $def['html'] ) . '" dir="' . esc_attr( $def['dir'] ) . '"' );
}

add_action( 'wp_head', 'antradus_hreflang_tags', 2 );
/**
 * Tell search engines the two addresses are the same page.
 */
function antradus_hreflang_tags() {
	if ( is_404() ) {
		return;
	}
	foreach ( antradus_languages() as $code => $def ) {
		printf(
			'<link rel="alternate" hreflang="%1$s" href="%2$s">' . "\n",
			esc_attr( $def['html'] ),
			esc_url( antradus_lang_switch_url( $code ) )
		);
	}
	printf(
		'<link rel="alternate" hreflang="x-default" href="%s">' . "\n",
		esc_url( antradus_lang_switch_url( antradus_default_lang() ) )
	);
}

add_filter( 'body_class', 'antradus_lang_body_class' );
/**
 * @param array $classes Existing classes.
 * @return array
 */
function antradus_lang_body_class( $classes ) {
	$classes[] = 'ant-lang-' . antradus_lang();
	if ( antradus_is_rtl() ) {
		$classes[] = 'ant-rtl';
	}
	return $classes;
}

/*
 * Keep the language on internal links.
 *
 * Only the reader's own navigation is rewritten - never a feed, never a REST
 * response, never an admin screen - so nothing a machine reads changes shape
 * because a human was reading Arabic.
 */
add_filter( 'post_link', 'antradus_filter_permalink', 20 );
add_filter( 'page_link', 'antradus_filter_permalink', 20 );
add_filter( 'post_type_link', 'antradus_filter_permalink', 20 );
add_filter( 'term_link', 'antradus_filter_permalink', 20 );
add_filter( 'paginate_links', 'antradus_filter_permalink', 20 );
/**
 * @param string $url Permalink.
 * @return string
 */
function antradus_filter_permalink( $url ) {
	if ( is_admin() || is_feed() || wp_doing_ajax() || antradus_default_lang() === antradus_lang() ) {
		return $url;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return $url;
	}
	return antradus_localize_url( $url );
}

/**
 * The language switcher, as a list of links.
 *
 * @return array<int,array{code:string,label:string,short:string,url:string,current:bool}>
 */
function antradus_lang_links() {
	$out     = array();
	$current = antradus_lang();

	/*
	 * The documentation is English-only and forces itself back to English, so
	 * on those pages "العربية" would reload the same English page and look
	 * broken. Send it to the home page in that language instead: the switch
	 * still does what it says - it takes you to the site in Arabic - it just
	 * cannot take you to this page in Arabic, because there isn't one.
	 */
	$stranded = function_exists( 'antradus_docs_is_english_only' ) && antradus_docs_is_english_only();

	foreach ( antradus_languages() as $code => $def ) {
		$url = ( $stranded && $code !== $current )
			? antradus_localize_url( home_url( '/' ), $code )
			: antradus_lang_switch_url( $code );

		$out[] = array(
			'code'    => $code,
			'label'   => $def['native'],
			'short'   => $def['short'],
			'url'     => $url,
			'current' => ( $code === $current ),
		);
	}
	return $out;
}

/**
 * Render the header / footer language switch.
 *
 * @param string $class Extra classes.
 */
function antradus_lang_switch( $class = '' ) {
	$links = antradus_lang_links();
	if ( count( $links ) < 2 ) {
		return;
	}
	printf(
		'<div class="ant-langs %s" role="group" aria-label="%s">',
		esc_attr( $class ),
		esc_attr__( 'Language', 'antradus' )
	);
	foreach ( $links as $link ) {
		printf(
			'<a class="ant-lang%1$s" href="%2$s" hreflang="%3$s" lang="%3$s"%4$s>%5$s</a>',
			$link['current'] ? ' is-current' : '',
			esc_url( $link['url'] ),
			esc_attr( $link['code'] ),
			$link['current'] ? ' aria-current="true"' : '',
			esc_html( $link['label'] )
		);
	}
	echo '</div>';
}

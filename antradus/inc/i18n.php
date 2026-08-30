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
 * when it is a URL, an image, a colour, a Freemius ID or a slug - those are
 * the same in both languages by definition, so there is exactly one place to
 * edit them and no way for the two languages to drift apart.
 *
 * Form shortcodes are the exception, and they earned it. They look like pure
 * wiring, and they were global until it turned out a form IS words: its
 * labels, its placeholder text, its confirmation email and its error
 * messages are all written in one language, so a site publishing in two
 * languages builds two forms and has two shortcodes. Each language now holds
 * its own, and an Arabic one left empty still falls back to the English form
 * rather than leaving the page with no form at all.
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

/* ===========================================================================
 * Published, or still being written
 * ========================================================================= */

/**
 * Is this language switched on for readers?
 *
 * A language the theme knows about is not the same thing as a language the
 * site publishes in. Arabic can be written, proof-read and previewed for weeks
 * before anybody outside wp-admin is allowed to see it, which is the whole
 * point of the switch on the Brand tab: go live in English, finish the Arabic
 * afterwards, turn it on in one tick.
 *
 * The option is read raw, on purpose. antradus_opt() resolves through
 * antradus_options(), antradus_options() asks which language this is, and that
 * asks this - a loop with no bottom, and the same trap antradus_languages()
 * already dodges by never calling __(). get_option() plus the shipped defaults
 * answers the question without going round.
 *
 * @param string $lang Language code.
 * @return bool
 */
function antradus_lang_is_published( $lang ) {
	if ( ! antradus_is_lang( $lang ) ) {
		return false;
	}
	// The default language is the site. It cannot be switched off.
	if ( antradus_default_lang() === $lang ) {
		return true;
	}

	static $cache = array();
	if ( isset( $cache[ $lang ] ) ) {
		return $cache[ $lang ];
	}

	$key   = 'lang_' . $lang . '_publish';
	$saved = get_option( ANTRADUS_OPTION, array() );
	$saved = is_array( $saved ) ? $saved : array();

	if ( array_key_exists( $key, $saved ) ) {
		$value = $saved[ $key ];
	} else {
		$shipped = antradus_default_options();
		$value   = isset( $shipped[ $key ] ) ? $shipped[ $key ] : '';
	}

	$cache[ $lang ] = ( '1' === (string) $value );
	return $cache[ $lang ];
}

/**
 * May the person reading this see a language that is not published yet?
 *
 * Whoever can edit the settings can preview the translation on the real site,
 * because reading it in a settings table is not the same as reading it laid
 * out right to left at the width it will actually be published at. Everybody
 * else is sent to the English page - see antradus_redirect_unpublished_lang().
 *
 * @return bool
 */
function antradus_can_preview_langs() {
	static $can = null;
	if ( null !== $can ) {
		return $can;
	}

	/*
	 * Answered, but not remembered, until WordPress knows who is asking.
	 *
	 * The theme's own gettext filter calls antradus_lang() from
	 * after_setup_theme, which runs before WP has resolved the login cookie
	 * into a user. Caching "no" at that moment would be caching the answer to
	 * a question nobody had asked yet, and an administrator would then be
	 * refused the preview for the whole request - including at wp_head and in
	 * the templates, which are the only places the answer matters.
	 */
	if ( ! function_exists( 'current_user_can' ) || ! did_action( 'init' ) ) {
		return false;
	}

	$can = current_user_can( antradus_settings_cap() );
	return $can;
}

/**
 * The languages a reader is offered.
 *
 * @return array<string,array<string,string>>
 */
function antradus_public_languages() {
	$out = array();
	foreach ( antradus_languages() as $code => $def ) {
		if ( antradus_lang_is_published( $code ) ) {
			$out[ $code ] = $def;
		}
	}
	return $out;
}

/**
 * Is this request looking at a language the public cannot see yet?
 *
 * @return bool
 */
function antradus_is_lang_preview() {
	return ! is_admin() && ! antradus_lang_is_published( antradus_lang() );
}

/**
 * The language of this request.
 *
 * On the front end that is ?lang=. In wp-admin it is whichever language tab
 * the settings screen is showing, which is what makes one field renderer serve
 * both vocabularies.
 *
 * A language that is switched off is not a language on the front end: ?lang=ar
 * resolves to English for a reader, and only to Arabic for somebody who can
 * edit the settings, so the translation can be proof-read on the real site
 * before it goes live. wp-admin always honours the request, because that is
 * the screen the translation is written on.
 *
 * @return string
 */
function antradus_lang() {
	if ( isset( $GLOBALS['antradus_lang_context'] ) && antradus_is_lang( $GLOBALS['antradus_lang_context'] ) ) {
		return $GLOBALS['antradus_lang_context'];
	}

	/*
	 * What was asked for is fixed for the request and worth remembering. What
	 * the answer is, is not: it depends on who is reading, and who is reading
	 * is not known yet the first time this runs - see
	 * antradus_can_preview_langs(). So the parse is cached and the decision is
	 * taken fresh, which costs one array lookup and two memoised calls.
	 */
	static $asked = null;
	if ( null === $asked ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public display preference, not an action.
		$asked = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
		$asked = antradus_is_lang( $asked ) ? $asked : '';
	}

	if ( '' === $asked ) {
		return antradus_default_lang();
	}
	if ( antradus_lang_is_published( $asked ) || is_admin() || antradus_can_preview_langs() ) {
		return $asked;
	}
	return antradus_default_lang();
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
			'blog_per_page',
			// Repeater sub-keys.
			'link',
			'cta_url',
			'plan_id',
			'licenses',
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
 * translated. Text fields that hold a URL or an ID are named in
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
 * May "bring back the shipped wording" replace this field?
 *
 * Nearly the same question as the one above, and it used to be the same
 * answer, which is why the restore simply asked that one. The form shortcodes
 * split the two apart: a translator has to see them, because a form is built
 * in a language, but the shipped default for one is a Forminator ID belonging
 * to this site and restoring it over somebody else's would point their contact
 * page at our form. A field says so for itself with 'restore' => false, next to
 * the field, rather than in a second list that can fall out of step with this
 * one.
 *
 * @param array $field Field definition.
 * @return bool
 */
function antradus_field_is_restorable( $field ) {
	if ( ! antradus_field_is_translatable( $field ) ) {
		return false;
	}
	return ( ! isset( $field['restore'] ) || false !== $field['restore'] );
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
 *
 * Only for languages that are actually published. An hreflang pointing at a
 * language the site is not serving yet is an invitation to crawl and index a
 * page that answers in English, which is the one outcome a switched-off
 * translation is meant to avoid.
 *
 * A single published language needs no alternates at all - hreflang describes
 * a set of equivalents, and a set of one is just the page.
 */
function antradus_hreflang_tags() {
	if ( is_404() ) {
		return;
	}
	$langs = antradus_public_languages();
	if ( count( $langs ) < 2 ) {
		return;
	}
	foreach ( $langs as $code => $def ) {
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

add_action( 'template_redirect', 'antradus_redirect_unpublished_lang' );
/**
 * Send a reader who asked for a switched-off language back to the English page.
 *
 * Rendering English at /pricing/?lang=ar instead would leave the same article
 * on two addresses, which is a duplicate a search engine has to pick between
 * and a link somebody can share by accident. A 302 is right rather than a 301:
 * the language is coming back, and a permanent redirect cached in every
 * browser that ever saw it would outlive the decision.
 *
 * Somebody who can edit the settings is previewing, and is left alone.
 */
function antradus_redirect_unpublished_lang() {
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_robots() ) {
		return;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public display preference, not an action.
	$asked = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
	if ( '' === $asked ) {
		return;
	}

	// A language that is live is the normal case and stays where it is.
	if ( antradus_lang_is_published( $asked ) ) {
		return;
	}
	// A language that is written but not live is previewable by its editor.
	if ( antradus_is_lang( $asked ) && antradus_can_preview_langs() ) {
		return;
	}
	// Anything else - a switched-off language, or ?lang=fr typed by a crawler -
	// is a second address for a page that has only one, so it is folded back.

	wp_safe_redirect( antradus_lang_switch_url( antradus_default_lang() ), 302 );
	exit;
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
	if ( antradus_is_lang_preview() ) {
		$classes[] = 'ant-lang-preview';
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
 * Only published languages, unless the person reading can edit the settings -
 * they get the unpublished ones too, flagged, so previewing the translation is
 * the same two clicks as reading the site.
 *
 * @return array<int,array{code:string,label:string,short:string,url:string,current:bool,draft:bool}>
 */
function antradus_lang_links() {
	$out     = array();
	$current = antradus_lang();
	$preview = antradus_can_preview_langs();

	/*
	 * The documentation is English-only and forces itself back to English, so
	 * on those pages "العربية" would reload the same English page and look
	 * broken. Send it to the home page in that language instead: the switch
	 * still does what it says - it takes you to the site in Arabic - it just
	 * cannot take you to this page in Arabic, because there isn't one.
	 */
	$stranded = function_exists( 'antradus_docs_is_english_only' ) && antradus_docs_is_english_only();

	foreach ( antradus_languages() as $code => $def ) {
		$live = antradus_lang_is_published( $code );
		if ( ! $live && ! $preview ) {
			continue;
		}

		$url = ( $stranded && $code !== $current )
			? antradus_localize_url( home_url( '/' ), $code )
			: antradus_lang_switch_url( $code );

		$out[] = array(
			'code'    => $code,
			'label'   => $def['native'],
			'short'   => $def['short'],
			'url'     => $url,
			'current' => ( $code === $current ),
			'draft'   => ! $live,
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
		$classes = 'ant-lang';
		$classes .= $link['current'] ? ' is-current' : '';
		$classes .= ! empty( $link['draft'] ) ? ' is-draft' : '';

		printf(
			'<a class="%1$s" href="%2$s" hreflang="%3$s" lang="%3$s"%4$s%5$s>%6$s</a>',
			esc_attr( $classes ),
			esc_url( $link['url'] ),
			esc_attr( $link['code'] ),
			$link['current'] ? ' aria-current="true"' : '',
			! empty( $link['draft'] ) ? ' title="' . esc_attr__( 'Not published - only you can see this', 'antradus' ) . '"' : '',
			esc_html( $link['label'] )
		);
	}
	echo '</div>';
}

add_action( 'wp_body_open', 'antradus_lang_preview_notice' );
/**
 * Say out loud that this page is not the one a reader gets.
 *
 * Previewing a switched-off translation without being told is how a site ends
 * up "live in Arabic" in one person's browser and nowhere else. The strip only
 * ever renders for somebody who can turn the language on, and it links to the
 * switch that does it.
 */
function antradus_lang_preview_notice() {
	if ( is_admin() || ! antradus_is_lang_preview() || ! antradus_can_preview_langs() ) {
		return;
	}
	$langs = antradus_languages();
	$lang  = antradus_lang();
	$name  = isset( $langs[ $lang ] ) ? $langs[ $lang ]['native'] : $lang;

	printf(
		'<div class="ant-preview-bar" role="status"><strong>%1$s</strong> <span>%2$s</span> <a href="%3$s">%4$s</a></div>',
		esc_html(
			sprintf(
				/* translators: %s: language name. */
				__( '%s is switched off.', 'antradus' ),
				$name
			)
		),
		esc_html__( 'You are previewing it. Visitors are sent to the English page.', 'antradus' ),
		esc_url( admin_url( 'themes.php?page=antradus-content&tab=brand' ) ),
		esc_html__( 'Turn it on', 'antradus' )
	);
}

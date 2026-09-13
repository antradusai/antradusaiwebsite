<?php
/**
 * Antradus theme - the search metadata for the ten designed pages.
 *
 * The theme does not want to be an SEO plugin, and this file is careful not to
 * become one. What it owns is narrow and specific:
 *
 *   1. A focus keyword, a search title and a meta description for each of the
 *      ten pages the theme designs, in both languages, editable like every
 *      other word on the site. They ship written rather than blank, because a
 *      description nobody got round to writing is the usual reason a page goes
 *      live with the first two lines of its hero in the search results.
 *
 *   2. One button that copies the English set into Rank Math, as the post meta
 *      Rank Math itself writes. After that the values are Rank Math's: it
 *      scores them, previews them and renders them, and this theme prints
 *      nothing at all on an English page.
 *
 *   3. The Arabic half, which Rank Math has nowhere to put. Both languages
 *      live at the same post - /pricing/ and /pricing/?lang=ar - and post meta
 *      has room for one title and one description. So on an Arabic page the
 *      theme swaps its own values in through the SEO plugin's own filters, and
 *      corrects the canonical, which would otherwise point every Arabic page
 *      at its English twin and quietly ask Google to ignore the translation.
 *
 *      The button writes Rank Math, because that is what this site runs.
 *      Serving Arabic is a different question and is answered for Yoast too:
 *      whichever of the two is installed, an Arabic page has to go out with
 *      Arabic metadata rather than English metadata nobody noticed.
 *
 * With no SEO plugin installed at all, the same values are printed directly -
 * a description, an Open Graph block and a canonical - so the metadata is
 * worth having on day one rather than after a plugin decision.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * The three things we hold per page.
 *
 * @return array<string,string> Part key => admin label.
 */
function antradus_seo_parts() {
	return array(
		'focus' => __( 'Focus keyword', 'antradus' ),
		'title' => __( 'Search title', 'antradus' ),
		'desc'  => __( 'Meta description', 'antradus' ),
	);
}

/**
 * The option key holding one part of one page's metadata.
 *
 * @param string $page Page key, e.g. 'pricing'.
 * @param string $part 'focus', 'title' or 'desc'.
 * @return string
 */
function antradus_seo_key( $page, $part ) {
	return 'seo_' . $page . '_' . $part;
}

/**
 * One value, in one language, with the usual English fallback.
 *
 * @param string      $page Page key.
 * @param string      $part Part key.
 * @param string|null $lang Language, or null for the current one.
 * @return string
 */
function antradus_seo_value( $page, $part, $lang = null ) {
	$all = antradus_options( $lang );
	$key = antradus_seo_key( $page, $part );
	return isset( $all[ $key ] ) ? trim( (string) $all[ $key ] ) : '';
}

/**
 * All three, for one page, in one language.
 *
 * @param string      $page Page key.
 * @param string|null $lang Language, or null for the current one.
 * @return array{focus:string,title:string,desc:string}
 */
function antradus_seo_set( $page, $lang = null ) {
	return array(
		'focus' => antradus_seo_value( $page, 'focus', $lang ),
		'title' => antradus_seo_value( $page, 'title', $lang ),
		'desc'  => antradus_seo_value( $page, 'desc', $lang ),
	);
}

/* ===========================================================================
 * Which SEO plugin, if any
 * ========================================================================= */

/**
 * Is Rank Math running?
 *
 * @return bool
 */
function antradus_seo_rank_math_active() {
	return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
}

/**
 * The SEO plugin in charge of the document head, if there is one.
 *
 * Only used to decide whether the theme should print anything itself. Two
 * plugins writing a meta description is worse than none.
 *
 * @return string Slug, or '' when the theme is on its own.
 */
function antradus_seo_plugin() {
	if ( antradus_seo_rank_math_active() ) {
		return 'rankmath';
	}
	if ( defined( 'WPSEO_VERSION' ) ) {
		return 'yoast';
	}
	if ( defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' ) ) {
		return 'aioseo';
	}
	if ( defined( 'SEOPRESS_VERSION' ) ) {
		return 'seopress';
	}
	return '';
}

/**
 * The Rank Math post meta keys we write, by part.
 *
 * These are Rank Math's own field names, which is the entire point: nothing
 * here is a parallel store the plugin has to be taught about. Write them and
 * the metabox shows the values, the analysis scores them, and the front end
 * renders them, exactly as if they had been typed in by hand.
 *
 * @return array<string,string>
 */
function antradus_seo_rank_math_keys() {
	return array(
		'focus' => 'rank_math_focus_keyword',
		'title' => 'rank_math_title',
		'desc'  => 'rank_math_description',
	);
}

/* ===========================================================================
 * The button: copy the English set into Rank Math
 * ========================================================================= */

/**
 * What one page would do if the button were pressed.
 *
 * Worked out separately from the writing so the settings screen can show the
 * outcome before anybody commits to it - which pages are ready, which have no
 * page to write to yet, and which already hold something of their own.
 *
 * @param string $page      Page key.
 * @param bool   $overwrite Replace values already set in Rank Math.
 * @return array{page:string,post:int,status:string,fields:array<string,string>}
 */
function antradus_seo_plan_page( $page, $overwrite = false ) {
	$out = array(
		'page'   => $page,
		'post'   => 0,
		'status' => 'nopage',
		'fields' => array(),
	);

	$object = antradus_page_object( $page );
	if ( ! $object ) {
		return $out;
	}
	$out['post'] = (int) $object->ID;

	$values = antradus_seo_set( $page, antradus_default_lang() );
	$keys   = antradus_seo_rank_math_keys();

	$writes = 0;
	$kept   = 0;
	foreach ( $keys as $part => $meta_key ) {
		if ( '' === $values[ $part ] ) {
			$out['fields'][ $part ] = 'empty';
			continue;
		}
		$current = trim( (string) get_post_meta( $out['post'], $meta_key, true ) );

		if ( $current === $values[ $part ] ) {
			$out['fields'][ $part ] = 'same';
			continue;
		}
		if ( '' !== $current && ! $overwrite ) {
			$out['fields'][ $part ] = 'kept';
			++$kept;
			continue;
		}
		$out['fields'][ $part ] = 'write';
		++$writes;
	}

	if ( $writes ) {
		$out['status'] = 'write';
	} elseif ( $kept ) {
		$out['status'] = 'kept';
	} else {
		$out['status'] = 'same';
	}

	return $out;
}

/**
 * Copy the English metadata onto the pages, as Rank Math's own post meta.
 *
 * @param bool $overwrite Replace values already set in Rank Math.
 * @return array{pages:int,fields:int,kept:int,missing:string[]}
 */
function antradus_seo_push_to_rank_math( $overwrite = false ) {
	$report = array(
		'pages'   => 0,
		'fields'  => 0,
		'kept'    => 0,
		'missing' => array(),
	);

	$keys   = antradus_seo_rank_math_keys();
	$labels = antradus_pages();

	foreach ( array_keys( $labels ) as $page ) {
		$plan = antradus_seo_plan_page( $page, $overwrite );

		if ( ! $plan['post'] ) {
			$report['missing'][] = $labels[ $page ]['label'];
			continue;
		}

		$values  = antradus_seo_set( $page, antradus_default_lang() );
		$touched = 0;

		foreach ( $plan['fields'] as $part => $verdict ) {
			if ( 'kept' === $verdict ) {
				++$report['kept'];
				continue;
			}
			if ( 'write' !== $verdict ) {
				continue;
			}
			update_post_meta( $plan['post'], $keys[ $part ], $values[ $part ] );
			++$touched;
		}

		if ( $touched ) {
			++$report['pages'];
			$report['fields'] += $touched;
		}
	}

	return $report;
}

/* ===========================================================================
 * The Arabic half, which post meta has no room for
 * ========================================================================= */

/**
 * The metadata for the page being rendered, or null.
 *
 * @return array{focus:string,title:string,desc:string}|null
 */
function antradus_seo_current() {
	if ( is_admin() ) {
		return null;
	}
	$page = antradus_current_page_key();
	if ( '' === $page ) {
		return null;
	}
	$set = antradus_seo_set( $page );
	return ( '' === $set['title'] && '' === $set['desc'] ) ? null : $set;
}

/**
 * Is this a page whose metadata the theme is entitled to supply?
 *
 * English is Rank Math's: the button hands the words over and the plugin owns
 * them from then on, so whatever the owner then edits in the metabox is what
 * gets rendered. Arabic is the theme's, because there is no second metabox.
 *
 * @return bool
 */
function antradus_seo_theme_owns_head() {
	return ( antradus_default_lang() !== antradus_lang() ) && null !== antradus_seo_current();
}

add_filter( 'rank_math/frontend/title', 'antradus_seo_filter_title' );
/**
 * @param string $title Rank Math's title.
 * @return string
 */
function antradus_seo_filter_title( $title ) {
	if ( ! antradus_seo_theme_owns_head() ) {
		return $title;
	}
	$set = antradus_seo_current();
	return ( $set && '' !== $set['title'] ) ? $set['title'] : $title;
}

add_filter( 'rank_math/frontend/description', 'antradus_seo_filter_description' );
/**
 * @param string $description Rank Math's description.
 * @return string
 */
function antradus_seo_filter_description( $description ) {
	if ( ! antradus_seo_theme_owns_head() ) {
		return $description;
	}
	$set = antradus_seo_current();
	return ( $set && '' !== $set['desc'] ) ? $set['desc'] : $description;
}

/*
 * Yoast, for the same three things.
 *
 * The button on the settings screen writes Rank Math's fields, because that is
 * what this site runs. Serving the Arabic half is a different question: it has
 * to work under whichever plugin is installed, or an Arabic page quietly goes
 * out with English metadata and a canonical pointing at the English twin. Two
 * plugins are named throughout the product, so both are handled here.
 */
add_filter( 'wpseo_title', 'antradus_seo_filter_title' );
add_filter( 'wpseo_metadesc', 'antradus_seo_filter_description' );
add_filter( 'wpseo_opengraph_title', 'antradus_seo_filter_title' );
add_filter( 'wpseo_opengraph_desc', 'antradus_seo_filter_description' );
add_filter( 'wpseo_twitter_title', 'antradus_seo_filter_title' );
add_filter( 'wpseo_twitter_description', 'antradus_seo_filter_description' );

add_filter( 'rank_math/frontend/canonical', 'antradus_seo_canonical' );
add_filter( 'wpseo_canonical', 'antradus_seo_canonical' );
add_filter( 'wpseo_opengraph_url', 'antradus_seo_canonical' );
add_filter( 'get_canonical_url', 'antradus_seo_canonical' );
/**
 * Point an Arabic page at itself.
 *
 * Everything that generates a canonical - Rank Math, and WordPress core when
 * no plugin is installed - builds it from the permalink, and the permalink of
 * /pricing/?lang=ar is /pricing/. Left alone, every Arabic page tells search
 * engines that the real page is the English one, which is the instruction to
 * drop the translation from the index. It has to be self-referencing, and it
 * only has to be corrected while a second language is actually being served.
 *
 * @param string $url Canonical URL.
 * @return string
 */
function antradus_seo_canonical( $url ) {
	if ( is_admin() || ! is_string( $url ) || '' === $url ) {
		return $url;
	}
	$lang = antradus_lang();
	if ( antradus_default_lang() === $lang || ! antradus_lang_is_published( $lang ) ) {
		return $url;
	}
	return antradus_localize_url( $url, $lang );
}

/* ===========================================================================
 * With no SEO plugin at all
 * ========================================================================= */

add_filter( 'document_title_parts', 'antradus_seo_document_title' );
/**
 * Use the search title as the page title when nothing else is going to.
 *
 * Rank Math and the rest replace the title outright, further down the stack,
 * so this only ever has the last word on a site running none of them.
 *
 * @param array $parts Title parts.
 * @return array
 */
function antradus_seo_document_title( $parts ) {
	if ( is_admin() || '' !== antradus_seo_plugin() ) {
		return $parts;
	}
	$set = antradus_seo_current();
	if ( ! $set || '' === $set['title'] ) {
		return $parts;
	}
	return array( 'title' => $set['title'] );
}

add_action( 'wp_head', 'antradus_seo_fallback_tags', 3 );
/**
 * The description and the sharing block, when no plugin is printing them.
 */
function antradus_seo_fallback_tags() {
	if ( '' !== antradus_seo_plugin() ) {
		return;
	}
	$set = antradus_seo_current();
	if ( ! $set ) {
		return;
	}

	$title = '' !== $set['title'] ? $set['title'] : wp_get_document_title();

	if ( '' !== $set['desc'] ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $set['desc'] ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $set['desc'] ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $set['desc'] ) );
	}

	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf(
		'<meta property="og:url" content="%s">' . "\n",
		esc_url( antradus_lang_switch_url( antradus_lang() ) )
	);
	printf(
		'<meta property="og:site_name" content="%s">' . "\n",
		esc_attr( antradus_opt( 'brand_name', get_bloginfo( 'name' ) ) )
	);
	printf(
		'<meta property="og:locale" content="%s">' . "\n",
		esc_attr( antradus_is_rtl() ? 'ar_AR' : 'en_US' )
	);
}

/* ===========================================================================
 * Structured data for the Transcript Extractor page
 * ========================================================================= */

add_action( 'wp_head', 'antradus_seo_transcript_schema', 20 );
/**
 * What the extension is, where it installs, what it costs, and where its page
 * sits on the site.
 *
 * SoftwareApplication is the type Google reads for an app, and the breadcrumb
 * lets a result read "antradusai.com > Transcript Extractor" rather than a bare
 * address. Both are built from the Transcript Extractor tab - the product name,
 * the install link and one offer per plan card - so a price changed there
 * changes here too, and there is no second copy to forget.
 *
 * Deliberately missing: a rating. There are no reviews on this site to rate
 * the extension with, and an aggregate nobody collected is exactly what a
 * manual action is for. Rank Math's own graph describes the page rather than
 * the product, so the two sit side by side without competing.
 */
function antradus_seo_transcript_schema() {
	if ( is_admin() || 'transcript' !== antradus_current_page_key() ) {
		return;
	}

	$name = trim( (string) antradus_opt( 'tx_app_name', '' ) );
	if ( '' === $name ) {
		return;
	}

	$url   = antradus_lang_switch_url( antradus_lang() );
	$brand = antradus_opt( 'brand_name', get_bloginfo( 'name' ) );

	$app = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'SoftwareApplication',
		'name'                => $name,
		'applicationCategory' => 'BrowserApplication',
		'operatingSystem'     => 'Chrome, Edge, Brave',
		'url'                 => $url,
		'publisher'           => array(
			'@type' => 'Organization',
			'name'  => $brand,
			'url'   => home_url( '/' ),
		),
	);

	$desc = antradus_seo_value( 'transcript', 'desc' );
	if ( '' !== $desc ) {
		$app['description'] = $desc;
	}

	// Only a full address is somewhere to install from; "#plans" is not.
	$install = antradus_link( antradus_opt( 'tx_cta1_url', '' ) );
	if ( 0 === strpos( $install, 'http' ) ) {
		$app['installUrl'] = $install;
	}

	/*
	 * Every price on the page is written in dollars, so every offer is in USD.
	 * A card whose amount is words rather than a number is not an offer.
	 */
	$offers = array();
	foreach ( antradus_rows( 'tx_plans' ) as $plan ) {
		$amount = preg_replace( '/[^0-9]/', '', (string) antradus_cell( $plan, 'amount' ) );
		if ( '' === $amount ) {
			continue;
		}
		$cents    = substr( preg_replace( '/[^0-9]/', '', (string) antradus_cell( $plan, 'cents' ) ) . '00', 0, 2 );
		$offers[] = array(
			'@type'         => 'Offer',
			'name'          => antradus_cell( $plan, 'name' ),
			'price'         => $amount . '.' . $cents,
			'priceCurrency' => 'USD',
		);
	}
	if ( $offers ) {
		$app['offers'] = $offers;
	}

	$graphs = array( $app );

	/*
	 * Rank Math already prints a BreadcrumbList in its own graph, and two of
	 * them on one page is two answers to the same question. Ours is only for a
	 * site running without it.
	 */
	if ( antradus_seo_rank_math_active() ) {
		antradus_seo_print_json_ld( $graphs );
		return;
	}

	$pages    = antradus_pages();
	$graphs[] = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => $brand,
				'item'     => antradus_localize_url( home_url( '/' ) ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $pages['transcript']['nav'],
				'item'     => $url,
			),
		),
	);

	antradus_seo_print_json_ld( $graphs );
}

/**
 * Print structured-data blocks, one script tag each.
 *
 * @param array[] $graphs Schema.org objects.
 */
function antradus_seo_print_json_ld( $graphs ) {
	foreach ( $graphs as $graph ) {
		// JSON_HEX_TAG: a "</script>" typed into a setting must not close the tag.
		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) // phpcs:ignore WordPress.Security.EscapeOutput -- JSON, with tags hex-escaped.
		);
	}
}

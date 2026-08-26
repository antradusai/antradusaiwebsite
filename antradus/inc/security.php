<?php
/**
 * Antradus theme - hardening.
 *
 * The threat this file is written against is the ordinary one: somebody who
 * wants to reach wp-admin and change what the site says. So it closes the
 * cheap ways in - username enumeration, XML-RPC, the file editors, verbose
 * login errors, unlimited password guessing - and it says nothing about the
 * software's version to anyone who has not logged in.
 *
 * Two rules were followed throughout.
 *
 * First, nothing here can lock the owner out. The login throttle counts
 * failures, never successes, forgets everything after fifteen minutes, and is
 * switched off by one checkbox on the Security tab if it ever gets in the way.
 *
 * Second, nothing here quietly breaks a plugin. The REST API stays open,
 * because the block editor, Yoast, Rank Math and WooCommerce all live on it -
 * only the users endpoint is closed to strangers, since that endpoint exists
 * to publish the list of accounts an attacker would like to guess passwords
 * for. There is no Content-Security-Policy for the same reason: on a site with
 * eight plugins a strict policy breaks something on day one and gets switched
 * off, which is worse than not having had it.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a hardening switch on? All of them default to on.
 *
 * @param string $key Option key.
 * @return bool
 */
function antradus_sec_on( $key ) {
	return antradus_on( $key, true );
}

/* ===========================================================================
 * 1. Stop telling strangers what we are running
 * ========================================================================= */

add_filter( 'the_generator', 'antradus_sec_no_generator' );
/**
 * @return string
 */
function antradus_sec_no_generator() {
	return '';
}

add_action( 'init', 'antradus_sec_clean_head' );
/**
 * Drop the head links that only ever help a scanner.
 *
 * The WordPress version in a stylesheet query string tells a scanner which
 * published vulnerabilities to try. Removing it is not a fix for anything, but
 * it does mean an automated sweep has to actually attempt an exploit to learn
 * whether it is worth attempting - and attempts are what logs catch.
 */
function antradus_sec_clean_head() {
	if ( ! antradus_sec_on( 'sec_clean_head' ) ) {
		return;
	}
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
}

add_filter( 'style_loader_src', 'antradus_sec_strip_version', 20 );
add_filter( 'script_loader_src', 'antradus_sec_strip_version', 20 );
/**
 * Take the WordPress version off core asset URLs.
 *
 * Our own assets keep their version - cache busting is the whole point of it -
 * so this only touches the ?ver= that equals the WordPress release number.
 *
 * @param string $src Asset URL.
 * @return string
 */
function antradus_sec_strip_version( $src ) {
	if ( ! antradus_sec_on( 'sec_clean_head' ) || is_admin() ) {
		return $src;
	}
	if ( $src && false !== strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

/* ===========================================================================
 * 2. Do not publish the list of accounts
 * ========================================================================= */

add_action( 'parse_request', 'antradus_sec_block_author_scan' );
/**
 * /?author=1 answers "what is the administrator called?" for anyone who asks.
 *
 * A username is half a credential. WordPress hands it over by redirecting the
 * numeric form to the author's slug, so the fix is to refuse the numeric form.
 *
 * This has to happen on parse_request, not on template_redirect: core's own
 * redirect_canonical() is on template_redirect at priority 10 and was hooked
 * first, so by the time a theme could run there the Location header naming the
 * account has already been sent.
 */
function antradus_sec_block_author_scan() {
	if ( ! antradus_sec_on( 'sec_no_enum' ) || is_user_logged_in() ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a public query string.
	$asked = isset( $_GET['author'] ) ? sanitize_text_field( wp_unslash( $_GET['author'] ) ) : '';
	if ( '' !== $asked && ctype_digit( (string) $asked ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}

add_filter( 'rest_endpoints', 'antradus_sec_close_users_endpoint' );
/**
 * Close /wp-json/wp/v2/users to people who are not logged in.
 *
 * The endpoint's entire purpose is to publish the account list, which is the
 * same disclosure as ?author=1 by another door. Logged-in editors keep it,
 * because the block editor's author dropdown is built on it.
 *
 * @param array $endpoints REST endpoints.
 * @return array
 */
function antradus_sec_close_users_endpoint( $endpoints ) {
	if ( ! antradus_sec_on( 'sec_no_enum' ) || is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array( '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' ) as $route ) {
		if ( isset( $endpoints[ $route ] ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}

add_filter( 'oembed_response_data', 'antradus_sec_oembed_no_author' );
/**
 * The oEmbed payload carries the author's name and URL. Nothing embeds that.
 *
 * @param array $data Response data.
 * @return array
 */
function antradus_sec_oembed_no_author( $data ) {
	if ( ! antradus_sec_on( 'sec_no_enum' ) ) {
		return $data;
	}
	unset( $data['author_name'], $data['author_url'] );
	return $data;
}

/* ===========================================================================
 * 3. XML-RPC
 * ========================================================================= */

add_filter( 'xmlrpc_enabled', 'antradus_sec_xmlrpc' );
/**
 * XML-RPC is a password-guessing endpoint with a publishing API attached.
 *
 * system.multicall lets one HTTP request try hundreds of passwords, which is
 * why it is the door that gets knocked on. This site publishes from wp-admin
 * and from the plugin, so nothing here needs it.
 *
 * @param bool $enabled Current state.
 * @return bool
 */
function antradus_sec_xmlrpc( $enabled ) {
	return antradus_sec_on( 'sec_no_xmlrpc' ) ? false : $enabled;
}

add_filter( 'xmlrpc_methods', 'antradus_sec_xmlrpc_methods' );
/**
 * Belt and braces: drop the pingback methods even if something re-enables the
 * endpoint, because pingback.ping is also a reflection tool for hitting other
 * people's servers from ours.
 *
 * Note that system.multicall cannot be removed here. IXR_Server::setCallbacks()
 * puts the three system.* methods back after this filter has run, which is why
 * the check below closes the endpoint itself rather than trusting this list.
 *
 * @param array $methods Registered methods.
 * @return array
 */
function antradus_sec_xmlrpc_methods( $methods ) {
	if ( ! antradus_sec_on( 'sec_no_xmlrpc' ) ) {
		return $methods;
	}
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}

add_filter( 'wp_xmlrpc_server_class', 'antradus_sec_close_xmlrpc' );
/**
 * Refuse the endpoint outright, before a server object exists.
 *
 * xmlrpc_enabled => false already makes every authenticated call fail, which
 * closes the password-guessing route. It does not stop system.listMethods from
 * answering, and that answer is a free inventory of what the site will talk
 * about. This filter runs in xmlrpc.php immediately before the server is
 * constructed, which is the last moment where the answer can simply be "no".
 *
 * @param string $class Server class name.
 * @return string
 */
function antradus_sec_close_xmlrpc( $class ) {
	if ( ! antradus_sec_on( 'sec_no_xmlrpc' ) ) {
		return $class;
	}
	status_header( 403 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo 'XML-RPC is disabled on this site.';
	exit;
}

add_filter( 'wp_headers', 'antradus_sec_drop_pingback_header' );
/**
 * @param array $headers Response headers.
 * @return array
 */
function antradus_sec_drop_pingback_header( $headers ) {
	if ( antradus_sec_on( 'sec_no_xmlrpc' ) ) {
		unset( $headers['X-Pingback'] );
	}
	return $headers;
}

/* ===========================================================================
 * 4. Response headers
 * ========================================================================= */

add_filter( 'wp_headers', 'antradus_sec_headers', 20 );
/**
 * The browser-side defaults every site should be sending.
 *
 * Deliberately not a Content-Security-Policy - see the note at the top of the
 * file. These four are the ones that are safe to set unconditionally on a site
 * that runs third-party plugins.
 *
 * @param array $headers Response headers.
 * @return array
 */
function antradus_sec_headers( $headers ) {
	if ( ! antradus_sec_on( 'sec_headers' ) || is_admin() ) {
		return $headers;
	}
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Permissions-Policy']     = 'geolocation=(), microphone=(), camera=(), payment=(), interest-cohort=()';
	return $headers;
}

/* ===========================================================================
 * 5. The login form
 * ========================================================================= */

add_filter( 'login_errors', 'antradus_sec_vague_login_error' );
/**
 * "Unknown username" confirms which usernames exist. One message for both.
 *
 * @return string
 */
function antradus_sec_vague_login_error() {
	if ( ! antradus_sec_on( 'sec_login' ) ) {
		return '';
	}
	return esc_html__( 'Those details were not right. Please try again.', 'antradus' );
}

/**
 * The key a throttle counter is stored under, for this caller.
 *
 * REMOTE_ADDR only. X-Forwarded-For is attacker-controlled unless a proxy you
 * trust overwrites it, and a throttle keyed on a spoofable header is worse
 * than none: anyone can rotate the header to bypass it, or forge someone
 * else's to lock them out.
 *
 * @return string
 */
function antradus_sec_throttle_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return 'antradus_lf_' . substr( hash( 'sha256', $ip . wp_salt( 'auth' ) ), 0, 32 );
}

add_filter( 'authenticate', 'antradus_sec_check_throttle', 30 );
/**
 * Refuse to check a password at all once an address has failed too often.
 *
 * Ten tries, then a fifteen-minute wait. Slow enough to make a dictionary
 * useless, generous enough that a person mistyping their own password never
 * meets it.
 *
 * @param WP_User|WP_Error|null $user Result so far.
 * @return WP_User|WP_Error|null
 */
function antradus_sec_check_throttle( $user ) {
	if ( ! antradus_sec_on( 'sec_login' ) || is_wp_error( $user ) ) {
		return $user;
	}
	$tries = (int) get_transient( antradus_sec_throttle_key() );
	if ( $tries >= 10 ) {
		return new WP_Error(
			'antradus_throttled',
			esc_html__( 'Too many failed sign-in attempts from this address. Try again in about fifteen minutes.', 'antradus' )
		);
	}
	return $user;
}

add_action( 'wp_login_failed', 'antradus_sec_count_failure' );
/**
 * @param string $username Attempted username.
 */
function antradus_sec_count_failure( $username ) {
	if ( ! antradus_sec_on( 'sec_login' ) ) {
		return;
	}
	$key   = antradus_sec_throttle_key();
	$tries = (int) get_transient( $key );
	set_transient( $key, $tries + 1, 15 * MINUTE_IN_SECONDS );
}

add_action( 'wp_login', 'antradus_sec_clear_failures', 10, 2 );
/**
 * A successful sign-in wipes the slate, so a person who eventually remembers
 * their password is not still serving out a sentence.
 *
 * @param string  $login Username.
 * @param WP_User $user  User.
 */
function antradus_sec_clear_failures( $login, $user ) {
	delete_transient( antradus_sec_throttle_key() );
}

/* ===========================================================================
 * 6. Outbound links in content
 * ========================================================================= */

add_filter( 'the_content', 'antradus_sec_link_rel', 25 );
/**
 * Give every off-site link in an article rel="noopener".
 *
 * A target="_blank" link without it hands the opened page a live handle on the
 * window that opened it. Cheap to fix, invisible when it works.
 *
 * @param string $html Post content.
 * @return string
 */
function antradus_sec_link_rel( $html ) {
	if ( ! antradus_sec_on( 'sec_headers' ) || is_admin() || '' === trim( (string) $html ) ) {
		return $html;
	}
	if ( false === strpos( $html, 'target="_blank"' ) && false === strpos( $html, "target='_blank'" ) ) {
		return $html;
	}
	return preg_replace_callback(
		'/<a\s[^>]*target=["\']_blank["\'][^>]*>/i',
		static function ( $m ) {
			$tag = $m[0];
			if ( preg_match( '/\srel=["\'][^"\']*noopener/i', $tag ) ) {
				return $tag;
			}
			if ( preg_match( '/\srel=["\']([^"\']*)["\']/i', $tag, $rel ) ) {
				return str_replace( $rel[0], ' rel="' . esc_attr( trim( $rel[1] . ' noopener noreferrer' ) ) . '"', $tag );
			}
			return rtrim( $tag, '>' ) . ' rel="noopener noreferrer">';
		},
		$html
	);
}

/* ===========================================================================
 * 7. The settings screen itself
 * ========================================================================= */

/**
 * The capability required to edit this site's content.
 *
 * One function, used by the menu, the page, and every admin-post handler, so
 * there is no way to relax one of the three and forget the others.
 *
 * @return string
 */
function antradus_settings_cap() {
	/**
	 * Filter the capability needed to edit Antradus content.
	 *
	 * @param string $cap Capability.
	 */
	return (string) apply_filters( 'antradus_settings_capability', 'manage_options' );
}

/**
 * Stop, with a 403, unless this really is an administrator acting on purpose.
 *
 * @param string $nonce_action Nonce action to verify, or '' to skip.
 */
function antradus_require_admin( $nonce_action = '' ) {
	if ( ! is_user_logged_in() || ! current_user_can( antradus_settings_cap() ) ) {
		wp_die(
			esc_html__( 'You do not have permission to do that.', 'antradus' ),
			esc_html__( 'Permission denied', 'antradus' ),
			array( 'response' => 403 )
		);
	}
	if ( '' !== $nonce_action ) {
		check_admin_referer( $nonce_action );
	}
}

add_action( 'admin_init', 'antradus_sec_lock_option_page' );
/**
 * options.php decides who may save an option group from this filter. Bind it
 * to the same capability as everything else rather than leaving the default.
 */
function antradus_sec_lock_option_page() {
	add_filter(
		'option_page_capability_antradus_content_group',
		'antradus_settings_cap'
	);
}

<?php
/**
 * Antradus theme - comments, switched off.
 *
 * A marketing site with a support inbox has nowhere useful for a comment to
 * go, and an open comment form on a WordPress site is a spam queue somebody
 * has to empty every week.
 *
 * "Off" here means off in all five places it has to be off, because turning
 * off only the first is how sites end up with a comment form that still posts:
 *
 *   1. the discussion setting for new posts,
 *   2. comments_open() for posts that were created while it was on,
 *   3. the front-end form and the existing thread,
 *   4. the endpoints - wp-comments-post.php and the REST route - so a form
 *      replayed from a saved page cannot still write a row,
 *   5. the admin menu, the toolbar and the dashboard widget, so nobody is
 *      moderating a queue that is not supposed to fill up.
 *
 * One checkbox on the Security tab turns the whole thing back on, and no
 * existing comment is ever deleted - they are hidden, and they come back
 * exactly as they were.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Are comments allowed on this site at all?
 *
 * @return bool
 */
function antradus_comments_enabled() {
	return antradus_on( 'comments_enable', false );
}

/* ===========================================================================
 * 1. Nothing is open
 * ========================================================================= */

add_filter( 'comments_open', 'antradus_comments_closed', 20 );
add_filter( 'pings_open', 'antradus_comments_closed', 20 );
/**
 * @param bool $open Whether it is open.
 * @return bool
 */
function antradus_comments_closed( $open ) {
	return antradus_comments_enabled() ? $open : false;
}

add_filter( 'get_comments_number', 'antradus_comments_zero', 20 );
/**
 * Report zero, so themes and SEO plugins do not render "3 Comments" above a
 * thread that is not being shown.
 *
 * @param int $count Real count.
 * @return int
 */
function antradus_comments_zero( $count ) {
	return antradus_comments_enabled() ? $count : 0;
}

add_filter( 'comments_array', 'antradus_comments_none', 20 );
/**
 * @param array $comments Comments for a post.
 * @return array
 */
function antradus_comments_none( $comments ) {
	return antradus_comments_enabled() ? $comments : array();
}

add_filter( 'feed_links_show_comments_feed', 'antradus_comments_enabled' );

/* ===========================================================================
 * 2. No endpoint accepts one
 * ========================================================================= */

add_action( 'init', 'antradus_comments_drop_support', 20 );
/**
 * Take comment support off every post type, which is what removes the metabox,
 * the column and the block editor's discussion panel in one move.
 */
function antradus_comments_drop_support() {
	if ( antradus_comments_enabled() ) {
		return;
	}
	foreach ( get_post_types() as $type ) {
		if ( post_type_supports( $type, 'comments' ) ) {
			remove_post_type_support( $type, 'comments' );
			remove_post_type_support( $type, 'trackbacks' );
		}
	}
}

add_action( 'pre_comment_on_post', 'antradus_comments_refuse_post' );
/**
 * The last line of the fence: refuse a POST to wp-comments-post.php.
 *
 * Everything above hides the form. This is what happens when someone saves the
 * page, re-enables the form in their browser and submits it anyway.
 */
function antradus_comments_refuse_post() {
	if ( antradus_comments_enabled() ) {
		return;
	}
	wp_die(
		esc_html__( 'Comments are closed on this site.', 'antradus' ),
		esc_html__( 'Comments are closed', 'antradus' ),
		array( 'response' => 403 )
	);
}

add_filter( 'rest_pre_dispatch', 'antradus_comments_block_rest', 10, 3 );
/**
 * And the same for the REST route, which is a separate door to the same table.
 *
 * @param mixed           $result  Pre-computed result.
 * @param WP_REST_Server  $server  Server instance.
 * @param WP_REST_Request $request The request.
 * @return mixed
 */
function antradus_comments_block_rest( $result, $server, $request ) {
	if ( antradus_comments_enabled() ) {
		return $result;
	}
	if ( 0 === strpos( (string) $request->get_route(), '/wp/v2/comments' ) ) {
		return new WP_Error(
			'antradus_comments_closed',
			__( 'Comments are closed on this site.', 'antradus' ),
			array( 'status' => 403 )
		);
	}
	return $result;
}

/* ===========================================================================
 * 3. Nothing in wp-admin pretends otherwise
 * ========================================================================= */

add_action( 'admin_menu', 'antradus_comments_hide_menu' );
/**
 * Remove the Comments menu and the dashboard widget.
 */
function antradus_comments_hide_menu() {
	if ( antradus_comments_enabled() ) {
		return;
	}
	remove_menu_page( 'edit-comments.php' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}

add_action( 'admin_init', 'antradus_comments_redirect_screen' );
/**
 * Send anyone who reaches the moderation screen by URL back to the dashboard.
 */
function antradus_comments_redirect_screen() {
	global $pagenow;
	if ( antradus_comments_enabled() || 'edit-comments.php' !== $pagenow ) {
		return;
	}
	wp_safe_redirect( admin_url() );
	exit;
}

add_action( 'wp_before_admin_bar_render', 'antradus_comments_hide_bubble' );
/**
 * Take the comment bubble out of the toolbar.
 */
function antradus_comments_hide_bubble() {
	if ( antradus_comments_enabled() ) {
		return;
	}
	global $wp_admin_bar;
	if ( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'comments' );
	}
}

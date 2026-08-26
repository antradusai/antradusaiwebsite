<?php
/**
 * Antradus theme - galleries become a showcase carousel.
 *
 * A WordPress gallery - block or classic shortcode - is rendered as one large
 * image with arrows, a caption, and a clickable thumbnail strip underneath.
 * Galleries with fewer than two images are left exactly as they were, because
 * a carousel of one is just an image with extra furniture.
 *
 * This is the snippet that used to live in WPCode, moved into the theme so the
 * site works with no snippets at all.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'antradus_gallery_register' );
/**
 * Register - not enqueue. The assets only load on pages that actually have a
 * gallery, and they are pulled in from the render callback below.
 */
function antradus_gallery_register() {
	wp_register_style(
		'antradus-gallery',
		get_template_directory_uri() . '/assets/css/gallery.css',
		array( 'antradus' ),
		ANTRADUS_VERSION
	);
	wp_register_script(
		'antradus-gallery',
		get_template_directory_uri() . '/assets/js/gallery.js',
		array(),
		ANTRADUS_VERSION,
		true
	);
}

/**
 * Build the carousel markup for a list of attachment ids.
 *
 * @param int[] $ids Attachment ids, in order.
 * @return string Markup, or '' when the first image cannot be rendered.
 */
function antradus_gallery_markup( $ids ) {
	$main = wp_get_attachment_image( $ids[0], 'large', false, array( 'class' => 'antgl-main-img' ) );
	if ( ! $main ) {
		return '';
	}

	$thumbs = '';
	foreach ( $ids as $n => $id ) {
		$thumb = wp_get_attachment_image( $id, 'medium' );
		$full  = wp_get_attachment_image_url( $id, 'large' );
		if ( ! $thumb || ! $full ) {
			continue;
		}
		$caption = wp_get_attachment_caption( $id );
		$title   = $caption ? $caption : get_the_title( $id );

		$thumbs .= '<button type="button" class="antgl-thumb' . ( 0 === $n ? ' is-active' : '' ) . '"'
			. ' data-full="' . esc_url( $full ) . '"'
			. ' data-title="' . esc_attr( $title ) . '"'
			/* translators: %d: image number in the gallery. */
			. ' aria-label="' . esc_attr( sprintf( __( 'Show image %d', 'antradus' ), $n + 1 ) ) . '">'
			. $thumb . '</button>';
	}

	wp_enqueue_style( 'antradus-gallery' );
	wp_enqueue_script( 'antradus-gallery' );

	$first_caption = wp_get_attachment_caption( $ids[0] );
	$first_title   = $first_caption ? $first_caption : get_the_title( $ids[0] );

	return '<div class="antgl" role="region" aria-label="' . esc_attr__( 'Image gallery', 'antradus' ) . '">'
		. '<div class="antgl-main">'
		. '<button type="button" class="antgl-nav antgl-prev" aria-label="' . esc_attr__( 'Previous image', 'antradus' ) . '">'
		. '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
		. '</button>'
		. $main
		. '<button type="button" class="antgl-nav antgl-next" aria-label="' . esc_attr__( 'Next image', 'antradus' ) . '">'
		. '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
		. '</button>'
		. '</div>'
		. '<div class="antgl-title">' . esc_html( $first_title ) . '</div>'
		. '<div class="antgl-thumbs">' . $thumbs . '</div>'
		. '</div>';
}

add_filter( 'render_block', 'antradus_gallery_block', 10, 2 );
/**
 * Block editor galleries.
 *
 * @param string $content Rendered block.
 * @param array  $block   Block data.
 * @return string
 */
function antradus_gallery_block( $content, $block ) {
	if ( is_admin() || 'core/gallery' !== ( isset( $block['blockName'] ) ? $block['blockName'] : '' ) ) {
		return $content;
	}

	$ids = array();
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $inner ) {
			if ( ! empty( $inner['attrs']['id'] ) ) {
				$ids[] = (int) $inner['attrs']['id'];
			}
		}
	}
	if ( ! $ids && ! empty( $block['attrs']['ids'] ) ) {
		$ids = array_map( 'absint', (array) $block['attrs']['ids'] );
	}
	if ( count( $ids ) < 2 ) {
		return $content;
	}

	$html = antradus_gallery_markup( array_values( $ids ) );
	return $html ? $html : $content;
}

add_filter( 'post_gallery', 'antradus_gallery_shortcode', 10, 2 );
/**
 * Classic [gallery] shortcodes.
 *
 * @param string $output Existing output.
 * @param array  $attr   Shortcode attributes.
 * @return string
 */
function antradus_gallery_shortcode( $output, $attr ) {
	$raw = isset( $attr['ids'] ) ? $attr['ids'] : '';
	$ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) );
	if ( count( $ids ) < 2 ) {
		return $output;
	}
	$html = antradus_gallery_markup( $ids );
	return $html ? $html : $output;
}

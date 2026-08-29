<?php
/**
 * Antradus theme - the front end serves the file you uploaded.
 *
 * WordPress cuts every upload into presets - thumbnail, medium, medium_large,
 * large - and then writes a `srcset` to talk the browser into taking the
 * smallest one that fits the box. For a photograph that is exactly right. This
 * site is a shop window for a piece of software, and nearly every picture on it
 * is a screenshot of an interface: the point of it is the text inside it, and a
 * 300px-wide candidate stretched across a card is mush. Reading beats bytes
 * here, so the front end asks for the original every time and never for a size.
 *
 * That takes three hooks, because a picture reaches a page by three routes:
 *
 *  1. `image_downsize` - anything that turns an attachment ID into a file: the
 *     theme's own image slots, post thumbnails, the gallery carousel, the
 *     picture an SEO plugin hands a social card.
 *  2. `wp_calculate_image_srcset_meta` - the srcset that would otherwise offer
 *     the browser the very presets route 1 just refused.
 *  3. `wp_content_img_tag` - a picture inside a post, whose address was written
 *     into the HTML the moment the editor inserted it, and which route 1 never
 *     sees.
 *
 * wp-admin is left alone. The settings screen, the media library and the block
 * editor all want the small copies, and none of them is the front end.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether this request is the site being read, rather than being edited.
 *
 * `is_admin()` is not enough on its own: the block editor and the media library
 * fetch their pictures through the REST API, which is not an admin request, and
 * would lose every thumbnail if this returned true for them.
 *
 * @return bool
 */
function antradus_is_frontend() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return false;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}
	return true;
}

/**
 * The file that was uploaded: its address and its real dimensions.
 *
 * "Full" is not that file. Anything wider than the big-image threshold - 2560px
 * unless a site changes it - is resized on upload, the copy is stored as
 * `-scaled`, and the copy is what `full` returns. So the original is asked for
 * by name.
 *
 * Its dimensions are measured rather than read when that copy exists, because
 * the metadata then describes the copy and nothing anywhere records how big the
 * file you uploaded actually was. `wp_getimagesize()` reads the header and not
 * the picture, and the answer is remembered for the rest of the request, since
 * one attachment is usually asked for several times on one page.
 *
 * @param int $id Attachment ID.
 * @return array{url:string,width:int,height:int}|null Null when it is not an image.
 */
function antradus_original_image( $id ) {
	static $cache = array();

	$id = (int) $id;
	if ( array_key_exists( $id, $cache ) ) {
		return $cache[ $id ];
	}
	$cache[ $id ] = null;

	if ( ! wp_attachment_is_image( $id ) ) {
		return null;
	}

	$url = wp_get_original_image_url( $id );
	if ( ! $url ) {
		return null;
	}

	$meta   = wp_get_attachment_metadata( $id );
	$width  = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
	$height = isset( $meta['height'] ) ? (int) $meta['height'] : 0;

	if ( ! empty( $meta['original_image'] ) ) {
		$path = wp_get_original_image_path( $id );
		$read = $path ? wp_getimagesize( $path ) : false;
		if ( $read ) {
			$width  = (int) $read[0];
			$height = (int) $read[1];
		}
	}

	$cache[ $id ] = array(
		'url'    => $url,
		'width'  => $width,
		'height' => $height,
	);
	return $cache[ $id ];
}

add_filter( 'image_downsize', 'antradus_image_downsize', 10, 2 );
/**
 * Hand back the uploaded file whatever size was asked for.
 *
 * Returning an array here short-circuits the whole of `image_downsize()`, so
 * the requested size is never looked up - which is the point: a call site that
 * still says 'medium' gets the original, and there is one rule for the site
 * rather than a list of call sites to keep honest.
 *
 * Images only. A PDF goes through here too, and its "sizes" are the cover
 * thumbnails WordPress rendered for it; handing back the PDF would put a
 * document where a picture belongs.
 *
 * @param array|false $out Short-circuit value from an earlier filter.
 * @param int         $id  Attachment ID.
 * @return array|false array( url, width, height, is_intermediate ).
 */
function antradus_image_downsize( $out, $id ) {
	if ( false !== $out || ! antradus_is_frontend() ) {
		return $out;
	}
	$file = antradus_original_image( $id );
	if ( ! $file ) {
		return $out;
	}
	// False for the last one: this is the file itself, not a cut of it.
	return array( $file['url'], $file['width'], $file['height'], false );
}

add_filter( 'wp_calculate_image_srcset_meta', 'antradus_no_srcset' );
/**
 * No srcset on the front end.
 *
 * Emptying the size list is what switches it off: with nothing to choose
 * between, WordPress writes neither `srcset` nor `sizes` and the browser loads
 * the one address in `src`. Oversampling is the feature here.
 *
 * @param array $meta Attachment metadata the srcset would be built from.
 * @return array
 */
function antradus_no_srcset( $meta ) {
	return antradus_is_frontend() ? array() : $meta;
}

add_filter( 'wp_content_img_tag', 'antradus_content_image_full', 10, 3 );
/**
 * Point a picture inside a post at the file that was uploaded.
 *
 * The editor writes the address of a preset straight into the post content -
 * `...-1024x576.jpg` - so nothing on the way out of the database can reach it.
 * This filter is handed the finished tag and the attachment it belongs to,
 * which is enough to swap the address, drop the srcset that came with it and
 * correct the dimensions, in case the preset was a cropped one and the shape
 * has changed. A picture that is not in the media library has no attachment ID
 * and is left exactly as it is.
 *
 * @param string $tag           The `<img>` tag.
 * @param string $context       Where the tag came from.
 * @param int    $attachment_id Attachment ID, or 0.
 * @return string
 */
function antradus_content_image_full( $tag, $context, $attachment_id ) {
	if ( ! $attachment_id || ! antradus_is_frontend() ) {
		return $tag;
	}
	$file = antradus_original_image( (int) $attachment_id );
	if ( ! $file ) {
		return $tag;
	}

	$tag = preg_replace( '/\s(?:srcset|sizes)="[^"]*"/i', '', $tag );

	foreach ( array( 'width', 'height' ) as $attribute ) {
		if ( $file[ $attribute ] > 0 ) {
			$tag = preg_replace(
				'/\s' . $attribute . '="[^"]*"/i',
				' ' . $attribute . '="' . (int) $file[ $attribute ] . '"',
				$tag,
				1
			);
		}
	}

	// A callback, not a replacement string: an address can contain a $.
	return preg_replace_callback(
		'/(\ssrc=")[^"]*(")/i',
		static function ( $match ) use ( $file ) {
			return $match[1] . esc_url( $file['url'] ) . $match[2];
		},
		$tag,
		1
	);
}

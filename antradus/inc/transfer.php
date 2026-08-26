<?php
/**
 * Antradus theme - moving content between sites.
 *
 * The job this file exists for is "I built the site on my laptop, now put it
 * on the live server". Both languages, every plan, every image.
 *
 * The words are easy - they are two option rows of plain text. The images are
 * the whole problem, and it is worth being explicit about why.
 *
 * An image setting stores an attachment ID. ID 42 on the laptop is a different
 * picture on the live site, or no picture at all, so an export that carried
 * the IDs across would produce a site where every image is wrong in a way that
 * looks deliberate. So on the way out, every image field is expanded into what
 * it actually is: a filename, a URL, and - when it is small enough - the file
 * itself, base64 encoded, travelling inside the export.
 *
 * Carrying the bytes matters because of localhost. A live server cannot fetch
 * http://localhost/…/logo.png, so a URL-only export is exactly the export that
 * fails on the one journey people most often make. With the bytes embedded the
 * transfer works from a laptop with no public address at all.
 *
 * On the way in, each image is resolved in this order:
 *
 *   1. an attachment already imported from the same source URL - reuse it, so
 *      importing twice does not fill the Media Library with duplicates,
 *   2. an attachment that already has that URL on this site,
 *   3. the embedded bytes, written into the Media Library as a new attachment,
 *   4. downloading the URL, for exports made before the size cap,
 *   5. the plain URL, left as-is - the theme renders a URL perfectly well, so
 *      a picture that cannot be copied still shows rather than disappearing.
 *
 * Nothing here can run without manage_options and a valid nonce.
 *
 * @package Antradus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Biggest single file we will carry inside an export.
 *
 * Two megabytes covers a logo, a screenshot and a hero image comfortably. A
 * 30MB photograph straight off a camera is left as a URL instead, because the
 * alternative is an export that exhausts PHP's memory limit while building.
 */
const ANTRADUS_EMBED_MAX_FILE = 2097152;   // 2 MB.
const ANTRADUS_EMBED_MAX_TOTAL = 25165824; // 24 MB.

/* ===========================================================================
 * 1. Export
 * ========================================================================= */

/**
 * Every image field in the schema, as key => 'field' or key => sub-key list.
 *
 * @return array{plain:string[],repeaters:array<string,string[]>}
 */
function antradus_image_fields() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}
	$map = array(
		'plain'     => array(),
		'repeaters' => array(),
	);
	foreach ( antradus_schema_fields() as $key => $field ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		if ( 'image' === $type ) {
			$map['plain'][] = $key;
			continue;
		}
		if ( 'repeater' === $type && ! empty( $field['fields'] ) ) {
			$subs = array();
			foreach ( $field['fields'] as $sub ) {
				if ( 'image' === $sub['type'] ) {
					$subs[] = $sub['key'];
				}
			}
			if ( $subs ) {
				$map['repeaters'][ $key ] = $subs;
			}
		}
	}
	return $map;
}

/**
 * Describe one image field's value well enough to rebuild it elsewhere.
 *
 * @param string $value  Stored value: an attachment ID or a URL.
 * @param int    $budget Bytes of embedding left, by reference.
 * @return array|string A descriptor array, or '' when there is nothing here.
 */
function antradus_describe_image( $value, &$budget ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	$out = array( '__antradus_image' => 1 );

	// A stored value is either an attachment ID or a URL. Resolve both to the
	// same three things: the address, the file on disk, and who it is.
	$id = ctype_digit( $value ) ? (int) $value : (int) attachment_url_to_postid( $value );

	if ( $id ) {
		$url = wp_get_attachment_url( $id );
		if ( ! $url ) {
			return '';
		}
		$path            = get_attached_file( $id );
		$out['url']      = $url;
		$out['filename'] = $path ? basename( $path ) : basename( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		$out['alt']      = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		$out['title']    = get_the_title( $id );
		$out['mime']     = (string) get_post_mime_type( $id );
	} else {
		// A pasted URL pointing at somebody else's server. We can name it, but
		// there is no local file to carry.
		$path            = null;
		$out['url']      = $value;
		$out['filename'] = basename( (string) wp_parse_url( $value, PHP_URL_PATH ) );
	}

	// Carry the bytes when they fit in what is left of the budget.
	if ( $path && file_exists( $path ) ) {
		$size = (int) filesize( $path );
		if ( $size > 0 && $size <= ANTRADUS_EMBED_MAX_FILE && $size <= $budget ) {
			$bytes = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- a local file we just resolved, not a remote fetch.
			if ( false !== $bytes ) {
				$out['data'] = base64_encode( $bytes );
				$budget     -= $size;
			}
		}
	}

	return $out;
}

/**
 * Walk one language's values, turning every image field into a descriptor.
 *
 * @param array $values Stored option values.
 * @param int   $budget Embedding budget, by reference.
 * @return array
 */
function antradus_expand_images( $values, &$budget ) {
	$fields = antradus_image_fields();

	foreach ( $fields['plain'] as $key ) {
		if ( isset( $values[ $key ] ) && '' !== $values[ $key ] ) {
			$values[ $key ] = antradus_describe_image( $values[ $key ], $budget );
		}
	}

	foreach ( $fields['repeaters'] as $key => $subs ) {
		if ( empty( $values[ $key ] ) || ! is_array( $values[ $key ] ) ) {
			continue;
		}
		foreach ( $values[ $key ] as $index => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			foreach ( $subs as $sub ) {
				if ( isset( $row[ $sub ] ) && '' !== $row[ $sub ] ) {
					$values[ $key ][ $index ][ $sub ] = antradus_describe_image( $row[ $sub ], $budget );
				}
			}
		}
	}

	return $values;
}

/**
 * The whole transferable state of the site's content.
 *
 * @param bool $with_media Embed the image bytes.
 * @return array
 */
function antradus_export_payload( $with_media = true ) {
	$budget = $with_media ? ANTRADUS_EMBED_MAX_TOTAL : 0;

	$payload = array(
		'antradus' => 'content-export',
		'version'  => ANTRADUS_VERSION,
		'saved'    => current_time( 'mysql' ),
		'site'     => home_url( '/' ),
		'content'  => array(),
	);

	foreach ( antradus_languages() as $code => $unused ) {
		$values = get_option( antradus_option_name( $code ), array() );
		$values = is_array( $values ) ? $values : array();
		$payload['content'][ $code ] = antradus_expand_images( $values, $budget );
	}

	return $payload;
}

add_action( 'admin_post_antradus_export', 'antradus_handle_export' );
/**
 * Send the export as a file, which is how it gets from a laptop to a server.
 */
function antradus_handle_export() {
	antradus_require_admin( 'antradus_export' );

	$with_media = ! empty( $_POST['with_media'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked above.
	$payload    = antradus_export_payload( $with_media );
	$json       = wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	$host = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$name = 'antradus-content-' . sanitize_file_name( $host ) . '-' . gmdate( 'Y-m-d' ) . '.json';

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $name . '"' );
	header( 'Content-Length: ' . strlen( (string) $json ) );
	echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a JSON download, not markup.
	exit;
}

/* ===========================================================================
 * 2. Import
 * ========================================================================= */

/**
 * Turn one image descriptor back into something this site can use.
 *
 * @param mixed $value  Descriptor array, a bare URL, or an old attachment ID.
 * @param array $report Counters, by reference.
 * @return string Attachment ID or URL, as a string.
 */
function antradus_restore_image( $value, &$report ) {
	// A bare string from an older export. An ID from another site is
	// meaningless here, so only a URL is worth keeping.
	if ( ! is_array( $value ) ) {
		$value = trim( (string) $value );
		if ( '' === $value || ctype_digit( $value ) ) {
			++$report['dropped'];
			return '';
		}
		return esc_url_raw( $value );
	}

	$url      = isset( $value['url'] ) ? esc_url_raw( (string) $value['url'] ) : '';
	$filename = isset( $value['filename'] ) ? sanitize_file_name( (string) $value['filename'] ) : '';

	if ( '' === $url && empty( $value['data'] ) ) {
		++$report['dropped'];
		return '';
	}

	// 1. Did a previous import already bring this exact file across?
	if ( '' !== $url ) {
		$seen = get_posts(
			array(
				'post_type'        => 'attachment',
				'post_status'      => 'inherit',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'meta_key'         => '_antradus_source_url', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- runs once per image, on an explicit import.
				'meta_value'       => $url,                   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'suppress_filters' => false,
			)
		);
		if ( $seen ) {
			++$report['reused'];
			return (string) $seen[0];
		}

		// 2. Is it already in this site's own library?
		$existing = attachment_url_to_postid( $url );
		if ( $existing ) {
			++$report['reused'];
			return (string) $existing;
		}
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = '';

	// 3. The bytes travelled with the export - much the best case.
	if ( ! empty( $value['data'] ) && is_string( $value['data'] ) ) {
		$bytes = base64_decode( $value['data'], true );
		if ( false !== $bytes && '' !== $bytes ) {
			$tmp = wp_tempnam( $filename ? $filename : 'antradus-image' );
			if ( $tmp ) {
				file_put_contents( $tmp, $bytes ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- writing a temp file for media_handle_sideload.
			}
		}
	}

	// 4. No bytes: try fetching. Only http(s), and only when the host is not
	//    this machine talking to itself, which is the localhost export case.
	if ( '' === $tmp && '' !== $url ) {
		$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
		if ( in_array( $scheme, array( 'http', 'https' ), true ) ) {
			$fetched = download_url( $url, 20 );
			if ( ! is_wp_error( $fetched ) ) {
				$tmp = $fetched;
			}
		}
	}

	if ( '' === $tmp || ! file_exists( $tmp ) ) {
		// 5. Keep the URL. The theme renders one perfectly well, and a picture
		//    that shows is better than a slot that silently emptied itself.
		++$report['linked'];
		return $url;
	}

	$file = array(
		'name'     => $filename ? $filename : 'antradus-image.png',
		'tmp_name' => $tmp,
	);

	$id = media_handle_sideload( $file, 0, isset( $value['title'] ) ? sanitize_text_field( (string) $value['title'] ) : null );

	if ( is_wp_error( $id ) ) {
		if ( file_exists( $tmp ) ) {
			wp_delete_file( $tmp );
		}
		++$report['linked'];
		return $url;
	}

	update_post_meta( $id, '_antradus_source_url', $url );
	if ( ! empty( $value['alt'] ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( (string) $value['alt'] ) );
	}

	++$report['created'];
	return (string) $id;
}

/**
 * Walk one language's incoming values, rebuilding every image field.
 *
 * @param array $values Incoming values.
 * @param array $report Counters, by reference.
 * @return array
 */
function antradus_restore_images( $values, &$report ) {
	$fields = antradus_image_fields();

	foreach ( $fields['plain'] as $key ) {
		if ( isset( $values[ $key ] ) && '' !== $values[ $key ] ) {
			$values[ $key ] = antradus_restore_image( $values[ $key ], $report );
		}
	}

	foreach ( $fields['repeaters'] as $key => $subs ) {
		if ( empty( $values[ $key ] ) || ! is_array( $values[ $key ] ) ) {
			continue;
		}
		foreach ( $values[ $key ] as $index => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			foreach ( $subs as $sub ) {
				if ( isset( $row[ $sub ] ) && '' !== $row[ $sub ] ) {
					$values[ $key ][ $index ][ $sub ] = antradus_restore_image( $row[ $sub ], $report );
				}
			}
		}
	}

	return $values;
}

/**
 * Read the JSON an administrator handed us, from a file or from the textarea.
 *
 * @return array|null
 */
function antradus_read_import_payload() {
	$json = '';

	if ( ! empty( $_FILES['payload_file']['tmp_name'] ) && UPLOAD_ERR_OK === (int) $_FILES['payload_file']['error'] ) {
		$tmp = sanitize_text_field( $_FILES['payload_file']['tmp_name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- a PHP-provided temp path, validated by is_uploaded_file below.
		if ( is_uploaded_file( $tmp ) ) {
			$json = (string) file_get_contents( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading an upload, not a remote URL.
		}
	}

	if ( '' === trim( $json ) && isset( $_POST['payload'] ) ) {
		$json = (string) wp_unslash( $_POST['payload'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- JSON, sanitized field by field after decoding.
	}

	$data = json_decode( trim( $json ), true );
	return is_array( $data ) ? $data : null;
}

/**
 * Apply a decoded export to this site.
 *
 * @param array $data Decoded payload.
 * @return array{ok:bool,langs:int,created:int,reused:int,linked:int,dropped:int}
 */
function antradus_apply_import( $data ) {
	$report = array(
		'ok'      => false,
		'langs'   => 0,
		'created' => 0,
		'reused'  => 0,
		'linked'  => 0,
		'dropped' => 0,
	);

	// Both shapes: the two-language envelope, and a flat array from an older
	// single-language export.
	$bundle = ( isset( $data['content'] ) && is_array( $data['content'] ) )
		? $data['content']
		: array( antradus_default_lang() => $data );

	$schema = antradus_schema_fields();
	$known  = false;
	foreach ( $bundle as $values ) {
		if ( is_array( $values ) && array_intersect( array_keys( $values ), array_keys( $schema ) ) ) {
			$known = true;
			break;
		}
	}
	if ( ! $known ) {
		return $report; // Not one of ours; change nothing.
	}

	antradus_snapshot();

	foreach ( $bundle as $code => $values ) {
		if ( ! antradus_is_lang( $code ) || ! is_array( $values ) ) {
			continue;
		}
		$words_only = ( antradus_default_lang() !== $code );

		if ( ! $words_only ) {
			// Images live on the base row only, so this is the one pass that
			// touches the Media Library.
			$values = antradus_restore_images( $values, $report );
		}

		$clean = array();
		foreach ( $values as $key => $value ) {
			if ( ! isset( $schema[ $key ] ) ) {
				continue;
			}
			if ( $words_only && ! antradus_field_is_translatable( $schema[ $key ] ) ) {
				continue;
			}
			$clean[ $key ] = antradus_sanitize_value( $schema[ $key ], $value, $words_only );
		}

		update_option( antradus_option_name( $code ), $clean );
		++$report['langs'];
	}

	$report['ok'] = $report['langs'] > 0;
	return $report;
}

<?php
/**
 * Package the theme as antradus-theme-<version>.zip.
 *
 * Run with:  c:/xampp/php/php.exe build-zip.php
 *
 * Why this exists rather than a one-line Compress-Archive: Windows PowerShell
 * writes zip entries with BACKSLASHES ("antradus\style.css"). The zip format
 * requires forward slashes, so PHP's ZipArchive - which is what WordPress
 * unpacks an uploaded theme with - never finds antradus/style.css and rejects
 * the upload with "The theme is missing the style.css stylesheet." The archive
 * looks perfectly fine in Explorer and in .NET, which is what makes it a trap.
 */

$root = __DIR__;
$src  = $root . '/antradus';

if ( ! is_dir( $src ) ) {
	fwrite( STDERR, "No antradus/ directory beside this script.\n" );
	exit( 1 );
}

/*
 * The filename carries the version, and that is not decoration.
 *
 * "The theme is missing the style.css stylesheet" is the message WordPress
 * prints for every package failure, including the one where the file being
 * uploaded is simply not this file - an older build in Downloads, a copy on
 * another machine, or GitHub's Code -> Download ZIP, which hands over the whole
 * repository because the built zip is gitignored and has never been on GitHub.
 *
 * Every one of those impostors is called antradus-theme.zip. None of them can
 * be called antradus-theme-2.12.0.zip. A name that changes every release is
 * the difference between "I uploaded the zip" and "I uploaded THIS zip", and
 * it is checkable in the file picker without opening anything.
 */
$src_header = (string) file_get_contents( $src . '/style.css' );
$declared   = preg_match( '/^[ \t\/*#@]*Version:(.*)$/mi', $src_header, $m ) ? trim( $m[1] ) : '';
if ( '' === $declared ) {
	fwrite( STDERR, "antradus/style.css has no readable Version header.\n" );
	exit( 1 );
}

$out = $root . '/antradus-theme-' . $declared . '.zip';

if ( file_exists( $out ) && ! unlink( $out ) ) {
	fwrite( STDERR, "Could not remove the existing zip.\n" );
	exit( 1 );
}

/*
 * Clear away the old un-versioned name if it is still lying about. It is a
 * build artifact this script owns, it is gitignored, and leaving it beside the
 * real file is exactly how the wrong one gets picked.
 */
$stale = $root . '/antradus-theme.zip';
$cleared = false;
if ( file_exists( $stale ) ) {
	$cleared = unlink( $stale );
}

$zip = new ZipArchive();
if ( true !== $zip->open( $out, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "Could not create the zip.\n" );
	exit( 1 );
}

// Anything that belongs to the workspace rather than to the theme.
$skip = array( '.git', '.gitignore', '.gitattributes', 'node_modules', '.DS_Store', 'Thumbs.db' );

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ( $files as $file ) {
	$path = $file->getPathname();
	// Forward slashes, always - this is the whole point of the script.
	$rel = 'antradus/' . str_replace( '\\', '/', substr( $path, strlen( $src ) + 1 ) );

	$parts   = explode( '/', $rel );
	$skipped = false;
	foreach ( $parts as $part ) {
		if ( in_array( $part, $skip, true ) ) {
			$skipped = true;
			break;
		}
	}
	if ( $skipped ) {
		continue;
	}

	if ( $file->isDir() ) {
		$zip->addEmptyDir( $rel );
		continue;
	}
	$zip->addFile( $path, $rel );
	$count++;
}

$zip->close();

/*
 * Verify the way WordPress does, not the way that is easy.
 *
 * Asking the archive whether it contains "antradus/style.css" is not the
 * question WordPress asks, which is why a zip could pass here and still be
 * rejected on upload. WordPress unpacks the file, expects to find exactly ONE
 * directory at the top, descends into it, and reads style.css from there - and
 * "The theme is missing the style.css stylesheet" is the message for every one
 * of those steps failing, not just the last.
 *
 * So this unpacks the finished file to a temporary directory and walks the same
 * three steps against what actually came out.
 */

/**
 * Delete a directory and everything under it.
 *
 * @param string $dir Directory.
 */
function antradus_rmdir( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
	}
	$items = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ( $items as $item ) {
		$item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
	}
	rmdir( $dir );
}

/**
 * Fail loudly and leave no half-verified zip behind.
 *
 * @param string $why  What went wrong.
 * @param string $temp Directory to clean up.
 */
function antradus_reject( $why, $temp ) {
	antradus_rmdir( $temp );
	fwrite( STDERR, "REJECTED: " . $why . "\n" );
	fwrite( STDERR, "WordPress would refuse this file. The zip has NOT been left in a usable state.\n" );
	exit( 1 );
}

$temp = sys_get_temp_dir() . '/antradus-verify-' . getmypid();
antradus_rmdir( $temp );
mkdir( $temp, 0777, true );

$check = new ZipArchive();
if ( true !== $check->open( $out ) ) {
	antradus_reject( 'the finished file is not a readable zip.', $temp );
}
if ( ! $check->extractTo( $temp ) ) {
	$check->close();
	antradus_reject( 'the finished file does not unpack.', $temp );
}
$check->close();

// 1. Exactly one directory at the top, and nothing else beside it.
$top = array_values( array_diff( scandir( $temp ), array( '.', '..' ) ) );
if ( 1 !== count( $top ) || ! is_dir( $temp . '/' . $top[0] ) ) {
	antradus_reject(
		'the top of the archive holds ' . count( $top ) . ' entries (' . implode( ', ', $top ) . '); WordPress needs exactly one directory.',
		$temp
	);
}

// 2. style.css inside it.
$style = $temp . '/' . $top[0] . '/style.css';
if ( ! is_file( $style ) ) {
	antradus_reject( $top[0] . '/style.css is not in the unpacked archive.', $temp );
}

// 3. A header WordPress can actually read a theme out of.
$header  = (string) file_get_contents( $style );
$name    = preg_match( '/^[ \t\/*#@]*Theme Name:(.*)$/mi', $header, $m ) ? trim( $m[1] ) : '';
$version = preg_match( '/^[ \t\/*#@]*Version:(.*)$/mi', $header, $m ) ? trim( $m[1] ) : '';
if ( '' === $name || '' === $version ) {
	antradus_reject( 'style.css has no readable Theme Name / Version header.', $temp );
}

antradus_rmdir( $temp );

/*
 * The absolute path is printed because "which file do I upload" is the
 * question that actually goes wrong. The built theme is deliberately not in
 * git, so GitHub has no copy of it - and GitHub's green Code -> Download ZIP
 * button hands you the whole repository, which unpacks to a folder holding
 * antradus/, build-zip.php and README.md side by side. WordPress needs exactly
 * one directory at the top, finds three entries, and rejects it with "The theme
 * is missing the style.css stylesheet" - the same sentence as every other
 * failure, which is what makes it so hard to tell apart.
 *
 * Upload the path below, and check the byte count against the file the browser
 * is about to send.
 */
printf(
	"%s\n  %s %s\n  %d files, %s bytes\n  sha256 %s\n  verified: unpacks to one folder '%s' with a readable style.css\n",
	basename( $out ),
	$name,
	$version,
	$count,
	number_format( (int) filesize( $out ) ),
	hash_file( 'sha256', $out ),
	$top[0]
);

if ( $cleared ) {
	printf( "  removed the old un-versioned antradus-theme.zip, so it cannot be picked by mistake\n" );
}

printf(
	"\n  UPLOAD THIS FILE:\n  %s\n\n  In the file picker, the name must read exactly:  %s\n  and the size must read:  %s bytes (about %d KB)\n"
		. "  Anything called antradus-theme.zip, or any other size, is not this build.\n",
	str_replace( '/', DIRECTORY_SEPARATOR, $out ),
	basename( $out ),
	number_format( (int) filesize( $out ) ),
	(int) round( filesize( $out ) / 1024 )
);
exit( 0 );

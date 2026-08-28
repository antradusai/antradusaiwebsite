<?php
/**
 * Package the theme as antradus-theme.zip.
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
$out  = $root . '/antradus-theme.zip';

if ( ! is_dir( $src ) ) {
	fwrite( STDERR, "No antradus/ directory beside this script.\n" );
	exit( 1 );
}

if ( file_exists( $out ) && ! unlink( $out ) ) {
	fwrite( STDERR, "Could not remove the existing zip.\n" );
	exit( 1 );
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

// Prove the one thing WordPress checks before saying yes.
$check = new ZipArchive();
$check->open( $out );
$has = false !== $check->locateName( 'antradus/style.css' );
$check->close();

printf( "%s  %d files  %s\n", basename( $out ), $count, $has ? 'antradus/style.css OK' : 'STYLE.CSS MISSING' );
exit( $has ? 0 : 1 );

<?php
/**
 * Utility general functions.
 *
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */

declare( strict_types=1 );

/** @noinspection PhpUnused */

/**
 * Returns the plugin path to a specified file.
 *
 * @since   1.0.0
 *
 * @param string $filename The specified file.
 *
 * @return  string
 */
function mnca_get_path( string $filename = '' ): string {
	return MNCA_WP_INC_PATH . ltrim( $filename, '/' );
}

/**
 * Includes a file within the MNCA WP plugin.
 *
 * @since   1.0.0
 *
 * @param string $filename The specified file.
 *
 * @return  void
 */
function mnca_include( string $filename = '' ) {
	$file_path = mnca_get_path( $filename );
	if ( file_exists( $file_path ) ) {
		include_once $file_path;
	}
}

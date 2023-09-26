<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\Helper;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class Array_Helper {

	/**
	 * Filter an array with partial keyword.
	 *
	 * @param array $array The array where search the keyword.
	 * @param string $keyword The partial string to search.
	 * @param bool $use_key Whether to use array keys for search. False by default.
	 *
	 * @return array|false The filtered array by partial keyword. False otherwise.
	 */
	public static function array_filter_partial( array $array, string $keyword, bool $use_key = false ) {

		$found = array_filter( $array, function ( $value, $key ) use ( $keyword, $use_key ) {

			return $use_key ? ! ( false === strpos( $key, $keyword ) ) : ! ( false === strpos( $value, $keyword ) );

		}, ARRAY_FILTER_USE_BOTH );

		return ! empty( $found ) ? $found : false;
	}

	/**
	 * Flatten an arbitrarily nested array and don’t care about the keys.
	 *
	 * @link https://www.lambda-out-loud.com/posts/flatten-arrays-php/
	 *
	 * @param array $array The array to flatten.
	 *
	 * @return array The flatten array.
	 */
	public static function flatten_array( array $array ): array {
		$result = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, self::flatten_array( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Flatten an arbitrarily nested array and do care about the keys.
	 *
	 * @link https://www.lambda-out-loud.com/posts/flatten-arrays-php/
	 *
	 * @param array $array The array to flatten.
	 *
	 * @return array The flatten array.
	 */
	public static function flatten_array_preserve_keys( array $array ): array {
		$recursiveArrayIterator = new RecursiveArrayIterator(
			$array,
			RecursiveArrayIterator::CHILD_ARRAYS_ONLY
		);
		$iterator               = new RecursiveIteratorIterator( $recursiveArrayIterator );

		return iterator_to_array( $iterator );
	}
}

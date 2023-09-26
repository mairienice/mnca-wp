<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\Helper;

use Transliterator;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class String_Helper {

	/**
	 * Slugify a string
	 *
	 * @param string $string The string to slugify
	 *
	 * @return array|string|string[]|null
	 * @noinspection SpellCheckingInspection
	 */
	public static function slugify( string $string ) {
		$rule           = 'NFD; [:Nonspacing Mark:] Remove; NFC';
		$transliterator = Transliterator::create( $rule );
		$string         = $transliterator->transliterate( $string );

		return preg_replace(
			'/[^a-z0-9]/',
			'-',
			strtolower( trim( strip_tags( $string ) ) )
		);
	}

	/**
	 * Replace only first occurrence of a substring.
	 *
	 * @link https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
	 *
	 * @param string $search The substring to search.
	 * @param string $replace The replacement string.
	 * @param string $subject The string where search.
	 *
	 * @return string
	 */
	public static function str_replace_first( string $search, string $replace, string $subject ): string {
		$pos = strpos( $subject, $search );
		if ( $pos !== false ) {
			return substr_replace( $subject, $replace, $pos, strlen( $search ) );
		}

		return $subject;
	}

	/**
	 * Sanitizes a form field name.
	 *
	 * Keys are used as internal identifiers.
	 * Lowercase alphanumeric characters,dashes, and underscores are allowed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Field name.
	 *
	 * @return string Sanitized field name.
	 */
	public static function sanitize_field_name( string $name ): string {
		$sanitized_name = strtolower( $name );

		return preg_replace( '/[^a-z0-9_.*~\-]/', '', $sanitized_name );
	}
}

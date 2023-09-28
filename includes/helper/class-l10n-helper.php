<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\helper;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */
class L10n_Helper {

	/**
	 * Retrieve the only 2 letters format of current locale.
	 *
	 * Example: a current locale 'fr_FR' will return 'fr'.
	 *
	 * @return string The shortened locale.
	 */
	public static function get_short_locale(): string {

		return substr( get_locale(), 0, 2 );
	}

}

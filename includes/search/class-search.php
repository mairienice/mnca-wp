<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */
class Search {

	static function init() {
		// register hooks and load functions.
	}
	static function load_functions() {
		mnca_include('search/search.php');
	}
}

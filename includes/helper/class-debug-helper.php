<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\Helper;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class Debug_Helper {

	/**
	 * Display all rewrite rules for debug.
	 *
	 * @global \WP_Rewrite $wp_rewrite WordPress rewrite component.
	 * @return void
	 */
	public
	static function debug_rewrite_rules(): void {
		global $wp_rewrite;

		$debug_output = print_r( $wp_rewrite->rules, true );
		echo "<pre>$debug_output</pre>";
	}

	/**
	 * Display all query vars for debug.
	 *
	 * @global \WP_Query $wp_query WordPress Query object.
	 * @return void
	 */
	public
	static function debug_query_vars(): void {
		global $wp_query;

		$debug_output = print_r( $wp_query->query_vars, true );
		echo "<pre>$debug_output</pre>";
	}

	/**
	 * Display all tax_queries for debug.
	 *
	 * @global \WP_Query $wp_query WordPress Query object.
	 * @return void
	 */
	public
	static function debug_tax_query(): void {
		global $wp_query;

		$debug_output = print_r( $wp_query->tax_query->queries, true );
		echo "<pre>$debug_output</pre>";
	}
}

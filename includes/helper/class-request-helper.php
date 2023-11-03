<?php
/** @noinspection PhpUnused */
declare( strict_types=1 );

namespace MNCA_WP\Helper;

use WP_Query;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class Request_Helper {

	/**
	 * Set parameters of a WP Query from a parameters array.
	 *
	 * @param array $args The parameters for the WP Query.
	 * @param WP_Query $query The WP Query to set the parameters.
	 *
	 * @return void
	 */
	public static function set_query_args( array $args, WP_Query $query ) {
		foreach ( $args as $key => $arg ) {
			$query->set( $key, $arg );
		}
	}

	/**
	 * Verify if the action query var match a specific action name.
	 *
	 * @param string $action_name The action name to check.
	 * @param WP_Query|null $query The
	 *
	 * @return bool Whether the action name match.
	 */
	public static function is_query_action( string $action_name, WP_Query $query = null ): bool {

		return $query ?
			$query->get( 'action' ) === $action_name : get_query_var( 'action' ) === $action_name;
	}

	/**
	 * Retrieve the URL of the current page.
	 *
	 * @return string|null URL of the current page. Otherwise, null.
	 *
	 * @global \WP $wp Current WordPress environment instance.
	 */
	public static function get_current_page_url(): ?string {
		global $wp;

		return home_url( add_query_arg( array(), $wp->request ) );
	}

	/**
	 * Retrieve the URL of the current pag with query strings.
	 *
	 * @return string|null URL of the current page with query strings. Otherwise, null.
	 *
	 * @global \WP $wp Current WordPress environment instance.
	 */
	public static function get_current_page_full_url(): ?string {
		global $wp;

		return home_url( add_query_arg( $wp->query_vars, $wp->request ) );
	}
}

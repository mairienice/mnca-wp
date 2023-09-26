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
class Plugin_Helper {

	/**
	 * Determine if we are running the admin deactivation plugin flow.
	 *
	 * @param string|null $plugin Path to the plugin file relative to the plugins' directory.
	 *                            By default, we suppose the path like name_of_plugin/name_of_plugin.php
	 *
	 * @return bool Whether we are in admin deactivation plugin flow.
	 */
	public
	static function is_deactivation_plugin(
		?string $plugin = null
	): bool {

		if ( ! $plugin ) {
			$plugin_dir = plugin_basename( dirname( __FILE__, 2 ) );
			$plugin     = "$plugin_dir/$plugin_dir.php";
		}

		return is_admin()
		       && isset( $_GET['action'] )
		       && $_GET['action'] === 'deactivate'
		       && $_GET['plugin'] === $plugin;
	}
}

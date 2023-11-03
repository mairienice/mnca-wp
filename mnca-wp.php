<?php
/**
 * MNCA WP Framework
 *
 * @package           MNCA_WP
 * @author            MNCA
 * @link              https://www.nicecotedazur.org
 *
 * @wordpress-plugin
 * Plugin Name:       MNCA WP Framework
 * Plugin URI:        https://www.nicecotedazur.org
 * Description:       Help create WordPress websites for MNCA.
 * Version:           1.1.0
 * Requires at least: 6.1.0
 * Requires PHP:      7.4
 * Author:            MNCA
 * Author URI:        https://www.nicecotedazur.org
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       mnca-wp
 * Domain Path:       /languages
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

define( 'MNCA_WP', true );
define( 'MNCA_WP_VERSION', '1.1.0' );
define( 'MNCA_WP_PATH', plugin_dir_path( __FILE__ ) );
define( 'MNCA_WP_BASENAME', plugin_basename( __FILE__ ) );
define( 'MNCA_WP_REL_PATH', dirname( MNCA_WP_BASENAME ) );
define( 'MNCA_WP_INC_PATH', realpath( MNCA_WP_PATH . 'includes/' ) . '/' );

// Load utility functions
require_once( MNCA_WP_INC_PATH . 'utility.php' );

// Composer autoload.
if ( file_exists( MNCA_WP_PATH . 'vendor/autoload.php' ) ) {
	require MNCA_WP_PATH . 'vendor/autoload.php';
}

// Load translations.
if ( ! is_admin() ) {
	load_plugin_textdomain(
		'mnca-wp',
		false,
		MNCA_WP_REL_PATH . '/languages/'
	);
}


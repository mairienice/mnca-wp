<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP;

use Exception;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */
abstract class Plugin {

	/**
	 * The Singleton's instance is stored in a static field. This field is an
	 * array, because we'll allow our Singleton to have subclasses. Each item in
	 * this array will be an instance of a specific Singleton's subclass. You'll
	 * see how this works in a moment.
	 */
	private static array $instances = [];

	protected array $languages = [];

	protected string $option_name = '';

	/**
	 * The Singleton's constructor should always be private to prevent direct
	 * construction calls with the `new` operator.
	 */
	abstract protected function __construct();

	/**
	 * Singletons should not be cloneable.
	 */
	protected function __clone() {
	}

	/**
	 * Singletons should not be restorable from strings.
	 * @throws Exception
	 * @noinspection SpellCheckingInspection
	 */
	public function __wakeup() {
		throw new Exception( "Cannot unserialize a singleton." );
	}

	/**
	 * This is the static method that controls the access to the singleton
	 * instance. On the first run, it creates a singleton object and places it
	 * into the static field. On subsequent runs, it returns the client existing
	 * object stored in the static field.
	 *
	 * This implementation lets you subclass the Singleton class while keeping
	 * just one instance of each subclass around.
	 */
	public static function get_instance(): Plugin {
		$cls = static::class;
		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new static();
		}

		return self::$instances[ $cls ];
	}

	/**
	 * @return array
	 */
	public function get_languages(): array {
		return $this->languages;
	}

	/**
	 * @return string
	 */
	public function get_option_name(): string {
		return $this->option_name;
	}

	/**
	 * Retrieve general admin options
	 *
	 * @return false|array The array of options. False otherwise
	 */
	public function get_general_options() {
		return get_option( $this->get_option_name() );
	}

	/**
	 * Check and return if exist the plugin option.
	 *
	 * @param string $option_name The option name saved in database.
	 *
	 * @return false|mixed
	 */
	public function have_option( string $option_name ) {
		$general_options = $this->get_general_options();

		if ( $general_options && isset( $general_options[ $option_name ] ) ) {
			return $general_options[ $option_name ];
		}

		return false;
	}

}

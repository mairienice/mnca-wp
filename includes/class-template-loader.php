<?php
declare( strict_types=1 );

namespace MNCA_WP;

/**
 * Template Loader for Plugins.
 *
 * When using in a plugin, create a new class that extends this one and just overrides the properties.
 *
 * Based on {@link http://github.com/GaryJones/Gamajo-Template-Loader}
 *
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */
class Template_Loader {

	/**
	 * Prefix for filter names.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected string $filter_prefix = '';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected string $theme_template_directory = 'templates';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * Can either be a defined constant, or a relative reference from where the subclass lives.
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected string $plugin_directory = MNCA_WP_PATH;

	/**
	 * Retrieve a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $name Optional. Default null.
	 * @param bool $load Optional. Default true.
	 * @param array $args
	 *
	 * @param string $slug
	 *
	 * @return string
	 * @uses Template_Loader::locate_template() Retrieve the name of the highest priority template
	 *     file that exists.
	 *
	 * @uses Template_Loader::get_template_file_names() Create file names of templates.
	 */
	public function get_template_part( string $slug, string $name = null, bool $load = true, array $args = array() ) {
		// Execute code for this part
		do_action( 'get_template_part_' . $slug, $slug, $name );

		// Get files names of templates, for given slug and name.
		$templates = $this->get_template_file_names( $slug, $name );

		// Return the part that is found
		return $this->locate_template( $templates, $load, false, $args );
	}

	/**
	 * Given a slug and optional name, create the file names of templates.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug
	 * @param string|null $name
	 *
	 * @return array
	 */
	protected function get_template_file_names( string $slug, ?string $name = null ): array {
		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Allow template choices to be filtered.
		 *
		 * The resulting array should be in the order of most specific first, to the least specific last.
		 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug Template slug.
		 * @param string|null $name Template name.
		 *
		 * @param array $templates Names of template files that should be looked for, for given slug and name.
		 */
		return apply_filters( $this->filter_prefix . '_get_template_part', $templates, $slug, $name );
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
	 * inherit from a parent theme can just overload one file. If the template is
	 * not found in either of those, it looks in the theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $load If true the template file will be loaded if it is found.
	 * @param bool $require_once Whether to require_once or require. Default true.
	 *   Has no effect if $load is false.
	 *
	 * @param string|array $template_names Template file(s) to search for, in order.
	 *
	 * @return string The template filename if one is located.
	 * @uses         Template_Loader::get_template_paths() Return a list of paths to check for template locations.
	 * @noinspection SpellCheckingInspection
	 */
	protected function locate_template( $template_names, bool $load = false, bool $require_once = true, $args = array() ) {
		// No file found yet
		$located = false;

		// Remove empty entries
		$template_names = array_filter( (array) $template_names );
		// Try to find a template file
		foreach ( $template_names as $template_name ) {
			// Trim off any slashes from the template name
			$template_name = ltrim( $template_name, '/' );

			// Try locating this template file by looping through the template paths
			foreach ( $this->get_template_paths() as $template_path ) {
				if ( file_exists( $template_path . $template_name ) ) {
					$located = $template_path . $template_name;
					break;
				}
			}
		}

		if ( $load && $located ) {
			load_template( $located, $require_once, $args );
		}

		return $located;
	}

	/**
	 * Return a list of paths to check for template locations.
	 *
	 * Default is to check in a child theme (if relevant) before a parent theme, so that themes which inherit from a
	 * parent theme can just overload one file. If the template is not found in either of those, it looks in the
	 * theme-compat folder last.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_template_paths(): array {
		$theme_directory = trailingslashit( $this->theme_template_directory );

		$file_paths = array(
			10  => trailingslashit( get_template_directory() ) . $theme_directory,
			100 => $this->get_templates_dir()
		);

		// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
		if ( is_child_theme() ) {
			$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
		}

		/**
		 * Allow ordered list of template paths to be amended.
		 *
		 * @since 1.0.0
		 *
		 * @param array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100.
		 *
		 */
		$file_paths = apply_filters( $this->filter_prefix . '_template_paths', $file_paths );

		// sort the file paths based on priority
		ksort( $file_paths, SORT_NUMERIC );

		/** @noinspection SpellCheckingInspection */
		return array_map( 'trailingslashit', $file_paths );

	}

	/**
	 * Return the path to the templates directory in this plugin.
	 *
	 * May be overridden in subclass.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_templates_dir(): string {
		return $this->plugin_directory ?
			$this->plugin_directory . 'templates' : plugin_dir_path( dirname( __FILE__ ) ) . 'templates';
	}
}

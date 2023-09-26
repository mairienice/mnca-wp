<?php
declare( strict_types=1 );

namespace MNCA_WP\Walker;

use Walker_PageDropdown;
use WP_Post;

/**
 * A custom walker for wp_dropdown_pages
 *
 * @see wp_dropdown_pages()
 *
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */
class PageDropdown_Walker extends Walker_PageDropdown {

	/**
	 * Starts the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 5.9.0 Renamed `$page` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Optional. Uses 'selected' argument for selected page to
	 *                                   set selected HTML attribute for option element. Uses
	 *                                   'value_field' argument to fill "value" attribute.
	 *                                   See wp_dropdown_pages(). Default empty array.
	 * @param int $current_object_id Optional. ID of the current page. Default 0.
	 *
	 * @param string $output Used to append additional content. Passed by reference.
	 * @param WP_Post $data_object Page data object.
	 *
	 * @param int $depth Optional. Depth of page in reference to parent pages.
	 *                                   Used for padding. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method.
		$page = $data_object;
		$pad  = str_repeat( '&nbsp;', $depth * 3 );

		if ( ! isset( $args['value_field'] ) || ! isset( $page->{$args['value_field']} ) ) {
			$args['value_field'] = 'ID';
		}

		$output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $page->{$args['value_field']} ) . '"';
		if ( $page->post_name == $args['selected'] ) {
			$output .= ' selected="selected"';
		}
		$output .= '>';

		$title = $page->post_title;
		if ( '' === $title ) {
			/* translators: %d: ID of a post. */
			$title = sprintf( __( '#%d (no title)' ), $page->ID );
		}

		/**
		 * Filters the page title when creating an HTML drop-down list of pages.
		 *
		 * @since 3.1.0
		 *
		 * @param WP_Post $page Page data object.
		 * @param string $title Page title.
		 */
		$title = apply_filters( 'list_pages', $title, $page );

		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}

}

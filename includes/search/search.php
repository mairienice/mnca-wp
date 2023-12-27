<?php
/**
 * Functions to help displaying search form in theme, templates.
 *
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 */

/** @noinspection PhpUnused */

declare( strict_types=1 );

use MNCA_WP\Helper\Array_Helper;
use MNCA_WP\Helper\Request_Helper;
use MNCA_WP\Helper\String_Helper;
use MNCA_WP\Walker\PageDropdown_Walker;

global $template_loader;
$template_loader = new MNCA_WP\Template_Loader();

/**
 * Filters the query variables to add custom variables used by search filter template tags.
 *
 * Callback of filter hook 'query_vars' (cf. {@see WP::parse_request()}).
 *
 * @since 1.0.0
 *
 * @param string[] $vars The array of allowed query variable names.
 *
 * @return string[] The filtered array of allowed query variable names.
 */
function mnca_search_query_vars( array $vars ): array {

	if ( $_REQUEST && isset( $_REQUEST['_search'] ) ) {
		$search_field_names = preg_grep( '/^[_~]/', array_keys( $_REQUEST ) );
		foreach ( $search_field_names as $search_field_name ) {
			$vars[] = $search_field_name;
		}
	}

	return $vars;
}

add_filter( 'query_vars', 'mnca_search_query_vars' );

/**
 * Modify the main query to create a custom query for search.
 *
 * Callback function of hook action 'pre_get_posts' (cf. {@see WP_Query::get_posts()}).
 *
 * @since   1.0.0
 *
 * @param WP_Query $query The main query.
 *
 * @return  void
 */
function mnca_search_custom_query( WP_Query $query ) {

	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( isset( $_REQUEST['_search'] ) ) {

		if ( count( $_REQUEST ) === 1 ) {
			wp_redirect( Request_Helper::get_current_page_url() );
			exit;
		}

		if ( ! wp_verify_nonce( wp_unslash( $_REQUEST['_search'] ), 'mnca-search' ) ) {
			wp_nonce_ays( 'mnca-search' );
		}

		$query->set( 'posts_per_page', 12 );
		$query->set( 'post_status', 'publish' );

		$tax_query             = $query->get( 'tax_query', array() );
		$tax_query['relation'] = 'AND';

		foreach ( get_taxonomies( array(), 'objects' ) as $tax ) {

			if ( ( $tax_slug = $query->get( "_$tax->query_var" ) ) && $tax_slug !== '-1' ) {
				$tax_query[] = array(
					'taxonomy' => $tax->query_var,
					'field'    => 'slug',
					'terms'    => $tax_slug,
				);
			}
		}

		$query->set( 'tax_query', $tax_query );

		$acf_field_vars = Array_Helper::array_filter_partial( $query->query_vars, '~', true );

		if ( $acf_field_vars ) {
			$meta_query = $query->get( 'meta_query', array() );

			foreach ( $acf_field_vars as $acf_field_var_key => $acf_field_var_value ) {
				$acf_field_name = ltrim( $acf_field_var_key, '~' );

				if (
					( $acf_field = acf_get_field( $acf_field_name ) )
					&& $acf_field['type'] === 'relationship'
					&& ( $post_name = sanitize_key( $acf_field_var_value ) )
					&& ( $post = get_page_by_path( $post_name, OBJECT, $acf_field['post_type'] ) )
				) {
					$meta_query[]           = array(
						'key'     => $acf_field_name,
						'value'   => $post->ID,
						'compare' => 'LIKE',
					);
					$meta_query['relation'] = 'AND';
				}
			}

			$query->set( 'meta_query', $meta_query );
		}
	}
}

add_action( 'pre_get_posts', 'mnca_search_custom_query' );

function mnca_search_posts_where( string $where, WP_Query $query ): string {
	global $wpdb;

	if ( $alpha = $query->get( '_alpha' ) ) {
		$where .= " AND $wpdb->posts.post_title LIKE '$alpha%'";
	}

	return $where;
}

add_filter( 'posts_where', 'mnca_search_posts_where', 10, 2 );

/**
 * Display the opening <form> tag of search form.
 *
 * @see project://templates/tags/search-form-start.php
 *
 * @since 1.0.0
 *
 * @param array{
 *                id: string,
 *                class: string,
 *                action: string,
 *                method: string,
 *              } $args Optional.
 *
 * @return void
 */
function mnca_search_form_start( array $args = array() ) {
	global $template_loader;

	$defaults = array(
		'id'     => 'mnca-search',
		'class'  => 'form--search',
		'action' => Request_Helper::get_current_page_url_no_paging(),
		'method' => 'get',
	);

	if ( isset( $args['class'] ) ) {
		$args['class'] = $args['class'] . " " . $defaults['class'];
	}

	$args = wp_parse_args( $args, $defaults );

	ob_start();

	$template_loader->get_template_part( 'tags/search', 'form-start', true, $args );

	echo ob_get_clean();
}

/**
 * Display a select list with taxonomy terms for search filtering.
 *
 * @since 1.0.0
 *
 * @param string $taxonomy Taxonomy key.
 * @param string|null $show_option_none Text to display for the default empty option where value is -1.
 * @param string|null $selected Option that should be selected.
 *                              Default is the query var of $taxonomy prefixed by `_`.
 * @param string|null $class The HTML class attribute value of the select. Default is `form-select`.
 *
 * @return void
 * @uses wp_dropdown_categories()
 *
 */
function mnca_search_select_taxonomy(
	string $taxonomy,
	?string $show_option_none = null,
	?string $selected = null,
	?string $class = null,
	?int $child_of = null
) {
	$selected        = $selected ?? get_query_var( "_$taxonomy" );
	$default_options = array(
		'taxonomy'         => '',
		'name'             => sanitize_key( "_$taxonomy" ),
		'show_option_none' => '',
		'selected'         => '',
		'hide_if_empty'    => true,
		'value_field'      => 'slug',
		'class'            => 'form-select',
		'child_of'         => '',
	);
	$options         = array(
		'taxonomy'         => sanitize_key( $taxonomy ),
		'show_option_none' => sanitize_text_field( $show_option_none ),
		'selected'         => sanitize_key( $selected ),
		'class'            => esc_attr( $class ),
		'child_of'         => is_int( $child_of ) ? $child_of : '',
	);

	$filtered_options = array_filter( $options );

	$parsed_options = wp_parse_args( $filtered_options, $default_options );

	ob_start();

	wp_dropdown_categories( $parsed_options );

	echo ob_get_clean();
}

/**
 * Display a select list of posts from specific post type for search filtering.
 *
 * @since 1.0.0
 *
 * @param string $post_type Post type key.
 * @param string|null $show_option_none Text to display for the default empty option where value is -1.
 * @param string|null $selected Option that should be selected.
 *                              Default is the query var of $post_type prefixed by `_`.
 * @param string|null $class The HTML class attribute value of the select. Default is `form-select`.
 *
 * @return void
 * @uses wp_dropdown_pages()
 * @uses \MNCA_WP\Walker\PageDropdown_Walker
 *
 */
function mnca_search_select_post_type(
	string $post_type,
	?string $name,
	?string $show_option_none = null,
	?string $selected = null,
	?string $class = null
) {
	$selected        = $selected ?? get_query_var( "_$post_type" );
	$default_options = array(
		'post_type'         => '',
		'name'              => sanitize_key( "_$post_type" ),
		'show_option_none'  => '',
		'option_none_value' => - 1,
		'selected'          => '',
		'value_field'       => 'post_name',
		'walker'            => new PageDropdown_Walker(),
		'class'             => 'form-select',
	);
	$options         = array(
		'post_type'        => sanitize_key( $post_type ),
		'name'             => String_Helper::sanitize_field_name( $name ),
		'show_option_none' => sanitize_text_field( $show_option_none ),
		'selected'         => sanitize_key( $selected ),
		'class'            => esc_attr( $class ),
	);

	$filtered_options = array_filter( $options );

	$parsed_options = wp_parse_args( $filtered_options, $default_options );

	ob_start();

	wp_dropdown_pages( $parsed_options );

	echo ob_get_clean();
}

/**
 * Display a select list of posts from specific post type used in an ACF relationship field.
 *
 * @param string $acf_field_name The acf field name
 * @param string|null $post_type
 * @param array{
 *                show_option_none: string,
 *                selected: string,
 *                class: string,
 *              } $args
 *
 * @return void
 */
function mnca_search_select_acf_relationship(
	string $acf_field_name,
	?string $post_type = null,
	array $args = array()
) {

	if (
		! ( $acf_field_object = acf_get_field( $acf_field_name ) )
		&& $acf_field_object['type'] !== 'relationship'
	) {
		return;
	}

	$defaults = array(
		'show_option_none' => null,
		'selected'         => get_query_var( "~$acf_field_name", null ),
		'class'            => null,
	);

	$args = wp_parse_args( $args, $defaults );

	$post_type ??= $acf_field_object['post_type'][0];

	if ( $post_type ) {
		mnca_search_select_post_type(
			$post_type,
			"~$acf_field_name",
			$args['show_option_none'],
			$args['selected'],
			$args['class']
		);
	}
}

/**
 * Display an alphabetical filter
 *
 * @see project://templates/tags/search-alpha.php
 *
 * @since 1.0.0
 *
 * @param array{
 *                value: string,
 *             } $args Optional.
 *
 * @return void
 */
function mnca_search_alpha( array $args = array() ) {
	global $template_loader;
	global $wp_query;

	$defaults = array(
		'value' => $wp_query->get( '_alpha', - 1 ),
	);
	$args     = wp_parse_args( $args, $defaults );

	ob_start();

	$template_loader->get_template_part( 'tags/search', 'alpha', true, $args );

	echo ob_get_clean();
}

/**
 * Display an input type submit.
 *
 * @see project://templates/tags/search-input-submit.php
 *
 * @since 1.0.0
 *
 * @param array{
 *                value: string,
 *             } $args Optional.
 *
 * @return void
 */
function mnca_search_input_submit( array $args = array() ) {
	global $template_loader;
	$defaults = array(
		'value' => __( 'Filtering', 'mnca-wp' ),
	);
	$args     = wp_parse_args( $args, $defaults );

	ob_start();

	$template_loader->get_template_part( 'tags/search', 'input-submit', true, $args );

	echo ob_get_clean();
}

/**
 * Display a reset button.
 *
 * @see project://templates/tags/search-reset.php
 *
 * @since 1.0.0
 *
 * @param array{
 *                value: string,
 *                href: string
 *             } $args Optional.
 *
 * @return void
 */
function mnca_search_reset( array $args = array() ) {
	global $template_loader;
	$defaults = array(
		'value' => __( 'Reset', 'mnca-wp' ),
		'href'  => Request_Helper::get_current_page_url_no_paging(),
	);
	$args     = wp_parse_args( $args, $defaults );

	ob_start();

	$template_loader->get_template_part( 'tags/search', 'reset', true, $args );

	echo ob_get_clean();
}

/**
 * Display a hidden nonce field for security purposes.
 *
 * @since 1.0.0
 *
 * @param array{
 *                referer: boolean,
 *             } $args Optional.
 *
 * @return void
 * @uses wp_nonce_field()
 *
 */
function mnca_search_hidden_nonce( array $args = array() ) {
	$defaults = array(
		'referer' => false,
	);
	$args     = wp_parse_args( $args, $defaults );

	ob_start();

	wp_nonce_field( 'mnca-search', '_search', $args['referer'] );

	echo ob_get_clean();
}

/**
 * Display the ending <form> tag of search form with hidden nonce field.
 *
 * @see project://templates/tags/search-form-end.php
 *
 * @since 1.0.0
 *
 * @return void
 */
function mnca_search_form_end() {

	global $template_loader;

	ob_start();

	$template_loader->get_template_part( 'tags/search', 'form-end' );

	echo ob_get_clean();

}

/**
 * Check if a value is the current selected query_var value.
 *
 * @param string $query_var The name of the query_var.
 * @param mixed $value The value wanted to test.
 * @param mixed $on_success The returned value if the test is successful. True by default.
 *
 * @return bool|mixed The $on_success value on success. False otherwise.
 */
function mnca_search_selected( string $query_var, $value, $on_success = true ) {
	global $wp_query;

	if ( $wp_query->get( $query_var, false ) === $value ) {
		return $on_success;
	}

	return false;
}


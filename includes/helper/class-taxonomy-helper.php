<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\Helper;

use WP_Post;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class Taxonomy_Helper {

	/**
	 * Retrieve the top level terms (parent terms) of a taxonomy.
	 *
	 * @param WP_Post $post Post object.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return false|\WP_Error|\WP_Term[]
	 */
	public static function get_the_top_level_terms( WP_Post $post, string $taxonomy ) {
		$terms = get_the_terms( $post->ID, $taxonomy );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			return array_filter( $terms, function ( $term ) {
				return $term->parent === 0;
			} );
		}

		return false;
	}

	/**
	 * Retrieve the numeric terms from string terms of a taxonomy that are attached to the post and sorted them.
	 *
	 * @param int|WP_Post $post The post ID or object.
	 * @param string $taxonomy The taxonomy key.
	 * @param string|null $numeric_term_suffix Add a same suffix to all numeric term of the list. Optional.
	 *
	 * @return array|false The list of numeric terms sorted with `min` and `max` keys. False otherwise
	 * @uses get_the_terms()
	 */
	public static function get_the_numeric_terms_sorted( $post, string $taxonomy, ?string $numeric_term_suffix = null ) {
		$terms_obj_list = get_the_terms( $post, $taxonomy );

		if ( ! $terms_obj_list || is_wp_error( $terms_obj_list ) ) {
			return false;
		}

		$numeric_term_list = array_map( fn( $value ): int => intval( $value ), wp_list_pluck( $terms_obj_list, 'name' ) );
		sort( $numeric_term_list, SORT_NUMERIC );
		$last_key                 = array_key_last( $numeric_term_list );
		$numeric_term_list['max'] = $numeric_term_list[ $last_key ];
		$numeric_term_list['min'] = $numeric_term_list[0];
		unset( $numeric_term_list[ $last_key ] );
		unset( $numeric_term_list[0] );
		asort( $numeric_term_list, SORT_NUMERIC );

		if ( $numeric_term_list && $numeric_term_suffix ) {
			array_walk( $numeric_term_list, fn( &$value, $key ): string => $value .= " " . $numeric_term_suffix );
		}

		return $numeric_term_list;
	}

	/**
	 * Retrieve the terms list of a taxonomy sorted by ID.
	 *
	 * @param int|WP_Post $post Post ID or object.
	 * @param string $taxonomy
	 *
	 * @return array|false The array list sorted by id where the keys are `term_id` and values `name`.
	 *                     False otherwise.
	 * @uses get_the_terms()
	 */
	public static function get_the_terms_sorted_by_id( $post, string $taxonomy ) {
		$terms_obj_list = get_the_terms( $post, $taxonomy );

		if ( ! $terms_obj_list || is_wp_error( $terms_obj_list ) ) {
			return false;
		}

		$terms_sorted_by_id = wp_list_pluck( $terms_obj_list, 'name', 'term_id' );
		ksort( $terms_sorted_by_id );

		return $terms_sorted_by_id;
	}
}

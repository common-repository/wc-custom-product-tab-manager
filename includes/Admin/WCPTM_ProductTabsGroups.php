<?php

namespace WPRealizer\WCCustomProductTabManager\Admin;

/**
 * WCPTM_ProductTabsGroups class
 *
 * @since 1.0.0
 */
class WCPTM_ProductTabsGroups {

	/**
	 * Returns all the global groups (if any) and their tabs
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_all_global_groups() {
		$global_groups = array();

		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => 'wpr_wcptm_tabs',
			'post_status'      => 'any',
			'suppress_filters' => true,
		);

		$global_group_posts = get_posts( $args );

		foreach ( (array) $global_group_posts as $global_group_post ) {
			$global_groups[] = wpr_wc_custom_product_tab_manager()->product_tabs_groups->get_group( $global_group_post );
		}

		return $global_groups;
	}


	/**
	 * Gets a global tabs group from the provided post in a structure response
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public static function get_group( $post ) {
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$term_ids = (array) wp_get_post_terms( $post->ID, array( 'product_cat' ), array( 'fields' => 'ids' ) );

		if ( 1 === (int) get_post_meta( $post->ID, '_wcptm_global_all_products', true ) ) {
			$categories = array();
		} else {
			$categories = array();
			foreach ( $term_ids as $term_id ) {
				$term = get_term_by( 'id', $term_id, 'product_cat' );
				if ( $term ) {
					$categories[ $term_id ] = $term->name;
				}
			}
		}

		return array(
			'id'                     => $post->ID,
			'name'                   => $post->post_title,
			'restrict_to_categories' => $categories,
		);
	}
}

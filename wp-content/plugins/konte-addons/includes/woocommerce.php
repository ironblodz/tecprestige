<?php
/**
 * WooCommerce hooks and functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove subcategories from the shop page and tell the Konte theme know that removed.
 */
function konte_addons_remove_shop_subcategories() {
	remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
}

add_action( 'woocommerce_before_shop_loop', 'konte_addons_remove_shop_subcategories', 49 );

/**
 * Loads and get size guide instance
 *
 * @return bool|object
 */
function konte_addons_size_guide() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( ! class_exists( 'Konte_Addons_WooCommerce_Size_Guide' ) ) {
		require_once 'size-guide.php';
	}

	return Konte_Addons_WooCommerce_Size_Guide::instance();
}

add_action( 'plugins_loaded', 'konte_addons_size_guide' );

/**
 * Add custom headers to the ajax filter response
 *
 * @since  2.2.3
 *
 * @return void
 */
function konte_addons_product_filter_headers() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! is_filtered() || ! isset( $_GET['product_cat'] ) ) {
		return;
	}

	// Stop if pretty permalink is not active.
	if ( ! get_option( 'permalink_structure' ) ) {
		return;
	}

	$cat_link = get_term_link( trim( $_GET['product_cat'] ), 'product_cat' );

	if ( is_wp_error( $cat_link ) ) {
		return;
	}

	parse_str( $_SERVER['QUERY_STRING'], $params );
	unset( $params['product_cat'] );

	if ( 1 == count( $params ) && array_key_exists( 'filter', $params ) ) {
		$filter_link = $cat_link;
	} else {
		$filter_link = add_query_arg( $params, $cat_link );
	}

	// If is ajax.
	if ( is_ajax() || ! empty( $_POST['filter_time'] ) ) {
		if ( ! headers_sent() ) {
			header( 'Konte-Filter-Link: ' . $filter_link );
		}
	} else {
		wp_safe_redirect( $filter_link );
	}
}

add_filter( 'template_redirect', 'konte_addons_product_filter_headers' );

/**
 * Modify the tax query of filter requests
 *
 * @param array $tax_query
 *
 * @return array
 */
function konte_addons_product_filter_tax_query( $tax_query ) {
	if ( ! is_main_query() || ! is_filtered() ) {
		return $tax_query;
	}

	// Stock status filter. Supports 'instock' and 'outofstock'.
	if ( ! empty( $_GET['stock'] ) ) {
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$outstock_term = $product_visibility_terms['outofstock'];

		// Remove any query that relates to stock status.
		foreach ( $tax_query as $key => $query ) {
			if ( 'relation' === $key || 'product_visibility' != $query['taxonomy'] ) {
				continue;
			}

			if ( in_array( $outstock_term, $query['terms'] ) ) {
				$query['terms'] = array_diff( $query['terms'], array( $outstock_term ) );
			}

			if ( empty( $query['terms'] ) ) {
				unset( $tax_query[ $key ] );
			} else {
				$tax_query[ $key ] = $query;
			}
		}

		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( $outstock_term ),
			'operator' => 'outofstock' == $_GET['stock'] ? 'IN' : 'NOT IN',
		);
	}

	return $tax_query;
}

add_filter( 'woocommerce_product_query_tax_query', 'konte_addons_product_filter_tax_query' );

/**
 * Add filter to posts clauses to support searching products by sku.
 *
 * @param object $query
 */
function konte_addons_product_search_by_sku_hook( $query ) {
	if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! $query->is_search() || ! in_array( 'product', (array) $query->get( 'post_type' ) ) ) {
		return;
	}

	add_filter( 'posts_clauses', 'konte_addons_product_search_by_sku_query_clauses' );
}

add_action( 'pre_get_posts', 'konte_addons_product_search_by_sku_hook' );

/**
 * Modify the product search query clauses to support searching by sku.
 *
 * @todo Support searching in product_variation
 * @param array $clauses
 * @return array
 */
function konte_addons_product_search_by_sku_query_clauses( $clauses ) {
	global $wpdb;

	// Double check because we can't remove filter.
	if (
		! get_query_var( 's' )
		|| ! in_array( 'product', (array) get_query_var( 'post_type' ) )
	) {
		return $clauses;
	}

	$join    = $clauses['join'];
	$where   = $clauses['where'];
	$groupby = $clauses['groupby'];

	// Use the wc_product_meta_lookup, for a better performance.
	if ( $wpdb->wc_product_meta_lookup ) {
		$join .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";

		$where = preg_replace(
			"/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
			"({$wpdb->posts}.post_title LIKE $1) OR (wc_product_meta_lookup.sku LIKE $1)", $where );
	} else { // Old WC. Use postmeta table.
		$join .= " LEFT JOIN {$wpdb->postmeta} ON (" . $wpdb->posts . ".ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key='_sku')";

		$where = preg_replace(
			"/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
			"({$wpdb->posts}.post_title LIKE $1) OR ({$wpdb->postmeta}.meta_value LIKE $1)", $where );
	}

	// GROUP BY: product id; to avoid duplication.
	$id_group = "{$wpdb->posts}.ID";

	if ( ! strlen( trim( $groupby ) ) ) {
		$groupby = $id_group;
	} elseif ( ! preg_match( "/$id_group/", $groupby ) ) {
		$groupby = $groupby . ', ' . $id_group;
	}

	$clauses['join']    = $join;
	$clauses['where']   = $where;
	$clauses['groupby'] = $groupby;

	return $clauses;
}

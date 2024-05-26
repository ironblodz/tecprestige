<?php
/**
 * Custom template tags of layout for this theme
 *
 * @package Konte
 */


if ( ! function_exists( 'konte_get_layout' ) ) :
	/**
	 * Get layout base on current page
	 *
	 * @return string
	 */
	function konte_get_layout() {
		$layout = konte_get_option( 'layout_default' );

		if (
			is_404()
			|| is_singular( array( 'product', 'portfolio', 'elementor_library' ) )
			|| konte_is_order_tracking_page()
			|| is_post_type_archive( 'portfolio' )
		) {
			$layout = 'no-sidebar';
		} elseif ( post_type_exists( 'portfolio' ) && is_tax( get_object_taxonomies( 'portfolio' ) ) ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
			$layout = 'no-sidebar';
		} elseif ( is_singular() && get_post_meta( get_the_ID(), 'custom_layout', true ) ) {
			$layout = get_post_meta( get_the_ID(), 'layout', true );
		} elseif ( is_singular( 'post' ) ) {
			$layout = konte_get_option( 'layout_post' );
		} elseif ( function_exists( 'WC' ) && ( is_shop() || is_product_taxonomy() ) ) {
			if ( 'carousel' == konte_get_option( 'shop_layout' ) ) {
				$layout = 'no-sidebar';
			} else {
				$layout = konte_get_option( 'layout_shop' );
			}
		} elseif ( is_page() ) {
			$layout = 'no-sidebar';
		}

		return apply_filters( 'konte_get_layout', $layout );
	}

endif;

if ( ! function_exists( 'konte_get_sidebar_id' ) ) :
	/**
	 * Get the sidebar id.
	 *
	 * @return string
	 */
	function konte_get_sidebar_id() {
		$sidebar = 'blog-sidebar';

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$sidebar = 'shop-sidebar';
		}

		return apply_filters( 'konte_get_sidebar_id', $sidebar );
	}

endif;

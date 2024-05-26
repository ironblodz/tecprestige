<?php
/**
 * Hooks of my account.
 *
 * @package Konte
 */

/**
 * Class of account template.
 */
class Konte_WooCommerce_Template_Account {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Add wishlist to the account menu.
		if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'menu_items' ) );
			add_filter( 'woocommerce_get_endpoint_url', array( __CLASS__, 'wishlist_url' ), 10, 2 );
		}
	}

	/**
	 * Add the wishlist menu to account menu.
	 *
	 * @param array $items List of account menu items.
	 *
	 * @return array List of account menu items.
	 */
	public static function menu_items( $items ) {
		// Ensure the class exists.
		if ( ! class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			return $items;
		}

		$wishlist_url = Konte_WooCommerce_Template_Wishlist::get_wishlist_url();

		if ( $wishlist_url ) {
			$top_items             = array_slice( $items, 0, -1 );
			$top_items['wishlist'] = esc_html__( 'Wishlist', 'konte' );

			$items = array_merge( $top_items, $items );
		}

		return $items;
	}

	/**
	 * Change the url of wishlist in account menu
	 *
	 * @param string $url      The URL.
	 * @param string $endpoint The endpoint.
	 *
	 * @return string
	 */
	public static function wishlist_url( $url, $endpoint ) {
		if ( 'wishlist' != $endpoint || ! class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			return $url;
		}

		return Konte_WooCommerce_Template_Wishlist::get_wishlist_url();
	}
}

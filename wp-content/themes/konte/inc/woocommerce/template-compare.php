<?php
/**
 * Hooks for compare templates.
 *
 * @package Konte
 */

/**
 * Class of the compare template
 */
class Konte_WooCommerce_Template_Compare {

	/**
	 * Initialize.
	 */
	public static function init() {
		$icon = get_option( 'wcboost_products_compare_button_icon' );

		if ( ! $icon ) {
			add_action( 'template_redirect', array( __CLASS__, 'remove_default_button' ) );
		} elseif ( 'simple' != konte_get_option( 'shop_product_hover' ) ) {
			add_action( 'konte_woocommerce_shop_loop_buttons', array( __CLASS__, 'loop_button' ), 40 );
			add_action( 'template_redirect', array( __CLASS__, 'remove_default_button' ) );
		}

		add_filter( 'wcboost_products_compare_svg_icon', array( __CLASS__, 'svg_icon' ), 10, 2 );

		// Add the compare button to single product page.
		switch ( konte_get_option( 'product_layout' ) ) {
			case 'v1':
				if ( $icon ) {
					add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_button' ) );
				}
				break;

			case 'v2':
			case 'v6':
			case 'v7':
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'single_button' ), 36 );
				break;

			case 'v3':
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_button' ), 20 );
				break;

			case 'v4':
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_button' ) );

				// Compare button for bundled product.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'remove_default_button' ) );
					add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'single_button' ), 20 );
				}
				break;

			case 'v5':
				if ( $icon ) {
					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'single_button' ), 34 );
				}
				break;
		}
	}

	/**
	 * Remove the default button of plugin.
	 */
	public static function remove_default_button() {
		remove_action( 'woocommerce_after_shop_loop_item', [ \WCBoost\ProductsCompare\Frontend::instance(), 'loop_add_to_compare_button' ], 15 );
		remove_action( 'woocommerce_after_add_to_cart_form', [ \WCBoost\ProductsCompare\Frontend::instance(), 'single_add_to_compare_button' ] );
	}

	/**
	 * Display the add to compare button on catalog pages.
	 */
	public static function loop_button() {
		\WCBoost\ProductsCompare\Frontend::instance()->loop_add_to_compare_button();
	}

	/**
	 * Display the add to compare button on catalog pages.
	 */
	public static function single_button() {
		echo '<div class="product-compare-button-wrapper">';
		\WCBoost\ProductsCompare\Frontend::instance()->single_add_to_compare_button();
		echo '</div>';
	}

	/**
	 * Change the spinner icon of the WCBoost Compoare plugin
	 *
	 * @param  string $svg
	 * @param  string $icon
	 * @return string
	 */
	public static function svg_icon( $svg, $icon ) {
		switch ( $icon ) {
			case 'spinner':
				$svg = '<span class="spinner"></span>';
				break;

			case 'close':
				$svg = konte_svg_icon( 'icon=close&echo=0' );
				break;
		}

		return $svg;
	}
}

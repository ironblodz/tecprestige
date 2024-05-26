<?php
/**
 * Hooks for wishlist templates.
 *
 * @package Konte
 */

/**
 * Class of the wishlist template
 */
class Konte_WooCommerce_Template_Wishlist {

	/**
	 * Initialize.
	 */
	public static function init() {
		add_filter( 'wcboost_wishlist_add_to_wishlist_fragments', array( __CLASS__, 'ajax_fragments' ) );
		add_filter( 'add_to_wishlist_fragments', array( __CLASS__, 'ajax_fragments' ) ); // Soo Wishlist.

		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'konte_get_layout', array( __CLASS__, 'wishlist_page_layout' ) );

		add_filter( 'soo_wishlist_item_remove_link', array( __CLASS__, 'item_remove_link' ) );
		add_filter( 'wcboost_wishlist_item_remove_link', array( __CLASS__, 'item_remove_link' ) );

		// Remove the plugin button then add it later.
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'remove_plugin_button' ) );

		if ( class_exists( 'WC_Bundles' ) ) {
			// Remove wishlist button for bundled product.
			add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'remove_plugin_button' ) );
			add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'single_button' ), 20 );
		}

		// Add the wishlist button to shop loop.
		if ( 'simple' != konte_get_option( 'shop_product_hover' ) ) {
			add_action( 'konte_woocommerce_shop_loop_buttons', array( __CLASS__, 'loop_button' ), 30 );
		}

		// Add the wishlist button to single product page.
		switch ( konte_get_option( 'product_layout' ) ) {
			case 'v1':
			case 'v3':
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_button' ) );
				break;

			case 'v2':
			case 'v5':
			case 'v6':
			case 'v7':
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'single_button' ), 34 );
				break;

			case 'v4':
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'single_button' ) );

				// Change button text.
				add_filter( 'wcboost_wishlist_button_add_text', array( __CLASS__, 'button_text_fixed' ) );
				add_filter( 'wcboost_wishlist_button_view_text', array( __CLASS__, 'button_text_fixed' ) );
				add_filter( 'wcboost_wishlist_button_remove_text', array( __CLASS__, 'button_text_fixed' ) );

				// Wishlist button for bundled product.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'remove_plugin_button' ) );
					add_action( 'woocommerce_bundles_add_to_cart_button', array( __CLASS__, 'single_button' ), 20 );
				}
				break;
		}

		// Edit add to wishlist button of Soo Wishlist plugin.
		add_filter( 'soo_wishlist_button', array( __CLASS__, 'soo_wishlist_button' ), 10, 2 );

		add_filter( 'wcboost_wishlist_button_icon', array( __CLASS__, 'button_icon' ), 10, 2 );
		add_filter( 'wcboost_wishlist_svg_icon', array( __CLASS__, 'svg_icon' ), 10, 2 );
	}

	/**
	 * Add ajax fragments
	 *
	 * @param  array $fragments
	 * @return array
	 */
	public static function ajax_fragments( $fragments ) {
		$fragments['span.counter.wishlist-counter'] = '<span class="counter wishlist-counter">' . self::count_wishlist_items() . '</span>';

		return $fragments;
	}

	/**
	 * Check if the WCBoost Wishlist plugin is installed.
	 *
	 * @return bool
	 */
	public static function is_wcboost_wishlist_enabled() {
		return class_exists( '\WCBoost\Wishlist\Plugin' );
	}

	/**
	 * Check if the Soo Wishlist plugin is installed.
	 *
	 * @return bool
	 */
	public static function is_soo_wishlist_enabled() {
		return class_exists( 'Soo_Wishlist_Plugin' );
	}

	/**
	 * Check if the YITH Wishlist plugin is installed.
	 *
	 * @return bool
	 */
	public static function is_yith_wishlist_enabled() {
		return defined( 'YITH_WCWL' );
	}

	/**
	 * Check if this is the wishlist page.
	 *
	 * @return bool
	 */
	public static function is_wishlist_page() {
		if ( self::is_wcboost_wishlist_enabled() && \WCBoost\Wishlist\Helper::is_wishlist() ) {
			return true;
		}

		if ( self::is_soo_wishlist_enabled() && soow_is_wishlist() ) {
			return true;
		}

		if ( self::is_yith_wishlist_enabled() && yith_wcwl_is_wishlist_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Add wishlist classes to body.
	 *
	 * @param  array $classes
	 * @return array
	 */
	public static function body_class( $classes ) {
		if ( self::is_wishlist_page() ) {
			$classes[] = 'woocommerce-wishlist';
		}

		return $classes;
	}

	/**
	 * Fixed the page layout to no-sidebar if this is the wishlist page.
	 *
	 * @param  string $layout
	 * @return string
	 */
	public static function wishlist_page_layout( $layout ) {
		if ( self::is_wishlist_page() ) {
			return 'no-sidebar';
		}

		return $layout;
	}

	/**
	 * Change the item remove item of a wishlist item.
	 *
	 * @param  string $link
	 * @return string
	 */
	public static function item_remove_link( $link ) {
		return str_replace( '&times;', konte_svg_icon( 'icon=close&class=close-icon&echo=0' ), $link );
	}

	/**
	 * Display the add to wishlist button on catalog pages.
	 */
	public static function loop_button() {
		if ( self::is_wcboost_wishlist_enabled() ) {
			\WCBoost\Wishlist\Frontend::instance()->loop_add_to_wishlist_button();
		} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
			echo do_shortcode( '[add_to_wishlist]' );
		}
	}

	/**
	 * Display the add to wishlist button on catalog pages.
	 */
	public static function single_button() {
		if ( self::is_wcboost_wishlist_enabled() ) {
			\WCBoost\Wishlist\Frontend::instance()->single_add_to_wishlist_button();
		} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
			echo do_shortcode( '[add_to_wishlist]' );
		}
	}

	/**
	 * Remove the add-to-wishlist button of the plugin.
	 * This button will be added later.
	 */
	public static function remove_plugin_button() {
		remove_action( 'woocommerce_after_add_to_cart_button', array(
			'Soo_Wishlist_Frontend',
			'single_product_button',
		) );
	}

	/**
	 * Edit add to wihslist button of Soo Wishlist plugin.
	 *
	 * @param string $button The wishlist button HTML.
	 * @param array  $args   Wishlish button argurments.
	 *
	 * @return string
	 */
	public static function soo_wishlist_button( $button, $args ) {
		$product_layout = konte_get_option( 'product_layout' );

		return sprintf(
			'<a href="%s" data-product_id="%s" data-product_type="%s" class="%s" rel="nofollow">
				<span class="add-to-wishlist add">
					%s
					<span class="screen-reader-text button-text">%s</span>
				</span>
				<span class="adding-to-wishlist adding">
					<span class="spinner"></span>
					<span class="screen-reader-text button-text">%s</span>
				</span>
				<span class="brow-wishlist added">
					%s
					<span class="screen-reader-text button-text">%s</span>
				</span>
			</a>',
			esc_url( $args['url'] ),
			esc_attr( $args['product_id'] ),
			esc_attr( $args['product_type'] ),
			esc_attr( implode( ' ', $args['classes'] ) ),
			konte_svg_icon( 'icon=heart-o&echo=0' ),
			'v4' == $product_layout ? esc_html__( 'Wishlist', 'konte' ) : esc_html__( 'Add to wishlist', 'konte' ),
			'v4' == $product_layout ? esc_html__( 'Wishlist', 'konte' ) : esc_html__( 'Adding to wishlist', 'konte' ),
			konte_svg_icon( 'icon=heart&echo=0' ),
			'v4' == $product_layout ? esc_html__( 'Wishlist', 'konte' ) : esc_html__( 'Added to wishlist', 'konte' )
		);
	}

	/**
	 * Filter function to change the wishlist icon of the button.
	 *
	 * @param  string $svg
	 * @param  string $icon_name
	 * @return string
	 */
	public static function button_icon( $svg, $icon_name ) {
		if ( 'heart' == $icon_name ) {
			$svg = konte_svg_icon( 'icon=heart-o&echo=0' );
		} elseif ( 'heart-filled' == $icon_name ) {
			$svg = konte_svg_icon( 'icon=heart&echo=0' );
		}

		return $svg;
	}

	/**
	 * Get the wishlist page URL, based on what plugin is installed.
	 *
	 * @return string
	 */
	public static function get_wishlist_url() {
		if ( self::is_wcboost_wishlist_enabled() ) {
			return wc_get_page_permalink( 'wishlist' );
		} elseif ( self::is_soo_wishlist_enabled() ) {
			return soow_get_wishlist_url();
		} elseif ( self::is_yith_wishlist_enabled() ) {
			return YITH_WCWL()->get_wishlist_url();
		}

		return null;
	}

	/**
	 * Count items in the wishlist
	 *
	 * @return int
	 */
	public static function count_wishlist_items() {
		if ( self::is_wcboost_wishlist_enabled() ) {
			return \WCBoost\Wishlist\Helper::get_wishlist()->count_items();
		} elseif ( self::is_soo_wishlist_enabled() ) {
			return soow_count_products();
		} elseif ( self::is_yith_wishlist_enabled() ) {
			return yith_wcwl_count_products();
		}

		return 0;
	}

	/**
	 * Fixed button text for product layout v4.
	 *
	 * @param  string $text
	 * @return string
	 */
	public static function button_text_fixed( $text ) {
		if ( is_product() ) {
			return esc_html__( 'Wishlist', 'konte' );
		}

		return $text;
	}

	/**
	 * Change the spinner icon of the WCBoost Wishlist plugin
	 *
	 * @param  string $svg
	 * @param  string $icon
	 * @return string
	 */
	public static function svg_icon( $svg, $icon ) {
		if ( 'spinner' == $icon ) {
			$svg = '<span class="spinner"></span>';
		}

		return $svg;
	}

	/**
	 * Get the wishlist icon
	 *
	 * @param  bool $filled
	 *
	 * @return string
	 */
	public static function get_icon( $filled = false ) {
		$icon = '';

		if ( self::is_wcboost_wishlist_enabled() ) {
			$name = get_option( 'wcboost_wishlist_button_icon', 'heart' );
			$icon = \WCBoost\Wishlist\Helper::get_wishlist_icon( $filled );

			if ( $icon && 'heart' !== $name ) {
				$icon = '<span class="svg-icon wishlist-icon--wcboost">' . $icon . '</span>';
			}
		}

		if ( ! $icon ) {
			$icon = konte_svg_icon( 'icon=heart-o&echo=0' );
		}

		return $icon;
	}
}

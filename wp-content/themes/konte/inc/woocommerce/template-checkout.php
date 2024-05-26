<?php
/**
 * Hooks of checkout.
 *
 * @package Konte
 */

/**
 * Class of checkout template.
 */
class Konte_WooCommerce_Template_Checkout {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Wrap checkout login and coupon notices.
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'checkout_login_form' ), 10 );
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'checkout_coupon_form' ), 15 );

		// Edit name fields.
		add_filter( 'woocommerce_form_field_args', array( __CLASS__, 'form_field_args' ), 10, 2 );
	}

	/**
	 * Checkout login form.
	 */
	public static function checkout_login_form() {
		if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
			return;
		}

		echo '<div class="checkout-login">';
		woocommerce_checkout_login_form();
		echo '</div>';
	}

	/**
	 * Checkout coupon form.
	 */
	public static function checkout_coupon_form() {
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		echo '<div class="checkout-coupon">';
		woocommerce_checkout_coupon_form();
		echo '</div>';
	}

	/**
	 * Edit form fields.
	 *
	 * @param array  $args Form field argurments.
	 * @param string $key  Field name
	 *
	 * @return array
	 */
	public static function form_field_args( $args, $key ) {
		switch ( $key ) {
			case 'billing_first_name':
			case 'shipping_first_name':
			case 'account_first_name':
				$args['placeholder'] = $args['label'];
				$args['label']       = esc_html__( 'Name', 'konte' );
				break;

			case 'billing_last_name':
			case 'shipping_last_name':
			case 'account_last_name':
				$args['placeholder'] = $args['label'];
				break;

			case 'account_password':
				$args['label'] = esc_html__( 'Account password', 'konte' );
				break;
		}

		return $args;
	}
}
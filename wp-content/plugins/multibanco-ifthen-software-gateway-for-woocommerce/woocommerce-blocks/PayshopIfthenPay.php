<?php
namespace Automattic\WooCommerce\Blocks\Payments\Integrations;

use Automattic\WooCommerce\Blocks\Assets\Api;

/**
 * Payshop payment method integration
 */
final class PayshopIfthenPay extends AbstractPaymentMethodType {
	/**
	 * Payment method name defined by payment methods extending this class.
	 *
	 * @var string
	 */
	protected $name = 'payshop_ifthen_for_woocommerce';

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = WC_IfthenPay_Webdados()->payshop_settings;
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		wp_register_script(
			'wc-payment-method-payshop-ifthenpay',
			plugins_url( 'build/payshop-block.js', __FILE__ ),
			array(),
			WC_IfthenPay_Webdados()->get_version().( WP_DEBUG ? '.' . wp_rand( 0, 9999 ) : '' ),
			true
		);
		return [ 'wc-payment-method-payshop-ifthenpay' ];
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return [
			'title'                             => isset( $this->settings['title'] ) ? $this->settings['title'] : '',
			'description'                       => isset( $this->settings['description'] ) ? $this->settings['description'] : '',
			'icon'                              => WC_IfthenPay_Webdados()->payshop_icon,
			'only_portugal'                     => $this->settings['only_portugal'] == 'yes',
			'only_above'                        => floatval( $this->settings['only_above'] ) > 0 ? floatval( $this->settings['only_above'] ) : null,
			'only_bellow'                       => floatval( $this->settings['only_bellow'] ) > 0 ? floatval( $this->settings['only_bellow'] ) : null,
			//'support_woocommerce_subscriptions' => isset( $this->settings['support_woocommerce_subscriptions'] ) && ( 'yes' === $this->settings['support_woocommerce_subscriptions'] ), //Not on payshop
			//More settings needed?
		];
	}
}

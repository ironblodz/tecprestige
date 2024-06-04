<?php
/**
 * Plugin Name: Multibanco, MB WAY, Credit card, Payshop and Cofidis Pay (IfthenPay) for WooCommerce
 * Plugin URI: https://www.webdados.pt/wordpress/plugins/multibanco-ifthen-software-gateway-woocommerce-wordpress/
 * Description: This plugin allows customers with a Portuguese bank account to pay WooCommerce orders using Multibanco (Pag. Serviços), MB WAY, Credit card and Payshop through IfthenPay’s payment gateway.
 * Version: 9.3.0
 * Author: PT Woo Plugins (by Webdados)
 * Author URI: https://ptwooplugins.com
 * Text Domain: multibanco-ifthen-software-gateway-for-woocommerce
 * Domain Path: /lang
 * Requires at least: 5.6
 * Tested up to: 6.6
 * Requires PHP: 7.0
 * WC requires at least: 6.0
 * WC tested up to: 8.9
 * Requires Plugins: woocommerce
 **/

/* WooCommerce CRUD ready */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'WC_IFTHENPAY_WEBDADOS_REQUIRED_WC_VERSION', '6.0' );
define( 'WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE', __FILE__ );

/* Our own order class and the main classes */
function mbifthen_init() {
	if ( class_exists( 'WooCommerce' ) && defined( 'WC_VERSION' ) && version_compare( WC_VERSION, WC_IFTHENPAY_WEBDADOS_REQUIRED_WC_VERSION, '>=' ) ) {
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-ifthen-webdados.php';
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-multibanco-ifthen-webdados.php';
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-mbway-ifthen-webdados.php';
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-creditcard-ifthen-webdados.php';
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-payshop-ifthen-webdados.php';
		require_once dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/class-wc-cofidispay-ifthen-webdados.php';
		$GLOBALS['WC_IfthenPay_Webdados'] = WC_IfthenPay_Webdados();
		/* Add settings links - This is here because inside the main class we cannot call the correct plugin_basename( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) */
		add_filter( 'plugin_action_links_' . plugin_basename( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ), array( WC_IfthenPay_Webdados(), 'add_settings_link' ) );
	} else {
		add_action( 'admin_notices', 'mbifthen_woocommerce_not_active_admin_notices' );
	}
}
add_action( 'plugins_loaded', 'mbifthen_init', 1 );

/* Main class */
function WC_IfthenPay_Webdados() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( ! defined( 'WC_IFTHENPAY_WEBDADOS_VERSION' ) ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Should not be necessary, but we never know...
		}
		$data = get_plugin_data( dirname( WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE ) . '/multibanco_ifthen_for_woocommerce.php' );
		define( 'WC_IFTHENPAY_WEBDADOS_VERSION', $data['Version'] );
	}
	return WC_IfthenPay_Webdados::instance( WC_IFTHENPAY_WEBDADOS_VERSION );
}

/* Format MB reference - We keep it because someone may be using it externally */
function mbifthen_format_ref( $ref ) {
	return class_exists( 'WC_IfthenPay_Webdados' ) ? WC_IfthenPay_Webdados()->format_multibanco_ref( $ref ) : $ref;
}

/* Admin notice if not active */
function mbifthen_woocommerce_not_active_admin_notices() {
	?>
	<div class="notice notice-error is-dismissible">
		<p>
			<?php
			printf(
				/* translators: %s: required WooCommerce version */
				__( '<strong>Multibanco, MB WAY, Credit card, Payshop and Cofidis Pay (IfthenPay) for WooCommerce</strong> is installed and active but <strong>WooCommerce (%s or above)</strong> is not.', 'multibanco-ifthen-software-gateway-for-woocommerce' ),
				esc_html( WC_IFTHENPAY_WEBDADOS_REQUIRED_WC_VERSION )
			);
			?>
		</p>
	</div>
	<?php
}

/* HPOS & Blocks Compatible */
add_action(
	'before_woocommerce_init',
	function() {
		if ( version_compare( WC_VERSION, '7.1', '>=' ) && class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WC_IFTHENPAY_WEBDADOS_PLUGIN_FILE, true );
		}
	}
);

/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */

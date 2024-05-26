<?php
/**
 * Register the required plugins.
 *
 * @see        http://tgmpluginactivation.com/configuration/
 *
 * @package    Konte
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/lib/tgmpa/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'konte_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function konte_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => esc_html__( 'WooCommerce', 'konte' ),
			'slug'     => 'woocommerce',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Meta Box', 'konte' ),
			'slug'     => 'meta-box',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Kirki', 'konte' ),
			'slug'     => 'kirki',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Konte Addons', 'konte' ),
			'slug'     => 'konte-addons',
			'source'   => 'https://uix.store/plugins/konte-addons.zip',
			'version'  => '2.2.4',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Slider Revolution', 'konte' ),
			'slug'     => 'revslider',
			'source'   => 'https://uix.store/plugins/revslider.zip',
			'required' => false,
			'version'  => '6.7.11',
		),
		array(
			'name'     => esc_html__( 'WooCommerce Currency Switcher', 'konte' ),
			'slug'     => 'woocommerce-currency-switcher',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'Contact Form 7', 'konte' ),
			'slug'     => 'contact-form-7',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'MailChimp for WordPress', 'konte' ),
			'slug'     => 'mailchimp-for-wp',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Variation Swatches', 'konte' ),
			'slug'     => 'wcboost-variation-swatches',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Wishlist', 'konte' ),
			'slug'     => 'wcboost-wishlist',
			'required' => false,
		),
		array(
			'name'     => esc_html__( 'WCBoost - Products Compare', 'konte' ),
			'slug'     => 'wcboost-products-compare',
			'required' => false,
		),
	);

	if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) ) {
		if ( false === get_option( 'wpb_js_composer_license_activation_notified' ) ) {
			$plugins[] = array(
				'name'     => esc_html__( 'Elementor', 'konte' ),
				'slug'     => 'elementor',
				'required' => true,
			);
		} else {
			$plugins[] = array(
				'name'     => esc_html__( 'WPBakery Page Builder', 'konte' ),
				'slug'     => 'js_composer',
				'source'   => 'https://uix.store/plugins/js_composer.zip',
				'version'  => '7.6',
				'required' => true,
			);
		}
	} else {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$plugins[] = array(
				'name'     => esc_html__( 'Elementor', 'konte' ),
				'slug'     => 'elementor',
				'required' => true,
			);
		}

		if ( defined( 'WPB_VC_VERSION' ) ) {
			$plugins[] = array(
				'name'     => esc_html__( 'WPBakery Page Builder', 'konte' ),
				'slug'     => 'js_composer',
				'source'   => 'https://uix.store/plugins/js_composer.zip',
				'version'  => '7.6',
				'required' => true,
			);
		}
	}

	$config = array(
		'id'           => 'konte',
		'default_path' => '',
		'menu'         => 'install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}

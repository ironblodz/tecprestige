<?php
/**
 * Plugin Name: Konte Addons
 * Plugin URI: https://uix.store/intro/konte/
 * Description: A collection of extra elements for WPBakery Page Builder. It was made for Konte premium theme and requires Konte theme installed in order to work properly.
 * Author: UIX Themes
 * Author URI: https://uix.store
 * Version: 2.2.8
 * Text Domain: konte-addons
 * Domain Path: /languages
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Konte Addons.
 * Main class.
 */
final class Konte_Addons {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init();
	}

	/**
	 * Defines constants
	 */
	public function define_constants() {
		define( 'KONTE_ADDONS_VER', '2.2.8' );
		define( 'KONTE_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'KONTE_ADDONS_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Load files
	 */
	public function includes() {
		include_once( KONTE_ADDONS_DIR . 'includes/functions.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/user.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/flex-post.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/portfolio.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/woocommerce.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/import.php' );

		// Shortcodes
		include_once( KONTE_ADDONS_DIR . 'includes/shortcodes.php' );

		// WPB
		include_once( KONTE_ADDONS_DIR . 'includes/js-composer.php' );

		// Load widgets.
		include_once( KONTE_ADDONS_DIR . 'includes/widgets/instagram.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/widgets/popular-posts.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/widgets/socials.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/widgets/posts-slider.php' );
		include_once( KONTE_ADDONS_DIR . 'includes/widgets/products-filter.php' );

		// Mega menu.
		include_once( KONTE_ADDONS_DIR . 'includes/mega-menu/menus.php' );
	}

	/**
	 * Initialize
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'check_dependencies' ) );

		// load_plugin_textdomain( 'konte-addons', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'vc_before_init', 'vc_set_as_theme' );
		add_action( 'vc_after_init', array( 'Konte_Addons_JS_Composer', 'init' ) );
		add_action( 'vc_after_init', array( 'Konte_Addons_JS_Composer', 'customize_elements' ) );
		add_action( 'vc_after_init', array( 'Konte_Addons_JS_Composer', 'map_shortcodes' ) );
		add_action( 'vc_load_default_templates_action', array( 'Konte_Addons_JS_Composer', 'add_templates' ) );

		add_action( 'plugins_loaded', array( $this, 'init_elementor' ) );

		add_action( 'init', array( 'Konte_Addons_Shortcodes', 'init' ), 50 );

		add_action( 'init', array( 'Konte_Addons_Flex_Post', 'init' ) );
		add_action( 'init', array( 'Konte_Addons_Portfolio', 'init' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Undocumented function
	 */
	public function init_elementor() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, '2.0.0', '>=' ) ) {
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		include_once( KONTE_ADDONS_DIR . 'includes/elementor/elementor.php' );
	}

	/**
	 * Check plugin dependencies.
	 * Check if page builder plugin is installed.
	 */
	public function check_dependencies() {
		if ( ! defined( 'WPB_VC_VERSION' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
			$plugin_data = get_plugin_data( __FILE__ );

			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				sprintf(
					__( '<strong>%s</strong> requires <strong><a href="http://bit.ly/wpbakery-page-builder" target="_blank">WPBakery Page Builder</a></strong> or <strong><a href="https://wordpress.org/plugins/elementor/" target="_blank">Elementor Page Builder</a></strong> plugin to be installed and activated on your site.', 'konte-addons' ),
					$plugin_data['Name']
				)
			);
		}
	}

	/**
	 * Register widgets
	 */
	public function register_widgets() {
		register_widget( 'Konte_Addons_Instagram_Widget' );
		register_widget( 'Konte_Addons_Popular_Posts_Widget' );
		register_widget( 'Konte_Addons_Social_Links_Widget' );
		register_widget( 'Konte_Addons_Posts_Slider_Widget' );

		if ( class_exists( 'WooCommerce' ) ) {
			register_widget( 'Konte_Addons_Products_Filter_Widget' );
		}
	}
}

new Konte_Addons();

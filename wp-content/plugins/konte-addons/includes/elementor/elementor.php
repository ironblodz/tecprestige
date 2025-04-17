<?php
namespace KonteAddons;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor {
	/**
	 * Instance
	 *
	 * @access private
	 */
	private static $_instance = null;

	/**
	 * Elementor modules
	 *
	 * @var array
	 */
	public $modules = [];

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Konte_Addons_Elementor An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();
		$this->setup_hooks();
	}

	/**
	 * Auto load widgets
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$path = explode( '\\', $class );
		$filename = strtolower( array_pop( $path ) );
		$filename = str_replace( '_', '-', $filename );

		$module = array_pop( $path );

		if ( 'Widgets' == $module ) {
			$filename = KONTE_ADDONS_DIR . 'includes/elementor/widgets/' . $filename . '.php';
		} elseif ( 'Base' == $module ) {
			$filename = KONTE_ADDONS_DIR . 'includes/elementor/base/' . $filename . '.php';
		} elseif ( 'Modules' == $module ) {
			$filename = KONTE_ADDONS_DIR . 'includes/elementor/modules/' . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	/**
	 * Includes files which are not widgets
	 */
	private function _includes() {
		require KONTE_ADDONS_DIR . 'includes/elementor/utils.php';

		if ( class_exists( 'Woocommerce' ) ) {
			require KONTE_ADDONS_DIR . 'includes/elementor/products.php';
		}
	}

	/**
	 * Hooks to init
	 */
	protected function setup_hooks() {
		// On Editor - Register WooCommerce frontend hooks before the Editor init.
		// Priority = 5, in order to allow plugins remove/add their wc hooks on init.
		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
			add_action( 'init', [ $this, 'register_wc_hooks' ], 5 );
		}

		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

		add_action( 'elementor/controls/register', [ $this, 'init_controls' ] );
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );

		add_action( 'elementor/init', [ $this, 'init_modules' ] );
	}

	/**
	 * Register WC hooks for Elementor editor
	 */
	public function register_wc_hooks() {
		if ( function_exists( 'wc' ) ) {
			wc()->frontend_includes();
		}
	}

	/**
	 * Register styles
	 */
	public function enqueue_styles() {

	}

	/**
	 * Register styles
	 */
	public function register_scripts() {
		wp_register_script( 'konte-elementor-widgets', KONTE_ADDONS_URL . 'assets/js/elementor-widgets.dist.js', ['jquery', 'elementor-frontend', 'regenerator-runtime'], KONTE_ADDONS_VER, true );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . \KonteAddons\Elementor\Widgets\Google_Map::get_api_key() );
		}

		wp_enqueue_script( 'konte-elementor-widgets' );
	}

	/**
	 * Enqueue editor scripts
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_style( 'konte-elementor-editor',  KONTE_ADDONS_URL . 'assets/css/elementor-editor.css', [], KONTE_ADDONS_VER );

		wp_enqueue_script(
			'konte-elementor-modules',
			KONTE_ADDONS_URL . 'assets/js/elementor-modules.js',
			[
				'backbone-marionette',
				'elementor-common-modules',
				'elementor-editor-modules',
			],
			KONTE_ADDONS_VER,
			true
		);
	}

	/**
	 * Init Controls
	 */
	public function init_controls() {}

	/**
	 * Init Widgets
	 */
	public function init_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Accordion() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Tabs() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Icon_Box() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Price_Table() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Button() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Testimonial() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Cta() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Info_List() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Promotion() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Team_Member() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Separator_Dash() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Posts_Grid() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Message_Box() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Chart() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Countdown() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Testimonial_Carousel() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Testimonial_Slideshow() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Posts_Carousel() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Category_Banner() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Countdown_Banner() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Banner_Image() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Banner_Grid() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Banner_Carousel() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Google_Map() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Heading() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Instagram() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Instagram_Carousel() );
		$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Team_Member_Carousel() );

		if ( class_exists( 'Woocommerce' ) ) {
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Grid() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Masonry() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Carousel() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Carousel_2() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Tabs() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Products_Tabs_Carousel() );
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Product_Banner() );
		}

		if ( post_type_exists( 'mc4wp-form' ) ) {
			$widgets_manager->register( new \KonteAddons\Elementor\Widgets\Subscribe_Box() );
		}
	}

	/**
	 * Add Konte category
	 */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'konte',
			[
				'title' => __( 'Konte', 'konte-addons' )
			]
		);
	}

	/**
	 * Init modules
	 */
	public function init_modules() {
		$this->modules['theme-display-settings'] = \KonteAddons\Elementor\Modules\Display_Settings::instance();

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$this->modules['custom-css'] = \KonteAddons\Elementor\Modules\Custom_CSS::instance();
		}
	}
}

Elementor::instance();

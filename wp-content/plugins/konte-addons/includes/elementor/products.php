<?php
namespace KonteAddons\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Products {
	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_nopriv_konte_get_products_tab', [ $this, 'ajax_get_products_tab' ] );
		add_action( 'wp_ajax_konte_get_products_tab', [ $this, 'ajax_get_products_tab' ] );
		add_action( 'wc_ajax_konte_get_products_tab', [ $this, 'ajax_get_products_tab' ] );
	}

	/**
	 * Ajax load products tab
	 */
	public function ajax_get_products_tab() {
		if ( empty( $_POST['atts'] ) ) {
			wp_send_json_error( esc_html__( 'No query data.', 'konte-addons' ) );
			exit;
		}

		$carousel = isset( $_POST['carousel'] ) && is_array( $_POST['carousel'] ) ? $_POST['carousel'] : false;

		$output = $this->get_content( $_POST['atts'], $carousel );

		wp_send_json_success( $output );
	}

	/**
	 * Get products loop content.
	 *
	 * @param array $atts
	 * @param array|bool $carousel
	 * @return string
	 */
	public function get_content( $atts, $carousel = false ) {
		if ( $carousel && ! empty( $carousel['dots'] ) ) {
			add_action( 'woocommerce_shortcode_after_' . $atts['type'] . '_loop', '\KonteAddons\Elementor\Utils::carousel_pagination' );
		}

		if ( $carousel ) {
			$class = isset( $atts['class'] ) ? $atts['class'] : '';
			$class .= ' swiper-container konte-product-carousel konte-product-carousel--elementor konte-carousel--elementor konte-carousel--swiper';
			$atts['class'] = $class;
		}

		$shortcode = new \WC_Shortcode_Products( $atts, $atts['type'] );
		$output = $shortcode->get_content();

		if ( $carousel && ! empty( $carousel['dots'] ) ) {
			remove_action( 'woocommerce_shortcode_after_' . $atts['type'] . '_loop', '\KonteAddons\Elementor\Utils::carousel_pagination' );
		}

		if ( $carousel && ! empty( $carousel['arrows'] ) ) {
			$arrows = \KonteAddons\Elementor\Utils::carousel_navigation( $carousel['arrows'], false );
			$output .= implode( '', $arrows );
		}

		return $output;
	}
}

Products::instance();

<?php
namespace KonteAddons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

abstract class Products_Widget_Base extends Widget_Base {
	/**
	 * Register controls for products query
	 *
	 * @param array $controls
	 */
	protected function register_products_controls( $controls = [] ) {
		$supported_controls = [
			'limit'    => 10,
			'type'     => 'recent_products',
			'category' => '',
			'tag'      => '',
			'orderby'  => '',
			'order'    => '',
		];

		$controls = 'all' == $controls ? $supported_controls : $controls;

		foreach ( $controls as $option => $default ) {
			switch ( $option ) {
				case 'limit':
					$this->add_control(
						'limit',
						[
							'label'     => __( 'Number of Products', 'konte-addons' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => -1,
							'max'       => 100,
							'step'      => 1,
							'default'   => $default,
						]
					);
					break;

				case 'type':
					$this->add_control(
						'type',
						[
							'label' => __( 'Type', 'konte-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => $this->get_options_product_type(),
							'default' => $default,
						]
					);
					break;

				case 'category':
					$this->add_control(
						'category',
						[
							'label' => __( 'Category', 'konte-addons' ),
							'type' => Controls_Manager::SELECT2,
							'options' => \KonteAddons\Elementor\Utils::get_terms_options( 'product_cat' ),
							'default' => $default,
							'multiple' => true,
						]
					);
					break;

				case 'tag':
					$this->add_control(
						'tag',
						[
							'label' => __( 'Tag', 'konte-addons' ),
							'type' => Controls_Manager::SELECT2,
							'options' => \KonteAddons\Elementor\Utils::get_terms_options( 'product_tag' ),
							'default' => $default,
							'multiple' => true,
						]
					);
					break;

				case 'orderby':
					$this->add_control(
						'orderby',
						[
							'label' => __( 'Order By', 'konte-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => $this->get_options_product_orderby(),
							'default' => $default,
							'condition' => [
								'type' => ['featured', 'sale']
							],
						]
					);
					break;

				case 'order':
					$this->add_control(
						'order',
						[
							'label' => __( 'Order', 'konte-addons' ),
							'type' => Controls_Manager::SELECT,
							'options' => [
								'ASC'  => __( 'Ascending', 'konte-addons' ),
								'DESC' => __( 'Descending', 'konte-addons' ),
							],
							'default' => $default,
							'condition' => [
								'type' => ['featured', 'sale'],
								'orderby!' => ['', 'rand'],
							],
						]
					);
					break;
			}
		}
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 * @return string
	 */
	protected function get_products_loop_content( $settings = false ) {
		$settings  = $this->parse_settings( $settings );
		$shortcode = new \WC_Shortcode_Products( $settings, $settings['type'] );

		return $shortcode->get_content();
	}

	/**
	 * Render products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 */
	protected function render_products( $settings = false ) {
		echo $this->get_products_loop_content( $settings );
	}

	/**
	 * Parase shortcode attributes
	 *
	 * @param array $settings
	 * @return array
	 */
	protected function parse_settings( $settings = false ) {
		$settings = $settings ? $settings : $this->get_settings_for_display();

		// Ensure the product type is correct.
		$types = $this->get_options_product_type();
		$type  = isset( $settings['type'] ) && array_key_exists( $settings['type'], $types ) ? $settings['type'] : 'recent_products';
		$settings['type'] = $type;

		switch ( $type ) {
			case 'recent_products':
				$settings['order']        = 'DESC';
				$settings['orderby']      = 'date';
				$settings['cat_operator'] = 'IN';
				break;

			case 'top_rated_products':
				$settings['orderby']      = 'title';
				$settings['order']        = 'ASC';
				$settings['cat_operator'] = 'IN';
				break;

			case 'sale_products':
			case 'best_selling_products':
				$settings['cat_operator'] = 'IN';
				break;

			case 'featured_products':
				$settings['cat_operator'] = 'IN';
				$settings['visibility']   = 'featured';
				break;

			case 'product':
				$settings['skus']  = isset( $settings['sku'] ) ? $settings['sku'] : '';
				$settings['ids']   = isset( $settings['id'] ) ? $settings['id'] : '';
				$settings['limit'] = '1';
				break;
		}

		// Use the default product order setting.
		if ( empty( $settings['orderby'] ) ) {
			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'DESC';

			if ( in_array( $orderby, array( 'menu_order', 'price' ) ) ) {
				$order = 'ASC';
			}

			$settings['orderby'] = strtolower( $orderby );
			$settings['order'] = strtoupper( $order );
		}

		// Convert category list to string.
		if ( ! empty( $settings['category'] ) && is_array( $settings['category'] ) ) {
			$settings['category'] = implode( ',', $settings['category'] );
		}

		// Convert tag list to string.
		if ( ! empty( $settings['tag'] ) && is_array( $settings['tag'] ) ) {
			$settings['tag'] = implode( ',', $settings['tag'] );
		}

		// Remove Elementor's ID keys.
		if ( isset( $settings['_id'] ) ) {
			unset( $settings['_id'] );
		}

		return $settings;
	}

	/**
	 * Get all available orderby options.
	 *
	 * @return array
	 */
	protected function get_options_product_orderby() {
		return [
			''           => __( 'Default', 'konte-addons' ),
			'menu_order' => __( 'Menu Order', 'konte-addons' ),
			'date'       => __( 'Date', 'konte-addons' ),
			'id'         => __( 'Product ID', 'konte-addons' ),
			'title'      => __( 'Product Title', 'konte-addons' ),
			'rand'       => __( 'Random', 'konte-addons' ),
			'price'      => __( 'Price', 'konte-addons' ),
			'popularity' => __( 'Popularity (Sales)', 'konte-addons' ),
			'rating'     => __( 'Rating', 'konte-addons' ),
		];
	}

	/**
	 * Get all supported product type options.
	 *
	 * @return array
	 */
	protected function get_options_product_type() {
		return [
			'recent_products'       => __( 'Recent Products', 'konte-addons' ),
			'featured_products'     => __( 'Featured Products', 'konte-addons' ),
			'sale_products'         => __( 'Sale Products', 'konte-addons' ),
			'best_selling_products' => __( 'Best Selling Products', 'konte-addons' ),
			'top_rated_products'    => __( 'Top Rated Products', 'konte-addons' ),
		];
	}
}
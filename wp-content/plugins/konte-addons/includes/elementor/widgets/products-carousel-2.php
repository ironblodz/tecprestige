<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use KonteAddons\Elementor\Base\Products_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Products_Carousel_2 extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-products-carousel-2';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Products Carousel 2', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['konte'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'products carousel', 'products', 'carousel', 'woocommerce', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_products_carousel_2',
			[
				'label' => __( 'Products Carousel 2', 'konte-addons' ),
			]
		);

		$this->register_products_controls( 'all' );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'   => esc_html__( 'Slides to show', 'konte-addons' ),
				'type'    => Controls_Manager::HIDDEN,
				'desktop_default' => 'auto',
				'tablet_default' => 3,
				'mobile_default' => 2,
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'   => esc_html__( 'Slides to scroll', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__( 'Navigation', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'arrows' => esc_html__( 'Arrows', 'konte-addons' ),
					'dots'   => esc_html__( 'Dots', 'konte-addons' ),
					'none'   => esc_html__( 'None', 'konte-addons' ),
				],
				'default' => 'arrows',
				'toggle'  => false,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// Additional Settings.
		$this->start_controls_section(
			'section_additional_settings',
			[ 'label' => esc_html__( 'Additional Settings', 'konte-addons' ) ]
		);

		$this->add_control(
			'autoplay',
			[
				'label'     => __( 'Autoplay', 'konte-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'konte-addons' ),
				'label_on'  => __( 'On', 'konte-addons' ),
				'default'   => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'   => __( 'Autoplay Speed', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3000,
				'min'     => 100,
				'step'    => 100,
				'frontend_available' => true,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => __( 'Pause on Hover', 'konte-addons' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'konte-addons' ),
				'label_on'  => __( 'On', 'konte-addons' ),
				'default'   => 'yes',
				'frontend_available' => true,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'       => __( 'Animation Speed', 'konte-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 800,
				'min'         => 100,
				'step'        => 50,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_products_carousel',
			[
				'label' => esc_html__( 'Carousel', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation!' => 'none',
				],
			]
		);

		$this->add_control(
			'arrow_style_heading',
			[
				'label'     => __( 'Arrows', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => [ 'both', 'arrows' ]
				],
			]
		);

		$this->start_controls_tabs( 'arrow_style_tabs' );
		$this->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => __( 'Normal', 'konte-addons' ),
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__arrow, {{WRAPPER}} .konte-carousel__arrow:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'both', 'arrows' ]
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__arrow' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => __( 'Hover', 'konte-addons' ),
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'arrow_color_hover',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__arrow:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'arrow_bg_color_hover',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__arrow:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				]
			]
		);

		$this->end_controls_tab();

		$this->add_control(
			'dots_style_heading',
			[
				'label'     => __( 'Dots', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'both', 'dots' ]
				],
			]
		);

		$this->add_control(
			'dots_align',
			[
				'label'   => __( 'Dots Alignment', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__pagination' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'both', 'dots' ]
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'both', 'dots' ]
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_product',
			[
				'label' => esc_html__( 'Product', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'product_name_style_heading',
			[
				'label' => __( 'Product Name', 'konte-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label'     => __( 'Color', 'konte-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product-carousel2 .product-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography:: get_type(),
			[
				'label'    => __( 'Typography', 'konte-addons' ),
				'name'     => 'product_title_typography',
				'selector' => '{{WRAPPER}} .konte-product-carousel2 .product-title',
			]
		);

		$this->add_control(
			'product_price_style_heading',
			[
				'label'     => __( 'Price', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label'     => __( 'Color', 'konte-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product-carousel2 .product-price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography:: get_type(),
			[
				'label'    => __( 'Typography', 'konte-addons' ),
				'name'     => 'product_price_typography',
				'selector' => '{{WRAPPER}} .konte-product-carousel2 .product-price',
			]
		);

		$this->add_control(
			'product_button_style_heading',
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_button_color',
			[
				'label'     => __( 'Color', 'konte-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product-carousel2 .add-to-cart' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography:: get_type(),
			[
				'label'    => __( 'Typography', 'konte-addons' ),
				'name'     => 'product_button_typography',
				'selector' => '{{WRAPPER}} .konte-product-carousel2 .add-to-cart',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-products',
			'konte-product-carousel2',
			'konte-product-carousel2--elementor',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
			'woocommerce',
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php $this->render_products( $settings ) ?>

			<?php if ( in_array( $settings['navigation'], ['dots', 'both'] ) ) : ?>
				<div class="konte-carousel__pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php if ( in_array( $settings['navigation'], ['arrows', 'both'] ) ) : ?>
			<div class="konte-carousel__arrow konte-carousel-navigation--angle konte-carousel-navigation--prev">
				<span class="svg-icon icon-left"><svg><use xlink:href="#left"></use></svg></span>
			</div>
			<div class="konte-carousel__arrow konte-carousel-navigation--angle konte-carousel-navigation--next">
				<span class="svg-icon icon-right"><svg><use xlink:href="#right"></use></svg></span>
			</div>
		<?php endif;
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @param array $settings Shortcode attributes
	 * @return string
	 */
	protected function render_products( $settings = false ) {
		$settings  = $this->parse_settings( $settings );
		$shortcode = new \WC_Shortcode_Products( $settings, $settings['type'] );

		$args = $shortcode->get_query_args();
		$args['fields'] = 'ids';

		$query       = new \WP_Query( $args );
		$product_ids = $query->posts;

		echo '<ul class="products swiper-wrapper">';

		foreach ( $product_ids as $index => $product_id ) {
			$_product   = wc_get_product( $product_id );
			$button_key = $this->get_repeater_setting_key( 'product_button', 'products_carousel2', $index );

			$this->add_render_attribute( $button_key, [
				'href' => $_product->add_to_cart_url(),
				'data-product_id' => $_product->get_id(),
				'data-product_sku' => $_product->get_sku(),
				'aria-label' => $_product->add_to_cart_description(),
				'data-quantity' => '1',
				'rel' => 'nofollow',
				'class' => [
					'underline-hover',
					'short-line',
					'add-to-cart',
				],
 			] );

			if ( $_product->is_purchasable() && $_product->is_in_stock() ) {
				$this->add_render_attribute( $button_key, 'class', 'add_to_cart_button' );
			}

			if ( $_product->supports( 'ajax_add_to_cart' ) ) {
				$this->add_render_attribute( $button_key, 'class', 'ajax_add_to_cart' );
			}

			// Image Size
			if ( $settings['image_size'] != 'custom' ) {
				$image_size = $settings['image_size'];
			} else {
				$image_size = [
					$settings['image_custom_dimension']['width'],
					$settings['image_custom_dimension']['height'],
				];
			}

			$image_id = $_product->get_image_id();
			$image_src = '';

			if ( $image_id ) {
				$image_src = wp_get_attachment_image_src( $image_id, 'woocommerce_thumbnail' );
				$image_src = $image_src ? $image_src[0] : wc_placeholder_img_src( $image_size );
			}

			$image = Group_Control_Image_Size::get_attachment_image_html( [
				'image' => [
					'id'  => $image_id,
					'url' => $image_src,
				],
				'image_size' => $settings['image_size'],
				'image_custom_dimension' => $settings['image_custom_dimension'],
			] );

			if ( ! $image ) {
				$image = sprintf( '<img src="%s" alt="%s"/>', wc_placeholder_img_src( $image_size ), $_product->get_title() );
			}
			?>
			<li class="swiper-slide product <?php echo esc_attr( 'product-type-' . $_product->get_type() ); ?>">
				<a href="<?php echo esc_url( $_product->get_permalink() ) ?>" class="product-link">
					<?php echo $image ?>
					<span class="product-summary">
						<h3 class="product-title"><?php echo $_product->get_title() ?></h3>
						<span class="product-price"><?php echo $_product->get_price_html() ?></span>
					</span>
				</a>
				<a <?php echo $this->get_render_attribute_string( $button_key ) ?>><?php esc_html_e( 'Buy Now', 'konte-addons' ) ?></a>
			</li>
			<?php
		}

		echo '</ul>';
	}
}

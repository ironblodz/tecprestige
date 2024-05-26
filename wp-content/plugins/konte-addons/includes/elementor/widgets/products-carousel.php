<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Controls_Manager;
use KonteAddons\Elementor\Base\Products_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Products_Carousel extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-products-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Products Carousel', 'konte-addons' );
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
	 *
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
			'section_products_carousel',
			[
				'label' => __( 'Products Carousel', 'konte-addons' ),
			]
		);

		$this->register_products_controls( 'all' );

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'              => esc_html__( 'Slides to show', 'konte-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 10,
				'default'            => 4,
				'frontend_available' => true,
				'separator'          => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'              => esc_html__( 'Slides to scroll', 'konte-addons' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 10,
				'default'            => 1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__( 'Navigation', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'both'   => esc_html__( 'Arrows and Dots', 'konte-addons' ),
					'arrows' => esc_html__( 'Arrows', 'konte-addons' ),
					'dots'   => esc_html__( 'Dots', 'konte-addons' ),
					'none'   => esc_html__( 'None', 'konte-addons' ),
				],
				'default'            => 'arrows',
				'toggle'             => false,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// Additional Settings.
		$this->start_controls_section(
			'section_carousel_settings',
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
				'label' => esc_html__( 'Products Carousel', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'products_style_heading',
			[
				'label'     => __( 'Products', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'product_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} li.product, {{WRAPPER}} li.product a:not(.add-to-wishlist-button)' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.products:not(.hover-simple) .product-inner:hover a' => 'color: #161619',
					'{{WRAPPER}} .konte-tabs__panels:after' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'arrow_style_heading',
			[
				'label'     => __( 'Arrows', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'arrow_type',
			[
				'label'   => __( 'Arrow Type', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'angle' => __( 'Button', 'konte-addons' ),
					'arrow' => __( 'Arrow', 'konte-addons' ),
				],
				'default'   => 'angle',
				'frontend_available' => true,
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
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
					'{{WRAPPER}} .konte-carousel__arrow' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
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
					'arrow_type' => [ 'angle' ],
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
					'arrow_type' => [ 'angle' ],
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dots_style_heading',
			[
				'label'     => __( 'Dots', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'both', 'dots' ],
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
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-products',
			'konte-product-carousel',
			'konte-product-carousel--elementor',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
			'woocommerce',
		] );

		switch ( $settings['arrow_type'] ) {
			case 'arrow':
				$left  = 'arrow-left';
				$right = 'arrow-left';
				break;

			default:
				$left  = 'left';
				$right = 'right';
				break;
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php $this->render_products() ?>

			<?php if ( in_array( $settings['navigation'], ['dots', 'both'] ) ) : ?>
				<div class="konte-carousel__pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php if ( in_array( $settings['navigation'], ['arrows', 'both'] ) ) : ?>
			<div class="konte-carousel__arrow konte-carousel-navigation--<?php echo esc_attr( $settings['arrow_type'] ) ?> konte-carousel-navigation--prev">
				<span class="svg-icon icon-<?php echo esc_attr( $left ) ?>"><svg><use xlink:href="#<?php echo esc_attr( $left ) ?>"></use></svg></span>
			</div>
			<div class="konte-carousel__arrow konte-carousel-navigation--<?php echo esc_attr( $settings['arrow_type'] ) ?> konte-carousel-navigation--next">
				<span class="svg-icon icon-<?php echo esc_attr( $right ) ?>"><svg><use xlink:href="#<?php echo esc_attr( $right ) ?>"></use></svg></span>
			</div>
		<?php endif;
	}

	/**
	 * Render products loop content.
	 *
	 * @param boolean $settings
	 */
	protected function render_products( $settings = false ) {
		$content = $this->get_products_loop_content( $settings );
		$content = preg_replace( '/<div class=["\']woocommerce[^>]+>(.*?)(<\/div>)$/is' , '$1', $content );

		echo $content;
	}
}

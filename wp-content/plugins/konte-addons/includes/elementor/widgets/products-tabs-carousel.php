<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Products_Tabs_Carousel extends Products_Tabs {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-products-tabs-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Products Tabs Carousel', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-product-tabs konte-elementor-widget';
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
		return [ 'products tabs carousel', 'tabs', 'carousel', 'woocommerce', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->remove_control( 'columns' );

		$this->start_injection(
			[
				'type' => 'section',
				'at'   => 'end',
				'of'   => 'section_product_tabs'
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'   => esc_html__( 'Slides to show', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 4,
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'   => esc_html__( 'Slides to scroll', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 4,
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
				'default' => 'arrows',
				'toggle'  => false,
				'frontend_available' => true,
			]
		);

		$this->end_injection();

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
				'label' => esc_html__( 'Products Carousel', 'konte-addons' ),
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
			'konte-product-tabs',
			'konte-product-tabs--elementor',
			'konte-product-tabs--carousel',
			'konte-product-tabs__' . $settings['tabs_type'],
			'konte-tabs',
			'konte-tabs--elementor',
		] );

		$tabs = $this->get_tabs_data();
		$query_args = [];

		if ( empty( $tabs ) ) {
			return;
		}

		$carousel_args = [
			'dots'   => in_array( $settings['navigation'], ['both', 'dots'] ),
			'arrows' => in_array( $settings['navigation'], ['both', 'arrows'] ) ? $settings['arrow_type'] : false,
		];

		$this->add_render_attribute( 'panel', 'class', [
			'konte-products',
			'konte-product-tabs__panel',
			'konte-tabs__panel',
			'active',
		] );

		$this->add_render_attribute( 'panel', 'data-panel', '1' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<ul class="konte-product-tabs__tabs konte-tabs__nav">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<?php
					$tab_key = $this->get_repeater_setting_key( 'tab', 'products_tab', $key );
					$tab['args']['class'] = 'konte-carousel--nav-' . $settings['navigation'];

					$this->add_render_attribute( $tab_key, [
						'data-target' => $tab['index'],
						'data-atts'   => json_encode( $tab['args'] ),
					] );

					if ( 1 === $tab['index'] ) {
						$this->add_render_attribute( $tab_key, 'class', 'active' );
						$query_args = $tab['args'];
					}
					?>
					<li <?php echo $this->get_render_attribute_string( $tab_key ) ?>><?php echo esc_html( $tab['title'] ); ?></li>
				<?php endforeach; ?>
			</ul>
			<div class="konte-product-tabs__panels konte-tabs__panels">
				<div <?php echo $this->get_render_attribute_string( 'panel' ) ?>>
					<?php
					// $this->render_products( $query_args );
					$query_args['class'] .= ' konte-product-carousel konte-product-carousel--elementor swiper-container konte-carousel--elementor konte-carousel--swiper';
					echo \KonteAddons\Elementor\Products::instance()->get_content( $query_args, $carousel_args );
					?>
				</div>
			</div>
		</div>
		<?php
	}
}

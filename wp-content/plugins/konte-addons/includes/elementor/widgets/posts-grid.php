<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Posts_Grid extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-posts-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Posts Grid', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid konte-elementor-widget';
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
		return [ 'post grid', 'post', 'grid', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_posts_grid',
			[ 'label' => __( 'Posts Grid', 'konte-addons' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'     => __( 'Number Of Posts', 'konte-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 3,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					1 => __( '1 Column', 'konte-addons' ),
					2 => __( '2 Columns', 'konte-addons' ),
					3 => __( '3 Columns', 'konte-addons' ),
					4 => __( '4 Columns', 'konte-addons' ),
				],
				'default' => 3,
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'toggle'          => false,
				'selectors'       => [
					'{{WRAPPER}} .konte-post-grid--elementor .hentry' => 'width: calc(1/{{VALUE}}*100%)',
				],
				'required'        => true,
				'device_args'     => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'selectors' => [
							'{{WRAPPER}} .konte-post-grid--elementor .hentry' => 'width: calc(1/{{VALUE}}*100%)',
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'selectors' => [
							'{{WRAPPER}} .konte-post-grid--elementor .hentry' => 'width: calc(1/{{VALUE}}*100%)',
						],
					],
				]
			]
		);

		$this->add_control(
			'category',
			[
				'label'    => __( 'Category', 'konte-addons' ),
				'type'     => Controls_Manager::SELECT2,
				'options'  => \KonteAddons\Elementor\Utils::get_terms_options(),
				'default'  => '',
				'multiple' => true,
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_posts_grid',
			[
				'label' => __( 'Posts Grid', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => __( 'Gap', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 10,
					],
				],
				'default' => [
					'size' => 20
				],
				'selectors' => [
					'{{WRAPPER}} .konte-post-grid--elementor' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .konte-post-grid--elementor .hentry' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
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

		$atts = [
			'per_page'  => $settings['limit'],
			'columns'   => $settings['columns'],
			'category'  => is_array( $settings['category'] ) ? implode( ',', $settings['category'] ) : $settings['category'],
			'el_class'  => 'konte-post-grid--elementor',
		];

		echo \Konte_Addons_Shortcodes::post_grid( $atts );
	}
}

<?php
namespace KonteAddons\Elementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

abstract class Carousel_Widget_Base extends Widget_Base {
	/**
	 * Register carousel controls
	 *
	 * @param array $controls
	 */
	protected function register_carousel_controls( $controls = [] ) {
		$supported_controls = [
			'slides_to_show'  => 3,
			'slides_to_sroll' => 1,
			'navigation'      => 'dots',
			'infinite'        => 'yes',
			'autoplay'        => 'yes',
			'speed'           => 800,
			'spacing'         => '',
			'arrow_type'      => 'angle',
			'dots_align'      => 'left',
		];

		$controls = 'all' == $controls ? $supported_controls : $controls;

		foreach ( $controls as $option => $default ) {
			switch( $option ) {
				case 'slides_to_show':
					$this->add_responsive_control(
						'slides_to_show',
						[
							'label'              => esc_html__( 'Slides to show', 'konte-addons' ),
							'type'               => Controls_Manager::NUMBER,
							'min'                => 1,
							'max'                => 10,
							'default'            => $default,
							'frontend_available' => true,
							'separator'          => 'before',
						]
					);
					break;

				case 'slides_to_scroll':
					$this->add_responsive_control(
						'slides_to_scroll',
						[
							'label'              => esc_html__( 'Slides to scroll', 'konte-addons' ),
							'type'               => Controls_Manager::NUMBER,
							'min'                => 1,
							'max'                => 10,
							'default'            => $default,
							'frontend_available' => true,
						]
					);
					break;

				case 'navigation':
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
							'default'            => $default,
							'toggle'             => false,
							'frontend_available' => true,
						]
					);
					break;

				case 'infinite':
					$this->add_control(
						'infinite',
						[
							'label'              => __( 'Infinite Loop', 'konte-addons' ),
							'type'               => Controls_Manager::SWITCHER,
							'label_off'          => __( 'Off', 'konte-addons' ),
							'label_on'           => __( 'On', 'konte-addons' ),
							'default'            => $default,
							'frontend_available' => true,
						]
					);
					break;

				case 'autoplay':
					$this->add_control(
						'autoplay',
						[
							'label'              => __( 'Autoplay', 'konte-addons' ),
							'type'               => Controls_Manager::SWITCHER,
							'label_off'          => __( 'Off', 'konte-addons' ),
							'label_on'           => __( 'On', 'konte-addons' ),
							'default'            => $default,
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
					break;

				case 'speed':
					$this->add_control(
						'speed',
						[
							'label'              => __( 'Animation Speed', 'konte-addons' ),
							'type'               => Controls_Manager::NUMBER,
							'default'            => $default,
							'min'                => 100,
							'step'               => 50,
							'frontend_available' => true,
						]
					);
					break;

				case 'spacing':
					$this->add_control(
						'image_spacing',
						[
							'label'   => __( 'Spacing', 'konte-addons' ),
							'type'    => Controls_Manager::SELECT,
							'options' => [
								''       => __( 'Default', 'konte-addons' ),
								'custom' => __( 'Custom', 'konte-addons' ),
							],
							'default'   => $default,
							'condition' => [
								'slides_to_show!' => '1',
							],
						]
					);

					$this->add_control(
						'image_spacing_custom',
						[
							'label' => __( 'Custom Spacing', 'konte-addons' ),
							'type' => Controls_Manager::SLIDER,
							'range' => [
								'px' => [
									'max' => 400,
								],
							],
							'default' => [
								'size' => 0,
							],
							'show_label' => false,
							'condition' => [
								'image_spacing' => 'custom',
							],
							'frontend_available' => true,
							'render_type' => 'none',
						]
					);
					break;

				case 'arrow_type':
					$this->add_control(
						'arrow_type',
						[
							'label'   => __( 'Arrow Type', 'konte-addons' ),
							'type'    => Controls_Manager::SELECT,
							'options' => [
								'angle' => __( 'Button', 'konte-addons' ),
								'arrow' => __( 'Arrow', 'konte-addons' ),
							],
							'default'   => $default,
							'separator' => 'before',
							'condition' => [
								'navigation' => [ 'both', 'arrows' ]
							],
						]
					);
					break;

				case 'dots_align':
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
							'default'   => $default,
							'selectors' => [
								'{{WRAPPER}} .konte-carousel__pagination' => 'text-align: {{VALUE}};',
							],
							'separator' => 'before',
							'condition' => [
								'navigation' => [ 'both', 'dots' ]
							],
						]
					);
					break;
			}
		}
	}

	/**
	 * Render the carousel navigation
	 *
	 * @param string $icon
	 */
	protected function render_carousel_navigation( $icon = null ) {
		if ( ! in_array( $this->get_settings_for_display( 'navigation' ), ['arrows', 'both'] ) ) {
			return;
		}

		if ( ! $icon ) {
			$icon = $this->get_settings_for_display( 'arrow_type' );
			$icon = $icon ? $icon : 'angle';
		}

		\KonteAddons\Elementor\Utils::carousel_navigation( $icon );
	}

	/**
	 * Render the carousel pagination
	 */
	protected function render_carousel_pagination() {
		if ( ! in_array( $this->get_settings_for_display( 'navigation' ), ['dots', 'both'] ) ) {
			return;
		}

		\KonteAddons\Elementor\Utils::carousel_pagination();
	}
}
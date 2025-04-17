<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Chart extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-chart';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Chart', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter-circle konte-elementor-widget';
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
	return [ 'chart', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_chart',
			[ 'label' => __( 'Circle Chart', 'konte-addons' ) ]
		);

		$this->add_control(
			'value',
			[
				'label' => __( 'Value', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'description' => esc_html__( 'Enter the chart value in percentage.', 'konte-addons' ),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'size',
			[
				'label'     => __( 'Circle Size', 'konte-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 2000,
				'step'      => 1,
				'default'   => 200,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'thickness',
			[
				'label'     => __( 'Circle Thickness', 'konte-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 8,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'label_source',
			[
				'label' => __( 'Label', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => __( 'Percentage', 'konte-addons' ),
					'custom' => __( 'Custom', 'konte-addons' ),
				],
				'default' => 'auto',
			]
		);

		$this->add_control(
			'label',
			[
				'label' => __( 'Label', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'show_label' => false,
				'condition' => [
					'label_source' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'circle_align',
			[
				'label'   => __( 'Alignment', 'konte-addons' ),
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
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_circle',
			[
				'label' => __( 'Circle', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'empty_color',
			[
				'label' => __( 'Circle Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Fill Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => __( 'Label', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selector' => '{{WRAPPER}} .konte-chart__text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .konte-chart__text',
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
			'konte-chart',
			'konte-chart--' . $settings['value']['size'],
			'konte-chart--elementor',
		] );

		$label = 'custom' == $settings['label_source'] ? $settings['label'] : '<span class="konte-chart__unit">%</span>' . esc_html( $settings['value']['size'] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div class="konte-chart__text"><?php echo wp_kses_post( $label ) ?></div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', [
			'konte-chart',
			'konte-chart-' + settings.value.size,
			'konte-chart--elementor',
		] );

		view.addRenderAttribute( 'text', 'class', [ 'konte-chart__text' ] );
		var label = ('custom' === settings.label_source) ? settings.label : '<span class="unit">' + settings.value.unit + '</span>' + settings.value.size.toString();
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ label }}}</div>
		</div>
		<?php
	}
}

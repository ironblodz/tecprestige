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
class Separator_Dash extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-separator-dash';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Separator Dash', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-divider-shape konte-elementor-widget';
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
		return [ 'separator dash', 'separator', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_separator',
			[ 'label' => __( 'Separator', 'konte-addons' ) ]
		);

		$this->add_control(
			'text',
			[
				'label'       => __( 'Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Enter your text', 'konte-addons' )
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			[
				'label' => __( 'Separator', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-dash' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'line_style_heading',
			[
				'label'     => __( 'Line', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'line_width',
			[
				'label' => __( 'Width', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-dash--elementor .konte-dash__line' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'line_height',
			[
				'label' => __( 'Height', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-dash--elementor .konte-dash__line' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'text_style_heading',
			[
				'label'     => __( 'Text', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .konte-dash__text',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label' => __( 'Spacing', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .konte-dash--elementor .konte-dash__text' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', ['konte-dash', 'konte-dash--elementor'] );
		$this->add_render_attribute( 'text', 'class', 'konte-dash__text' );

		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<span <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<span class="konte-dash__line"></span>
			<?php if ( ! empty( $settings['text'] ) ) : ?>
				<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo esc_html( $settings['text'] ) ?></span>
			<?php endif;?>
		</span>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', ['konte-dash', 'konte-dash--elementor'] );
		view.addRenderAttribute( 'text', 'class', 'konte-dash__text' );

		view.addInlineEditingAttributes( 'text', 'none' );
		#>
		<span {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<span class="konte-dash__line"></span>
			<# if ( settings.text ) { #>
				<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
			<#}#>
		</span>
		<?php
	}
}

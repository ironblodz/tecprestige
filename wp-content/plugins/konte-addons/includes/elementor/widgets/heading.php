<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Heading extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-heading';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Heading', 'konte-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'konte' ];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'heading', 'title', 'text', 'konte' ];
	}

	/**
	 * Register heading widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Title', 'konte-addons' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your title', 'konte-addons' ),
				'default' => __( 'Add Your Heading Text Here', 'konte-addons' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'konte-addons' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'elementor' ),
					'xsmall'  => __( 'Extra Small', 'elementor' ),
					'small'   => __( 'Small', 'elementor' ),
					'medium'  => __( 'Medium', 'elementor' ),
					'large'   => __( 'Large', 'elementor' ),
					'xlarge'  => __( 'Extra Large', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'HTML Tag', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'konte-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'konte-addons' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .konte-heading-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .konte-heading-title',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => __( 'Blend Mode', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'konte-addons' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .konte-heading-title' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( '' === $settings['title'] ) {
			return;
		}

		$this->add_render_attribute( 'title', 'class', [ 'konte-heading-title', 'konte-heading-title--' . $settings['size'] ] );

		$this->add_inline_editing_attributes( 'title' );

		$title = $settings['title'];

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		printf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title' ), $title );
	}

	/**
	 * Render heading widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		var title = settings.title;

		if ( '' !== settings.link.url ) {
			title = '<a href="' + settings.link.url + '">' + title + '</a>';
		}

		view.addRenderAttribute( 'title', 'class', [ 'konte-heading-title', 'konte-heading-title--' + settings.size ] );

		view.addInlineEditingAttributes( 'title' );
		#>
		<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ title }}}</{{{settings.title_size}}}>
		<?php
	}
}

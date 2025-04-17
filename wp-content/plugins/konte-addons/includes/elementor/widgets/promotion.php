<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Promotion extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-promotion';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Promotion', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-banner konte-elementor-widget';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['konte'];
	}

	public function get_keywords() {
		return [ 'promotion', 'button', 'konte' ];
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
			'section_promotion',
			[ 'label' => __( 'Promotion', 'konte-addons' ) ]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard'  => __( 'Standard', 'konte-addons' ),
					'inline' => __( 'Inline', 'konte-addons' ),
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => __( 'Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is the description', 'konte-addons' ),
				'placeholder' => __( 'Enter your description', 'konte-addons' ),
				'rows'        => 4,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text', [
				'label'       => esc_html__( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Click Here', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_link', [
				'label'         => esc_html__( 'Button Link', 'konte-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'konte-addons' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_promotion_style',
			[
				'label' => __( 'Promotion', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Description
		$this->add_control(
			'description_style_heading',
			[
				'label'     => __( 'Description', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-promotion__text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-promotion__text',
			]
		);

		// Button
		$this->add_control(
			'button_style_heading',
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-promotion .konte-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-promotion .konte-button',
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
			'konte-promotion',
			'konte-promotion--' . $settings['layout'],
			'layout-' . $settings['layout']
		] );
		$this->add_render_attribute( 'description', 'class', 'konte-promotion__text' );
		$this->add_render_attribute( 'button', 'class', 'konte-button button-underline underline-full small' );

		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );
		}

		$this->add_inline_editing_attributes( 'description', 'none' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<p <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></p>
			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php  ?><?php echo esc_html( $settings['button_text'] ); ?></a>
			<?php endif; ?>
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
			'konte-promotion',
			'konte-promotion--' + settings.layout,
			'layout-' + settings.layout
		] );
		view.addRenderAttribute( 'description', 'class', 'konte-promotion__text' );
		view.addRenderAttribute( 'button', 'class', 'konte-button button-underline underline-full small' );

		if ( settings.button_link.url ) {
			view.addRenderAttribute( 'button', 'href', settings.button_link.url );
		}

		view.addInlineEditingAttributes( 'description', 'none' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<p {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</p>
			<a {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button_text }}}</a>
		</div>
		<?php
	}
}

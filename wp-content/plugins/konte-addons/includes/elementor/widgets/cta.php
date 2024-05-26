<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Cta extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-cta';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Call to Action', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
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
		return [ 'call to action', 'cta', 'button', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		// Content
		$this->start_controls_section(
			'section_content',
			[ 'label' => __( 'Call To Action', 'konte-addons' ) ]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title & Content', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This is the heading', 'konte-addons' ),
				'placeholder' => __( 'Enter your heading', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is the content', 'konte-addons' ),
				'placeholder' => __( 'Enter your content', 'konte-addons' ),
				'rows'        => 4,
				'show_label'  => false,
			]
		);

		$this->add_control(
			'button_options_heading',
			[
				'label' => __( 'Button', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Click Here', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Link', 'konte-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'konte-addons' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
				],
			]
		);

		$this->add_control(
			'note',
			[
				'label'       => __( 'Note', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is a short note', 'konte-addons' ),
				'placeholder' => __( 'Enter a short note', 'konte-addons' ),
				'rows'        => 4,
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();

		//Background
		$this->start_controls_section(
			'section_style_background',
			[
				'label' => esc_html__( 'Background', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'cta_background',
				'label'          => __( 'Background', 'konte-addons' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .konte-cta',
			]
		);
		$this->end_controls_section();

		// Style.
		$this->start_controls_section(
			'section_style_general',
			[
				'label' => __( 'Call To Action', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'padding',
			[
				'label' => __( 'Padding', 'konte-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .konte-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-cta__heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-cta__heading',
			]
		);

		$this->add_control(
			'description_style_heading',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-cta__text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-cta__text',
			]
		);

		$this->add_control(
			'button_style_heading',
			[
				'label' => __( 'Button', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-button',
			]
		);

		$this->start_controls_tabs('style_button_tabs');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'konte-addons' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'konte-addons' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'note_style_heading',
			[
				'label' => __( 'Note', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'note_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-cta__note' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'note_typography',
				'selector' => '{{WRAPPER}} .konte-cta__note',
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

		$this->add_render_attribute( 'wrapper', 'class', ['konte-cta', 'konte-cta--elementor'] );
		$this->add_render_attribute( 'title', 'class', 'konte-cta__heading' );
		$this->add_render_attribute( 'description', 'class', 'konte-cta__text' );
		$this->add_render_attribute( 'note', 'class', 'konte-cta__note' );
		$this->add_render_attribute( 'button', 'class', 'konte-button button-outline button' );

		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );
		}

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description', 'none' );
		$this->add_inline_editing_attributes( 'note', 'none' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="konte-cta__content">
				<h5 <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo esc_html( $settings['title'] ) ?></h5>
				<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>

				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<div class="konte-cta__button">
						<a <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php echo esc_html( $settings['button_text'] ) ?></a>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $settings['note'] ) ) : ?>
					<p <?php echo $this->get_render_attribute_string( 'note' ); ?>><?php echo esc_html( $settings['note'] ) ?></p>
				<?php endif; ?>
			</div>
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
		view.addRenderAttribute( 'wrapper', 'class', ['konte-cta']);
		view.addRenderAttribute( 'title', 'class', 'konte-cta__heading' );
		view.addRenderAttribute( 'description', 'class', 'konte-cta__text' );
		view.addRenderAttribute( 'note', 'class', 'konte-cta__note' );
		view.addRenderAttribute( 'button', 'class', 'konte-button button-outline button' );

		if ( settings.button_link.url ) {
			view.addRenderAttribute( 'button', 'href', settings.button_link.url );
		}

		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'description', 'none' );
		view.addInlineEditingAttributes( 'note', 'none' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="konte-cta__content">
				<h5 {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</h5>
				<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
				<div class="konte-cta__button">
					<a {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button_text }}}</a>
				</div>
				<p {{{ view.getRenderAttributeString( 'note' ) }}}>{{{ settings.note }}}</p>
			</div>
		</div>
		<?php
	}
}

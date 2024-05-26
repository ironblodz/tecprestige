<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Button widget
 */
class Button extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-button';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Button', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-button';
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
		return [ 'button', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_button',
			[ 'label' => __( 'Button', 'konte-addons' ) ]
		);

		$this->add_control(
			'text',
			[
				'label'       => __( 'Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'konte-addons' ),
				'placeholder' => __( 'Button text', 'konte-addons' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'konte-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'konte-addons' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => __( 'Type', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'    => __( 'Normal', 'konte-addons' ),
					'outline'   => __( 'Outline', 'konte-addons' ),
					'underline' => __( 'Underline', 'konte-addons' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_shape',
			[
				'label' => __( 'Shape', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'square',
				'options' => [
					'square'  => __( 'Square', 'konte-addons' ),
					'rounded' => __( 'Rounded', 'konte-addons' ),
				],
				'condition' => [
					'button_type!' => 'underline'
				]
			]
		);

		$this->add_control(
			'button_line_width',
			[
				'label' => __( 'Line Width', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'small' => __( 'Small', 'konte-addons' ),
					'full'  => __( 'Full', 'konte-addons' ),
				],
				'condition' => [
					'button_type' => 'underline',
				]
			]
		);

		$this->add_control(
			'button_line_position',
			[
				'label' => __( 'Line Position', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'   => __( 'Left', 'konte-addons' ),
					'center' => __( 'Center', 'konte-addons' ),
					'right'  => __( 'Right', 'konte-addons' ),
				],
				'condition' => [
					'button_type' => 'underline',
				]
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'small'  => __( 'Small', 'konte-addons' ),
					'normal' => __( 'Normal', 'konte-addons' ),
					'medium' => __( 'Medium', 'konte-addons' ),
					'large'  => __( 'Large', 'konte-addons' ),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => __( 'Alignment', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'konte-addons' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'konte-button-wrapper--align-',
				'condition' => [
					'button_type!' => 'underline',
				]
			]
		);

		$this->add_responsive_control(
			'align_underline',
			[
				'label'   => __( 'Alignment', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'konte-button-wrapper--align-',
				'condition' => [
					'button_type' => 'underline',
				]
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label'   => __( 'Button ID', 'konte-addons' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => '',
				'title'       => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'konte-addons' ),
				'label_block' => false,
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'konte-addons' ),
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();

		// Style section
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'condition' => [
					'button_type' => 'normal'
				]
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
				'condition' => [
					'button_type' => [ 'normal', 'outline' ]
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'button', 'class', [
			'konte-button',
			'konte-button--elementor',
			'konte-button--type-' . $settings['button_type'],
			'button-' . $settings['button_type'],
			'konte-button--size-' . $settings['size'],
			$settings['size']
		] );
		$this->add_render_attribute( 'button', 'role', 'button' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['link'] );
			$this->add_render_attribute( 'button', 'class', 'konte-button--link' );
		}

		if ( 'underline' == $settings['button_type'] ) {
			$this->add_render_attribute( 'button', 'class', 'underline-' . $settings['button_line_width'] );

			if ( 'small' == $settings['button_line_width'] ) {
				$this->add_render_attribute( 'button', 'class', 'underline-' . $settings['button_line_position'] );
			}
		} else {
			$this->add_render_attribute( 'button', 'class', [ 'button', $settings['button_shape'] ] );
		}

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		$this->add_render_attribute( 'text', 'class', ['konte-button__text'] );
		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo esc_html( $settings['text'] ) ?></span>
		</a>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'button', 'class', [
			'konte-button',
			'konte-button--elementor',
			'konte-button--type-' + settings.button_type,
			'button-' + settings.button_type,
			'konte-button--size-' + settings.size,
			settings.size
		] );
		view.addRenderAttribute( 'button', 'role', 'button' );

		if ( settings.link.url ) {
			view.addRenderAttribute( 'button', 'class', 'konte-button--link' );
			view.addRenderAttribute( 'button', 'href', settings.link.url );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'button', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'button', 'rel', 'nofollow' );
			}
		}

		if ( 'underline' === settings.button_type ) {
			view.addRenderAttribute( 'button', 'class', 'underline-' + settings.button_line_width );

			if ( 'small' === settings.button_line_width ) {
				view.addRenderAttribute( 'button', 'class', 'underline-' + settings.button_line_position );
			}
		} else {
			view.addRenderAttribute( 'button', 'class', ['button', settings.button_shape ] );
		}

		if ( settings.button_css_id ) {
			view.addRenderAttribute( 'button', 'id', settings.addRenderAttribute );
		}

		view.addRenderAttribute( 'text', 'class', ['konte-button__text'] );
		view.addInlineEditingAttributes( 'text', 'none' );
		#>
		<a {{{ view.getRenderAttributeString( 'button' ) }}}>
			<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
		</a>
		<?php
	}
}

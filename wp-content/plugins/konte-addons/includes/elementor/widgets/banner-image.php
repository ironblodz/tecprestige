<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

/**
 * Banner Image widget
 */
class Banner_Image extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-banner';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Banner Image', 'konte-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-rollover';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
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
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'banner', 'image', 'konte' ];
	}

	/**
	 * Register widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_banner',
			[ 'label' => __( 'Banner Image', 'konte-addons' ) ]
		);

		$this->add_control(
			'image_source',
			[
				'label'   => esc_html__( 'Image Source', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'library',
				'options' => [
					'library' => [
						'title' => __( 'Media Library', 'konte-addons' ),
						'icon'  => 'eicon-upload',
					],
					'external' => [
						'title' => __( 'External Image', 'konte-addons' ),
						'icon'  => 'eicon-editor-link',
					],
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'konte-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_source' => 'library',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'condition' => [
					'image_source' => 'library'
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://via.placeholder.com/640x400.png?text=Place+Holder',
				'label_block' => true,
				'condition' => [
					'image_source' => 'external',
				],
			]
		);

		$this->add_control(
			'tagline',
			[
				'label' => __( 'Tagline', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the title', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'show_label' => false,
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'elementor' ),
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
				'default' => 'h3',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'konte-addons' ),
				'placeholder' => __( 'Button text', 'konte-addons' ),
				'label_block' => true,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => __( 'Button Type', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					'normal'    => __( 'Normal', 'konte-addons' ),
					'outline'   => __( 'Outline', 'konte-addons' ),
					'underline' => __( 'Underline', 'konte-addons' ),
				],
			]
		);

		$this->add_control(
			'button_shape',
			[
				'label' => __( 'Button Shape', 'konte-addons' ),
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
			'button_size',
			[
				'label' => __( 'Button Size', 'konte-addons' ),
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

		$this->add_control(
			'button_line_width',
			[
				'label' => __( 'Line Width', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
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
			'button_visibility',
			[
				'label' => __( 'Button Visibility', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'always',
				'options' => [
					'always' => __( 'Always Visible', 'konte-addons' ),
					'hover'  => __( 'On Hover', 'konte-addons' ),
				],
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
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'content_position',
			[
				'label' => __( 'Content Position', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top-left',
				'options' => [
					'top-center'    => __( 'Top', 'konte-addons' ),
					'bottom-center' => __( 'Bottom', 'konte-addons' ),
					'left'          => __( 'Left', 'konte-addons' ),
					'center'        => __( 'Center', 'konte-addons' ),
					'right'         => __( 'Right', 'konte-addons' ),
					'top-left'      => __( 'Top Left', 'konte-addons' ),
					'top-right'     => __( 'Top Right', 'konte-addons' ),
					'bottom-left'   => __( 'Bottom Left', 'konte-addons' ),
					'bottom-right'  => __( 'Bottom Right', 'konte-addons' ),
					'under-image'   => __( 'Under Image', 'konte-addons' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label' => __( 'Hover Effect', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'zoomin',
				'options' => [
					'none'    => __( 'None', 'konte-addons' ),
					'zoomin'  => __( 'Zoom In', 'konte-addons' ),
					'zoomout' => __( 'Zoom Out', 'konte-addons' ),
					'shadow'  => __( 'Shadow', 'konte-addons' ),
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_banner',
			[
				'label' => __( 'Banner Image', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'   => __( 'Text Align', 'konte-addons' ),
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
				'selectors' => [
					'{{WRAPPER}} .konte-banner__content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Content Padding', 'konte-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .konte-banner__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Tagline typography settings.
		$this->add_control(
			'tagline_style_heading',
			[
				'label'     => __( 'Tagline', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tagline_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__tagline' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tagline_typography',
				'selector' => '{{WRAPPER}} .konte-banner__tagline',
			]
		);

		$this->add_responsive_control(
			'tagline_spacing',
			[
				'label' => __( 'Spacing', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner__tagline' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		// Title typography settings.
		$this->add_control(
			'title_style_heading',
			[
				'label'     => __( 'Tile', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-banner__text',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Spacing', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner__text' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		// Description typography settings.
		$this->add_control(
			'desc_style_heading',
			[
				'label'     => __( 'Description', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .konte-banner__description',
			]
		);

		$this->add_responsive_control(
			'desc_spacing',
			[
				'label' => __( 'Spacing', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		// Button settings.
		$this->add_control(
			'button_style_heading',
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-banner__button',
			]
		);

		$this->start_controls_tabs( 'style_button_tabs' );
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
					'{{WRAPPER}} .konte-banner__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => [ 'normal' ],
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
					'{{WRAPPER}} .konte-banner__button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__button:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'button_type' => [ 'normal', 'outline' ],
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-banner',
			'konte-banner--elementor',
			'konte-banner--hover-' . $settings['hover_effect'],
			'konte-banner--content-' . $settings['content_position'],
		] );
		$this->add_render_attribute( 'content', 'class', [ 'konte-banner__content', 'konte-banner-content--' . $settings['content_position'] ] );
		$this->add_render_attribute( 'tagline', 'class', [ 'konte-banner__tagline' ] );
		$this->add_render_attribute( 'title', 'class', [ 'konte-banner__title', 'konte-banner__text' ] );
		$this->add_render_attribute( 'description', 'class', [ 'konte-banner__description' ] );
		$this->add_render_attribute( 'button', [
			'class' => [
				'konte-banner__button',
				'konte-button',
				'button-' . $settings['button_type'],
				$settings['button_size'],
			],
			'role' => 'button',
		] );

		if ( 'underline' == $settings['button_type'] ) {
			$this->add_render_attribute( 'button', 'class', 'underline-' . $settings['button_line_width'] );

			if ( $settings['text_align'] ) {
				$line_align = $settings['text_algin'];
			} else {
				$line_align = in_array( $settings['content_position'], ['top-center', 'center', 'bottom-center'] ) ? 'center' : ( is_rtl() ? 'right' : 'left' );
			}

			$this->add_render_attribute( 'button', 'class', 'underline-' . $line_align );
		} else {
			$this->add_render_attribute( 'button', 'class', [ 'button', $settings['button_shape'] ] );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}

		if ( 'library' == $settings['image_source'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		} else {
			$image = '<img src="' . esc_url( $settings['image_url'] ) . '" alt="' . esc_attr( $settings['title'] ) . '">';
		}

		if ( ! empty( 'button_text' ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'konte-banner--button-visible-' . $settings['button_visibility'] );
		}

		$this->add_inline_editing_attributes( 'tagline', 'none' );
		$this->add_inline_editing_attributes( 'title', 'basic' );
		$this->add_inline_editing_attributes( 'description', 'advanced' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ) ?>>
			<?php endif; ?>
			<span class="konte-banner__image-wrapper"><?php echo $image; ?></span>
			<div <?php echo $this->get_render_attribute_string( 'content' ) ?>>
				<?php if ( ! empty( $settings['tagline'] ) ) : ?>
					<span <?php echo $this->get_render_attribute_string( 'tagline' ) ?>><?php echo esc_html( $settings['tagline'] ) ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo $settings['title_size'] ?> <?php echo $this->get_render_attribute_string( 'title' ) ?>><?php echo wp_kses_post( $settings['title'] ) ?></<?php echo $settings['title_size'] ?>>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ) ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<span <?php echo $this->get_render_attribute_string( 'button' ) ?>><?php echo esc_html( $settings['button_text'] ) ?></span>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
				</a>
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
		view.addRenderAttribute( 'wrapper', 'class', ['konte-banner', 'konte-banner--elementor', 'konte-banner--hover-' + settings.hover_effect, 'konte-banner--content' + settings.content_position]);
		view.addRenderAttribute( 'content', 'class', ['konte-banner__content', 'konte-banner-content--' + settings.content_position]);
		view.addRenderAttribute( 'tagline', 'class', ['konte-banner__tagline'] );
		view.addRenderAttribute( 'title', 'class', ['konte-banner__title', 'konte-banner__text'] );
		view.addRenderAttribute( 'description', 'class', ['konte-banner__description'] );
		view.addRenderAttribute( 'button', 'class', ['konte-banner__button', 'konte-button', 'button-' + settings.button_type, settings.button_size] );
		view.addRenderAttribute( 'button', 'role', 'button' );

		if ( 'underline' === settings.button_type ) {
			view.addRenderAttribute( 'button', 'class', 'underline-' + settings.button_line_width );

			if ( settings.text_align ) {
				var lineAlign = settings.text_align;
			} else {
				var lineAlign = settings.content_position.indexOf( 'center' ) >= 0 ? 'center' : '<?php echo is_rtl() ? 'right' : 'left'; ?>';
			}

			view.addRenderAttribute( 'button', 'class', 'underline-' + lineAlign );
		} else {
			view.addRenderAttribute( 'button', 'class', [ 'button', settings.button_shape ] );
		}

		if ( settings.link.url ) {
			view.addRenderAttribute( 'link', 'href', settings.link.url );
		}

		if ( settings.button_text ) {
			view.addRenderAttribute( 'wrapper', 'class', 'konte-banner--button-visible-' + settings.button_visibility );
		}

		view.addInlineEditingAttributes( 'tagline', 'none' );
		view.addInlineEditingAttributes( 'title', 'basic' );
		view.addInlineEditingAttributes( 'description', 'advanced' );

		var imageUrl = '';

		if ( 'library' === settings.image_source ) {
			imageUrl = elementor.imagesManager.getImageUrl( {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			} );
		} else {
			imageUrl = settings.image_url;
		}
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# if ( settings.link.url ) { #>
				<a {{{ view.getRenderAttributeString( 'link' ) }}}>
			<# } #>
			<span class="konte-banner__image-wrapper"><img src="{{ imageUrl }}" /></span>
			<div {{{ view.getRenderAttributeString( 'content' ) }}}>
				<# if ( settings.tagline ) { #>
					<span {{{ view.getRenderAttributeString( 'tagline' ) }}}>{{{ settings.tagline }}}</span>
				<# } #>
				<# if ( settings.title ) { #>
					<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{{ settings.title_size }}}>
				<# } #>
				<# if ( settings.description ) { #>
					<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
				<# } #>
				<# if ( settings.button_text ) { #>
					<span {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button_text }}}</span>
				<# } #>
			</div>
			<# if ( settings.link.url ) { #>
				</a>
			<# } #>
		</div>
		<?php
	}
}

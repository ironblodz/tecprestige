<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

/**
 * Banner Grid widget
 */
class Banner_Grid extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-banner-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Banner Grid', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-inner-section';
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
		return [ 'banner grid', 'banners', 'grid', 'konte' ];
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
			'section_grid',
			[ 'label' => __( 'Banner Grid', 'konte-addons' ) ]
		);
		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
				'desktop_default' => [ 'unit' => 'px', 'size' => 844 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 504 ],
				'mobile_default' => ['unit' => 'px', 'size' => 1008 ],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'gap',
			[
				'label' => __( 'Gap', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid' => 'margin: calc( -{{SIZE}}{{UNIT}} / 2 )',
					'{{WRAPPER}} .konte-banner-grid .konte-banner-grid__banner' => 'padding: calc( {{SIZE}}{{UNIT}} / 2 )',
				],
			]
		);
		$this->end_controls_section();

		// Banners.
		for( $i = 1; $i < 5; $i++ ) {
			$this->register_banner_controls( $i );
		}
	}

	/**
	 * Register the banner controls
	 */
	protected function register_banner_controls( $i = 1 ) {
		$image_source_key = $this->get_banner_setting_key( 'image_source', $i );

		$this->start_controls_section(
			'section_banner_' . $i,
			[
				'label' => sprintf( __( 'Banner %s', 'konte-addons' ), $i )
			]
		);

		$this->add_control(
			$image_source_key,
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
			$this->get_banner_setting_key( 'image', $i ),
			[
				'label'   => esc_html__( 'Choose Image', 'konte-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					$image_source_key => 'library',
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .banner-image' => 'background-image: url({{URL}})',
				],
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'image_url', $i ),
			[
				'label' => __( 'Image URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://via.placeholder.com/640x640.png?text=Place+Holder',
				'label_block' => true,
				'condition' => [
					$image_source_key => 'external',
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .banner-image' => 'background-image: url("{{VALUE}}")',
				],
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'content_heading', $i ),
			[
				'label'       => __( 'Content', 'konte-addons' ),
				'type'        => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'tagline', $i ),
			[
				'label'       => __( 'Tagline', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Tagline', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'title', $i ),
			[
				'label'       => __( 'Title', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is the title', 'konte-addons' ),
				'label_block' => true
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'button_text', $i ),
			[
				'label'       => __( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'konte-addons' ),
				'placeholder' => __( 'Button text', 'konte-addons' ),
				'separator'   => 'before'
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'link', $i ),
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
			$this->get_banner_setting_key( 'content_position', $i ),
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
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			$this->get_banner_setting_key( 'text_align', $i ),
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
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_banner_' . $i,
			[
				'label' => sprintf( __( 'Banner %s', 'konte-addons' ), $i ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			$this->get_banner_setting_key( 'padding', $i ),
			[
				'label' => __( 'Padding', 'konte-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-banner-grid__banner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Tagline
		$this->add_control(
			$this->get_banner_setting_key( 'style_tagline_heading', $i ),
			[
				'label'     => __( 'Tagline', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'tagline_color', $i ),
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-banner-grid__banner-tagline' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $this->get_banner_setting_key( 'tagline_typography', $i ),
				'selector' => '{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-banner-grid__banner-tagline',
			]
		);

		// Title
		$this->add_control(
			$this->get_banner_setting_key( 'style_title_heading', $i ),
			[
				'label'     => __( 'Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'title_color', $i ),
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-banner-grid__banner-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $this->get_banner_setting_key( 'title_typography', $i ),
				'selector' => '{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-banner-grid__banner-text',
			]
		);

		// Button.
		$this->add_control(
			$this->get_banner_setting_key( 'style_button_heading', $i ),
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$this->get_banner_setting_key( 'button_color', $i ),
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $this->get_banner_setting_key( 'button_typography', $i ),
				'selector' => '{{WRAPPER}} .konte-banner-grid__banner'. $i .' .konte-button',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get banner setting key
	 *
	 * @param string $setting
	 * @param int $index
	 *
	 * @return string
	 */
	public function get_banner_setting_key( $setting, $index = 0 ) {
		return 'banner_' . $index . '_' . $setting;
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$this->add_render_attribute( 'wrapper', 'class', ['konte-banner-grid', 'konte-banner-grid--elementor'] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php
			for ( $i = 1; $i < 5; $i++ ) {
				$this->render_banner( $i );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render banner output on the frontend
	 */
	protected function render_banner( $i ) {
		$settings             = $this->get_settings_for_display();
		$wrapper_key          = $this->get_banner_setting_key( 'wrapper', $i );
		$link_key             = $this->get_banner_setting_key( 'link', $i );
		$content_position_key = $this->get_banner_setting_key( 'content_position', $i );
		$text_align_key       = $this->get_banner_setting_key( 'text_align', $i );
		$tagline_key          = $this->get_banner_setting_key( 'tagline', $i );
		$title_key            = $this->get_banner_setting_key( 'title', $i );
		$button_key           = $this->get_banner_setting_key( 'button', $i );
		$button_text_key      = $this->get_banner_setting_key( 'button_text', $i );

		// Wrapper Attribute
		$this->add_render_attribute( $wrapper_key, 'class', [
			'konte-banner-grid__banner',
			'konte-banner-grid__banner' . $i,
			'text-position-' . $settings[ $content_position_key ],
			'konte-banner-grid__banner--content-' . $settings[ $content_position_key ],
		] );

		if ( $settings[ $text_align_key ] ) {
			$this->add_render_attribute( $wrapper_key, 'class', 'konte-banner-grid__banner--align-' . $settings[ $text_align_key ] );
		}

		$this->add_render_attribute( $link_key, 'class', 'konte-banner-grid__banner-link' );

		$tag = 'span';

		if ( ! empty( $settings[ $link_key ]['url'] ) ) {
			$tag = 'a';
			$this->add_link_attributes( $link_key, $settings[ $link_key ] );
		}

		$this->add_render_attribute( $link_key, 'title', $settings[ $title_key ] );

		$this->add_render_attribute( $button_key, 'class', [ 'konte-button', 'button-underline', 'underline-small' ] );

		if ( $settings[ $text_align_key ] ) {
			$this->add_render_attribute( $button_key, 'class', 'underline-' . $settings[ $text_align_key ] );
		} elseif ( in_array( $settings[ $content_position_key ], ['top-center', 'center', 'bottom-center'] ) ) {
			$this->add_render_attribute( $button_key, 'class', 'underline-center' );
		} else {
			$this->add_render_attribute( $button_key, 'class', 'underline-' . ( is_rtl() ? 'right' : 'left' ) );
		}

		$this->add_render_attribute( $tagline_key, 'class', ['konte-banner-grid__banner-tagline'] );
		$this->add_render_attribute( $title_key, 'class', ['konte-banner-grid__banner-text', 'button-title'] );

		$this->add_inline_editing_attributes( $tagline_key, 'none' );
		$this->add_inline_editing_attributes( $title_key, 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( $wrapper_key ) ?>>
			<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( $link_key ) ?>>
				<span class="konte-banner-grid__banner-image banner-image"></span>
				<span class="konte-banner-grid__banner-content banner-content">
					<?php if ( ! empty( $settings[ $tagline_key ] ) ) : ?>
						<span <?php echo $this->get_render_attribute_string( $tagline_key ) ?>><?php echo esc_html( $settings[ $tagline_key ] ) ?></span>
					<?php endif; ?>

					<span <?php echo $this->get_render_attribute_string( $title_key ) ?>><?php echo wp_kses_post( $settings[ $title_key ] ) ?></span>

					<?php if ( ! empty( $settings[ $button_text_key ] ) ) : ?>
						<span <?php echo $this->get_render_attribute_string( $button_key ) ?>><?php echo esc_html( $settings[ $button_text_key ] ) ?></span>
					<?php endif; ?>
				</span>
			</<?php echo $tag; ?>>
		</div>
		<?php
	}

	/**
	 * Render icon box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<div class="konte-banner-grid konte-banner-grid--elementor">
			<#
			for ( var i = 1; i <= 4; i ++ ) {
				var tag                  = 'span',
					prefix               = 'banner_' + i.toString() + '_',
					wrapper_key          = prefix + 'wrapper',
					link_key             = prefix + 'link',
					button_text_key      = prefix + 'button_text',
					title_key            = prefix + 'title',
					tagline_key          = prefix + 'tagline',
					content_position_key = prefix + 'content_position',
					button_key           = prefix + 'button',
					text_align_key       = prefix + 'text_align';

				view.addRenderAttribute( wrapper_key, 'class', [
					'konte-banner-grid__banner',
					'konte-banner-grid__banner' + i,
					'text-position-' + settings[content_position_key],
					'konte-banner-grid__banner--content-' + settings[content_position_key],
					'konte-banner-grid__banner--align-' + settings[text_align_key]
				] );

				view.addRenderAttribute( link_key, 'class', 'konte-banner-grid__banner-link' );

				if ( settings[link_key]['url'] ) {
					tag = 'a';
					view.addRenderAttribute( link_key, 'href', settings[link_key]['url'] );
				}

				view.addRenderAttribute( button_key, 'class', [ 'konte-button', 'button-underline', 'underline-small' ] );

				if ( settings[text_align_key] ) {
					view.addRenderAttribute( button_key, 'class', 'underline-' + settings[text_align_key] );
				} else if ( settings[ content_position_key ].indexOf( 'center' ) >= 0 ) {
					view.addRenderAttribute( button_key, 'class', 'underline-center' );
				} else {
					view.addRenderAttribute( button_key, 'class', 'underline-<?php echo is_rtl() ? 'right' : 'left' ?>' );
				}

				view.addRenderAttribute( tagline_key, 'class', ['konte-banner-grid__banner-tagline'] );
				view.addRenderAttribute( title_key, 'class', ['konte-banner-grid__banner-text', 'banner-title'] );

				view.addInlineEditingAttributes( tagline_key, 'none' );
				view.addInlineEditingAttributes( title_key, 'basic' );
				#>
				<div {{{ view.getRenderAttributeString( wrapper_key ) }}}>
					<{{{ tag }}} {{{ view.getRenderAttributeString( link_key ) }}}>
						<span class="konte-banner-grid__banner-image banner-image"></span>
						<span class="konte-banner-grid__banner-content banner-content">
							<# if ( settings[tagline_key] ) { #>
								<span {{{ view.getRenderAttributeString( tagline_key ) }}}>{{{ settings[tagline_key] }}}</span>
							<# } #>
							<# if ( settings[title_key] ) { #>
								<span {{{ view.getRenderAttributeString( title_key ) }}}>{{{ settings[title_key] }}}</span>
							<# } #>
							<# if ( settings[button_text_key] ) { #>
								<span {{{ view.getRenderAttributeString( button_key ) }}}>{{{ settings[button_text_key] }}}</span>
							<# } #>
						</span>
					</{{{ tag }}}>
				</div>
				<#
			}
			#>

		</div>
		<?php
	}
}

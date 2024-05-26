<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

/**
 * Banner Image Carousel widget
 */
class Banner_Carousel extends Carousel_Widget_Base {
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
		return 'konte-banner-carousel';
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
		return __( '[Konte] Banner Carousel', 'konte-addons' );
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
		return 'eicon-slider-push';
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
		return [ 'banner', 'image', 'carousel', 'slides', 'konte' ];
	}

	/**
	 * Register widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_banner_carousel',
			[ 'label' => __( 'Banner Carousel', 'konte-addons' ) ]
		);

		$repeater = new \Elementor\Repeater();

		// Image.
		$repeater->add_control(
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

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'konte-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'show_label' => false,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_source' => 'library',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'condition' => [
					'image_source' => 'library',
				],
			]
		);


		$repeater->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://via.placeholder.com/1000x640.png?text=Place+Holder',
				'show_label' => false,
				'label_block' => true,
				'condition' => [
					'image_source' => 'external',
				],
			]
		);

		// Content.
		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the title', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'show_label' => false,
			]
		);

		// Content Style.
		$repeater->add_control(
			'content_style_toggle',
			[
				'label' => __( 'Content Style', 'konte-addons' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'konte-addons' ),
				'label_on' => __( 'Custom', 'konte-addons' ),
				'return_value' => 'yes',
			]
		);

		$repeater->start_popover();

		$repeater->add_control(
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

		$repeater->add_responsive_control(
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

		$repeater->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Content Padding', 'konte-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$repeater->add_control(
			'title_style_heading',
			[
				'label'     => __( 'Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'konte-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__title' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'title_font_family',
			[
				'label'     => __( 'Font', 'konte-addons' ),
				'type'      => Controls_Manager::FONT,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__title' => 'font-family: {{VALUE}}',
				],
			]
		);

		$repeater->add_responsive_control(
			'title_font_size',
			[
				'label'     => __( 'Font Size', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'vw' => [
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__title' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
			]
		);

		$repeater->add_control(
			'desc_style_heading',
			[
				'label'     => __( 'Description', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'konte-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__description' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'desc_font_family',
			[
				'label'     => __( 'Font', 'konte-addons' ),
				'type'      => Controls_Manager::FONT,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__description' => 'font-family: {{VALUE}}',
				],
			]
		);

		$repeater->add_responsive_control(
			'desc_font_size',
			[
				'label'     => __( 'Font Size', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'vw' => [
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .konte-banner__description' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$repeater->end_popover();

		// Button.
		$repeater->add_control(
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

		$repeater->add_control(
			'button_style_toggle',
			[
				'label' => __( 'Button Style', 'konte-addons' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'konte-addons' ),
				'label_on' => __( 'Custom', 'konte-addons' ),
				'return_value' => 'yes',
			]
		);

		$repeater->start_popover();

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
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

		$repeater->add_control(
			'button_normal',
			[
				'label'     => __( 'BUTTON NORMAL', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__button' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
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

		$repeater->add_control(
			'button_hover',
			[
				'label'     => __( 'BUTTON HOVER', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_hover_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner__button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
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

		$repeater->end_popover();

		// Link.
		$repeater->add_control(
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

		$repeater->add_control(
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
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'banners',
			[
				'label'       => __( 'Banner Slides', 'konte-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default' => [
					[
						'title' => __( 'Banner Image', 'konte-addons' ),
					],
					[
						'title' => __( 'Banner Image', 'konte-addons' ),
					],
					[
						'title' => __( 'Banner Image', 'konte-addons' ),
					],
					[
						'title' => __( 'Banner Image', 'konte-addons' ),
					],
				],
			]
		);

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->register_carousel_controls( [
			'slides_to_show'   => 1,
			'slides_to_scroll' => 1,
			'navigation'       => 'arrows',
		] );

		$this->update_responsive_control(
			'slides_to_show',
			[
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => __( 'Auto', 'konte-addons' ),
				] + $slides_to_show
			]
		);

		$this->update_responsive_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'options' => $slides_to_show,
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label' => __( 'Arrows Position', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'      => __( 'Default', 'konte-addons' ),
					'top'          => __( 'Top', 'konte-addons' ),
					'top-left'     => __( 'Top Left', 'konte-addons' ),
					'top-right'    => __( 'Top Right', 'konte-addons' ),
					'bottom'       => __( 'Bottom', 'konte-addons' ),
					'bottom-left'  => __( 'Bottom Left', 'konte-addons' ),
					'bottom-right' => __( 'Bottom Right', 'konte-addons' ),
				],
				'condition' => [
					'navigation' => 'arrows',
				],
				'prefix_class' => 'konte-carousel-arrows--',
			]
		);

		$this->add_control(
			'show_slide_index',
			[
				'label'       => __( 'Show Slide Index', 'konte-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'show_outside_slides',
			[
				'label'       => __( 'Show Outside Slides', 'konte-addons' ),
				'type'        => Controls_Manager::SWITCHER,
				'prefix_class' => 'konte-carousel-outside-visible--',
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
				'label' => __( 'Additional Options', 'konte-addons' ),
			]
		);

		$this->register_carousel_controls( [
			'infinite' => 'yes',
			'autoplay' => 'yes',
			'speed'    => 800,
		] );

		$this->end_controls_section();

		// Style section.
		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Banner Carousel', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_spacing',
			[
				'label'   => __( 'Spacing', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''       => __( 'Default', 'konte-addons' ),
					'custom' => __( 'Custom', 'konte-addons' ),
				],
				'default'   => '',
				'condition' => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_responsive_control(
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
				'condition' => [
					'image_spacing' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slide_index_position',
			[
				'label' => __( 'Slide Index Position', 'konte-addons' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => is_rtl() ? 'right' : 'left',
				'condition' => [
					'image_spacing' => 'custom',
					'show_slide_index' => 'yes',
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'selectors' => [
							'{{WRAPPER}} .konte-banner-carousel__slide-index' => '{{VALUE}}: calc(-{{image_spacing_custom_tablet.SIZE}}{{image_spacing_custom_tablet.UNIT}} / 2)',
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'selectors' => [
							'{{WRAPPER}} .konte-banner-carousel__slide-index' => '{{VALUE}}: calc(-{{image_spacing_custom_mobile.SIZE}}{{image_spacing_custom_mobile.UNIT}} / 2)',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-banner-carousel__slide-index' => '{{VALUE}}: calc(-{{image_spacing_custom.SIZE}}{{image_spacing_custom.UNIT}} / 2)',
				],
			]
		);

		$this->register_carousel_controls( [
			'arrow_type' => 'angle',
			'dots_align' => 'center',
		] );

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
			'konte-banner-carousel',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
		] );

		if ( $settings['show_slide_index'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'konte-banner-carousel--show-index' );
		}

		if ( $settings['show_outside_slides'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'konte-banner-carousel--outside-visible' );
		}

		if ( 'arrows' == $settings['navigation'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'konte-banner-carousel--arrows-' . $settings['arrows_position'] );
		}

		if ( 'auto' == $settings['slides_to_show'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'konte-banner-carousel--slides-autowidth' );
		}

		if ( is_rtl() ) {
			$this->add_render_attribute( 'wrapper', 'dir', 'rtl' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div class="konte-banner-carousel__list swiper-wrapper">
				<?php foreach( $settings['banners'] as $index => $banner ) : ?>
					<?php
					$banner_key  = $this->get_repeater_setting_key( 'wrapper', 'banner', $index );
					$content_key = $this->get_repeater_setting_key( 'content', 'banner', $index );
					$button_key  = $this->get_repeater_setting_key( 'button', 'banner', $index );
					$link_key    = $this->get_repeater_setting_key( 'link', 'banner', $index );

					$this->add_render_attribute( $banner_key, 'class', [
						'konte-banner-carousel__banner',
						'konte-banner',
						'konte-banner--elementor',
						'konte-banner--hover-' . $settings['hover_effect'],
						'konte-banner--content-' . $banner['content_position'],
						'elementor-repeater-item-' . $banner['_id'],
						'swiper-slide',
					] );

					$this->add_render_attribute( $content_key, 'class', [ 'konte-banner__content' ] );
					$this->add_render_attribute( $content_key, 'class', 'konte-banner-content--' . $banner['content_position'] );

					$this->add_render_attribute( $button_key, [
						'class' => [ 'konte-banner__button', 'konte-button', 'button-' . $banner['button_type'], $banner['button_size'] ],
						'role' => 'button',
					] );

					if ( 'underline' == $banner['button_type'] ) {
						$this->add_render_attribute( $button_key, 'class', 'underline-' . $banner['button_line_width'] );

						if ( $banner['text_align'] ) {
							$line_align = $banner['text_algin'];
						} else {
							$line_align = in_array( $banner['content_position'], ['top-center', 'center', 'bottom-center'] ) ? 'center' : ( is_rtl() ? 'right' : 'left' );
						}

						$this->add_render_attribute( $button_key, 'class', 'underline-' . $line_align );
					} else {
						$this->add_render_attribute( $button_key, 'class', [ 'button', $banner['button_shape'] ] );
					}

					if ( ! empty( $banner['link']['url'] ) ) {
						$this->add_link_attributes( $link_key, $banner['link'] );
					}

					if ( 'library' == $banner['image_source'] ) {
						$image = Group_Control_Image_Size::get_attachment_image_html( $banner );
					} else {
						$image = '<img src="' . esc_url( $banner['image_url'] ) . '" alt="' . esc_attr( $banner['title'] ) . '">';
					}
					?>
					<div <?php echo $this->get_render_attribute_string( $banner_key ) ?>>
						<?php if ( ! empty( $banner['link']['url'] ) ) : ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ) ?>>
						<?php endif; ?>
						<span class="konte-banner__image-wrapper"><?php echo $image; ?></span>
						<div <?php echo $this->get_render_attribute_string( $content_key ) ?>>
							<?php if ( ! empty( $banner['title'] ) ) : ?>
								<<?php echo $banner['title_size'] ?> class="konte-banner__title konte-banner__text"><?php echo wp_kses_post( $banner['title'] ) ?></<?php echo $banner['title_size'] ?>>
							<?php endif; ?>
							<?php if ( ! empty( $banner['description'] ) ) : ?>
								<div class="konte-banner__description"><?php echo wp_kses_post( $banner['description'] ) ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $banner['button_text'] ) ) : ?>
								<span <?php echo $this->get_render_attribute_string( $button_key ) ?>><?php echo esc_html( $banner['button_text'] ) ?></span>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $banner['link']['url'] ) ) : ?>
							</a>
						<?php endif; ?>
						<?php if ( $settings['show_slide_index'] ) : ?>
							<span class="konte-banner-carousel__slide-index konte-carousel__slide-index konte-dash">
								<span class="konte-dash__line"></span>
								<span class="konte-carousel__slide-index-number"><?php echo str_pad( $index + 1, 2, '0', STR_PAD_LEFT ); ?></span>
							</span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php $this->render_carousel_pagination(); ?>
		</div>
		<?php
		$this->render_carousel_navigation();
	}
}
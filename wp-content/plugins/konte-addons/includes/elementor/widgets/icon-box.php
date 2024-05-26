<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Icon_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-icon-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Icon Box', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
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
	   return [ 'icon box', 'icon', 'box', 'konte' ];
   	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_icon',
			[ 'label' => __( 'Icon Box', 'konte-addons' ) ]
		);

		$this->add_control(
			'icon_type',
			[
				'label' => __( 'Icon Type', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', 'konte-addons' ),
					'image' => __( 'Image', 'konte-addons' ),
					'external' => __( 'External', 'konte-addons' ),
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'konte-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_url',
			[
				'label' => __( 'External Icon URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'icon_type' => 'external',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'konte-addons' ),
				'placeholder' => __( 'Enter your title', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description',
			[
				'label' => '',
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
				'placeholder' => __( 'Enter your description', 'konte-addons' ),
				'rows' => 10,
				'separator' => 'none',
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
				'default' => 'h4',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();

		// Style Icon
		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => __( 'Icon', 'konte-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon_type' => 'icon'
				]
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-icon-box__icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .konte-icon-box__icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		// Title style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .konte-icon-box' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before'
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
					'{{WRAPPER}} .konte-icon-box__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-icon-box__title',
			]
		);

		$this->add_control(
			'description_style_heading',
			[
				'label' => __( 'Description', 'konte-addons' ),
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
					'{{WRAPPER}} .konte-icon-box__content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-icon-box__content',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', ['konte-icon-box', 'icon-type-' . $settings['icon_type'], 'box-align-' . $settings['align']] );
		$this->add_render_attribute( 'icon', 'class', 'konte-icon-box__icon' );
		$this->add_render_attribute( 'title', 'class', 'konte-icon-box__title' );
		$this->add_render_attribute( 'description', 'class', 'konte-icon-box__content' );

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
				<?php
				if ( 'image' == $settings['icon_type'] ) {
					echo $settings['image'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $settings['title'] ), esc_url( $settings['image']['url'] ) ) : '';
				} elseif ( 'external' == $settings['icon_type'] ) {
					echo $settings['icon_url'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $settings['title'] ), esc_url( $settings['icon_url'] ) ) : '';
				} else {
					Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
				}
				?>
			</div>
			<<?php echo $settings['title_size'] ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo esc_html( $settings['title'] ) ?></<?php echo $settings['title_size'] ?>>
			<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
		</div>
		<?php
	}

	/**
	 * Render icon box widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', ['konte-icon-box', 'icon-type-' + settings.icon_type, 'box-align-' + settings.align] );
		view.addRenderAttribute( 'icon', 'class', 'konte-icon-box__icon' );
		view.addRenderAttribute( 'title', 'class', 'konte-icon-box__title' );
		view.addRenderAttribute( 'description', 'class', 'konte-icon-box__content' );

		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'description', 'basic' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'icon' ) }}}>
			<#
			if ( 'image' === settings.icon_type ) {
				#><img src="{{ settings.image.url }}" alt="{{ settings.title }}"><#
			} else if ( 'external' === settings.icon_type ) {
				#><img src="{{ settings.icon_url }}" alt="{{ settings.title }}"><#
			} else {
				var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' );
				if ( iconHTML && iconHTML.rendered ) { #>
					{{{ iconHTML.value }}}
				<#}
			}
			#>
			</div>
			<{{{ settings.title_size }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{{ settings.title_size }}}>
			<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
		</div>
		<?php
	}
}

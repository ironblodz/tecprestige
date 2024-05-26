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
 * Pricing Table widget
 */
class Price_Table extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-price-table';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Price Table', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-price-table';
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
		return [ 'price table', 'price', 'table', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Price Table Header
		 */
		$this->start_controls_section(
			'section_price_table_header',
			[ 'label' => __( 'Header', 'konte-addons' ) ]
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
				'toggle' => false,
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

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_price_table_header_style',
			[
				'label' => __( 'Header', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'konte-addons' ),
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
					'{{WRAPPER}} .konte-pricing-table__icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'icon_type' => 'icon'
				],
			]
		);

		$this->add_control(
			'image_width',
			[
				'label' => __( 'Image Width', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1
					],
				],
				'default' => [],
				'condition' => [
					'icon_type!' => 'icon'
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Price Table Title and Description
		 */
		$this->start_controls_section(
			'section_price_table_title_and_description',
			[ 'label' => __( 'Title & Description', 'konte-addons' ) ]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'konte-addons' ),
				'placeholder' => __( 'Enter your title', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => __( 'Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
				'placeholder' => __( 'Enter your description', 'konte-addons' ),
				'rows'        => 10,
				'show_label'  => false,
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_title_and_description_style',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-pricing-table__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__title' => 'color: {{VALUE}}',
				],
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-pricing-table__description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Price Table Pricing
		 */
		$this->start_controls_section(
			'section_price_table_pricing',
			[ 'label' => __( 'Pricing', 'konte-addons' ) ]
		);

		$this->add_control(
			'price',
			[
				'label' => __( 'Price', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '19,90',
				'placeholder' => __( 'Enter your price', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'currency_symbol',
			[
				'label' => __( 'Currency Symbol', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '$',
				'placeholder' => __( 'Enter your currency symbol', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'period',
			[
				'label' => __( 'Period', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Per Month', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_price_table_pricing_style',
			[
				'label' => __( 'Pricing', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_style_heading',
			[
				'label'     => __( 'Price', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .konte-pricing-table__price',
			]
		);

		$this->add_control(
			'currency_font_size',
			[
				'label' => __( 'Currency Font Size', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__price .currency' => 'font-size: {{SIZE}}{{UNIT}}',
				],

			]
		);

		$this->add_control(
			'period_style_heading',
			[
				'label'     => __( 'Period', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'period_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__recurrence' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'period_typography',
				'selector' => '{{WRAPPER}} .konte-pricing-table__recurrence',
			]
		);

		$this->end_controls_section();

		// Table footer.
		$this->start_controls_section(
			'section_price_table_footer',
			[ 'label' => __( 'Footer', 'konte-addons' ) ]
		);

		$this->add_control(
			'button_text', [
				'label'       => esc_html__( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Click Here', 'konte-addons' ),
				'label_block' => true,
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
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_price_table_footer_style',
			[
				'label' => __( 'Footer', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_style_heading',
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-pricing-table__button',
			]
		);

		$this->start_controls_tabs('button_style_tabs');

		$this->start_controls_tab(
			'style_button_normal_tab',
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
					'{{WRAPPER}} .konte-pricing-table__button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_button_hover_tab',
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
					'{{WRAPPER}} .konte-pricing-table__button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-pricing-table__button:hover' => 'background-color: {{VALUE}}',
				],
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

		$this->add_render_attribute( 'wrapper', 'class', ['konte-pricing-table'] );
		$this->add_render_attribute( 'title', 'class', 'konte-pricing-table__title' );
		$this->add_render_attribute( 'description', 'class', 'konte-pricing-table__description' );
		$this->add_render_attribute( 'price', 'class', 'konte-pricing-table__price' );
		$this->add_render_attribute( 'period', 'class', 'konte-pricing-table__recurrence' );
		$this->add_render_attribute( 'button', 'class', 'button konte-pricing-table__button' );

		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );
		}

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( 'image' == $settings['icon_type'] ) {
				echo $settings['image'] ? sprintf( '<img alt="%s" width="%s" src="%s" class="konte-pricing-table__image">', esc_attr( $settings['title'] ), esc_attr( $settings['image_width']['size'] ), esc_url( $settings['image']['url'] ) ) : '';
			} elseif ( 'external' == $settings['icon_type'] ) {
				echo $settings['icon_url'] ? sprintf( '<img alt="%s" width="%s" src="%s" class="konte-pricing-table__image">', esc_attr( $settings['title'] ), esc_attr( $settings['image_width']['size'] ), esc_url( $settings['icon_url'] ) ) : '';
			} else {
				echo '<span class="konte-icon konte-pricing-table__icon">';
				Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
				echo '</span>';
			}
			?>
			<h4 <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo esc_html( $settings['title'] ) ?></h4>
			<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
			<div <?php echo $this->get_render_attribute_string( 'price' ); ?>>
				<span class="currency"><?php echo $settings['currency_symbol'] ?></span>
				<?php echo esc_html( $settings['price'] ) ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'period' ); ?>><?php echo wp_kses_post( $settings['period'] ) ?></div>
			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php echo esc_html( $settings['button_text'] ) ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'konte-pricing-table' );
		view.addRenderAttribute( 'title', 'class', 'konte-pricing-table__title' );
		view.addRenderAttribute( 'description', 'class', 'konte-pricing-table__description' );
		view.addRenderAttribute( 'price', 'class', 'konte-pricing-table__price' );
		view.addRenderAttribute( 'period', 'class', 'konte-pricing-table__recurrence' );
		view.addRenderAttribute( 'button', 'class', 'button konte-pricing-table__button' );

		if ( settings.button_link.url ) {
			view.addRenderAttribute( 'button', 'href', settings.button_link.url );
		}

		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'description', 'basic' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<#
			if ( 'image' === settings.icon_type ) {
				#><img src="{{ settings.image.url }}" class="konte-pricing-table__image" width="{{ settings.image_width.size }}"><#
			} else if ( 'external' === settings.icon_type ) {
				#><img src="{{ settings.icon_url }}" class="konte-pricing-table__image" width="{{ settings.image_width.size }}"><#
			} else {
				var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' );
				if ( iconHTML && iconHTML.rendered ) { #>
					<span class="konte-icon konte-pricing-table__icon">{{{ iconHTML.value }}}</span>
				<#}
			}
			#>
			<h4 {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</h4>
			<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
			<div {{{ view.getRenderAttributeString( 'price' ) }}}>
				<span class="currency">{{{ settings.currency_symbol }}}</span>
				{{{ settings.price }}}
			</div>
			<div {{{ view.getRenderAttributeString( 'period' ) }}}>{{{ settings.period }}}</div>
			<a {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button_text }}}</a>
		</div>
		<?php
	}
}

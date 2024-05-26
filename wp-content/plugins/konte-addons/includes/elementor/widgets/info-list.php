<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Info_List extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-info-list';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Info List', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-editor-list-ul';
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
		return [ 'info list', 'list', 'konte' ];
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
			'section_info_list',
			[ 'label' => __( 'Info List', 'konte-addons' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'label_block' => true,
			]
		);

		$this->add_control(
			'info_list',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title'     => esc_html__( 'Label #1', 'konte-addons' ),
						'desc'      => esc_html__( 'This is the description #1', 'konte-addons' ),

					],
					[
						'title'     => esc_html__( 'Label #2', 'konte-addons' ),
						'desc'      => esc_html__( 'This is the description #2', 'konte-addons' ),
					],
					[
						'title'     => esc_html__( 'Label #3', 'konte-addons' ),
						'desc'      => esc_html__( 'This is the description #3', 'konte-addons' ),
					],
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_info_list',
			[
				'label' => __( 'Info List', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_align',
			[
				'label'           => esc_html__( 'Item Alignment', 'konte-addons' ),
				'type'            => Controls_Manager::CHOOSE,
				'options'         => [
					'flex'    => [
						'title' => esc_html__( 'Horizontal', 'konte-addons' ),
						'icon'  => 'fa fa-ellipsis-h',
					],
					'block' => [
						'title' => esc_html__( 'Vertical', 'konte-addons' ),
						'icon'  => 'fa fa-ellipsis-v',
					],
				],
				'desktop_default' => 'flex',
				'tablet_default'  => 'flex',
				'mobile_default'  => 'flex',
				'toggle'          => false,
				'selectors'       => [
					'{{WRAPPER}} .konte-info-list li' => 'display: {{VALUE}}',
				],
				'required'        => true,
				'device_args'     => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'selectors' => [
							'{{WRAPPER}} .konte-info-list li' => 'display: {{VALUE}}',
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'selectors' => [
							'{{WRAPPER}} .konte-info-list li' => 'display: {{VALUE}}',
						],
					],
				]
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Spacing', 'konte-addons' ),
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
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .konte-info-list li' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'label_style_heading',
			[
				'label'     => __( 'Label', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-info-list .info-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .konte-info-list .info-name',
			]
		);

		$this->add_responsive_control(
			'label_width',
			[
				'label' => __( 'Width', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-info-list .info-name' => 'flex-basis: {{SIZE}}{{UNIT}}',
				],
			]
		);

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
					'{{WRAPPER}} .konte-info-list .info-value'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .konte-info-list .info-value a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .konte-info-list .info-value',
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

		$this->add_render_attribute( 'wrapper', 'class', ['konte-info-list', 'konte-info-list--elementor'] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<ul>
				<?php
				if ( ! empty( $settings['info_list'] ) ) {
					foreach( $settings['info_list'] as $index => $item ) {
						$title_key = $this->get_repeater_setting_key( 'title', 'info_list', $index );
						$desc_key = $this->get_repeater_setting_key( 'desc', 'info_list', $index );

						$this->add_render_attribute(  $title_key, 'class', [ 'info-list__item-name', 'info-name' ] );
						$this->add_render_attribute( $desc_key, 'class', [ 'info-list__item-value', 'info-value' ] );

						$this->add_inline_editing_attributes( $title_key, 'none' );
						$this->add_inline_editing_attributes( $desc_key, 'none' );

						$value = wp_kses_post( $item['desc'] );

						if ( is_email( $value ) ) {
							$value = sprintf( '<a href="mailto:%s" class="konte-info-list__email-link">%s</a>', $value, $value );
						}
						?>
						<li class="info-list__item">
							<div <?php echo $this->get_render_attribute_string( $title_key ); ?>><?php echo esc_html( $item['title'] ) ?></div>
							<div <?php echo $this->get_render_attribute_string( $desc_key ); ?>><?php echo $value ?></div>
						</li>
						<?php
					}
				}
				?>
			</ul>
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
		<# view.addRenderAttribute( 'wrapper', 'class', ['konte-info-list', 'konte-info-list--elementor']); #>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<ul>
				<#
				_.each( settings.info_list, function( item, index ) {
					var titleKey = view.getRepeaterSettingKey( 'title', 'info_list',index ),
						descKey = view.getRepeaterSettingKey( 'desc', 'info_list',index );

					view.addRenderAttribute( titleKey, 'class', 'info-name' );
					view.addRenderAttribute( descKey, 'class', 'info-value' );

					view.addInlineEditingAttributes( titleKey, 'none' );
					view.addInlineEditingAttributes( descKey, 'none' );
					#>
					<li class="info-list__item">
						<div {{{ view.getRenderAttributeString( titleKey ) }}}>{{{ item.title }}}</div>
						<div {{{ view.getRenderAttributeString( descKey ) }}}>{{{ item.desc }}}</div>
					</li>
					<#
				} );
				#>
			</ul>
		</div>
		<?php
	}
}

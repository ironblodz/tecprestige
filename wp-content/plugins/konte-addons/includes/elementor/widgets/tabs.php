<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Tabs widget.
 */
class Tabs extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve tabs widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-tabs';
	}

	/**
	 * Get widget title.
	 * Retrieve tabs widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tabs', 'konte-addons' );
	}

	/**
	 * Get widget icon.
	 * Retrieve tabs widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs konte-elementor-widget';
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'tabs', 'accordion', 'toggle', 'konte' ];
	}

	/**
	 * Register tabs widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Tabs', 'konte-addons' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tab Title', 'konte-addons' ),
				'placeholder' => __( 'Tab Title', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'default' => __( 'Tab Content', 'konte-addons' ),
				'placeholder' => __( 'Tab Content', 'konte-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
				'dynamic' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Tabs Items', 'konte-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Tab #1', 'konte-addons' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
					],
					[
						'tab_title' => __( 'Tab #2', 'konte-addons' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'konte-addons' ),
					'vertical' => __( 'Vertical', 'konte-addons' ),
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => __( 'Tabs', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'navigation_width',
			[
				'label' => __( 'Navigation Width', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'after',
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-tabs__tabs' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
				],
			]
		);

		$this->add_control(
			'style_title_heading',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-tab__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label' => __( 'Active Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-tab__title.konte-tab--active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-tab__title a',
			]
		);

		$this->add_control(
			'title_align',
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
					'justify' => [
						'title' => __( 'Justified', 'konte-addons' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'condition' => [
					'type' => 'horizontal',
				],
			]
		);

		$this->add_control(
			'style_description_heading',
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
					'{{WRAPPER}} .konte-tab__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-tab__content',
			]
		);

		$this->add_control(
			'description_align',
			[
				'label' => __( 'Alignment', 'konte-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'initial' => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'konte-addons' ),
						'icon' => 'eicon-text-align-center',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .konte-tab__content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'type' => 'horizontal',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render tabs widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tabs = $settings['tabs'];

		$this->add_render_attribute( 'wrapper', [
			'class' => [ 'konte-tabs', 'konte-tabs--' . $settings['type'] ],
			'role'  => 'tablist'
		] );

		$this->add_render_attribute( 'tabs', 'class', [ 'konte-tabs__tabs' ] );

		if ( 'horizontal' == $settings['type'] ) {
			$this->add_render_attribute( 'tabs', 'class', 'konte-tabs__tabs--' . $settings['title_align'] );
		}

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'tabs' ); ?>>
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

					$this->add_render_attribute( $tab_title_setting_key, [
						'id' => 'konte-tab-title-' . $id_int . $tab_count,
						'class' => [ 'konte-tab__title', 'konte-tab__title--desktop' ],
						'data-tab' => $tab_count,
						'role' => 'tab',
						'aria-controls' => 'konte-tab-content-' . $id_int . $tab_count,
					] );
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>><a href="#konte-tab-content-<?php echo $id_int . $tab_count; ?>"><?php echo $item['tab_title']; ?></a></div>
				<?php endforeach; ?>
			</div>
			<div class="konte-tabs__content">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );
					$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

					$this->add_render_attribute( $tab_content_setting_key, [
						'id' => 'konte-tab-content-' . $id_int . $tab_count,
						'class' => [ 'konte-tab__content', 'clearfix' ],
						'data-tab' => $tab_count,
						'role' => 'tabpanel',
						'aria-labelledby' => 'konte-tab-title-' . $id_int . $tab_count,
					] );

					$this->add_render_attribute( $tab_title_mobile_setting_key, [
						'class' => [ 'konte-tab__title', 'konte-tab__title--mobile' ],
						'data-tab' => $tab_count,
						'role' => 'tab',
					] );

					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_title_mobile_setting_key ); ?>><?php echo esc_html( $item['tab_title'] ); ?></div>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render tabs widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<div class="konte-tabs konte-tabs--{{ settings.type }}" role="tablist">
			<#
			if ( settings.tabs ) {
				var tabindex = view.getIDInt().toString().substr( 0, 3 );

				view.addRenderAttribute( 'tabs', 'class', [ 'konte-tabs__tabs' ] );

				if ( 'horizontal' === settings.type ) {
					view.addRenderAttribute( 'tabs', 'class', 'konte-tabs__tabs--' + settings.title_align );
				}
				#>
				<div class="konte-tabs__tabs konte-tabs__tabs--{{ settings.title_align }}">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1;
						#>
						<div id="konte-tab-title-{{ tabindex + tabCount }}" class="konte-tab__title konte-tab__title--desktop" data-tab="{{ tabCount }}" role="tab" aria-controls="konte-tab-content-{{ tabindex + tabCount }}">
							<a href="#konte-tab-content-{{ tabindex + tabCount }}">{{{ item.tab_title }}}</a>
						</div>
					<# } ); #>
				</div>
				<div class="konte-tabs__content">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1,
							tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs',index );

						view.addRenderAttribute( tabContentKey, {
							'id': 'konte-tab-content-' + tabindex + tabCount,
							'class': [ 'konte-tab__content', 'clearfix', 'elementor-repeater-item-' + item._id ],
							'data-tab': tabCount,
							'role' : 'tabpanel',
							'aria-labelledby' : 'konte-tab-title-' + tabindex + tabCount
						} );

						view.addInlineEditingAttributes( tabContentKey, 'advanced' );
						#>
						<div class="konte-tab__title konte-tab__title--mobile" data-tab="{{ tabCount }}" role="tab">{{{ item.tab_title }}}</div>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
					<# } ); #>
				</div>
			<# } #>
		</div>
		<?php
	}
}

<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Message_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-message-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Message Box', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-alert';
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
		return [ 'message box', 'message', 'box', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_message_box',
			[ 'label' => __( 'Message Box', 'konte-addons' ) ]
		);

		$this->add_control(
			'type',
			[
				'label'   => __( 'Type', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'success',
				'options' => [
					'success' => __( 'Success', 'konte-addons' ),
					'info'    => __( 'Info', 'konte-addons' ),
					'danger'  => __( 'Danger', 'konte-addons' ),
					'warning' => __( 'Warning', 'konte-addons' ),
					'custom'  => __( 'Custom', 'konte-addons' ),
				],
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
					'type' => 'custom'
				]
			]
		);

		$this->add_control(
			'content',
			[
				'label'       => __( 'Content', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is the content', 'konte-addons' ),
				'placeholder' => __( 'Enter your content', 'konte-addons' ),
				'separator'   => 'before'
			]
		);

		$this->add_control(
			'closeable',
			[
				'label'        => __( 'Dismiss Button', 'konte-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'konte-addons' ),
				'label_off'    => __( 'Hide', 'konte-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->end_controls_section();

		// Style section.
		$this->start_controls_section(
			'section_style_message_box',
			[
				'label' => __( 'Message Box', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-message-box' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-message-box' => 'color: {{VALUE}}',
					'{{WRAPPER}} .konte-message-box__close' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .konte-message-box__content'
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

		$this->add_render_attribute( 'wrapper', 'class',
			[
				'konte-message-box',
				'konte-message-box--elementor',
				'konte-message-box--' . $settings['type'],
				$settings['type']
			]
		);
		$this->add_render_attribute( 'icon', 'class', [ 'konte-message-box__icon', 'svg-icon', 'icon-' . $settings['type'] ] );
		$this->add_render_attribute( 'content', 'class', 'konte-message-box__content' );

		$closeable_button = '';

		if ( $settings['closeable'] == 'yes' ) {
			$this->add_render_attribute( 'wrapper', 'class', 'closeable' );
			$closeable_button = '<a class="konte-message-box__close close" href="#"><span class="svg-icon icon-close"><svg width="24" height="24"><use xlink:href="#close"></use></svg></span></a>';
		}

		$this->add_inline_editing_attributes( 'content' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
				<?php
				if ( $settings['type'] == 'custom' ) {
					Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
				} else {
					$type = str_replace( array( 'info', 'danger' ), array( 'information', 'error' ), $settings['type'] );
					printf( '<svg width="40" height="40"><use xlink:href="#%s"></use></svg>', $type );
				}
				?>
			</span>
			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>><?php echo wp_kses_post( $settings['content'] ); ?></div>
			<?php echo $closeable_button; ?>
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
		view.addRenderAttribute( 'wrapper', 'class',
			[
				'konte-message-box',
				'konte-message-box--elementor',
				'konte-message-box--' + settings.type,
				settings.type
			]
		);
		view.addRenderAttribute( 'icon', 'class', [ 'konte-message-box__icon', 'svg-icon', 'icon-' + settings.type ] );
		view.addRenderAttribute( 'content', 'class', 'konte-message-box__content' );

		var closeable_button = '';
		if ( settings.closeable === 'yes' ) {
			view.addRenderAttribute( 'wrapper', 'class', 'closeable' );
			closeable_button = '<a class="konte-message-box__close close" href="#"><span class="svg-icon icon-close"><svg width="24" height="24"><use xlink:href="#close"></use></svg></span></a>';
		}

		view.addInlineEditingAttributes( 'content' );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<span {{{ view.getRenderAttributeString( 'icon' ) }}}>
				<#
				if ( settings.type === 'custom' ) {
					var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' );
					if ( iconHTML && iconHTML.rendered ) { #>
						{{{ iconHTML.value }}}
					<#}
				} else {
					var type;
					switch( settings.type ) {
						case 'info':
							type = 'information';
							break;
						case 'danger':
							type = 'error';
							break;
						default:
							type = settings.type;
					}
					#>
					<svg width="40" height="40"><use xlink:href="#{{ type }}"></use></svg>
					<#
				}
				#>
			</span>
			<div {{{ view.getRenderAttributeString( 'content' ) }}}>{{{ settings.content }}}</div>
			{{{ closeable_button }}}
		</div>
		<?php
	}
}

<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Countdown extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-countdown';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Countdown', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
		return [ 'countdown', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_countdown',
			[ 'label' => __( 'Countdown', 'konte-addons' ) ]
		);


		$this->add_control(
			'date',
			[
				'label' => __( 'Date', 'konte-addons' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date( 'Y/m/d', strtotime( '+5 days' ) ),
				'picker_options' => [
					'dateFormat' => 'Y/m/d H:i:S'
				],
			]
		);

		$this->add_control(
			'labels_heading',
			[
				'label' => __( 'Labels', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'day_text',
			[
				'label' => __( 'Day', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Days', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'hour_text',
			[
				'label' => __( 'Hour', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Hours', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'min_text',
			[
				'label' => __( 'Minute', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Minutes', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'sec_text',
			[
				'label' => __( 'Second', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Seconds', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Text Alignment', 'konte-addons' ),
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
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .konte-countdown--elementor' => 'text-align: {{VALUE}};',
				],
				'label_block' => false,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'konte-addons' ),
					'small'   => __( 'Small', 'konte-addons' ),
				],
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_style_countdown',
			[
				'label' => __( 'Countdown', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'digit_style_heading',
			[
				'label'     => __( 'Time', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'digit_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-countdown__box .time' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digit_typography',
				'selector' => '{{WRAPPER}} .konte-countdown__box .time',
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
					'{{WRAPPER}} .konte-countdown__box-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .konte-countdown__box-label',
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

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-countdown',
			'konte-countdown--elementor',
			'konte-countdown--' . $settings['size'],
			'type-' . $settings['size'],
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="timers" data-date="<?php echo esc_attr( $settings['date'] ); ?>">
				<div class="timer-day konte-countdown__box">
					<span class="time day"></span>
					<span class="konte-countdown__box-label"><?php echo esc_html( $settings['day_text'] ); ?></span>
				</div>
				<div class="timer-hour konte-countdown__box">
					<span class="time hour"></span>
					<span class="konte-countdown__box-label"><?php echo esc_html( $settings['hour_text'] ); ?></span>
				</div>
				<div class="timer-min konte-countdown__box">
					<span class="time min"></span>
					<span class="konte-countdown__box-label"><?php echo esc_html( $settings['min_text'] ); ?></span>
				</div>
				<div class="timer-secs konte-countdown__box">
					<span class="time secs"></span>
					<span class="konte-countdown__box-label"><?php echo esc_html( $settings['sec_text'] ); ?></span>
				</div>
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
		view.addRenderAttribute( 'wrapper', 'class', [
			'konte-countdown',
			'konte-countdown--elementor',
			'konte-countdown--' + settings.size,
			'type-' + settings.size
		] );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div class="timers" data-date="{{settings.date}}">
				<div class="timer-day konte-countdown__box"><span class="time day"></span><span class="konte-countdown__box-label">{{{settings.day_text}}}</span></div>
				<div class="timer-hour konte-countdown__box"><span class="time hour"></span><span class="konte-countdown__box-label">{{{settings.hour_text}}}</span></div>
				<div class="timer-min konte-countdown__box"><span class="time min"></span><span class="konte-countdown__box-label">{{{settings.min_text}}}</span></div>
				<div class="timer-secs konte-countdown__box"><span class="time secs"></span><span class="konte-countdown__box-label">{{{settings.sec_text}}}</span></div>
			</div>
		</div>
		<?php
	}
}

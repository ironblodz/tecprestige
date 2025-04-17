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
class Subscribe_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-subscribe-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Subscribe Box', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal konte-elementor-widget';
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
		return [ 'subscribe box', 'form', 'konte' ];
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
			'section_subscribe_box',
			[ 'label' => __( 'Subscribe Box', 'konte-addons' ) ]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_form(),
				'description' => __( 'Select the MailChimp form', 'konte-addons' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title & Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'show_label'  => false,
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'plugin-domain' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' => __( 'Layout 1', 'plugin-domain' ),
					'style2' => __( 'Layout 2', 'plugin-domain' ),
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_title_heading',
			[
				'label' => __( 'Title', 'plugin-name' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-subscribe-box__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-subscribe-box__title',
			]
		);

		$this->add_control(
			'style_description_heading',
			[
				'label' => __( 'Description', 'plugin-name' ),
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
					'{{WRAPPER}} .konte-subscribe-box__desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-subscribe-box__desc',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get Mailchipm Form
	 */
	protected function get_form() {
		$forms = get_posts( ['post_type' => 'mc4wp-form', 'numberposts' => -1] );
		$options = [];

		foreach( $forms as $form ) {
			$options[$form->ID] = esc_html( $form->post_title ) . " - ID: $form->ID";
		}

		return $options;
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', ['konte-subscribe-box', 'konte-subscribe-box--' . $settings['layout'] ] );

		$this->add_render_attribute( 'title', 'class', 'konte-subscribe-box__title' );
		$this->add_render_attribute( 'description', 'class', 'konte-subscribe-box__desc' );

		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'description' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo esc_html( $settings['title'] ) ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'description' ); ?>><?php echo wp_kses_post( $settings['description'] ) ?></div>
			<?php endif; ?>
			<?php echo do_shortcode( '[mc4wp_form id="' . $settings['form_id'] . '"]' ) ?>
		</div>
		<?php
	}
}

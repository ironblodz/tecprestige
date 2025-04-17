<?php
namespace KonteAddons\Elementor\Widgets;


use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Google Map widget
 */
class Testimonial_Carousel extends Carousel_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-testimonial-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonial Carousel', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel konte-elementor-widget';
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
		return [ 'testimonial carousel', 'carousel', 'testimonial', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_testimonial_carousel',
			[ 'label' => __( 'Testimonial Carousel', 'konte-addons' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'testimonial_content',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
			]
		);

		$repeater->add_control(
			'testimonial_image',
			[
				'label' => __( 'Choose Image', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => KONTE_ADDONS_URL . '/assets/images/person.jpg',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'testimonial_image',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$repeater->add_control(
			'testimonial_name',
			[
				'label' => __( 'Name', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'John Doe', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'testimonial_company',
			[
				'label' => __( 'Company/Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Company Name', 'konte-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'       => __( 'Testimonials', 'konte-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ testimonial_name }}}',
				'default' => [
					[
						'testimonial_name'    => __( 'Name #1', 'konte-addons' ),
						'testimonial_company' => __( 'Company #1', 'konte-addons' ),
					],
					[
						'testimonial_name'    => __( 'Name #2', 'konte-addons' ),
						'testimonial_company' => __( 'Company #2', 'konte-addons' ),
					],
					[
						'testimonial_name'    => __( 'Name #3', 'konte-addons' ),
						'testimonial_company' => __( 'Company #3', 'konte-addons' ),
					]
				],
				'separator' => 'before',
			]
		);

		$this->register_carousel_controls( [
			'slides_to_show'   => 2,
			'slides_to_scroll' => 1,
			'navigation'       => 'dots',
		] );

		$this->end_controls_section();

		// Additional Settings.
		$this->start_controls_section(
			'section_additional_settings',
			[ 'label' => esc_html__( 'Additional Settings', 'konte-addons' ) ]
		);

		$this->register_carousel_controls( [
			'infinite' => 'yes',
			'autoplay' => 'yes',
			'speed'    => 800,
		] );

		$this->end_controls_section();

		// Style.
		$this->start_controls_section(
			'section_testimonials_style',
			[
				'label' => __( 'Testimonial Carousel', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_controls( [
			'spacing'    => '',
			'arrow_type' => 'angle',
			'dots_align' => 'center',
		] );

		// Content.
		$this->add_control(
			'content_style_heading',
			[
				'label'     => __( 'Content', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__content',
			]
		);

		// Name
		$this->add_control(
			'name_style_heading',
			[
				'label'     => __( 'Name', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__name',
			]
		);

		// Company
		$this->add_control(
			'company_style_heading',
			[
				'label'     => __( 'Company/Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'company_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__company' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'company_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__company',
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

		if ( empty( $settings['testimonials'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-testimonial-carousel',
			'konte-testimonial-carousel--elementor',
			'konte-carousel',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
		] );

		if ( is_rtl() ) {
			$this->add_render_attribute( 'wrapper', 'dir', 'rtl' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="konte-testimonial-carousel__list swiper-wrapper">
				<?php foreach( $settings['testimonials'] as $index => $slide ) : ?>
					<?php
					$wrapper_key = $this->get_repeater_setting_key( 'slide_wrapper', 'testimonial', $index );
					$img_key     = $this->get_repeater_setting_key( 'image', 'testimonial', $index );
					$name_key    = $this->get_repeater_setting_key( 'name', 'testimonial', $index );
					$company_key = $this->get_repeater_setting_key( 'company', 'testimonial', $index );
					$desc_key    = $this->get_repeater_setting_key( 'desc', 'testimonial', $index );

					$this->add_render_attribute( $wrapper_key, 'class', [ 'konte-testimonial', 'swiper-slide' ] );
					$this->add_render_attribute( $img_key, 'class', 'konte-testimonial__photo' );
					$this->add_render_attribute( $name_key, 'class', 'konte-testimonial__name' );
					$this->add_render_attribute( $company_key, 'class', 'konte-testimonial__company' );
					$this->add_render_attribute( $desc_key, 'class', 'konte-testimonial__content' );
					?>
					<div <?php echo $this->get_render_attribute_string( $wrapper_key ) ?>>
						<div <?php echo $this->get_render_attribute_string( $img_key ) ?>>
							<?php
							if ( $slide['testimonial_image']['url'] ) {
								echo Group_Control_Image_Size::get_attachment_image_html( $slide, 'testimonial_image' );
							} else {
								echo '<img src="' . KONTE_ADDONS_URL . '/assets/images/person.jpg" alt="' . esc_attr( $slide['testimonial_name'] ) . '">';
							}
							?>
						</div>
						<div class="konte-testimonial__entry">
							<div <?php echo $this->get_render_attribute_string( $desc_key ) ?>><?php echo wp_kses_post( $slide['testimonial_content'] ) ?></div>
							<div class="konte-testimonial__author">
								<span <?php echo $this->get_render_attribute_string( $name_key ) ?>><?php echo esc_html( $slide['testimonial_name'] ) ?></span>
								<span class="konte-testimonial__author-separator">-</span>
								<span <?php echo $this->get_render_attribute_string( $company_key ) ?>><?php echo esc_html( $slide['testimonial_company'] ) ?></span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php $this->render_carousel_pagination(); ?>
		</div>
		<?php
		$this->render_carousel_navigation();
	}
}

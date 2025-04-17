<?php
namespace KonteAddons\Elementor\Widgets;


use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Google Map widget
 */
class Testimonial_Slideshow extends Carousel_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-testimonial-slideshow';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonial Slideshow', 'konte-addons' );
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
		return [ 'testimonial slideshow', 'slideshow', 'carousel', 'testimonial', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_testimonial_slideshow',
			[ 'label' => __( 'Testimonial Slideshow', 'konte-addons' ) ]
		);

		$this->add_control(
			'slideshow_title',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Testimonials', 'konte-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
			]
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

		// Carousel Style.
		$this->start_controls_section(
			'section_carousel_style',
			[
				'label' => __( 'Slideshow', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slideshow_title_style_heading',
			[
				'label'     => __( 'Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'slideshow_title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial-slideshow__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'slideshow_title_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial-slideshow__title',
			]
		);

		$this->add_control(
			'slideshow_pagination_style_heading',
			[
				'label'     => __( 'Pagination', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-carousel__pagination' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'slideshow_background',
				'label'     => __( 'Background', 'konte-addons' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .konte-testimonial-slideshow__content',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slideshow_padding',
			[
				'label' => __( 'Padding', 'konte-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial-slideshow__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// Testimonail Style.
		$this->start_controls_section(
			'section_testimonials_style',
			[
				'label' => __( 'Testimonials', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_style_heading',
			[
				'label'     => __( 'Content', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'konte-testimonial-slideshow' ] );
		$this->add_render_attribute( 'title', 'class', [ 'konte-testimonial-slideshow__title' ] );
		$this->add_render_attribute( 'photos', 'class', [ 'konte-testimonial-slideshow__photos', 'swiper-container' ] );
		$this->add_render_attribute( 'quotes', 'class', [ 'konte-testimonial-slideshow__quotes', 'swiper-container' ] );

		if ( is_rtl() ) {
			$this->add_render_attribute( 'photos', 'dir', 'rtl' );
			$this->add_render_attribute( 'quotes', 'dir', 'rtl' );
		}

		$photos = $list = [];

		foreach ( $settings['testimonials'] as $testimonial ) {
			if ( ! empty( $testimonial['testimonial_image']['id'] ) ) {
				$image_src = Group_Control_Image_Size::get_attachment_image_src( $testimonial['testimonial_image']['id'], 'image', [
					'image_size' => $settings['image_size'],
					'image_custom_dimension' => $settings['image_custom_dimension'],
				] );
			} else {
				$image_src = ! empty( $testimonial['testimonial_image']['url'] ) ? $testimonial['testimonial_image']['url'] : KONTE_ADDONS_URL . '/assets/images/person.jpg';
			}

			$photos[] = sprintf(
				'<img src="%s" alt="%s" class="konte-testimonial-slideshow__photo swiper-slide">',
				esc_url( $image_src ),
				esc_attr( $testimonial['testimonial_name'] )
			);

			$list[] = sprintf(
				'<div class="konte-testimonial-slideshow__quote konte-testimonial swiper-slide">
					<div class="konte-testimonial__content">%s</div>
					<div class="konte-testimonial__author">
						<span class="konte-testimonial__name">%s</span>
						<span class="konte-testimonial__company">%s</span>
					</div>
				</div>',
				wp_kses_post( $testimonial['testimonial_content'] ),
				esc_html( $testimonial['testimonial_name'] ),
				esc_html( $testimonial['testimonial_company'] )
			);
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'photos' ) ?>>
				<div class="swiper-wrapper">
					<?php echo implode( '', $photos ); ?>
				</div>
			</div>
			<div class="konte-testimonial-slideshow__content">
				<?php if ( $settings['slideshow_title'] ) : ?>
					<h4 class="konte-testimonial-slideshow__title"><?php echo esc_html( $settings['slideshow_title'] ) ?></h4>
				<?php endif; ?>
				<div <?php echo $this->get_render_attribute_string( 'quotes' ) ?>>
					<div class="swiper-wrapper">
						<?php echo implode( '', $list ); ?>
					</div>
				</div>
				<?php \KonteAddons\Elementor\Utils::carousel_pagination(); ?>
			</div>
		</div>
		<?php
	}
}

<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Controls_Manager;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Instagram_Carousel extends Carousel_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-instagram-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Instagram Carousel', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-instagram-post konte-elementor-widget';
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
		return [ 'instagram carousel', 'carousel', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_instagram_carousel',
			[ 'label' => __( 'Instagram Carousel', 'konte-addons' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'   => __( 'Number of Photos', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 16,
			]
		);

		$this->add_control(
			'image_shape',
			[
				'label'        => __( 'Image Size', 'konte-addons' ),
				'type'         => Controls_Manager::SELECT,
				'options' => [
					'cropped' => __( 'Square', 'konte-addons' ),
					'original' => __( 'Original', 'konte-addons' ),
				],
				'default' => 'cropped',
			]
		);

		$this->register_carousel_controls( [
			'slides_to_show'   => 6,
			'slides_to_scroll' => 2,
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

		// Style section.
		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => esc_html__( 'Instagram Carousel', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_controls( [
			'spacing'    => '',
			'arrow_type' => 'angle',
			'dots_align' => 'center',
		] );

		$this->end_controls_section();
	}


	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! function_exists( 'konte_get_instagram_images' ) || ! function_exists( 'konte_instagram_image' ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-instagram-carousel',
			'konte-instagram-carousel--elementor',
			'konte-instagram--' . $settings['image_shape'],
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
		] );

		if ( is_rtl() ) {
			$this->add_render_attribute( 'wrapper', 'dir', 'rtl' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php
			$medias  = konte_get_instagram_images( $settings['limit'] );

			if ( is_wp_error( $medias ) ) {
				echo $medias->get_error_message();
			} elseif ( is_array( $medias ) ) {
				$medias = array_slice( $medias, 0, $settings['limit'] );
				?>
				<ul class="konte-instagram__list swiper-wrapper">
					<?php foreach ( $medias as $media ) : ?>
						<li class="konte-instagram__item swiper-slide">
							<?php echo konte_instagram_image( $media, $settings['image_shape'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php
				$this->render_carousel_pagination();
			}
			?>
		</div>
		<?php
		$this->render_carousel_navigation();
	}
}

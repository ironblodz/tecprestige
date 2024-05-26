<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

/**
 * Icon Box widget
 */
class Posts_Carousel extends Carousel_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-posts-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Posts Carousel', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
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
		return [ 'posts carousel', 'posts', 'carousel', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_posts_carousel',
			[ 'label' => __( 'Posts Carousel', 'konte-addons' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'     => __( 'Number Of Posts', 'konte-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 9,
			]
		);

		$this->add_control(
			'category',
			[
				'label'     => __( 'Category', 'konte-addons' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => \KonteAddons\Elementor\Utils::get_terms_options(),
				'default'   => '',
				'multiple'  => true,
				'separator' => 'after',
			]
		);

		$this->register_carousel_controls( [
			'slides_to_show'   => 3,
			'slides_to_scroll' => 1,
			'navigation'       => 'dots',
		] );

		$this->end_controls_section();

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

		// Additional Settings.
		$this->start_controls_section(
			'section_style_posts_carousel',
			[
				'label' => esc_html__( 'Posts Carousel', 'konte-addons' ),
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

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-post-carousel',
			'konte-post-grid',
			'konte-post-carousel--elementor',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
		] );

		if ( is_rtl() ) {
			$this->add_render_attribute( 'wrapper', 'dir', 'rtl' );
		}

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $settings['limit'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if ( $settings['category'] ) {
			$args['category_name'] = implode( ',', $settings['category'] );
		}

		$posts = new \WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return;
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<div class="konte-post-carousel__list swiper-wrapper">
				<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
					<div <?php post_class( 'post swiper-slide' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php echo esc_url( get_permalink() ) ?>" class="konte-post-carousel__post-thumbnail post-thumbnail">
								<?php the_post_thumbnail( 'konte-post-thumbnail-shortcode' ); ?>
							</a>
						<?php endif; ?>
						<div class="konte-post-carousel__summary konte-post-grid__summary">
							<h5 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title() ?></a></h5>
							<div class="post-summary"><?php echo get_the_excerpt() ?></div>
							<a class="button alt" href="<?php the_permalink() ?>"><?php esc_html_e( 'Continue reading', 'konte-addons' ) ?></a>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
			<?php
			wp_reset_postdata();

			$this->render_carousel_pagination();
			?>
		</div>
		<?php
		$this->render_carousel_navigation();
	}
}
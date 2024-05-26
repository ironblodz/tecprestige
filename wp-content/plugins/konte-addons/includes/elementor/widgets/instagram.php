<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Instagram extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-instagram';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Instagram', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-instagram-gallery';
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
		return [ 'instagram', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_instagram',
			[ 'label' => __( 'Instagram', 'konte-addons' ) ]
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
			'columns',
			[
				'label'   => __( 'Columns', 'konte-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 8,
				'default' => 8,
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
			'konte-instagram',
			'konte-instagram--elementor',
			'konte-instagram--' . $settings['image_shape'],
		] );
		
		$this->add_render_attribute( 'list', 'class', [
			'konte-instagram__list',
			'columns-' . $settings['columns'],
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php
			$medias  = konte_get_instagram_images( $settings['limit'] );

			if ( is_wp_error( $medias ) ) {
				echo $medias->get_error_message();
			} elseif ( is_array( $medias ) ) {
				$medias = array_slice( $medias, 0, $settings['limit'] );
				?>
				<ul <?php echo $this->get_render_attribute_string( 'list' ) ?>>
					<?php foreach ( $medias as $media ) : ?>
						<li class="konte-instagram__item">
							<?php echo konte_instagram_image( $media, $settings['image_shape'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php
			}
			?>
		</div>
		<?php
	}
}
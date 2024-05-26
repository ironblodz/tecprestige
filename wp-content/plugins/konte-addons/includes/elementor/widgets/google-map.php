<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Google Map widget
 */
class Google_Map extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-google-map';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Google Map', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
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
		return [ 'google', 'map', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_google_map',
			[ 'label' => __( 'Google Map', 'konte-addons' ) ]
		);

		$this->add_control(
			'address',
			[
				'label' => __( 'Address', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => _x( 'New York', 'The default address for Google Map', 'konte-addons' ),
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'latlng',
			[
				'label' => __( 'Or enter coordinates', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Latitude, Longitude', 'konte-addons' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'height',
			[
				'label'     => __( 'Height', 'konte-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'default' => [
					'size' => 600,
				],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .konte-google-map' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom', 'konte-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color Scheme', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''           => __( 'Default', 'konte-addons' ),
					'grey'       => __( 'Grey', 'konte-addons' ),
					'inverse'    => __( 'Inverse', 'konte-addons' ),
					'vista-blue' => __( 'Vista Blue', 'konte-addons' ),
					'black'      => __( 'Classic Black', 'konte-addons' ),

				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'address',
			[
				'label'   => __( 'Address', 'konte-addons' ),
				'type'    => Controls_Manager::TEXT,
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$repeater->add_control(
			'latlng',
			[
				'label' => __( 'Or enter coordinates', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Latitude, Longitude', 'konte-addons' ),
				'render_type' => 'ui',
			]
		);

		$repeater->add_control(
			'info',
			[
				'label' => __( 'Infomation', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'separator' => 'before',
				'render_type' => 'ui',
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Marker Icon', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'icon' ],
			]
		);

		$this->add_control(
			'markers',
			[
				'label'         => __( 'Markers', 'konte-addons' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{ address }}}',
				'default'       => [
					[ 'address' => _x( 'New York', 'The default address for Google Map', 'konte-addons' ) ]
				],
				'separator'     => 'before',
				'render_type'   => 'ui',
				'prevent_empty' => false,
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

		$latlng = explode( ',', $settings['latlng'] );

		if ( count( $latlng ) > 1 ) {
			$coordinates = [
				'lat' => floatval( $latlng[0] ),
				'lng' => floatval( $latlng[1] ),
			];
		}

		$api_key = self::get_api_key();

		if ( ! isset( $coordinates ) ) {
			$coordinates = \Konte_Addons_Shortcodes::get_coordinates( $settings['address'], $api_key );
		}

		if ( ! empty( $coordinates['error'] ) ) {
			echo $coordinates['error'];
			return;
		}

		if ( isset( $coordinates['address'] ) ) {
			unset( $coordinates['address'] );
		}

		$this->add_render_attribute( 'map', 'id', 'konte-google-map-' . $this->get_id() );
		$this->add_render_attribute( 'map', 'class', ['konte-google-map', 'konte-google-map--elementor'] );
		$this->add_render_attribute( 'map', 'data-location', json_encode( $coordinates ) );

		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key );
		?>

		<div <?php echo $this->get_render_attribute_string( 'map' ) ?>></div>

		<?php if ( $settings['markers'] ) : ?>
			<div class="konte-google-map__markers" aria-hidden="true">
				<?php foreach ( $settings['markers'] as $marker ) : ?>
					<?php
					$info = $marker['info'];
					unset( $marker['info'] );
					?>
					<div class="konte-google-map__marker" data-marker="<?php echo esc_attr( json_encode( $marker ) ) ?>"><?php echo wpautop( $info ) ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif;
	}

	/**
	 * Render widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'map', 'class', [ 'konte-google-map', 'konte-google-map--elementor' ] );
		#>
		<div {{{ view.getRenderAttributeString( 'map' ) }}}></div>
		<# if ( settings.markers ) { #>
			<div class="konte-google-map__markers">
				<# for ( var i = 0; i < settings.markers.length; i++ ) { #>
					<#
					var marker = settings.markers[i];
					var info = marker.info;
					delete marker.info;
					#>
					<div class="konte-google-map__marker" data-marker="{{ JSON.stringify( marker ) }}"><p>{{{ info.replace(/(?:\r\n|\r|\n)/g, '<br>') }}}</p></div>
				<# } #>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Get google map api key from theme option.
	 *
	 * @return string
	 */
	public static function get_api_key() {
		return function_exists( 'konte_get_option' ) ? konte_get_option( 'api_google_map' ) : get_theme_mod( 'api_google_map' );
	}
}

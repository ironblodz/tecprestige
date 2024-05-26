<?php
namespace KonteAddons\Elementor\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Base\Module;
use Elementor\Controls_Manager;
use Elementor\Core\DocumentTypes\PageBase as PageBase;

class Display_Settings extends Module {
	/**
	 * Get module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'display-settings';
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ] );

		add_action( 'elementor/documents/register_controls', [ $this, 'register_display_controls' ] );

		add_action( 'elementor/document/after_save', [ $this, 'sync_settings_from_elementor' ], 10, 2 );
		add_action( 'save_post', [ $this, 'sync_settings_from_page' ], 10, 2 );
		add_action( 'updated_page_meta', [ $this, 'sync_settings_to_elementor' ], 10, 4 );
	}

	/**
	 * Localize some default settings from the theme.
	 *
	 * @param array $settings
	 */
	public function localize_settings( $settings ) {
		$theme_settings = [
			'headerBackground'      => get_theme_mod( 'header_background' ),
			'headerBackgroundColor' => get_theme_mod( 'header_background_color' ),
			'headerTextColor'       => get_theme_mod( 'header_textcolor' ),
			'footerBackground'      => get_theme_mod( 'footer_background' ),
			'footerTextColor'       => get_theme_mod( 'footer_textcolor' ),
			'contentContainerClass' => 'container',
		];

		$settings['KonteThemeSettings'] = $theme_settings;

		return $settings;
	}

	/**
	 * Register display controls.
	 *
	 * @param object $document
	 */
	public function register_display_controls( $document ) {
		if ( ! $document instanceof PageBase ) {
			return;
		}

		$post_type = get_post_type( $document->get_main_id() );

		if ( 'page' != $post_type ) {
			return;
		}

		$this->register_split_template_controls( $document );

		$this->register_subtitle_control( $document );

		$this->register_header_controls( $document );

		$this->register_page_header_controls( $document );

		$this->register_content_controls( $document );

		$this->register_footer_controls( $document );
	}

	/**
	 * Register template controls for the Splited Content template.
	 *
	 * @param object $document
	 */
	protected function register_split_template_controls( $document ) {
		$document->start_injection( [
			'of' => 'template',
		] );

		$document->add_control(
			'split_content_position',
			[
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Content Position', 'konte-addons' ),
				'default' => 'right',
				'options' => [
					'left' => __( 'Left', 'konte-addons' ),
					'right' => __( 'Right', 'konte-addons' ),
				],
				'condition' => [
					'template' => 'templates/split.php',
				],
			]
		);

		$document->end_injection();
	}

	/**
	 * Register template control of the subtitle.
	 *
	 * @param object $document
	 */
	protected function register_subtitle_control( $document ) {
		$document->start_injection( [
			'of' => 'post_title',
		] );

		$document->add_control(
			'subtitle',
			[
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => __( 'Subtitle', 'konte-addons' ),
				'rows'        => 3,
				'lable_block' => true,
			]
		);

		$document->end_injection();
	}

	/**
	 * Register template controls of the site header.
	 *
	 * @param object $document
	 */
	protected function register_header_controls( $document ) {
		$document->start_controls_section(
			'section_site_header',
			[
				'label' => __( 'Header', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$document->add_control(
			'header_layout',
			[
				'label'       => esc_html__( 'Layout', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'v1'      => esc_html__( 'Header V1', 'konte-addons' ),
					'v2'      => esc_html__( 'Header V2', 'konte-addons' ),
					'v3'      => esc_html__( 'Header V3', 'konte-addons' ),
					'v4'      => esc_html__( 'Header V4', 'konte-addons' ),
					'v5'      => esc_html__( 'Header V5', 'konte-addons' ),
					'v6'      => esc_html__( 'Header V6', 'konte-addons' ),
					'v7'      => esc_html__( 'Header V7', 'konte-addons' ),
					'v8'      => esc_html__( 'Header V8', 'konte-addons' ),
					'v9'      => esc_html__( 'Header V9', 'konte-addons' ),
					'v10'     => esc_html__( 'Header V10', 'konte-addons' ),
				],
				'default'     => 'default',
			]
		);

		$document->end_controls_section();

		$document->start_controls_section(
			'section_site_header_style',
			[
				'label' => __( 'Header', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$document->add_control(
			'header_background',
			[
				'label'       => esc_html__( 'Background', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default'     => esc_html__( 'Default', 'konte-addons' ),
					'dark'        => esc_html__( 'Dark', 'konte-addons' ),
					'light'       => esc_html__( 'White', 'konte-addons' ),
					'transparent' => esc_html__( 'Transparent', 'konte-addons' ),
					'custom'      => esc_html__( 'Custom', 'konte-addons' ),
				],
				'default'     => 'default',
			]
		);

		$document->add_control(
			'header_background_color',
			[
				'label'       => esc_html__( 'Background Color', 'konte-addons' ),
				'show_label'  => false,
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'header_background' => 'custom',
				],
				'selectors' => [
					'#masthead' => 'background-color: {{VALUE}}',
				],
			]
		);

		$document->add_control(
			'header_textcolor',
			[
				'label'       => esc_html__( 'Text Color', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'dark'    => esc_html__( 'Dark', 'konte-addons' ),
					'light'   => esc_html__( 'White', 'konte-addons' ),
				],
				'default'     => 'default',
				'condition'   => [
					'header_background' => ['transparent', 'custom'],
				],
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Register template controls of the page header.
	 *
	 * @param object $document
	 */
	protected function register_page_header_controls( $document ) {
		$document->start_controls_section(
			'section_page_header_settings',
			[
				'label' => __( 'Page Header ', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$document->add_control(
			'page_title_display',
			[
				'label'       => esc_html__( 'Page Title Display', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default'     => esc_html__( 'Default', 'konte-addons' ),
					'none'        => esc_html__( 'Hide Page Title', 'konte-addons' ),
					'above'       => esc_html__( 'About Featured Image', 'konte-addons' ),
					'front'       => esc_html__( 'In Front Of Featured Image', 'konte-addons' ),
				],
				'default'     => 'default',
			]
		);

		$document->add_control(
			'page_header_content',
			[
				'label'   => esc_html__( 'Page Header Content', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Featured image', 'konte-addons' ),
					'video'   => esc_html__( 'Video', 'konte-addons' ),
					'map'     => esc_html__( 'Google Map', 'konte-addons' ),
					'hidden'  => esc_html__( 'Do not show', 'konte-addons' ),
				],
				'default' => 'default',
			]
		);

		$document->add_control(
			'page_header_content_video',
			[
				'label'       => esc_html__( 'Youtube Video URL', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [
					'page_header_content' => 'video',
				],
			]
		);

		$document->add_control(
			'page_header_content_mute',
			[
				'label'     => esc_html__( 'Mute The Video', 'konte-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'page_header_content' => 'video',
				],
			]
		);

		$document->add_control(
			'page_header_content_map_address',
			[
				'label'       => esc_html__( 'Address', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'page_header_content' => 'map',
				],
			]
		);

		$document->add_control(
			'page_header_content_map_coordinates',
			[
				'label'       => esc_html__( 'Or Coordinates', 'konte-addons' ),
				'placeholder' => esc_attr__( 'Latitude, Longitude', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'page_header_content' => 'map',
				],
			]
		);

		$document->add_control(
			'page_header_content_map_zoom',
			[
				'label'       => esc_html__( 'Zoom', 'konte-addons' ),
				'type'        => Controls_Manager::SLIDER,
				'default' => [ 'size' => 12 ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					]
				],
				'condition' => [
					'page_header_content' => 'map',
				],
			]
		);

		$document->add_control(
			'page_header_content_map_marker',
			[
				'label'     => esc_html__( 'Show the map marker', 'konte-addons' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'page_header_content' => 'map',
				],
			]
		);

		$document->end_controls_section();

		$document->start_controls_section(
			'section_page_header_style',
			[
				'label' => __( 'Page Header ', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$document->add_control(
			'page_header_height',
			[
				'label'   => esc_html__( 'Page Header Height', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'full'    => esc_html__( 'Fit To Screen', 'konte-addons' ),
					'manual'  => esc_html__( 'Custom', 'konte-addons' ),
				],
				'default'   => 'default',
			]
		);

		$document->add_control(
			'page_header_height_custom',
			[
				'label'      => esc_html__( 'Page Header Height', 'konte-addons' ),
				'show_label' => false,
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 2400,
					],
				],
				'default'   => [ 'size' => 800, 'unit' => 'px' ],
				'condition' => [
					'page_header_height' => 'manual',
				],
				'selectors' => [
					'.page-header.title-front, .page-header .entry-thumbnail' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$document->add_control(
			'page_title_textcolor',
			[
				'label'   => esc_html__( 'Page Title Color', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'dark'    => esc_html__( 'Dark', 'konte-addons' ),
					'light'   => esc_html__( 'White', 'konte-addons' ),
				],
				'default' => 'default',
				'condition' => [
					'page_title_display' => 'front',
				],
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Register template controls of the footer.
	 *
	 * @param object $document
	 */
	protected function register_footer_controls( $document ) {
		if ( function_exists( 'konte_footer_items_option' ) ) {
			$document->start_controls_section(
				'section_site_footer_settings',
				[
					'label' => __( 'Footer', 'konte-addons' ),
					'tab'   => Controls_Manager::TAB_SETTINGS,
					'condition'   => [
						'template' => 'templates/split.php',
					],
				]
			);


			$document->add_control(
				'footer_custom_content',
				[
					'label'       => esc_html__( 'Custom Footer Layout', 'konte-addons' ),
					'description' => esc_html__( 'Change the content of site footer', 'konte-addons' ),
					'type'        => Controls_Manager::SWITCHER,
				]
			);

			$document->add_control(
				'footer_custom_content_left',
				[
					'label'     => esc_html__( 'Footer Left', 'konte-addons' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'copyright',
					'options'   => konte_footer_items_option(),
					'condition' => [
						'footer_custom_content' => 'yes',
					],
				]
			);

			$document->add_control(
				'footer_custom_content_right',
				[
					'label'     => esc_html__( 'Footer Right', 'konte-addons' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'menu',
					'options'   => konte_footer_items_option(),
					'condition' => [
						'footer_custom_content' => 'yes',
					],
				]
			);

			$document->end_controls_section();
		}

		$document->start_controls_section(
			'section_site_footer_style',
			[
				'label' => __( 'Footer', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$document->add_control(
			'footer_background',
			[
				'label'   => esc_html__( 'Background', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'     => esc_html__( 'Default', 'konte-addons' ),
					'dark'        => esc_html__( 'Dark', 'konte-addons' ),
					'light'       => esc_html__( 'Light', 'konte-addons' ),
					'transparent' => esc_html__( 'Transparent', 'konte-addons' ),
					'custom'      => esc_html__( 'Custom', 'konte-addons' ),
				],
				'default' => 'default',
			]
		);

		$document->add_control(
			'footer_background_color',
			[
				'label'      => esc_html__( 'Background Color', 'konte-addons' ),
				'show_lable' => false,
				'type'       => Controls_Manager::COLOR,
				'condition'  => [
					'footer_background' => 'custom',
				],
				'selectors' => [
					'#colophon' => 'background-color: {{VALUE}}'
				],
			]
		);

		$document->add_control(
			'footer_textcolor',
			[
				'label'   => esc_html__( 'Text Color', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'dark'    => esc_html__( 'Dark', 'konte-addons' ),
					'light'   => esc_html__( 'White', 'konte-addons' ),
				],
				'default'   => 'default',
				'condition' => [
					'footer_background' => ['transparent', 'custom'],
				],
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Register template controls of the content area.
	 *
	 * @param object $document
	 */
	protected function register_content_controls( $document ) {
		$document->start_controls_section(
			'section_content_area_settings',
			[
				'label' => __( 'Content Area', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$document->add_control(
			'content_container_width',
			[
				'label'   => esc_html__( 'Content Width', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'  => esc_html__( 'Default', 'konte' ),
					'standard' => esc_html__( 'Standard', 'konte' ),
					'large'    => esc_html__( 'Large', 'konte' ),
					'wide'     => esc_html__( 'Wide', 'konte' ),
					'wider'    => esc_html__( 'Wider', 'konte' ),
					'full'     => esc_html__( 'Full Width', 'konte' ),
				],
				'default' => 'default',
			]
		);

		$document->add_control(
			'content_top_spacing',
			[
				'label'   => esc_html__( 'Top Spacing', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'none'    => esc_html__( 'No spacing', 'konte-addons' ),
					'custom'  => esc_html__( 'Custom', 'konte-addons' ),
				],
				'condition' => [
					'header_background!' => 'transparent',
				],
			]
		);

		$document->add_control(
			'content_top_spacing_custom',
			[
				'label'      => esc_html__( 'Custom Top Spacing', 'konte-addons' ),
				'show_label' => false,
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 2400,
					],
				],
				'default'   => [ 'size' => 60, 'unit' => 'px' ],
				'condition' => [
					'content_top_spacing' => 'custom',
					'header_background!' => 'transparent',
				],
				'selectors' => [
					'.site-content' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$document->add_control(
			'content_bottom_spacing',
			[
				'label'   => esc_html__( 'Bottom Spacing', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'konte-addons' ),
					'none'    => esc_html__( 'No spacing', 'konte-addons' ),
					'custom'  => esc_html__( 'Custom', 'konte-addons' ),
				],
				'default' => 'default',
			]
		);

		$document->add_control(
			'content_bottom_spacing_custom',
			[
				'label'      => esc_html__( 'Custom Bottom Spacing', 'konte-addons' ),
				'show_label' => false,
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 2400,
					],
				],
				'default'   => [ 'size' => 60, 'unit' => 'px' ],
				'condition' => [
					'content_bottom_spacing' => 'custom',
				],
				'selectors' => [
					'.site-content' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Map element settings to theme settings.
	 *
	 * @param \Elementor\Core\Base\Document $document
	 * @param array $data
	 */
	public function sync_settings_from_elementor( $document, $data ) {
		if ( ! isset( $data['settings'] ) ) {
			return;
		}

		$post_id  = $document->get_main_id();
		$settings = $this->get_settings_map();

		foreach ( $settings as $elementor_setting => $theme_setting ) {
			if ( isset( $data['settings'][ $elementor_setting ] ) ) {
				$value = $data['settings'][ $elementor_setting ];
			} else {
				$control = $document->get_controls( $elementor_setting );
				$value = isset( $control['default'] ) ? $control['default'] : '';
			}

			$value = 'default' === $value ? '' : $value;
			$value = is_array( $value ) && isset( $value['size'] ) ? $value['size'] : $value;

			update_post_meta( $post_id, $theme_setting, $value );
		}
	}

	/**
	 * Map theme settings to Elementor page settings.
	 *
	 * @param int $post_id
	 * @param object $post
	 */
	public function sync_settings_from_page( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || 'page' != $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['nonce_display-settings'] ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$this->convert_settings_to_elementor( $post_id, 'request' );
	}

	/**
	 * Convert the theme settings to document settings of Elementor
	 * when the page builder was changed to Elementor.
	 *
	 * @param int    $meta_id     ID of updated metadata entry.
	 * @param int    $object_id   ID of the object metadata is for. It is the page ID.
	 * @param string $meta_key    Metadata key.
	 * @param mixed  $meta_value Metadata value. Serialized if non-scalar.
	 */
	public function sync_settings_to_elementor( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( '_elementor_edit_mode' != $meta_key ) {
			return;
		}

		$this->convert_settings_to_elementor( $object_id, 'db' );
	}

	/**
	 * Convert theme settings to Elementor document settings.
	 *
	 * @param int $post_id
	 * @param string $source The setting source. It is 'request' or 'db'.
	 */
	protected function convert_settings_to_elementor( $post_id, $source = 'request' ) {
		$settings        = $this->get_settings_map();
		$page_settings   = [];
		$slider_settings = [
			'page_header_height_custom',
			'page_header_content_map_zoom',
			'content_top_spacing_custom',
			'content_bottom_spacing_custom',
		];
		$checkbox_settings = [
			'footer_custom_content',
		];
		$text_settings = [
			'subtitle',
			'page_header_content_video',
			'page_header_content_map_address',
			'page_header_content_map_coordinates',
		];

		foreach ( $settings as $elementor_setting => $theme_setting ) {
			// Metabox uses a empty string '' as the default value, while Elementor uses 'default' value.
			if ( 'request' == $source ) {
				$value = ! empty( $_POST[ $theme_setting ] ) ? $_POST[ $theme_setting ] : 'default';
			} else {
				$value = get_post_meta( $post_id, $theme_setting, true );
				$value = ! empty( $value ) ? $value : 'default';
			}

			// Double check for checkboxes and text fields.
			if ( in_array( $elementor_setting, $checkbox_settings ) ) {
				$value = 'default' == $value ? '' : 'yes';
			} elseif ( in_array( $elementor_setting, $text_settings ) ) {
				$value = 'default' == $value ? '' : $value;
			}

			if ( in_array( $elementor_setting, $slider_settings ) ) {
				$page_settings[ $elementor_setting ]['size'] = $value;
			} else {
				$page_settings[ $elementor_setting ] = $value;
			}
		}

		if ( ! empty( $page_settings ) ) {
			$elementor_page_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
			$elementor_page_manager->save_settings( $page_settings, $post_id );
		}
	}

	/**
	 * Get the array of mapping setting names.
	 *
	 * @return array
	 */
	protected function get_settings_map() {
		return [
			'split_content_position'              => 'split_content_position',
			'subtitle'                            => '_subtitle',
			'header_layout'                       => 'header_layout',
			'header_background'                   => 'header_background',
			'header_background_color'             => 'header_background_color',
			'header_textcolor'                    => 'header_textcolor',
			'page_title_display'                  => 'page_title_display',
			'page_title_textcolor'                => 'page_title_color',
			'page_header_height'                  => 'page_header_height',
			'page_header_height_custom'           => 'page_header_manual_height',
			'page_header_content'                 => 'page_featured_content',
			'page_header_content_video'           => 'page_featured_video',
			'page_header_content_video_mute'      => 'page_featured_video_mute',
			'page_header_content_map_address'     => 'page_featured_map_address',
			'page_header_content_map_coordinates' => 'page_featured_map_coordinates',
			'page_header_content_map_zoom'        => 'page_featured_map_zoom',
			'page_header_content_map_marker'      => 'page_featured_map_marker',
			'footer_background'                   => 'footer_background',
			'footer_background_color'             => 'footer_background_color',
			'footer_textcolor'                    => 'footer_textcolor',
			'footer_custom_content'               => 'split_content_custom_footer',
			'footer_custom_content_left'          => 'split_content_footer_left',
			'footer_custom_content_right'         => 'split_content_footer_right',
			'content_container_width'             => 'content_container_width',
			'content_top_spacing'                 => 'top_spacing',
			'content_top_spacing_custom'          => 'top_padding',
			'content_bottom_spacing'              => 'bottom_spacing',
			'content_bottom_spacing_custom'       => 'bottom_padding',
		];
	}
}

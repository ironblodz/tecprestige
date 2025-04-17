<?php
/**
 * Integrate with WPBakery Page Builder.
 */

class Konte_Addons_JS_Composer {
	/**
	 * Taxonomy terms
	 *
	 * @var array
	 */
	protected static $terms;

	/**
	 * Initialize.
	 */
	public static function init() {
		remove_action( 'admin_bar_menu', array( vc_frontend_editor(), 'adminBarEditLink' ), 1000 );

		if ( function_exists( 'vc_license' ) ) {
			remove_action( 'admin_notices', array( vc_license(), 'adminNoticeLicenseActivation' ) );
		}

		include_once( KONTE_ADDONS_DIR . 'includes/shortcodes/banner.php' );
	}

	/**
	 * Customize default shortcodes of WPBakery Page Builder.
	 */
	public static function customize_elements() {
		if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, array( __CLASS__, 'element_class' ), 10, 3 );
		}

		// Update vc_section params.
		$param = WPBMap::getParam( 'vc_section', 'full_width' );
		$param['value'][esc_attr__( 'Stretch left', 'konte-addons' )] = 'stretch_left';
		$param['value'][esc_attr__( 'Stretch left no padding', 'konte-addons' )] = 'stretch_left_no_padding';
		$param['value'][esc_attr__( 'Stretch right', 'konte-addons' )] = 'stretch_right';
		$param['value'][esc_attr__( 'Stretch right no padding', 'konte-addons' )] = 'stretch_right_no_padding';

		vc_update_shortcode_param( 'vc_section', $param );

		// Update vc_row params.
		$param = WPBMap::getParam( 'vc_row', 'gap' );
		$param['value'][esc_attr__( '40px', 'konte-addons' )] = '40';
		$param['value'][esc_attr__( '60px', 'konte-addons' )] = '60';
		$param['value'][esc_attr__( '120px', 'konte-addons' )] = '120';

		vc_update_shortcode_param( 'vc_row', $param );

		// Update vc_custom_heading
		vc_add_param( 'vc_custom_heading', array(
			'heading'     => esc_html__( 'Separate Link', 'konte-addons' ),
			'description' => esc_html__( 'Do not wrap heading text with the link tag. Display the link separately. It requires the link to has title.', 'konte-addons' ),
			'type'        => 'checkbox',
			'param_name'  => 'separate_link',
			'value'       => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
			'weight'      => 0,
		) );

		// Update vc_toggle params.
		vc_remove_param( 'vc_toggle', 'style' );
		vc_remove_param( 'vc_toggle', 'color' );
		vc_remove_param( 'vc_toggle', 'size' );
	}

	/**
	 * Undocumented function
	 */
	public static function add_templates() {
		$templates = array(
			'product-desc' => esc_html__( 'Product Description', 'konte-addons' ),
			'about-v1'     => esc_html__( 'About Us v1', 'konte-addons' ),
			'about-v2'     => esc_html__( 'About Us v2', 'konte-addons' ),
			'contact-v1'   => esc_html__( 'Contact Us v1', 'konte-addons' ),
			'contact-v2'   => esc_html__( 'Contact Us v2', 'konte-addons' ),
			'faq'          => esc_html__( 'FAQ Page', 'konte-addons' ),
			'team'         => esc_html__( 'Our Team Page', 'konte-addons' ),
			'home-v2'      => esc_html__( 'Home v2', 'konte-addons' ),
			'home-v3'      => esc_html__( 'Home v3', 'konte-addons' ),
			'home-v5'      => esc_html__( 'Home v5', 'konte-addons' ),
			'home-v6'      => esc_html__( 'Home v6', 'konte-addons' ),
			'home-v7'      => esc_html__( 'Home v7', 'konte-addons' ),
			'home-v9'      => esc_html__( 'Home v9', 'konte-addons' ),
			'home-v11'     => esc_html__( 'Home v11', 'konte-addons' ),
			'home-v12'     => esc_html__( 'Home v12', 'konte-addons' ),
			'home-v13'     => esc_html__( 'Home v13', 'konte-addons' ),
		);

		$weight = 0;

		foreach ( $templates as $template => $name ) {
			$file = KONTE_ADDONS_DIR . 'includes/wpb-templates/' . $template . '.php';

			if ( ! file_exists( $file ) ) {
				continue;
			}

			$data = array(
				'name'         => $name,
				'weight'       => $weight++,
				'image_path'   => KONTE_ADDONS_URL . 'assets/images/wpb-templates/' . $template . '.jpg',
				'custom_class' => 'wpb-template-' . $template,
			);

			$data['content'] = include $file;

			vc_add_default_templates( $data );
		}
	}

	/**
	 * Add custom classes to shortcodes.
	 *
	 * @param string $class
	 * @param string $base
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function element_class( $class, $base, $atts ) {
		if ( 'vc_tta_accordion' == $base ) {
			$class .= ' icon-' . $atts['c_position'];
		}

		if ( 'vc_section' == $base && ! empty( $atts['full_width'] ) ) {
			$class .= ' section_' . $atts['full_width'];

			if ( in_array( $atts['full_width'], array( 'stretch_left', 'stretch_left_no_padding', 'stretch_right', 'stretch_right_no_padding' ) ) ) {
				$class .= ' section_stretch_side';
			}
		}

		return $class;
	}

	/**
	 * Map theme's shortcodes.
	 */
	public static function map_shortcodes() {
		// Pricing Table.
		vc_map( array(
			'name'        => esc_html__( 'Pricing Table', 'konte-addons' ),
			'description' => esc_html__( 'Eye catching pricing table', 'konte-addons' ),
			'base'        => 'konte_pricing_table',
			'icon'        => self::get_icon( 'pricing-table.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Plan Name', 'konte-addons' ),
					'admin_label' => true,
					'param_name'  => 'title',
					'type'        => 'textfield',
				),
				array(
					'heading'    => esc_html__( 'Plan Image', 'konte-addons' ),
					'param_name' => 'image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'Image Link', 'konte-addons' ),
					'param_name'  => 'image_src',
					'type'        => 'textfield',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Image Width', 'konte-addons' ),
					'description' => esc_html__( 'Force image width if you want to resize it. Leave it empty to use the full size.', 'konte-addons' ),
					'param_name'  => 'image_width',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Price', 'konte-addons' ),
					'description' => esc_html__( 'Plan pricing', 'konte-addons' ),
					'param_name'  => 'price',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Currency', 'konte-addons' ),
					'description' => esc_html__( 'Price currency', 'konte-addons' ),
					'param_name'  => 'currency',
					'type'        => 'textfield',
					'value'       => '$',
				),
				array(
					'heading'     => esc_html__( 'Recurrence', 'konte-addons' ),
					'description' => esc_html__( 'Recurring payment unit', 'konte-addons' ),
					'param_name'  => 'recurrence',
					'type'        => 'textfield',
					'value'       => esc_html__( 'Per Month', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Description', 'konte-addons' ),
					'description' => esc_html__( 'Plan description.', 'konte-addons' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
				),
				array(
					'heading'    => esc_html__( 'Button Text', 'konte-addons' ),
					'param_name' => 'button_text',
					'type'       => 'textfield',
					'value'      => esc_html__( 'Get Started', 'konte-addons' ),
				),
				array(
					'heading'    => esc_html__( 'Button Link', 'konte-addons' ),
					'param_name' => 'button_link',
					'type'       => 'textfield',
					'value'      => '',
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
				),
			),
		) );

		// Google Map
		vc_map( array(
			'name'        => esc_html__( 'Google Map', 'konte-addons' ),
			'description' => esc_html__( 'Google map in style', 'konte-addons' ),
			'base'        => 'konte_map',
			'icon'        => self::get_icon( 'map.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Address', 'konte-addons' ),
					'description' => esc_html__( 'Enter address for map marker. If this option does not work correctly, use the Latitude and Longitude options bellow.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'admin_label' => true,
				),
				array(
					'heading'          => esc_html__( 'Latitude', 'konte-addons' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-xs-6',
					'param_name'       => 'lat',
					'admin_label'      => true,
				),
				array(
					'heading'          => esc_html__( 'Longitude', 'konte-addons' ),
					'type'             => 'textfield',
					'param_name'       => 'lng',
					'edit_field_class' => 'vc_col-xs-6',
					'admin_label'      => true,
				),
				array(
					'heading'     => esc_html__( 'Marker', 'konte-addons' ),
					'description' => esc_html__( 'Upload custom marker icon or leave this to use default one.', 'konte-addons' ),
					'param_name'  => 'marker',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Width', 'konte-addons' ),
					'description' => esc_html__( 'Map width in pixel or percentage.', 'konte-addons' ),
					'param_name'  => 'width',
					'type'        => 'textfield',
					'value'       => '100%',
				),
				array(
					'heading'     => esc_html__( 'Height', 'konte-addons' ),
					'description' => esc_html__( 'Map height in pixel.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'height',
					'value'       => '600px',
				),
				array(
					'heading'     => esc_html__( 'Zoom', 'konte-addons' ),
					'description' => esc_html__( 'Enter zoom level. The value is between 1 and 20.', 'konte-addons' ),
					'param_name'  => 'zoom',
					'type'        => 'textfield',
					'value'       => '12',
				),
				array(
					'heading'          => esc_html__( 'Color', 'konte-addons' ),
					'description'      => esc_html__( 'Select map color style', 'konte-addons' ),
					'edit_field_class' => 'vc_col-xs-12 vc_colored-dropdown',
					'param_name'       => 'color',
					'type'             => 'dropdown',
					'std'              => 'blue',
					'value'            => array(
						esc_html__( 'Default', 'konte-addons' )       => '',
						esc_html__( 'Blue', 'konte-addons' )          => 'blue',
						esc_html__( 'Vista Blue', 'konte-addons' )    => 'vista-blue',
						esc_html__( 'Grey', 'konte-addons' )          => 'grey',
						esc_html__( 'Classic Black', 'konte-addons' ) => 'black',
					),
				),
				array(
					'heading'     => esc_html__( 'Content', 'konte-addons' ),
					'description' => esc_html__( 'Enter content of info window.', 'konte-addons' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
					'holder'      => 'div',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Carousel.
		vc_map( array(
			'name'                    => esc_html__( 'Carousel', 'konte-addons' ),
			'description'             => esc_html__( 'Carousel of items', 'konte-addons' ),
			'base'                    => 'konte_carousel',
			'as_parent'               => array( 'only' => 'konte_testimonial,konte_icon_box,konte_member,konte_carousel_item' ),
			'content_element'         => true,
			'is_container'            => true,
			'show_settings_on_create' => false,
			'js_view'                 => 'VcColumnView',
			'icon'                    => self::get_icon( 'carousel.png' ),
			'category'                => esc_html__( 'Konte', 'konte-addons' ),
			'params'                  => array(
				array(
					'heading'    => esc_html__( 'Slide To Show', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'slide',
					'value'      => 2,
				),
				array(
					'heading'    => esc_html__( 'Slide To Scroll', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'scroll',
					'value'      => 1,
				),
				array(
					'heading'    => esc_html__( 'Infinite Scroll', 'konte-addons' ),
					'type'       => 'checkbox',
					'param_name' => 'infinite',
					'std'        => 'yes',
					'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
				),
				array(
					'heading' => esc_html__('Free mode', 'konte-addons'),
					'description' => esc_html__('Carousel items do not need to be equal.', 'konte-addons'),
					'type' => 'checkbox',
					'param_name' => 'free',
					'value' => array(esc_html__('Yes', 'konte-addons') => 'yes'),
				),
				array(
					'heading'    => esc_html__( 'Pagination Dots', 'konte-addons' ),
					'description' => esc_html__('Select position of carousel dots or disable them', 'konte-addons'),
					'type'       => 'dropdown',
					'param_name' => 'dots',
					'std'        => 'center',
					'value'      => array(
						esc_attr__( 'No Dots', 'konte-addons' )   => '',
						esc_attr__( 'At Bottom Left', 'konte-addons' )   => 'left',
						esc_attr__( 'At Bottom', 'konte-addons' ) => 'center',
						esc_attr__( 'At Bottom Right', 'konte-addons' )  => 'right',
					),
				),
				array(
					'heading' => esc_html__('Arrows', 'konte-addons'),
					'description' => esc_html__('Select position of carousel arrows or disable them', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'arrows',
					'std' => '',
					'value' => array(
						esc_attr__('No Arrows', 'konte-addons') => '',
						esc_attr__('On Top Right', 'konte-addons') => 'top-right',
						esc_attr__('At Middle', 'konte-addons') => 'center',
					),
				),
				array(
					'heading'    => esc_html__( 'Gap', 'konte-addons' ),
					'description' => esc_html__('Select gap between slides', 'konte-addons'),
					'type'       => 'dropdown',
					'param_name' => 'gap',
					'std'        => '40',
					'value'      => array(
						'20px'   => '20',
						'30px'   => '30',
						'40px'   => '40',
						'60px'   => '60',
						'90px'   => '90',
					),
				),
				array(
					'heading' => esc_html__('Show Slide Index', 'konte-addons'),
					'description' => esc_html__('Show the order number beside slides', 'konte-addons'),
					'type' => 'checkbox',
					'param_name' => 'show_index',
					'value' => array(esc_html__('Yes', 'konte-addons') => 'yes'),
				),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Carousel item.
		vc_map(array(
			'name' => esc_html__('Carousel Item', 'konte-addons'),
			'description' => esc_html__('Carousel item', 'konte-addons'),
			'base' => 'konte_carousel_item',
			'as_child' => array('only' => 'konte_carousel'),
			'icon' => self::get_icon('banner.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name' => 'image',
					'type' => 'attach_image',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'Image Size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme. Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => '1000x640',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'External image link.', 'konte-addons' ),
					'type' => 'textfield',
					'param_name' => 'image_src',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Title', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'title',
					'admin_label' => true,
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => esc_attr__('Shop Now', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Link', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'el_class',
				),
			),
		));

		// Testimonial.
		vc_map( array(
			'name'        => esc_html__( 'Testimonial', 'konte-addons' ),
			'description' => esc_html__( 'Written review from a satisfied customer', 'konte-addons' ),
			'base'        => 'konte_testimonial',
			'icon'        => self::get_icon( 'testimonial.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Photo', 'konte-addons' ),
					'description' => esc_html__( 'Author photo or avatar. Recommend 200x200 in dimension.', 'konte-addons' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
				),
				array(
					'heading'     => esc_html__( 'Name', 'konte-addons' ),
					'description' => esc_html__( 'Enter full name of the author', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Company', 'konte-addons' ),
					'description' => esc_html__( 'Enter company name of author', 'konte-addons' ),
					'param_name'  => 'company',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Content', 'konte-addons' ),
					'description' => esc_html__( 'Testimonial content', 'konte-addons' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Testimonial Carousel.
		vc_map( array(
			'name'                    => esc_html__( 'Testimonial Carousel', 'konte-addons' ),
			'description'             => esc_html__( 'Carousel of testimonials', 'konte-addons' ),
			'base'                    => 'konte_testimonial_carousel',
			'as_parent'               => array( 'only' => 'konte_testimonial' ),
			'content_element'         => true,
			'is_container'            => true,
			'show_settings_on_create' => false,
			'js_view'                 => 'VcColumnView',
			'icon'                    => self::get_icon( 'carousel.png' ),
			'category'                => esc_html__( 'Konte', 'konte-addons' ),
			'params'                  => array(
				array(
					'heading'    => esc_html__( 'Widget Title', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'title',
					'value'      => esc_attr__( 'Testimonials', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Button.
		vc_map( array(
			'name'        => esc_html__( 'Button', 'konte-addons' ),
			'description' => esc_html__( 'Button in style', 'konte-addons' ),
			'base'        => 'konte_button',
			'icon'        => self::get_icon( 'button.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Text', 'konte-addons' ),
					'description' => esc_html__( 'Enter button text', 'konte-addons' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'value'       => esc_attr__( 'Button Text', 'konte-addons' ),
					'param_name'  => 'content',
				),
				array(
					'heading'    => esc_html__( 'URL (Link)', 'konte-addons' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Style', 'konte-addons' ),
					'description' => esc_html__( 'Select button style', 'konte-addons' ),
					'param_name'  => 'style',
					'type'        => 'dropdown',
					'std'         => 'normal',
					'value'       => array(
						esc_html__( 'Normal', 'konte-addons' )    => 'normal',
						esc_html__( 'Outline', 'konte-addons' )   => 'outline',
						esc_html__( 'Underline', 'konte-addons' ) => 'underline',
					),
				),
				array(
					'heading'     => esc_html__( 'Shape', 'konte-addons' ),
					'description' => esc_html__( 'Select button shape', 'konte-addons' ),
					'param_name'  => 'shape',
					'type'        => 'dropdown',
					'std'         => 'normal',
					'value'       => array(
						esc_html__( 'Square', 'konte-addons' )   => 'square',
						esc_html__( 'Rounded', 'konte-addons' )  => 'rounded',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( 'normal', 'outline' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Size', 'konte-addons' ),
					'description' => esc_html__( 'Select button size', 'konte-addons' ),
					'param_name'  => 'size',
					'type'        => 'dropdown',
					'std'         => 'normal',
					'value'       => array(
						esc_html__( 'Small', 'konte-addons' )  => 'small',
						esc_html__( 'Normal', 'konte-addons' ) => 'normal',
						esc_html__( 'Medium', 'konte-addons' ) => 'medium',
						esc_html__( 'Large', 'konte-addons' )  => 'large',
					),
				),
				array(
					'heading'     => esc_html__( 'Line Width', 'konte-addons' ),
					'description' => esc_html__( 'Select underline size', 'konte-addons' ),
					'param_name'  => 'line_width',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Full width', 'konte-addons' )  => 'full',
						esc_html__( 'Small', 'konte-addons' )   => 'small',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => 'underline',
					),
				),
				array(
					'heading'     => esc_html__( 'Line Position', 'konte-addons' ),
					'description' => esc_html__( 'Select underline position', 'konte-addons' ),
					'param_name'  => 'line_position',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )  => 'left',
						esc_html__( 'Center', 'konte-addons' )   => 'center',
						esc_html__( 'Right', 'konte-addons' )   => 'right',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => 'underline',
					),
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select button color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Dark', 'konte-addons') => 'dark',
						esc_html__('Light', 'konte-addons') => 'light',
					),
				),
				array(
					'heading'     => esc_html__( 'Alignment', 'konte-addons' ),
					'description' => esc_html__( 'Select button alignment', 'konte-addons' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Inline', 'konte-addons' ) => 'inline',
						esc_html__( 'Left', 'konte-addons' )   => 'left',
						esc_html__( 'Center', 'konte-addons' ) => 'center',
						esc_html__( 'Right', 'konte-addons' )  => 'right',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Circle Chart.
		vc_map( array(
			'name'        => esc_html__( 'Circle Chart', 'konte-addons' ),
			'description' => esc_html__( 'Circle chart with animation', 'konte-addons' ),
			'base'        => 'konte_chart',
			'icon'        => self::get_icon( 'chart.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Value', 'konte-addons' ),
					'description' => esc_html__( 'Enter the chart value in percentage. Minimum 0 and maximum 100.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'value',
					'value'       => 100,
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Circle Size', 'konte-addons' ),
					'description' => esc_html__( 'Width of the circle', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'size',
					'value'       => 300,
				),
				array(
					'heading'     => esc_html__( 'Circle thickness', 'konte-addons' ),
					'description' => esc_html__( 'Width of the arc', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'thickness',
					'value'       => 10,
				),
				array(
					'heading'     => esc_html__( 'Color', 'konte-addons' ),
					'description' => esc_html__( 'Pick color for the circle', 'konte-addons' ),
					'type'        => 'colorpicker',
					'param_name'  => 'color',
					'value'       => '#161619',
				),
				array(
					'heading'     => esc_html__( 'Label Source', 'konte-addons' ),
					'description' => esc_html__( 'Chart label source', 'konte-addons' ),
					'param_name'  => 'label_source',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Auto', 'konte-addons' )   => 'auto',
						esc_html__( 'Custom', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'     => esc_html__( 'Custom label', 'konte-addons' ),
					'description' => esc_html__( 'Text label for the chart', 'konte-addons' ),
					'param_name'  => 'label',
					'type'        => 'textfield',
					'dependency'  => array(
						'element' => 'label_source',
						'value'   => 'custom',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Message Box.
		vc_map( array(
			'name'        => esc_html__( 'Message Box', 'konte-addons' ),
			'description' => esc_html__( 'Notification box with close button', 'konte-addons' ),
			'base'        => 'konte_message_box',
			'icon'        => self::get_icon( 'message-box.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'          => esc_html__( 'Type', 'konte-addons' ),
					'description'      => esc_html__( 'Select message box type', 'konte-addons' ),
					'edit_field_class' => 'vc_col-xs-12 vc_message-type',
					'type'             => 'dropdown',
					'param_name'       => 'type',
					'default'          => 'success',
					'admin_label'      => true,
					'value'            => array(
						esc_html__( 'Success', 'konte-addons' )       => 'success',
						esc_html__( 'Informational', 'konte-addons' ) => 'info',
						esc_html__( 'Error', 'konte-addons' )         => 'danger',
						esc_html__( 'Warning', 'konte-addons' )       => 'warning',
					),
				),
				array(
					'heading'    => esc_html__( 'Message Text', 'konte-addons' ),
					'type'       => 'textarea_html',
					'param_name' => 'content',
				),
				array(
					'heading'     => esc_html__( 'Closeable', 'konte-addons' ),
					'description' => esc_html__( 'Display close button for this box', 'konte-addons' ),
					'type'        => 'checkbox',
					'param_name'  => 'closeable',
					'std'         => 'yes',
					'value'       => array(
						esc_html__( 'Yes', 'konte-addons' ) => 'yes',
					),
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
				),
			),
		) );

		// Icon Box.
		vc_map( array(
			'name'        => esc_html__( 'Icon Box', 'konte-addons' ),
			'description' => esc_html__( 'Information box with icon', 'konte-addons' ),
			'base'        => 'konte_icon_box',
			'icon'        => self::get_icon( 'icon-box.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Icon library', 'konte-addons' ),
					'description' => esc_html__( 'Select icon library.', 'konte-addons' ),
					'param_name'  => 'icon_type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Font Awesome', 'konte-addons' )   => 'fontawesome',
						esc_html__( 'Open Iconic', 'konte-addons' )    => 'openiconic',
						esc_html__( 'Typicons', 'konte-addons' )       => 'typicons',
						esc_html__( 'Entypo', 'konte-addons' )         => 'entypo',
						esc_html__( 'Linecons', 'konte-addons' )       => 'linecons',
						esc_html__( 'Mono Social', 'konte-addons' )    => 'monosocial',
						esc_html__( 'Material', 'konte-addons' )       => 'material',
						esc_html__( 'Custom Image', 'konte-addons' )   => 'image',
						esc_html__( 'External Image', 'konte-addons' ) => 'external',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_fontawesome',
					'value'       => 'fa fa-adjust',
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_openiconic',
					'value'       => 'vc-oi vc-oi-dial',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'openiconic',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'openiconic',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_typicons',
					'value'       => 'typcn typcn-adjust-brightness',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'typicons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'typicons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_entypo',
					'value'       => 'entypo-icon entypo-icon-note',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'entypo',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'entypo',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_linecons',
					'value'       => 'vc_li vc_li-heart',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'linecons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'linecons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_monosocial',
					'value'       => 'vc-mono vc-mono-fivehundredpx',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'monosocial',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'monosocial',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon', 'konte-addons' ),
					'description' => esc_html__( 'Select icon from library.', 'konte-addons' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_material',
					'value'       => 'vc-material vc-material-cake',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'material',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'material',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Image', 'konte-addons' ),
					'description' => esc_html__( 'Upload icon image', 'konte-addons' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
					'value'       => '',
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
				),
				array(
					'heading'     => esc_html__( 'External Image', 'konte-addons' ),
					'description' => esc_html__( 'Image link', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'image_link',
					'value'       => '',
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'external',
					),
				),
				array(
					'heading'     => esc_html__( 'Alignment', 'konte-addons' ),
					'description' => esc_html__( 'Box content aliment', 'konte-addons' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )   => 'left',
						esc_html__( 'Center', 'konte-addons' ) => 'center',
						esc_html__( 'Right', 'konte-addons' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Title', 'konte-addons' ),
					'description' => esc_html__( 'The box title', 'konte-addons' ),
					'admin_label' => true,
					'param_name'  => 'title',
					'type'        => 'textfield',
					'value'       => esc_html__( 'I am Icon Box', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Title Font Size', 'konte-addons' ),
					'description' => esc_html__( 'Leave this empty to use the default font size.', 'konte-addons' ),
					'param_name'  => 'title_size',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Content', 'konte-addons' ),
					'description' => esc_html__( 'The box title', 'konte-addons' ),
					'holder'      => 'div',
					'param_name'  => 'content',
					'type'        => 'textarea_html',
					'value'       => esc_html__( 'I am icon box. Click edit button to change this text.', 'konte-addons' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
				array(
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'type' => 'css_editor',
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		) );

		// Team Member.
		vc_map( array(
			'name'        => esc_html__( 'Team Member', 'konte-addons' ),
			'description' => esc_html__( 'Single team member information', 'konte-addons' ),
			'base'        => 'konte_member',
			'icon'        => self::get_icon( 'member.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image', 'konte-addons' ),
					'description' => esc_html__( 'Member photo', 'konte-addons' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image Size', 'konte-addons' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
				),
				array(
					'heading'     => esc_html__( 'Full Name', 'konte-addons' ),
					'description' => esc_html__( 'Member name', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Job', 'konte-addons' ),
					'description' => esc_html__( 'The job/position name of member in your team', 'konte-addons' ),
					'param_name'  => 'job',
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'heading'    => esc_html__( 'Facebook', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'facebook',
				),
				array(
					'heading'    => esc_html__( 'Twitter', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'twitter',
				),
				array(
					'heading'    => esc_html__( 'Google Plus', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'google',
				),
				array(
					'heading'    => esc_html__( 'Pinterest', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'pinterest',
				),
				array(
					'heading'    => esc_html__( 'Linkedin', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'linkedin',
				),
				array(
					'heading'    => esc_html__( 'Youtube', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'youtube',
				),
				array(
					'heading'    => esc_html__( 'Instagram', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'instagram',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Post Grid.
		vc_map( array(
			'name'        => esc_html__( 'Post Grid', 'konte-addons' ),
			'description' => esc_html__( 'Display posts in a grid', 'konte-addons' ),
			'base'        => 'konte_post_grid',
			'icon'        => self::get_icon( 'post-grid.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of posts you want to show', 'konte-addons' ),
					'heading'     => esc_html__( 'Number of posts', 'konte-addons' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 3,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'konte-addons' ),
					'description' => esc_html__( 'Display posts in how many columns', 'konte-addons' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '3 Columns', 'konte-addons' ) => 3,
						esc_html__( '4 Columns', 'konte-addons' ) => 4,
					),
				),
				array(
					'heading'     => esc_html__( 'Columns Gap', 'konte-addons' ),
					'description' => esc_html__( 'Select gap between columns', 'konte-addons' ),
					'param_name'  => 'gap',
					'type'        => 'dropdown',
					'std'         => 40,
					'value'       => array(
						'30px' => 30,
						'40px' => 40,
						'60px' => 60,
					),
				),
				array(
					'heading'     => esc_html__( 'Category', 'konte-addons' ),
					'description' => esc_html__( 'Enter categories name', 'konte-addons' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => self::get_terms( 'category' ),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Post Carousel.
		vc_map( array(
			'name'        => esc_html__( 'Post Carousel', 'konte-addons' ),
			'description' => esc_html__( 'Display posts as a carousel', 'konte-addons' ),
			'base'        => 'konte_post_carousel',
			'icon'        => self::get_icon( 'post-grid.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number of posts', 'konte-addons' ),
					'description' => esc_html__( 'Total number of posts you want to get', 'konte-addons' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 9,
				),
				array(
					'heading'     => esc_html__( 'Category', 'konte-addons' ),
					'description' => esc_html__( 'Enter categories name', 'konte-addons' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => self::get_terms( 'category' ),
					),
				),
				array(
					'heading'    => esc_html__( 'Posts To Show', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'slide',
					'value'      => 3,
				),
				array(
					'heading'    => esc_html__( 'Posts To Scroll', 'konte-addons' ),
					'type'       => 'textfield',
					'param_name' => 'scroll',
					'value'      => 3,
				),
				array(
					'heading'    => esc_html__( 'Infinite Scroll', 'konte-addons' ),
					'type'       => 'checkbox',
					'param_name' => 'infinite',
					'std'        => 'yes',
					'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
				),
				array(
					'heading'    => esc_html__( 'Dots position', 'konte-addons' ),
					'type'       => 'dropdown',
					'param_name' => 'dots_position',
					'std'        => 'left',
					'value'      => array(
						esc_attr__( 'Left', 'konte-addons' )   => 'left',
						esc_attr__( 'Center', 'konte-addons' ) => 'center',
						esc_attr__( 'Right', 'konte-addons' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Separator Dash.
		vc_map( array(
			'name'        => esc_html__( 'Separator Dash', 'konte-addons' ),
			'description' => esc_html__( 'A short dash', 'konte-addons' ),
			'base'        => 'konte_dash',
			'icon'        => self::get_icon( 'dash.png' ),
			'show_settings_on_create' => false,
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading' => esc_html__('Text', 'konte-addons'),
					'description' => esc_html__('Optional. Enter the separator text or leave it empty to display the dash only.', 'konte-addons'),
					'param_name' => 'text',
					'admin_label' => true,
					'type' => 'textfield',
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select dash color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Dark', 'konte-addons') => 'dark',
						esc_html__('Light', 'konte-addons') => 'light',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'konte-addons' ),
					'param_name' => 'css',
					'group' => esc_html__( 'Design Options', 'konte-addons' ),
				),
			),
		) );

		// Info List.
		vc_map( array(
			'name'        => esc_html__( 'Info List', 'konte-addons' ),
			'description' => esc_html__( 'List of information', 'konte-addons' ),
			'base'        => 'konte_info_list',
			'icon'        => self::get_icon( 'info-list.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Information', 'konte-addons' ),
					'description' => esc_html__( 'Enter information', 'konte-addons' ),
					'type'        => 'param_group',
					'param_name'  => 'info',
					'value'       => urlencode( json_encode( array(
						array(
							'label' => esc_html__( 'Address', 'konte-addons' ),
							'value' => '9606 North MoPac Expressway Suite 700 Austin, TX 78759',
						),
						array(
							'label' => esc_html__( 'Phone', 'konte-addons' ),
							'value' => '+1 248-785-8545',
						),
						array(
							'label' => esc_html__( 'Email', 'konte-addons' ),
							'value' => 'kontetheme@google.com',
						),
					) ) ),
					'params'      => array(
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Label', 'konte-addons' ),
							'param_name'  => 'label',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Value', 'konte-addons' ),
							'param_name'  => 'value',
							'admin_label' => true,
						),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Countdown.
		vc_map( array(
			'name'        => esc_html__( 'Countdown', 'konte-addons' ),
			'description' => esc_html__( 'Countdown digital clock', 'konte-addons' ),
			'base'        => 'konte_countdown',
			'icon'        => self::get_icon( 'countdown.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Date', 'konte-addons' ),
					'description' => esc_html__( 'Enter the date in format: YYYY/MM/DD', 'konte-addons' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'param_name'  => 'date',
				),
				array(
					'heading'     => esc_html__( 'Style', 'konte-addons' ),
					'description' => esc_html__( 'Select countdown style', 'konte-addons' ),
					'param_name'  => 'type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Full', 'konte-addons' )   => 'full',
						esc_html__( 'Small', 'konte-addons' )  => 'small',
						esc_html__( 'Inline', 'konte-addons' ) => 'inline',
					),
				),
				array(
					'heading'     => esc_html__( 'Alignment', 'konte-addons' ),
					'description' => esc_html__( 'Select countdown aligment', 'konte-addons' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Inline', 'konte-addons' ) => 'inline',
						esc_html__( 'Left', 'konte-addons' )   => 'left',
						esc_html__( 'Center', 'konte-addons' ) => 'center',
						esc_html__( 'Right', 'konte-addons' )  => 'right',
					),
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Dark', 'konte-addons') => 'dark',
						esc_html__('Light', 'konte-addons') => 'light',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Category Banner.
		vc_map(array(
			'name' => esc_html__('Category Banner', 'konte-addons'),
			'description' => esc_html__('Styled banner for Konte.', 'konte-addons'),
			'base' => 'konte_category_banner',
			'icon' => self::get_icon('category-banner.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Category', 'konte-addons'),
					'description' => esc_html__('Enter category name', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'category',
				),
				array(
					'heading' => esc_html__('Link', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading' => esc_html__('Image Source', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'image_source',
					'std' => 'media_library',
					'value' => array(
						esc_attr__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_attr__( 'External link', 'konte-addons' ) => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Main Image', 'konte-addons'),
					'description' => esc_html__('The main banner image', 'konte-addons'),
					'type' => 'attach_image',
					'param_name' => 'image',
					'edit_field_class' => 'vc_col-xs-6',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading' => esc_html__('Sub Image', 'konte-addons'),
					'description' => esc_html__('Optional. Size of this image is fixed to 200x200.', 'konte-addons'),
					'type' => 'attach_image',
					'param_name' => 'sub_image',
					'edit_field_class' => 'vc_col-xs-6',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading' => esc_html__('Main Image Size', 'konte-addons'),
					'description' => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => 'full',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading' => esc_html__('Main Image', 'konte-addons'),
					'description' => esc_html__('The main banner image link', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_src',
					'edit_field_class' => 'vc_col-xs-6',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Sub Image', 'konte-addons'),
					'description' => esc_html__('Optional. The image size should be fixed to 200x200.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'sub_image_src',
					'edit_field_class' => 'vc_col-xs-6',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Alignment', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'align',
					'std' => 'left',
					'value' => array(
						esc_attr__( 'Left', 'konte-addons' ) => 'left',
						esc_attr__( 'Right', 'konte-addons' ) => 'right',
					),
				),
				array(
					'heading' => esc_html__('Banner Title', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'title',
				),
				array(
					'heading' => esc_html__('Title Position', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'title_position',
					'std' => 'bottom',
					'value' => array(
						esc_attr__('Bottom', 'konte-addons') => 'bottom',
						esc_attr__('Middle', 'konte-addons') => 'middle',
					),
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => esc_attr__( 'Explore Now', 'konte-addons' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
			),
		));

		// Banner.
		vc_map(array(
			'name' => esc_html__('Banner Image', 'konte-addons'),
			'description' => esc_html__('Banner image for promotion', 'konte-addons'),
			'base' => 'konte_banner',
			'icon' => self::get_icon('banner.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Image Source', 'konte-addons'),
					'description' => esc_html__('Select image source', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'source',
					'std' => 'media_library',
					'value' => array(
						esc_html__('Media library', 'konte-addons') => 'media_library',
						esc_html__('External link', 'konte-addons') => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Image', 'konte-addons'),
					'description' => esc_html__('Banner Image', 'konte-addons'),
					'param_name' => 'image',
					'type' => 'attach_image',
					'dependency' => array(
						'element' => 'source',
						'value' => 'media_library',
					),
				),
				array(
					'heading' => esc_html__('Image size', 'konte-addons'),
					'description' => esc_html__('Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => 'full',
					'dependency' => array(
						'element' => 'source',
						'value' => 'media_library',
					),
				),
				array(
					'heading' => esc_html__('External Link', 'konte-addons'),
					'description' => esc_html__('Select external link', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'custom_src',
					'dependency' => array(
						'element' => 'source',
						'value' => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Tagline', 'konte-addons'),
					'description' => esc_html__('A short tagline display above the banner text', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'tagline',
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Banner Text', 'konte-addons'),
					'description' => esc_html__('Enter the banner text', 'konte-addons'),
					'type' => 'textarea',
					'value' => esc_html__( 'Enter banner text here', 'konte-addons' ),
					'param_name' => 'content',
					'admin_label' => true,
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Banner description', 'konte-addons'),
					'description' => esc_html__('A short text display bellow the banner text', 'konte-addons'),
					'type' => 'textarea',
					'param_name' => 'desc',
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Banner Text Position', 'konte-addons'),
					'description' => esc_html__('Select the text position', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'text_position',
					'value' => array(
						esc_html__('Left', 'konte-addons') => 'left',
						esc_html__('Center', 'konte-addons') => 'center',
						esc_html__('Right', 'konte-addons') => 'right',
						esc_html__('Top', 'konte-addons') => 'top-center',
						esc_html__('Top Left', 'konte-addons') => 'top-left',
						esc_html__('Top Right', 'konte-addons') => 'top-right',
						esc_html__('Bottom', 'konte-addons') => 'bottom-center',
						esc_html__('Bottom Left', 'konte-addons') => 'bottom-left',
						esc_html__('Bottom Right', 'konte-addons') => 'bottom-right',
						esc_html__('Under Image', 'konte-addons') => 'under-image',
					),
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'type' => 'font_container',
					'param_name' => 'font_container',
					'value' => '',
					'settings' => array(
						'fields' => array(
							'font_size',
							'font_weight',
							'line_height',
							'font_size_description' => esc_html__( 'Font size of banner text', 'konte-addons'),
							'line_height_description' => esc_html__( 'Line height of banner text', 'konte-addons' ),
						),
					),
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Font Weight', 'konte-addons'),
					'description' => esc_html__('Font weight of banner text', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'content_weight',
					'std' => 'medium',
					'value' => array(
						esc_html__('Normal', 'konte-addons') => 'normal',
						esc_html__('Medium', 'konte-addons') => 'medium',
						esc_html__('Bold', 'konte-addons') => 'bold',
					),
					'group' => esc_html__('Content', 'konte-addons'),
					'dependency' => array(
						'element' => 'use_theme_fonts',
						'value' => 'yes',
					),
				),
				array(
					'heading' => esc_html__('Use theme default font family?', 'konte-addons'),
					'description' => esc_html__('Use font family from the theme.', 'konte-addons'),
					'type' => 'checkbox',
					'param_name' => 'use_theme_fonts',
					'std' => 'yes',
					'value' => array(esc_html__('Yes', 'konte-addons') => 'yes'),
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'type' => 'google_fonts',
					'param_name' => 'google_fonts',
					'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
					'settings' => array(
						'fields' => array(
							'font_family_description' => esc_html__('Select font family.', 'konte-addons'),
							'font_style_description' => esc_html__('Select font styling.', 'konte-addons'),
						),
					),
					'dependency' => array(
						'element' => 'use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Banner Color Scheme', 'konte-addons'),
					'description' => esc_html__('Select color scheme for banner texts and button', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'scheme',
					'value' => array(
						esc_html__('Default', 'konte-addons') => '',
						esc_html__('Dark', 'konte-addons') => 'dark',
						esc_html__('Light', 'konte-addons') => 'light',
					),
					'group' => esc_html__('Content', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Link (URL)', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
					'group' => esc_html__('Button', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Button Type', 'konte-addons'),
					'description' => esc_html__('Select button type', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'button_type',
					'std' => 'underline',
					'value' => array(
						esc_html__('Normal Button', 'konte-addons') => 'normal',
						esc_html__('Outline Button', 'konte-addons') => 'outline',
						esc_html__('Underline Button', 'konte-addons') => 'underline',
					),
					'group' => esc_html__('Button', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Button Size', 'konte-addons'),
					'description' => esc_html__('Select button size', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'button_size',
					'std' => 'large',
					'value' => array(
						esc_html__('Small', 'konte-addons')  => 'small',
						esc_html__('Normal', 'konte-addons') => 'normal',
						esc_html__('Medium', 'konte-addons') => 'medium',
						esc_html__('Large ', 'konte-addons') => 'large',
					),
					'group' => esc_html__('Button', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'description' => esc_html__('Enter the text for banner button', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => esc_html__('Button Text', 'konte-addons'),
					'group' => esc_html__('Button', 'konte-addons'),
				),
				array(
					'heading' => esc_html__('Button Visibility', 'konte-addons'),
					'type' => 'dropdown',
					'param_name' => 'button_visibility',
					'value' => array(
						esc_html__('Always Visible', 'konte-addons') => 'always',
						esc_html__('Visible on hover', 'konte-addons') => 'hover',
					),
					'group' => esc_html__('Button', 'konte-addons'),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'type' => 'css_editor',
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		));

		// Product Carousel.
		vc_map( array(
			'name'        => esc_html__( 'Product Carousel', 'konte-addons' ),
			'description' => esc_html__( 'Product carousel slider', 'konte-addons' ),
			'base'        => 'konte_product_carousel',
			'icon'        => self::get_icon( 'product-carousel.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number Of Products', 'konte-addons' ),
					'description' => esc_html__( 'Total number of products you want to show', 'konte-addons' ),
					'param_name'  => 'limit',
					'type'        => 'textfield',
					'value'       => 15,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'konte-addons' ),
					'description' => esc_html__( 'Display products in how many columns', 'konte-addons' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'std'         => 4,
					'value'       => array(
						esc_html__( '3 Columns', 'konte-addons' ) => 3,
						esc_html__( '4 Columns', 'konte-addons' ) => 4,
						esc_html__( '5 Columns', 'konte-addons' ) => 5,
						esc_html__( '6 Columns', 'konte-addons' ) => 6,
					),
				),
				array(
					'heading'     => esc_html__( 'Product Type', 'konte-addons' ),
					'description' => esc_html__( 'Select product type you want to show', 'konte-addons' ),
					'param_name'  => 'type',
					'admin_label' => true,
					'type'        => 'dropdown',
					'std'         => 'recent',
					'value'       => array(
						esc_html__( 'Recent Products', 'konte-addons' )       => 'recent',
						esc_html__( 'Featured Products', 'konte-addons' )     => 'featured',
						esc_html__( 'Sale Products', 'konte-addons' )         => 'sale',
						esc_html__( 'Best Selling Products', 'konte-addons' ) => 'best_selling',
						esc_html__( 'Top Rated Products', 'konte-addons' )    => 'top_rated',
					),
				),
				array(
					'heading'     => esc_html__( 'Categories', 'konte-addons' ),
					'description' => esc_html__( 'Select what categories you want to use. Leave it empty to use all categories.', 'konte-addons' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'value'       => '',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => self::get_terms(),
					),
				),
				array(
					'heading'     => esc_html__( 'Auto Play', 'konte-addons' ),
					'description' => esc_html__( 'Auto play speed in miliseconds. Enter "0" to disable auto play.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'autoplay',
					'value'       => 5000,
				),
				array(
					'heading'    => esc_html__( 'Loop', 'konte-addons' ),
					'type'       => 'checkbox',
					'param_name' => 'loop',
					'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
				),
				array(
					'heading'     => esc_html__( 'Navigation Stype', 'konte-addons' ),
					'description' => esc_html__( 'Select the style for navigation arrows', 'konte-addons' ),
					'param_name'  => 'nav_style',
					'type'        => 'dropdown',
					'std'         => 'arrow',
					'value'       => array(
						esc_html__( 'Arrow', 'konte-addons' ) => 'arrow',
						esc_html__( 'Angle', 'konte-addons' ) => 'angle',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Product Carousel 2.
		vc_map(array(
			'name' => esc_html__('Product Carousel 2', 'konte-addons'),
			'description' => esc_html__('Product carousel in a special style', 'konte-addons'),
			'base' => 'konte_product_carousel2',
			'icon' => self::get_icon('product-carousel.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Number Of Products', 'konte-addons'),
					'description' => esc_html__('Total number of products you want to show', 'konte-addons'),
					'param_name' => 'limit',
					'type' => 'textfield',
					'value' => 10,
				),
				array(
					'heading' => esc_html__('Product Type', 'konte-addons'),
					'description' => esc_html__('Select product type you want to show', 'konte-addons'),
					'param_name' => 'type',
					'admin_label' => true,
					'type' => 'dropdown',
					'std' => 'recent',
					'value' => array(
						esc_html__('Recent Products', 'konte-addons') => 'recent',
						esc_html__('Featured Products', 'konte-addons') => 'featured',
						esc_html__('Sale Products', 'konte-addons') => 'sale',
						esc_html__('Best Selling Products', 'konte-addons') => 'best_selling',
						esc_html__('Top Rated Products', 'konte-addons') => 'top_rated',
					),
				),
				array(
					'heading' => esc_html__('Categories', 'konte-addons'),
					'description' => esc_html__('Select what categories you want to use. Leave it empty to use all categories.', 'konte-addons'),
					'param_name' => 'category',
					'type' => 'autocomplete',
					'value' => '',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'values' => self::get_terms(),
					),
				),
				array(
					'heading' => esc_html__('Tags', 'konte-addons'),
					'description' => esc_html__('Product tag slugs, separated by commas', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'tag',
				),
				array(
					'heading' => esc_html__('Image Size', 'konte-addons'),
					'description' => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => '350x440',
				),
				array(
					'heading' => esc_html__('Auto Play', 'konte-addons'),
					'description' => esc_html__('Auto play speed in miliseconds. Enter "0" to disable auto play.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'autoplay',
					'value' => 5000,
				),
				array(
					'heading' => esc_html__('Loop', 'konte-addons'),
					'type' => 'checkbox',
					'param_name' => 'loop',
					'value' => array(esc_html__('Yes', 'konte-addons') => 'yes'),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
			),
		));

		// Product Grid.
		vc_map(array(
			'name' => esc_html__('Products Grid', 'konte-addons'),
			'description' => esc_html__('Display products in grid', 'konte-addons'),
			'base' => 'konte_product_grid',
			'icon' => self::get_icon('product-grid.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Title', 'konte-addons'),
					'description' => esc_html__('Title of the grid', 'konte-addons'),
					'param_name' => 'title',
					'admin_label' => true,
					'type' => 'textfield',
				),
				array(
					'heading' => esc_html__('Description', 'konte-addons'),
					'description' => esc_html__('Description of the grid', 'konte-addons'),
					'param_name' => 'content',
					'type' => 'textarea',
				),
				array(
					'heading' => esc_html__('Number Of Products', 'konte-addons'),
					'description' => esc_html__('Total number of products you want to show', 'konte-addons'),
					'param_name' => 'limit',
					'type' => 'textfield',
					'value' => 7,
				),
				array(
					'heading' => esc_html__('Columns', 'konte-addons'),
					'description' => esc_html__('Display products in how many columns', 'konte-addons'),
					'param_name' => 'columns',
					'type' => 'dropdown',
					'std' => 4,
					'value' => array(
						esc_html__('3 Columns', 'konte-addons') => 3,
						esc_html__('4 Columns', 'konte-addons') => 4,
						esc_html__('5 Columns', 'konte-addons') => 5,
						esc_html__('6 Columns', 'konte-addons') => 6,
					),
				),
				array(
					'heading' => esc_html__('Product Type', 'konte-addons'),
					'description' => esc_html__('Select product type you want to show', 'konte-addons'),
					'param_name' => 'type',
					'admin_label' => true,
					'type' => 'dropdown',
					'std' => 'recent',
					'value' => array(
						esc_html__('Recent Products', 'konte-addons') => 'recent',
						esc_html__('Featured Products', 'konte-addons') => 'featured',
						esc_html__('Sale Products', 'konte-addons') => 'sale',
						esc_html__('Best Selling Products', 'konte-addons') => 'best_selling',
						esc_html__('Top Rated Products', 'konte-addons') => 'top_rated',
					),
				),
				array(
					'heading'    => esc_html__( 'Order By', 'konte-addons' ),
					'param_name' => 'orderby',
					'type'       => 'dropdown',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'konte-addons' )            => '',
						__( 'Default Order (Menu Order)', 'konte-addons' ) => 'menu_order',
						__( 'Date', 'konte-addons' )               => 'date',
						__( 'Product ID', 'konte-addons' )         => 'id',
						__( 'Product Title', 'konte-addons' )      => 'title',
						__( 'Random', 'konte-addons' )             => 'rand',
						__( 'Price', 'konte-addons' )              => 'price',
						__( 'Popularity (Sales)', 'konte-addons' ) => 'popularity',
						__( 'Rating', 'konte-addons' )             => 'rating',
					),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'featured', 'sale' ),
					),
				),
				array(
					'heading'    => esc_html__( 'Order', 'konte-addons' ),
					'description' => esc_html__( 'This option will not be used if "Order By" option is "Default"', 'konte-addons' ),
					'param_name' => 'order',
					'type'       => 'dropdown',
					'std'        => 'ASC',
					'value'      => array(
						__( 'Ascending', 'konte-addons' )  => 'ASC',
						__( 'Descending', 'konte-addons' ) => 'DESC',
					),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'featured', 'sale' ),
					),
				),
				array(
					'heading' => esc_html__('Categories', 'konte-addons'),
					'description' => esc_html__('Select what categories you want to use. Leave it empty to use all categories.', 'konte-addons'),
					'param_name' => 'category',
					'type' => 'autocomplete',
					'value' => '',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'values' => self::get_terms(),
					),
				),
				array(
					'heading' => esc_html__('Tag', 'konte-addons'),
					'description' => esc_html__('Product tag slugs, separated by commas', 'konte-addons'),
					'param_name' => 'tag',
					'type' => 'textfield',
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
			),
		));

		// Product Masonry.
		vc_map(array(
			'name' => esc_html__('Products Masonry', 'konte-addons'),
			'description' => esc_html__('Display products in masonry layout', 'konte-addons'),
			'base' => 'konte_product_masonry',
			'icon' => self::get_icon('masonry.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Title', 'konte-addons'),
					'description' => esc_html__('Title of the grid', 'konte-addons'),
					'param_name' => 'title',
					'admin_label' => true,
					'type' => 'textfield',
					'value' => esc_html__( 'Explore Our Product', 'konte-addons' ),
				),
				array(
					'heading' => esc_html__('Description', 'konte-addons'),
					'description' => esc_html__('Description of the grid', 'konte-addons'),
					'param_name' => 'content',
					'type' => 'textarea',
				),
				array(
					'heading' => esc_html__('Number Of Products', 'konte-addons'),
					'description' => esc_html__('Total number of products you want to show', 'konte-addons'),
					'param_name' => 'limit',
					'type' => 'textfield',
					'value' => 4,
				),
				array(
					'heading' => esc_html__('Product Type', 'konte-addons'),
					'description' => esc_html__('Select product type you want to show', 'konte-addons'),
					'param_name' => 'type',
					'admin_label' => true,
					'type' => 'dropdown',
					'std' => 'recent',
					'value' => array(
						esc_html__('Recent Products', 'konte-addons') => 'recent',
						esc_html__('Featured Products', 'konte-addons') => 'featured',
						esc_html__('Sale Products', 'konte-addons') => 'sale',
						esc_html__('Best Selling Products', 'konte-addons') => 'best_selling',
						esc_html__('Top Rated Products', 'konte-addons') => 'top_rated',
					),
				),
				array(
					'heading'    => esc_html__( 'Order By', 'konte-addons' ),
					'param_name' => 'orderby',
					'type'       => 'dropdown',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'konte-addons' )            => '',
						__( 'Default Order (Menu Order)', 'konte-addons' ) => 'menu_order',
						__( 'Date', 'konte-addons' )               => 'date',
						__( 'Product ID', 'konte-addons' )         => 'id',
						__( 'Product Title', 'konte-addons' )      => 'title',
						__( 'Random', 'konte-addons' )             => 'rand',
						__( 'Price', 'konte-addons' )              => 'price',
						__( 'Popularity (Sales)', 'konte-addons' ) => 'popularity',
						__( 'Rating', 'konte-addons' )             => 'rating',
					),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'featured', 'sale' ),
					),
				),
				array(
					'heading'    => esc_html__( 'Order', 'konte-addons' ),
					'description' => esc_html__( 'This option will not be used if "Order By" option is "Default"', 'konte-addons' ),
					'param_name' => 'order',
					'type'       => 'dropdown',
					'std'        => 'ASC',
					'value'      => array(
						__( 'Ascending', 'konte-addons' )  => 'ASC',
						__( 'Descending', 'konte-addons' ) => 'DESC',
					),
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'featured', 'sale' ),
					),
				),
				array(
					'heading' => esc_html__('Categories', 'konte-addons'),
					'description' => esc_html__('Select what categories you want to use. Leave it empty to use all categories.', 'konte-addons'),
					'param_name' => 'category',
					'type' => 'autocomplete',
					'value' => '',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'values' => self::get_terms(),
					),
				),
				array(
					'heading' => esc_html__('Tag', 'konte-addons'),
					'description' => esc_html__('Product tag slugs, separated by commas', 'konte-addons'),
					'param_name' => 'tag',
					'type' => 'textfield',
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
			),
		));

		// Product Tabs.
		vc_map( array(
			'name'        => esc_html__( 'Products Tabs', 'konte-addons' ),
			'description' => esc_html__( 'Display products in tabs', 'konte-addons' ),
			'base'        => 'konte_product_tabs',
			'icon'        => self::get_icon( 'product-tabs.png' ),
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Number Of Products', 'konte-addons' ),
					'description' => esc_html__( 'Total number of products you want to show. Set -1 to show all products', 'konte-addons' ),
					'param_name'  => 'limit',
					'type'        => 'textfield',
					'value'       => 8,
				),
				array(
					'heading'     => esc_html__( 'Columns', 'konte-addons' ),
					'description' => esc_html__( 'Display products in how many columns', 'konte-addons' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'std'         => 4,
					'value'       => array(
						esc_html__( '3 Columns', 'konte-addons' ) => 3,
						esc_html__( '4 Columns', 'konte-addons' ) => 4,
						esc_html__( '5 Columns', 'konte-addons' ) => 5,
					),
				),
				array(
					'heading'     => esc_html__( 'Tab Type', 'konte-addons' ),
					'description' => esc_html__( 'Select what tab type do you want to use', 'konte-addons' ),
					'param_name'  => 'tab_type',
					'admin_label' => true,
					'type'        => 'dropdown',
					'std'         => 'category',
					'value'       => array(
						esc_html__( 'Categories', 'konte-addons' ) => 'category',
						esc_html__( 'Tags', 'konte-addons' )       => 'tag',
						esc_html__( 'Groups', 'konte-addons' )     => 'groups',
					),
				),
				array(
					'heading'     => esc_html__( 'Category', 'konte-addons' ),
					'description' => esc_html__( 'Select what categories you want to use.', 'konte-addons' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'value'       => '',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => self::get_terms( 'product_cat' ),
					),
					'dependency'  => array(
						'element' => 'tab_type',
						'value'   => 'category',
					),
				),
				array(
					'heading'     => esc_html__( 'Tags', 'konte-addons' ),
					'description' => esc_html__( 'Enter tag slugs. Separates by comma.', 'konte-addons' ),
					'param_name'  => 'tag',
					'type'        => 'textfield',
					'value'       => '',
					'dependency'  => array(
						'element' => 'tab_type',
						'value'   => 'tag',
					),
				),
				array(
					'heading'     => esc_html__( 'Groups', 'konte-addons' ),
					'description' => esc_html__( 'Select product types as groups.', 'konte-addons' ),
					'param_name'  => 'groups',
					'type'        => 'param_group',
					'value'       => urlencode( json_encode( array(
						array(
							'type'  => 'best_selling',
							'title' => esc_html__( 'Best Sellers', 'konte-addons' ),
						),
						array(
							'type'  => 'recent',
							'title' => esc_html__( 'Recent Products', 'konte-addons' ),
						),
						array(
							'type'  => 'sale',
							'title' => esc_html__( 'Sale Products', 'konte-addons' ),
						),
					) ) ),
					'params'      => array(
						array(
							'heading'     => esc_html__( 'Type', 'konte-addons' ),
							'description' => esc_html__( 'Select product type for this tab', 'konte-addons' ),
							'param_name'  => 'type',
							'type'        => 'dropdown',
							'value'       => array(
								esc_html__( 'Recent Products', 'konte-addons' )       => 'recent',
								esc_html__( 'Featured Products', 'konte-addons' )     => 'featured',
								esc_html__( 'Sale Products', 'konte-addons' )       => 'sale',
								esc_html__( 'Best Selling Products', 'konte-addons' ) => 'best_selling',
								esc_html__( 'Top Rated Products', 'konte-addons' )    => 'top_rated',
							),
						),
						array(
							'heading'     => esc_html__( 'Title', 'konte-addons' ),
							'description' => esc_html__( 'Enter title for this tab.', 'konte-addons' ),
							'param_name'  => 'title',
							'type'        => 'textfield',
						),
					),
					'dependency'  => array(
						'element' => 'tab_type',
						'value'   => 'groups',
					),
				),
				array(
					'heading'     => esc_html__( 'Adds "All" tab', 'konte-addons' ),
					'description' => esc_html__( 'Adds "All" tab as the default tab.', 'konte-addons' ),
					'param_name'  => 'tab_all',
					'type'        => 'checkbox',
					'value'       => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
				),
				array(
					'heading'     => esc_html__( 'Product Carousel', 'konte-addons' ),
					'description' => esc_html__( 'Display products in a carousel instead of a grid', 'konte-addons' ),
					'param_name'  => 'carousel',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes', 'konte-addons' ) => 'yes',
					),
				),
				array(
					'heading'     => esc_html__( 'Auto Play', 'konte-addons' ),
					'description' => esc_html__( 'Auto play speed in miliseconds. Enter "0" to disable auto play.', 'konte-addons' ),
					'type'        => 'textfield',
					'param_name'  => 'autoplay',
					'value'       => 5000,
					'dependency'  => array(
						'element' => 'carousel',
						'value'   => 'yes',
					),
					'edit_field_class' => 'vc_col-xs-6',
				),
				array(
					'heading'    => esc_html__( 'Carousel Loop', 'konte-addons' ),
					'type'       => 'checkbox',
					'param_name' => 'loop',
					'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => 'yes' ),
					'dependency'  => array(
						'element' => 'carousel',
						'value'   => 'yes',
					),
					'edit_field_class' => 'vc_col-xs-6',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Product.
		vc_map(array(
			'name' => esc_html__('Product', 'konte-addons'),
			'description' => esc_html__('Display a product item', 'konte-addons'),
			'base' => 'konte_product',
			'icon' => self::get_icon('product.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Product', 'konte-addons'),
					'description' => esc_html__('Input product ID or product SKU or product title to see suggestions', 'konte-addons'),
					'param_name' => 'id',
					'admin_label' => true,
					'type' => 'autocomplete'
				),
				array(
					'heading'    => esc_html__( 'Custom Product Image', 'konte-addons' ),
					'param_name' => 'image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__('Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => 'full',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__('External image link.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_src',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'description' => esc_html__('Optional. Enter the custom button text. Leave this empty to use the default text.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => '',
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select the text color', 'konte-addons'),
					'param_name' => 'text_color',
					'type' => 'dropdown',
					'std' => 'light',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Light', 'konte-addons') => 'light',
						esc_html__('Dark', 'konte-addons') => 'dark',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		));

		add_filter( 'vc_autocomplete_konte_product_id_callback', array( __CLASS__, 'product_id_autocomplete' ), 10, 1 );
		add_filter( 'vc_autocomplete_konte_product_id_render', array( __CLASS__, 'product_id_autocomplete_render' ), 10, 1 );

		// CTA.
		vc_map(array(
			'name' => esc_html__('Call To Action', 'konte-addons'),
			'description' => esc_html__('CTA block', 'konte-addons'),
			'base' => 'konte_cta',
			'icon' => self::get_icon('cta.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Heading', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'heading',
					'value' => esc_attr__( 'Mid Season Sale', 'konte-addons' ),
				),
				array(
					'heading' => esc_html__('Text', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textarea',
					'param_name' => 'content',
					'value' => esc_attr__( 'Up to 50% off', 'konte-addons' ),
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'description' => esc_html__('Enter the custom button text.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => esc_attr__( 'Shop Now', 'konte-addons' ),
				),
				array(
					'heading' => esc_html__('Button Link', 'konte-addons'),
					'description' => esc_html__('Set the link for the button', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading' => esc_html__('Note', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'note',
					'value' => '',
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select the text color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'std' => 'default',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Light', 'konte-addons') => 'light',
						esc_html__('Dark', 'konte-addons') => 'dark',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		));

		// Promotion.
		vc_map(array(
			'name' => esc_html__('Promotion', 'konte-addons'),
			'description' => esc_html__('Simple CTA block', 'konte-addons'),
			'base' => 'konte_promotion',
			'icon' => self::get_icon('banner2.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Layout', 'konte-addons'),
					'description' => esc_html__('Promotion layout', 'konte-addons'),
					'param_name' => 'layout',
					'type' => 'dropdown',
					'std' => 'standard',
					'value' => array(
						esc_html__('Standard', 'konte-addons') => 'standard',
						esc_html__('Inline', 'konte-addons') => 'inline',
					),
				),
				array(
					'heading' => esc_html__('Text', 'konte-addons'),
					'description' => esc_html__('Promotion text', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'text',
					'value' => '',
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'description' => esc_html__('Enter the custom button text.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => '',
				),
				array(
					'heading' => esc_html__('Button Link', 'konte-addons'),
					'description' => esc_html__('Set the link for button', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select the text color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'std' => 'default',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Light', 'konte-addons') => 'light',
						esc_html__('Dark', 'konte-addons') => 'dark',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		));

		// Countdown Banner.
		vc_map(array(
			'name' => esc_html__('Countdown Banner', 'konte-addons'),
			'description' => esc_html__('A banner with countdown clock', 'konte-addons'),
			'base' => 'konte_banner_countdown',
			'icon' => self::get_icon('banner-countdown.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__('Enter image size, example: "thumbnail", "medium", "large", "full" or other sizes defined by theme. Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_size',
					'value' => 'full',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__('External image link.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'image_src',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading' => esc_html__('Tagline', 'konte-addons'),
					'param_name' => 'tagline',
					'type' => 'textfield',
				),
				array(
					'heading' => esc_html__('Text', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'text',
					'value' => '',
				),
				array(
					'heading' => esc_html__('Button Text', 'konte-addons'),
					'description' => esc_html__('Enter the custom button text.', 'konte-addons'),
					'type' => 'textfield',
					'param_name' => 'button_text',
					'value' => esc_attr__( 'Shop Now', 'konte-addons' ),
				),
				array(
					'heading' => esc_html__('Button Link', 'konte-addons'),
					'description' => esc_html__('Set the link for button', 'konte-addons'),
					'type' => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading' => esc_html__('Date', 'konte-addons'),
					'description' => esc_html__('Enter the date in format: YYYY/MM/DD', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'date',
				),
				array(
					'heading' => esc_html__('Color', 'konte-addons'),
					'description' => esc_html__('Select the text color', 'konte-addons'),
					'param_name' => 'color',
					'type' => 'dropdown',
					'std' => 'default',
					'value' => array(
						esc_html__('Default', 'konte-addons') => 'default',
						esc_html__('Light', 'konte-addons') => 'light',
						esc_html__('Dark', 'konte-addons') => 'dark',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__('CSS box', 'konte-addons'),
					'param_name' => 'css',
					'group' => esc_html__('Design Options', 'konte-addons'),
				),
			),
		));

		// Banner grid.
		vc_map( array(
			'name'        => esc_html__( 'Banner Grid', 'konte-addons' ),
			'description' => esc_html__( 'Grid of banners', 'konte-addons' ),
			'base'        => 'konte_banner_grid',
			'icon'        => self::get_icon( 'banner-grid.png' ),
			'class'       => '',
			'category'    => esc_html__( 'Konte', 'konte-addons' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Height', 'konte-addons' ),
					'description' => esc_html__( 'Enter height of the grid', 'konte-addons' ),
					'param_name'  => 'height',
					'value'       => 840,
				),
				array(
					'heading'          => esc_html__( 'Banners Gap', 'konte-addons' ),
					'param_name'       => 'gap',
					'type'             => 'dropdown',
					'std'              => '4',
					'value'            => array(
						esc_html__( 'No gap', 'konte-addons' ) => '0',
						esc_html__( '2 px', 'konte-addons' )   => '2',
						esc_html__( '3 px', 'konte-addons' )   => '3',
						esc_html__( '4 px', 'konte-addons' )   => '4',
						esc_html__( '5 px', 'konte-addons' )   => '5',
						esc_html__( '6 px', 'konte-addons' )   => '6',
						esc_html__( '8 px', 'konte-addons' )   => '8',
						esc_html__( '10 px', 'konte-addons' )   => '10',
						esc_html__( '20 px', 'konte-addons' )   => '20',
						esc_html__( '30 px', 'konte-addons' )   => '30',
						esc_html__( '40 px', 'konte-addons' )   => '40',
					),
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					'param_name'  => 'el_class',
					'value'       => '',
				),
				// Banner1
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'banner1_image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'banner1_image',
					'type'        => 'attach_image',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner1_image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'External image link', 'konte-addons' ),
					'param_name'  => 'banner1_image_src',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner1_image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Desktop)', 'konte-addons' ),
					'param_name'       => 'banner1_image_position',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 1', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Mobile)', 'konte-addons' ),
					'param_name'       => 'banner1_image_position_mobile',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 1', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Desktop)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner1_image_position_custom',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 1', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner1_image_position',
						'value'   => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Mobile)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner1_image_position_mobile_custom',
					'edit_field_class' => 'vc_col-xs-6 vc_pull-right',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 1', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner1_image_position_mobile',
						'value'   => 'custom',
					),
				),
				array(
					'param_name'       => 'banner1_class',
					'type'             => 'hidden',
					'value'            => '',
					'group'            => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Link', 'konte-addons' ),
					'description' => esc_html__( 'Banner URL', 'konte-addons' ),
					'param_name'  => 'banner1_link',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'vc_link',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Button', 'konte-addons' ),
					'description' => esc_html__( 'Button Text', 'konte-addons' ),
					'param_name'  => 'banner1_button',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'textfield',
					'value'        => esc_html__( 'Shop Now', 'konte-addons' ),
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Tagline', 'konte-addons' ),
					'description' => esc_html__( 'Banner tagline', 'konte-addons' ),
					'param_name'  => 'banner1_tagline',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text', 'konte-addons' ),
					'description' => esc_html__( 'Banner text', 'konte-addons' ),
					'param_name'  => 'banner1_text',
					'type'        => 'textarea_safe',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Position', 'konte-addons' ),
					'description' => esc_html__( 'Banner text position', 'konte-addons' ),
					'param_name'  => 'banner1_text_position',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left',
						esc_html__( 'Center', 'konte-addons' )          => 'center',
						esc_html__( 'Right', 'konte-addons' )           => 'right',
						esc_html__( 'Top Left', 'konte-addons' )        => 'top-left',
						esc_html__( 'Top', 'konte-addons' )             => 'top-center',
						esc_html__( 'Top Right', 'konte-addons' )       => 'top-right',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'bottom-left',
						esc_html__( 'Bottom', 'konte-addons' )          => 'bottom-center',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'bottom-right',
					),
					'std'         => 'top-left',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Color', 'konte-addons' ),
					'description' => esc_html__( 'Banner text color', 'konte-addons' ),
					'param_name'  => 'banner1_text_color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Default', 'konte-addons' ) => 'default',
						esc_html__( 'White', 'konte-addons' ) => 'light',
						esc_html__( 'Dark', 'konte-addons' )  => 'dark',
					),
					'std'         => 'dark',
					'group'       => esc_html__( 'Banner 1', 'konte-addons' ),
				),
				// Banner 2.
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'banner2_image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'banner2_image',
					'type'        => 'attach_image',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner2_image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'External image link', 'konte-addons' ),
					'param_name'  => 'banner2_image_src',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner2_image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Desktop)', 'konte-addons' ),
					'param_name'       => 'banner2_image_position',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 2', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Mobile)', 'konte-addons' ),
					'param_name'       => 'banner2_image_position_mobile',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 2', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Desktop)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner2_image_position_custom',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 2', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner2_image_position',
						'value'   => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Mobile)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner2_image_position_mobile_custom',
					'edit_field_class' => 'vc_col-xs-6 vc_pull-right',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 2', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner2_image_position_mobile',
						'value'   => 'custom',
					),
				),
				array(
					'param_name'       => 'banner2_class',
					'type'             => 'hidden',
					'value'            => '',
					'group'            => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Link', 'konte-addons' ),
					'description' => esc_html__( 'Banner URL', 'konte-addons' ),
					'param_name'  => 'banner2_link',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'vc_link',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Button', 'konte-addons' ),
					'description' => esc_html__( 'Button Text', 'konte-addons' ),
					'param_name'  => 'banner2_button',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'textfield',
					'value'        => esc_html__( 'Shop Now', 'konte-addons' ),
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Tagline', 'konte-addons' ),
					'description' => esc_html__( 'Banner tagline', 'konte-addons' ),
					'param_name'  => 'banner2_tagline',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text', 'konte-addons' ),
					'description' => esc_html__( 'Banner text', 'konte-addons' ),
					'param_name'  => 'banner2_text',
					'type'        => 'textarea_safe',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Position', 'konte-addons' ),
					'description' => esc_html__( 'Banner text position', 'konte-addons' ),
					'param_name'  => 'banner2_text_position',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left',
						esc_html__( 'Center', 'konte-addons' )          => 'center',
						esc_html__( 'Right', 'konte-addons' )           => 'right',
						esc_html__( 'Top Left', 'konte-addons' )        => 'top-left',
						esc_html__( 'Top', 'konte-addons' )             => 'top-center',
						esc_html__( 'Top Right', 'konte-addons' )       => 'top-right',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'bottom-left',
						esc_html__( 'Bottom', 'konte-addons' )          => 'bottom-center',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'bottom-right',
					),
					'std'         => 'top-left',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Color', 'konte-addons' ),
					'description' => esc_html__( 'Banner text color', 'konte-addons' ),
					'param_name'  => 'banner2_text_color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Default', 'konte-addons' ) => 'default',
						esc_html__( 'White', 'konte-addons' ) => 'light',
						esc_html__( 'Dark', 'konte-addons' )  => 'dark',
					),
					'std'         => 'dark',
					'group'       => esc_html__( 'Banner 2', 'konte-addons' ),
				),
				// Banner 3.
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'banner3_image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'banner3_image',
					'type'        => 'attach_image',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner3_image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'External image link', 'konte-addons' ),
					'param_name'  => 'banner3_image_src',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner3_image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Desktop)', 'konte-addons' ),
					'param_name'       => 'banner3_image_position',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 3', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Mobile)', 'konte-addons' ),
					'param_name'       => 'banner3_image_position_mobile',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 3', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Desktop)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner3_image_position_custom',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 3', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner3_image_position',
						'value'   => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Mobile)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner3_image_position_mobile_custom',
					'edit_field_class' => 'vc_col-xs-6 vc_pull-right',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 3', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner3_image_position_mobile',
						'value'   => 'custom',
					),
				),
				array(
					'param_name'       => 'banner3_class',
					'type'             => 'hidden',
					'value'            => '',
					'group'            => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Link', 'konte-addons' ),
					'description' => esc_html__( 'Banner URL', 'konte-addons' ),
					'param_name'  => 'banner3_link',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'vc_link',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Button', 'konte-addons' ),
					'description' => esc_html__( 'Button Text', 'konte-addons' ),
					'param_name'  => 'banner3_button',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'textfield',
					'value'        => esc_html__( 'Shop Now', 'konte-addons' ),
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Tagline', 'konte-addons' ),
					'description' => esc_html__( 'Banner tagline', 'konte-addons' ),
					'param_name'  => 'banner3_tagline',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text', 'konte-addons' ),
					'description' => esc_html__( 'Banner text', 'konte-addons' ),
					'param_name'  => 'banner3_text',
					'type'        => 'textarea_safe',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Position', 'konte-addons' ),
					'description' => esc_html__( 'Banner text position', 'konte-addons' ),
					'param_name'  => 'banner3_text_position',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left',
						esc_html__( 'Center', 'konte-addons' )          => 'center',
						esc_html__( 'Right', 'konte-addons' )           => 'right',
						esc_html__( 'Top Left', 'konte-addons' )        => 'top-left',
						esc_html__( 'Top', 'konte-addons' )             => 'top-center',
						esc_html__( 'Top Right', 'konte-addons' )       => 'top-right',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'bottom-left',
						esc_html__( 'Bottom', 'konte-addons' )          => 'bottom-center',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'bottom-right',
					),
					'std'         => 'top-left',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Color', 'konte-addons' ),
					'description' => esc_html__( 'Banner text color', 'konte-addons' ),
					'param_name'  => 'banner3_text_color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Default', 'konte-addons' ) => 'default',
						esc_html__( 'White', 'konte-addons' ) => 'light',
						esc_html__( 'Dark', 'konte-addons' )  => 'dark',
					),
					'std'         => 'dark',
					'group'       => esc_html__( 'Banner 3', 'konte-addons' ),
				),
				// Banner 4.
				array(
					'heading'    => esc_html__( 'Image Source', 'konte-addons' ),
					'param_name' => 'banner4_image_source',
					'type'       => 'dropdown',
					'std'        => 'media_library',
					'value'      => array(
						esc_html__( 'Media library', 'konte-addons' ) => 'media_library',
						esc_html__( 'External link', 'konte-addons' ) => 'external_link',
					),
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'description' => esc_html__( 'Upload Image', 'konte-addons' ),
					'param_name'  => 'banner4_image',
					'type'        => 'attach_image',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner4_image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'description' => esc_html__( 'External image link', 'konte-addons' ),
					'param_name'  => 'banner4_image_src',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
					'dependency'  => array(
						'element' => 'banner4_image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Desktop)', 'konte-addons' ),
					'param_name'       => 'banner4_image_position',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'top-left',
					'group'            => esc_html__( 'Banner 4', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Image Position (Mobile)', 'konte-addons' ),
					'param_name'       => 'banner4_image_position_mobile',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'dropdown',
					'std'              => 'center center',
					'group'            => esc_html__( 'Banner 4', 'konte-addons' ),
					'value'            => array(
						esc_html__( 'Left', 'konte-addons' )            => 'left center',
						esc_html__( 'Center', 'konte-addons' )          => 'center center',
						esc_html__( 'Right', 'konte-addons' )           => 'right center',
						esc_html__( 'Top Left', 'konte-addons' )        => 'left top',
						esc_html__( 'Top', 'konte-addons' )             => 'center top',
						esc_html__( 'Top Right', 'konte-addons' )       => 'right top',
						esc_html__( 'Bottom Left', 'konte-addons' )     => 'left bottom',
						esc_html__( 'Bottom', 'konte-addons' )          => 'center bottom',
						esc_html__( 'Bottom Right', 'konte-addons' )    => 'right bottom',
						esc_html__( 'Custom Position', 'konte-addons' ) => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Desktop)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner4_image_position_custom',
					'edit_field_class' => 'vc_col-xs-6',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 4', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner4_image_position',
						'value'   => 'custom',
					),
				),
				array(
					'heading'          => esc_html__( 'Custom Image Position (Mobile)', 'konte-addons' ),
					'description'      => esc_html__( 'Enter value in format "vertical horizontal". Example: "10px 40px" or "30% 50%"', 'konte-addons' ),
					'param_name'       => 'banner4_image_position_mobile_custom',
					'edit_field_class' => 'vc_col-xs-6 vc_pull-right',
					'type'             => 'textfield',
					'std'              => '50% 50%',
					'group'            => esc_html__( 'Banner 4', 'konte-addons' ),
					'dependency'       => array(
						'element' => 'banner4_image_position_mobile',
						'value'   => 'custom',
					),
				),
				array(
					'param_name'       => 'banner4_class',
					'type'             => 'hidden',
					'value'            => '',
					'group'            => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Link', 'konte-addons' ),
					'description' => esc_html__( 'Banner URL', 'konte-addons' ),
					'param_name'  => 'banner4_link',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'vc_link',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Button', 'konte-addons' ),
					'description' => esc_html__( 'Button Text', 'konte-addons' ),
					'param_name'  => 'banner4_button',
					'edit_field_class' => 'vc_col-xs-6',
					'type'        => 'textfield',
					'value'        => esc_html__( 'Shop Now', 'konte-addons' ),
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Tagline', 'konte-addons' ),
					'description' => esc_html__( 'Banner tagline', 'konte-addons' ),
					'param_name'  => 'banner4_tagline',
					'type'        => 'textfield',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text', 'konte-addons' ),
					'description' => esc_html__( 'Banner text', 'konte-addons' ),
					'param_name'  => 'banner4_text',
					'type'        => 'textarea_safe',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Position', 'konte-addons' ),
					'description' => esc_html__( 'Banner text position', 'konte-addons' ),
					'param_name'  => 'banner4_text_position',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left', 'konte-addons' )         => 'left',
						esc_html__( 'Center', 'konte-addons' )       => 'center',
						esc_html__( 'Right', 'konte-addons' )        => 'right',
						esc_html__( 'Top Left', 'konte-addons' )     => 'top-left',
						esc_html__( 'Top', 'konte-addons' )          => 'top-center',
						esc_html__( 'Top Right', 'konte-addons' )    => 'top-right',
						esc_html__( 'Bottom Left', 'konte-addons' )  => 'bottom-left',
						esc_html__( 'Bottom', 'konte-addons' )       => 'bottom-center',
						esc_html__( 'Bottom Right', 'konte-addons' ) => 'bottom-right',
					),
					'std'         => 'top-left',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
				array(
					'heading'     => esc_html__( 'Text Color', 'konte-addons' ),
					'description' => esc_html__( 'Banner text color', 'konte-addons' ),
					'param_name'  => 'banner4_text_color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Default', 'konte-addons' ) => 'default',
						esc_html__( 'White', 'konte-addons' ) => 'light',
						esc_html__( 'Dark', 'konte-addons' )  => 'dark',
					),
					'std'         => 'dark',
					'group'       => esc_html__( 'Banner 4', 'konte-addons' ),
				),
			),
		) );

		// Subscribe Box.
		$forms = get_posts( array( 'post_type' => 'mc4wp-form', 'numberposts' => -1 ));

		if ( $forms ) {
			$options = array();

			foreach( $forms as $form ) {
				$options[$form->post_title . " - ID: $form->ID"] = $form->ID;
			}

			vc_map(array(
				'name' => esc_html__('Subscribe Box', 'konte-addons'),
				'description' => esc_html__('MailChimp subscribe form', 'konte-addons'),
				'base' => 'konte_subscribe_box',
				'icon' => self::get_icon('mail.png'),
				'category' => esc_html__('Konte', 'konte-addons'),
				'params' => array(
					array(
						'heading' => esc_html__('Title', 'konte-addons'),
						'admin_label' => true,
						'type' => 'textfield',
						'param_name' => 'title',
					),
					array(
						'heading' => esc_html__('Description', 'konte-addons'),
						'admin_label' => true,
						'type' => 'textarea',
						'param_name' => 'content',
					),
					array(
						'heading' => esc_html__('Style', 'konte-addons'),
						'param_name' => 'style',
						'type' => 'dropdown',
						'std' => 'style1',
						'value' => array(
							esc_html__('Style 1', 'konte-addons') => 'style1',
							esc_html__('Style 2', 'konte-addons') => 'style2',
						),
					),
					array(
						'heading' => esc_html__('Form', 'konte-addons'),
						'description' => esc_html__('Select the MailChimp form', 'konte-addons'),
						'param_name' => 'form_id',
						'type' => 'dropdown',
						'value' => $options,
					),
					vc_map_add_css_animation(),
					array(
						'heading' => esc_html__('Extra class name', 'konte-addons'),
						'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
						'param_name' => 'el_class',
						'type' => 'textfield',
						'value' => '',
					),
					array(
						'type' => 'css_editor',
						'heading' => esc_html__('CSS box', 'konte-addons'),
						'param_name' => 'css',
						'group' => esc_html__('Design Options', 'konte-addons'),
					),
				),
			));
		}

		// Empty Space.
		vc_map(array(
			'name' => esc_html__('Empty Space Advanced', 'konte-addons'),
			'description' => esc_html__('Empty spacing with resposive options', 'konte-addons'),
			'base' => 'konte_empty_space',
			'icon' => self::get_icon('empty.png'),
			'category' => esc_html__('Konte', 'konte-addons'),
			'params' => array(
				array(
					'heading' => esc_html__('Height', 'konte-addons'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'height',
					'value' => '32px',
				),
				array(
					'heading' => esc_html__('Extra class name', 'konte-addons'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'heading'          => esc_html__('Desktop', 'konte-addons'),
					'type'             => 'textfield',
					'param_name'       => 'height_lg',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
				array(
					'heading'          => esc_html__('Hide', 'konte-addons'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_lg',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
				array(
					'heading'          => esc_html__('Tablet', 'konte-addons'),
					'type'             => 'textfield',
					'param_name'       => 'height_md',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
				array(
					'heading'          => esc_html__('Hide', 'konte-addons'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_md',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
				array(
					'heading'          => esc_html__('Mobile', 'konte-addons'),
					'type'             => 'textfield',
					'param_name'       => 'height_xs',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
				array(
					'heading'          => esc_html__('Hide', 'konte-addons'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_xs',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options', 'konte-addons'),
				),
			),
		));

		// Instagram.
		vc_map(
			array(
				'name'        => esc_html__( 'Instagram', 'konte-addons' ),
				'base'        => 'konte_instagram',
				'description' => esc_html__( 'Instagram photos', 'konte-addons' ),
				'icon'        => self::get_icon( 'instagram.png' ),
				'class'       => '',
				'category'    => esc_html__( 'Konte', 'konte-addons' ),
				'params'      => array(
					array(
						'heading'    => esc_html__( 'Number of Photos', 'konte-addons' ),
						'param_name' => 'limit',
						'type'       => 'textfield',
						'value'      => 16,
					),
					array(
						'heading'    => esc_html__( 'Columns', 'konte-addons' ),
						'param_name' => 'columns',
						'std'        => '8',
						'type'       => 'dropdown',
						'value'      => array(
							esc_html__( '2 Columns', 'konte-addons' ) => '2',
							esc_html__( '3 Columns', 'konte-addons' ) => '3',
							esc_html__( '4 Columns', 'konte-addons' ) => '4',
							esc_html__( '5 Columns', 'konte-addons' ) => '5',
							esc_html__( '6 Columns', 'konte-addons' ) => '6',
							esc_html__( '7 Columns', 'konte-addons' ) => '7',
							esc_html__( '8 Columns', 'konte-addons' ) => '8',
						),
					),
					array(
						'heading'    => esc_html__( 'Image Size', 'konte-addons' ),
						'param_name' => 'size',
						'std'        => 'cropped',
						'type'       => 'dropdown',
						'value'      => array(
							esc_html__( 'Square', 'konte-addons' ) => 'cropped',
							esc_html__( 'Original', 'konte-addons' ) => 'original',
						),
					),
					vc_map_add_css_animation(),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
						'param_name'  => 'el_class',
						'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					),
				),
			)
		);

		// Instagram Carousel
		vc_map(
			array(
				'name'        => esc_html__( 'Instagram Carousel', 'konte-addons' ),
				'base'        => 'konte_instagram_carousel',
				'description' => esc_html__( 'Instagram photos carousel', 'konte-addons' ),
				'icon'        => self::get_icon( 'instagram.png' ),
				'category'    => esc_html__( 'Konte', 'konte-addons' ),
				'params'      => array(
					array(
						'heading'    => esc_html__( 'Number of Photos', 'konte-addons' ),
						'param_name' => 'limit',
						'type'       => 'textfield',
						'value'      => 16,
					),
					array(
						'heading'    => esc_html__( 'Image Size', 'konte-addons' ),
						'param_name' => 'size',
						'std'        => 'cropped',
						'type'       => 'dropdown',
						'value'      => array(
							esc_html__( 'Square', 'konte-addons' ) => 'cropped',
							esc_html__( 'Original', 'konte-addons' ) => 'original',
						),
					),
					array(
						'heading'    => esc_html__( 'Slide To Show', 'konte-addons' ),
						'type'       => 'textfield',
						'param_name' => 'slide',
						'value'      => 6,
					),
					array(
						'heading'    => esc_html__( 'Slide To Scroll', 'konte-addons' ),
						'type'       => 'textfield',
						'param_name' => 'scroll',
						'value'      => 3,
					),
					array(
						'heading'    => esc_html__( 'Infinite Scroll', 'konte-addons' ),
						'type'       => 'checkbox',
						'param_name' => 'infinite',
						'std'        => '1',
						'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => '1' ),
						'edit_field_class' => 'vc_col-xs-4',
					),
					array(
						'heading'    => esc_html__( 'Show Dots', 'konte-addons' ),
						'type'       => 'checkbox',
						'param_name' => 'dots',
						'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => '1' ),
						'edit_field_class' => 'vc_col-xs-4',
					),
					array(
						'heading'    => esc_html__( 'Show Arrows', 'konte-addons' ),
						'type'       => 'checkbox',
						'param_name' => 'arrows',
						'std'        => '1',
						'value'      => array( esc_html__( 'Yes', 'konte-addons' ) => '1' ),
						'edit_field_class' => 'vc_col-xs-4',
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Extra class name', 'konte-addons' ),
						'param_name'  => 'el_class',
						'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'konte-addons' ),
					),
				),
			)
		);
	}

	/**
	 * Get Icon URL
	 *
	 * @param string $file_name The icon file name with extension
	 *
	 * @return string Full URL of icon image
	 */
	public static function get_icon( $file_name ) {
		if ( file_exists( KONTE_ADDONS_DIR . 'assets/icons/' . $file_name ) ) {
			$url = KONTE_ADDONS_URL . 'assets/icons/' . $file_name;
		} else {
			$url = KONTE_ADDONS_URL . 'assets/icons/default.png';
		}

		return $url;
	}

	/**
	 * Get category for auto complete field
	 *
	 * @param string $taxonomy Taxnomy to get terms
	 *
	 * @return array
	 */
	protected static function get_terms( $taxonomy = 'product_cat' ) {
		// We don't want to query all terms again
		if ( isset( self::$terms[ $taxonomy ] ) ) {
			return self::$terms[ $taxonomy ];
		}

		$cats = get_terms( $taxonomy );
		if ( ! $cats || is_wp_error( $cats ) ) {
			return array();
		}

		$categories = array();
		foreach ( $cats as $cat ) {
			$categories[] = array(
				'label' => $cat->name,
				'value' => $cat->slug,
				'group' => 'category',
			);
		}

		// Store this in order to avoid double query this
		self::$terms[ $taxonomy ] = $categories;

		return $categories;
	}

	/**
	 * Suggester for autocomplete by id/name/title/sku
	 *
	 * @param $query
	 *
	 * @return array - id's from products with title/sku.
	 */
	public static function product_id_autocomplete($query) {
		global $wpdb;
		$product_id = (int)$query;
		$post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", $product_id > 0 ? $product_id : -1, stripslashes($query), stripslashes($query)), ARRAY_A);

		$results = array();
		if (is_array($post_meta_infos) && !empty($post_meta_infos)) {
			foreach ($post_meta_infos as $value) {
				$data = array();
				$data['value'] = $value['id'];
				$data['label'] = __('Id', 'konte-addons') . ': ' . $value['id'] . ((strlen($value['title']) > 0) ? ' - ' . __('Title', 'konte-addons') . ': ' . $value['title'] : '') . ((strlen($value['sku']) > 0) ? ' - ' . __('Sku', 'konte-addons') . ': ' . $value['sku'] : '');
				$results[] = $data;
			}
		}

		return $results;
	}

	/**
	 * Find product by id
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return bool|array
	 */
	public static function product_id_autocomplete_render($query) {
		$query = trim($query['value']); // get value from requested

		if (!empty($query)) {
			// get product
			$product_object = wc_get_product((int)$query);
			if (is_object($product_object)) {
				$product_sku = $product_object->get_sku();
				$product_title = $product_object->get_title();
				$product_id = $product_object->get_id();

				$product_sku_display = '';
				if (!empty($product_sku)) {
					$product_sku_display = ' - ' . __('Sku', 'konte-addons') . ': ' . $product_sku;
				}

				$product_title_display = '';
				if (!empty($product_title)) {
					$product_title_display = ' - ' . __('Title', 'konte-addons') . ': ' . $product_title;
				}

				$product_id_display = __('Id', 'konte-addons') . ': ' . $product_id;

				$data = array();
				$data['value'] = $product_id;
				$data['label'] = $product_id_display . $product_title_display . $product_sku_display;

				return !empty($data) ? $data : false;
			}

			return false;
		}

		return false;
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Konte_Carousel extends WPBakeryShortCodesContainer {}
	class WPBakeryShortCode_Konte_Testimonial_Carousel extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Konte_Testimonial extends WPBakeryShortCode {}
	class WPBakeryShortCode_Konte_Icon_Box extends WPBakeryShortCode {}
	class WPBakeryShortCode_Konte_Member extends WPBakeryShortCode {}
	class WPBakeryShortCode_Konte_Carousel_Item extends WPBakeryShortCode {}
}

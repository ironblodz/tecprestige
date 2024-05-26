<?php
/**
 * Theme options functions
 *
 * @package Konte
 */

/**
 * Get setting's value from the theme's customizer
 *
 * @param string $name Option name.
 *
 * @return mixed
 */
function konte_get_option( $name ) {
	global $konte_customize, $konte_options;

	if ( isset( $konte_options[ $name ] ) && ! is_customize_preview() ) {
		$value = $konte_options[ $name ];
	} elseif ( is_object( $konte_customize ) ) {
		$value = $konte_customize->get_option( $name );
	} else {
		$value = get_theme_mod( $name );
		$value = false !== $value ? $value : konte_get_option_default( $name );
	}

	$konte_options[ $name ] = $value;

	return apply_filters( 'konte_get_option', $value, $name );
}

/**
 * Get default option values
 *
 * @param string $name Option name.
 *
 * @return mixed
 */
function konte_get_option_default( $name ) {
	global $konte_customize;

	if ( is_object( $konte_customize ) ) {
		return $konte_customize->get_option_default( $name );
	}

	$config   = konte_get_customize_config();
	$settings = array_reduce( $config['settings'], 'array_merge', array() );

	if ( ! isset( $settings[ $name ] ) ) {
		return false;
	}

	return isset( $settings[ $name ]['default'] ) ? $settings[ $name ]['default'] : false;
}

/**
 * Options of topbar items
 *
 * @return array
 */
function konte_topbar_items_option() {
	return apply_filters( 'konte_topbar_items_option', array(
		'menu'     => esc_html__( 'Topbar Menu', 'konte' ),
		'socials'  => esc_html__( 'Socials Menu', 'konte' ),
		'currency' => esc_html__( 'Currency Switcher', 'konte' ),
		'language' => esc_html__( 'Language Switcher', 'konte' ),
		'text'     => esc_html__( 'Custom Text', 'konte' ),
		'close'    => esc_html__( 'Close Icon', 'konte' ),
	) );
}

/**
 * Options of header items
 *
 * @return array
 */
function konte_header_items_option() {
	return apply_filters( 'konte_header_items_option', array(
		'logo'           => esc_html__( 'Logo', 'konte' ),
		'menu-primary'   => esc_html__( 'Primary Menu', 'konte' ),
		'menu-secondary' => esc_html__( 'Secondary Menu', 'konte' ),
		'menu-socials'   => esc_html__( 'Socials Menu', 'konte' ),
		'hamburger'      => esc_html__( 'Hamburger Icon', 'konte' ),
		'search'         => esc_html__( 'Search Icon', 'konte' ),
		'cart'           => esc_html__( 'Cart Icon', 'konte' ),
		'wishlist'       => esc_html__( 'Wishlist Icon', 'konte' ),
		'account'        => esc_html__( 'Sign In', 'konte' ),
		'language'       => esc_html__( 'Language Switcher', 'konte' ),
		'currency'       => esc_html__( 'Currency Switcher', 'konte' ),
		'text'           => esc_html__( 'Custom Text', 'konte' ),
	) );
}

/**
 * Options of mobile header icons
 *
 * @return array
 */
function konte_mobile_header_icons_option() {
	return apply_filters( 'konte_mobile_header_icons_option', array(
		'cart'     => esc_html__( 'Cart Icon', 'konte' ),
		'wishlist' => esc_html__( 'Wishlist Icon', 'konte' ),
		'search'   => esc_html__( 'Search Icon', 'konte' ),
		'account'  => esc_html__( 'Sign In', 'konte' ),
	) );
}

/**
 * Options of mobile bottom navigation items
 *
 * @since 2.3.0
 *
 * @return array
 */
function konte_mobile_bottom_bar_items_option() {
	return apply_filters( 'konte_mobile_bottom_bar_items_option', array(
		'home'     => esc_html__( 'Home', 'konte' ),
		'menu'     => esc_html__( 'Menu', 'konte' ),
		'cart'     => esc_html__( 'Shopping Cart', 'konte' ),
		'wishlist' => esc_html__( 'Wishlist', 'konte' ),
		'account'  => esc_html__( 'My Account', 'konte' ),
		'search'   => esc_html__( 'Search', 'konte' ),
	) );
}

/**
 * Options of footer items
 *
 * @return array
 */
function konte_footer_items_option() {
	return apply_filters( 'konte_footer_items_option', array(
		'copyright'         => esc_html__( 'Copyright', 'konte' ),
		'menu'              => esc_html__( 'Footer menu', 'konte' ),
		'social'            => esc_html__( 'Social menu', 'konte' ),
		'currency'          => esc_html__( 'Currency Switcher', 'konte' ),
		'language'          => esc_html__( 'Language Switcher', 'konte' ),
		'currency_language' => esc_html__( 'Currency and Language Switchers', 'konte' ),
		'text'              => esc_html__( 'Custom text', 'konte' ),
	) );
}

/**
 * Get the list of fonts for Kirki
 *
 * @return array
 */
function konte_customizer_fonts_choices() {
	$custom_fonts = apply_filters( 'konte_custom_fonts_options', array(
		'families' => array(
			array( 'id' => 'function_pro', 'text' => 'Function Pro' ),
		),
		'variants' => array(
			'function_pro' => array( '300', 'regular', '500', '600', '700', ),
		),
	) );

	$fonts = array(
		'standard' => array( 'serif', 'sans-serif', 'monospace' ),
		'google'   => array(),
	);

	if ( ! empty( $custom_fonts['families'] ) ) {
		$fonts['families'] = array(
			'custom' => array(
				'text'     => esc_html__( 'Konte Custom Fonts', 'konte' ),
				'children' => $custom_fonts['families'],
			),
		);

		if ( ! empty( $custom_fonts['variants'] ) ) {
			$fonts['variants'] = $custom_fonts['variants'];
		}
	}

	return apply_filters( 'konte_customize_fonts_choices', array(
		'fonts' => $fonts,
	) );
}

/**
 * Theme customizer configurations.
 * Register panels, sections, settings and theme.
 *
 * @return array
 */
function konte_get_customize_config() {
	// Panels.
	$panels = array(
		'general'    => array(
			'priority' => 10,
			'title'    => esc_html__( 'General', 'konte' ),
		),
		'typography' => array(
			'priority' => 30,
			'title'    => esc_html__( 'Typography', 'konte' ),
		),
		'header'     => array(
			'priority' => 210,
			'title'    => esc_html__( 'Header', 'konte' ),
		),
		'blog'       => array(
			'priority' => 260,
			'title'    => esc_html__( 'Blog', 'konte' ),
		),
		'footer'     => array(
			'priority' => 450,
			'title'    => esc_html__( 'Footer', 'konte' ),
		),
		'tablet'     => array(
			'priority' => 460,
			'title'    => esc_html__( 'Tablet', 'konte' ),
		),
		'mobile'     => array(
			'priority' => 470,
			'title'    => esc_html__( 'Mobile', 'konte' ),
		),
	);

	// Sections.
	$sections = array(
		'api_keys'          => array(
			'title'    => esc_html__( 'API Keys', 'konte' ),
			'priority' => 200,
			'panel'    => 'general',
		),
		'preloader'         => array(
			'title'    => esc_html__( 'Preloader', 'konte' ),
			'priority' => 210,
			'panel'    => 'general',
		),
		'popup'             => array(
			'title'    => esc_html__( 'Popup', 'konte' ),
			'priority' => 220,
			'panel'    => 'general',
		),
		'language_switcher'             => array(
			'title'    => esc_html__( 'Languages', 'konte' ),
			'priority' => 230,
			'panel'    => 'general',
		),
		'maintenance'       => array(
			'title'    => esc_html__( 'Maintenance', 'konte' ),
			'priority' => 20,
		),
		'typo_main'         => array(
			'title'    => esc_html__( 'Main', 'konte' ),
			'priority' => 10,
			'panel'    => 'typography',
		),
		'typo_headings'     => array(
			'title'    => esc_html__( 'Headings', 'konte' ),
			'priority' => 20,
			'panel'    => 'typography',
		),
		'typo_header'       => array(
			'title'    => esc_html__( 'Header', 'konte' ),
			'priority' => 30,
			'panel'    => 'typography',
		),
		'typo_page'         => array(
			'title'    => esc_html__( 'Page', 'konte' ),
			'priority' => 40,
			'panel'    => 'typography',
		),
		'typo_posts'        => array(
			'title'    => esc_html__( 'Blog', 'konte' ),
			'priority' => 50,
			'panel'    => 'typography',
		),
		'typo_widget'       => array(
			'title'    => esc_html__( 'Widgets', 'konte' ),
			'priority' => 60,
			'panel'    => 'typography',
		),
		'typo_footer'       => array(
			'title'    => esc_html__( 'Footer', 'konte' ),
			'priority' => 70,
			'panel'    => 'typography',
		),
		'layout'            => array(
			'title'    => esc_html__( 'Site Layout', 'konte' ),
			'priority' => 20,
		),
		'colors'            => array(
			'title'    => esc_html__( 'Colors', 'konte' ),
			'priority' => 40,
		),
		'header_top'        => array(
			'title'    => esc_html__( 'Topbar', 'konte' ),
			'priority' => 10,
			'panel'    => 'header',
		),
		'header_layout'     => array(
			'title'    => esc_html__( 'Header Layout', 'konte' ),
			'priority' => 20,
			'panel'    => 'header',
		),
		'header_background' => array(
			'title'    => esc_html__( 'Header Background', 'konte' ),
			'priority' => 30,
			'panel'    => 'header',
		),
		'header_main'       => array(
			'title'    => esc_html__( 'Header Main', 'konte' ),
			'priority' => 40,
			'panel'    => 'header',
		),
		'header_bottom'     => array(
			'title'    => esc_html__( 'Header Bottom', 'konte' ),
			'priority' => 50,
			'panel'    => 'header',
		),
		'header_campaign'   => array(
			'title'    => esc_html__( 'Campaign Bar', 'konte' ),
			'priority' => 55,
			'panel'    => 'header',
		),
		'logo'              => array(
			'title'    => esc_html__( 'Logo', 'konte' ),
			'priority' => 60,
			'panel'    => 'header',
		),
		'header_menu'       => array(
			'title'    => esc_html__( 'Menus', 'konte' ),
			'priority' => 70,
			'panel'    => 'header',
		),
		'header_search'     => array(
			'title'    => esc_html__( 'Search', 'konte' ),
			'priority' => 80,
			'panel'    => 'header',
		),
		'header_cart'       => array(
			'title'    => esc_html__( 'Cart', 'konte' ),
			'priority' => 90,
			'panel'    => 'header',
		),
		'header_account'    => array(
			'title'    => esc_html__( 'Account', 'konte' ),
			'priority' => 100,
			'panel'    => 'header',
		),
		'header_hamburger'  => array(
			'title'    => esc_html__( 'Full-Screen Menu', 'konte' ),
			'priority' => 110,
			'panel'    => 'header',
		),
		'blog_header'       => array(
			'title'    => esc_html__( 'Blog Header', 'konte' ),
			'priority' => 10,
			'panel'    => 'blog',
		),
		'blog_archive'      => array(
			'title'    => esc_html__( 'Blog Archive', 'konte' ),
			'priority' => 20,
			'panel'    => 'blog',
		),
		'blog_single'       => array(
			'title'    => esc_html__( 'Blog Single', 'konte' ),
			'priority' => 30,
			'panel'    => 'blog',
		),
		'pages'             => array(
			'title'    => esc_html__( 'Pages', 'konte' ),
			'priority' => 270,
		),
		'footer_layout'     => array(
			'title'    => esc_html__( 'Footer Layout', 'konte' ),
			'priority' => 10,
			'panel'    => 'footer',
		),
		'footer_background' => array(
			'title'    => esc_html__( 'Footer Background', 'konte' ),
			'priority' => 20,
			'panel'    => 'footer',
		),
		'footer_extra'      => array(
			'title'    => esc_html__( 'Footer Extra', 'konte' ),
			'priority' => 30,
			'panel'    => 'footer',
		),
		'footer_widgets'    => array(
			'title'    => esc_html__( 'Footer Widgets', 'konte' ),
			'priority' => 40,
			'panel'    => 'footer',
		),
		'footer_instagram'  => array(
			'title'    => esc_html__( 'Footer Instagram', 'konte' ),
			'priority' => 50,
			'panel'    => 'footer',
		),
		'footer_main'       => array(
			'title'    => esc_html__( 'Footer Main', 'konte' ),
			'priority' => 60,
			'panel'    => 'footer',
		),
		'mobile_header'     => array(
			'title'    => esc_html__( 'Header', 'konte' ),
			'panel'    => 'mobile',
			'priority' => 10,
		),
		'mobile_menu'       => array(
			'title'    => esc_html__( 'Menu', 'konte' ),
			'panel'    => 'mobile',
			'priority' => 20,
		),
		'mobile_bottom_bar' => array(
			'title' => esc_html__( 'Bottom Bar', 'konte' ),
			'panel' => 'mobile',
			'priority' => 30,
		),
	);

	// Settings.
	$images_uri = get_template_directory_uri() . '/images/options';
	$settings   = array();

	// API Keys.
	$settings['api_keys'] = array(
		'api_google_map'      => array(
			'type'        => 'text',
			'label'       => esc_html__( 'Google Map API', 'konte' ),
			'description' => esc_html__( 'This API key is required to display Google Maps on your website.', 'konte' ) . ' <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . esc_html__( 'Get API key', 'konte' ) . '</a>',
		),
		'api_instagram_token' => array(
			'type'        => 'text',
			'label'       => esc_html__( 'Instagram Access Token', 'konte' ),
			'description' => esc_html__( 'This Access Token is required to display your Instagram photos on this website.', 'konte' ) . ' <a href="https://tools.uix.store/instagram-access-token/" target="_blank">' . esc_html__( 'Get my Access Token', 'konte' ) . '</a>',
		),
	);

	// Popup.
	$settings['popup'] = array(
		'popup_enable'        => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Popup', 'konte' ),
			'description' => esc_html__( 'Show a popup after website loaded.', 'konte' ),
			'section'     => 'popup',
			'default'     => false,
			'transport'   => 'postMessage',
		),
		'popup_layout'        => array(
			'type'            => 'radio-buttonset',
			'label'           => esc_html__( 'Popup Layout', 'konte' ),
			'default'         => '2-columns',
			'transport'       => 'postMessage',
			'choices'         => array(
				'1-column'  => esc_attr__( '1 Column', 'konte' ),
				'2-columns' => esc_attr__( '2 Columns', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_image'         => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Image', 'konte' ),
			'description'     => esc_html__( 'This image will be used as background of the popup if the layout is 1 Column', 'konte' ),
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'popup_content'       => array(
			'type'            => 'editor',
			'label'           => esc_html__( 'Popup Content', 'konte' ),
			'description'     => esc_html__( 'Enter popup content. HTML and shortcodes are allowed.', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'partial_refresh' => array(
				'popup_content' => array(
					'selector'        => '#popup-modal .popup-content-wrapper',
					'render_callback' => function() {
						echo do_shortcode( wp_kses_post( konte_get_option( 'popup_content' ) ) );
					},
				),
			),
		),
		'popup_frequency'     => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Frequency', 'konte' ),
			'description'     => esc_html__( 'Do NOT show the popup to the same visitor again until this much days has passed.', 'konte' ),
			'default'         => 1,
			'choices'         => array(
				'min'  => 0,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
		),
		'popup_visible'       => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Popup Visible', 'konte' ),
			'description'     => esc_html__( 'Select when the popup appear', 'konte' ),
			'default'         => 'loaded',
			'choices'         => array(
				'loaded' => esc_html__( 'Right after page loads', 'konte' ),
				'delay'  => esc_html__( 'Wait for seconds', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
		),
		'popup_visible_delay' => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Delay Time', 'konte' ),
			'description'     => esc_html__( 'The time (in seconds) before the popup is displayed, after the page loaded.', 'konte' ),
			'default'         => 5,
			'choices'         => array(
				'min'  => 0,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'popup_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'popup_visible',
					'operator' => '==',
					'value'    => 'delay',
				),
			),
			'transport'       => 'postMessage',
		),
	);

	// Language Switcher
	$settings['language_switcher'] = array(
		'language_configuration' => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Configuration', 'konte' ),
			'default' => 'name',
			'choices' => array(
				'name' => esc_html__( 'Displays the language name only', 'konte' ),
				'flag' => esc_html__( 'Displays the language flag only', 'konte' ),
				'both' => esc_html__( 'Displays both of name and flag', 'konte' ),
			),
		),
	);

	// Preloader.
	$settings['preloader'] = array(
		'preloader_enable'           => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Preloader', 'konte' ),
			'description' => esc_html__( 'Show a waiting screen when page is loading', 'konte' ),
			'default'     => false,
			'transport'   => 'postMessage',
		),
		'preloader_background_color' => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Background Color', 'konte' ),
			'default'         => 'rgba(255,255,255,1)',
			'choices'         => array(
				'alpha' => true,
			),
			'active_callback' => array(
				array(
					'setting'  => 'preloader_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'   => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => '#preloader',
					'property' => 'background-color',
				),
			),
		),
		'preloader'                  => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Preloader', 'konte' ),
			'default'         => 'default',
			'choices'         => array(
				'default'  => esc_attr__( 'Default Icon', 'konte' ),
				'image'    => esc_attr__( 'Upload custom image', 'konte' ),
				'external' => esc_attr__( 'External image URL', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'preloader_enable',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'partial_refresh' => array(
				'preloader_content' => array(
					'selector'        => '#preloader',
					'container_inclusive' => true,
					'render_callback' => function() {
						get_template_part( 'template-parts/preloader/preloader' );
					},
				),
			),
		),
		'preloader_image'            => array(
			'type'            => 'image',
			'description'     => esc_html__( 'Preloader Image', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'preloader_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'preloader',
					'operator' => '==',
					'value'    => 'image',
				),
			),
			'transport'       => 'postMessage',
		),
		'preloader_url'              => array(
			'type'            => 'text',
			'description'     => esc_html__( 'Preloader URL', 'konte' ),
			'choices'         => array(
				'default'  => esc_attr__( 'Default Icon', 'konte' ),
				'image'    => esc_attr__( 'Upload custom image', 'konte' ),
				'external' => esc_attr__( 'External image URL', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'preloader_enable',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'preloader',
					'operator' => '==',
					'value'    => 'external',
				),
			),
			'transport'       => 'postMessage',
		),
	);

	// Typography - body.
	$settings['typo_main'] = array(
		'typo_body'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Body', 'konte' ),
			'description' => esc_html__( 'Customize the body font', 'konte' ),
			'default'     => array(
				'font-family' => 'function_pro',
				'variant'     => 'regular',
				'font-size'   => '18px',
				'line-height' => '1.55556',
				'color'       => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'body',
				),
			),
		),
	);

	// Typography - headings.
	$settings['typo_headings'] = array(
		'typo_h1'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 1', 'konte' ),
			'description' => esc_html__( 'Customize the H1 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '60px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'h1, .h1',
				),
			),
		),
		'typo_h2'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 2', 'konte' ),
			'description' => esc_html__( 'Customize the H2 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '40px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'output',
			'output'    => array(
				array(
					'element' => 'h2, .h2',
				),
			),
		),
		'typo_h3'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 3', 'konte' ),
			'description' => esc_html__( 'Customize the H3 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '30px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'   => array(
				array(
					'element' => 'h3, .h3',
				),
			),
		),
		'typo_h4'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 4', 'konte' ),
			'description' => esc_html__( 'Customize the H4 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '24px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'h4, .h4',
				),
			),
		),
		'typo_h5'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 5', 'konte' ),
			'description' => esc_html__( 'Customize the H5 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '18px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'h5, .h5',
				),
			),
		),
		'typo_h6'                        => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Heading 6', 'konte' ),
			'description' => esc_html__( 'Customize the H6 font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '16px',
				'line-height'    => '1.55556',
				'color'          => '#161619',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => 'h6, .h6',
				),
			),
		),
	);

	// Typography - header.
	$settings['typo_header'] = array(
		'typo_menu'                      => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Menu', 'konte' ),
			'description' => esc_html__( 'Customize the menu font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '16px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'   => array(
				array(
					'element' => '.main-navigation a, .header-v8 .nav-menu > li > a, .header-v9 .nav-menu > li > a, .header-vertical .main-navigation .nav-menu > li > a',
				),
			),
		),
		'typo_submenu'                   => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Sub-Menu', 'konte' ),
			'description' => esc_html__( 'Customize the sub-menu font', 'konte' ),
			'section'     => 'typo_header',
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '14px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.main-navigation li li a, .header-vertical .main-navigation .sub-menu a',
				),
			),
		),
	);

	// Typography - page.
	$settings['typo_page'] = array(
		'typo_page_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Page Title', 'konte' ),
			'description' => esc_html__( 'Customize the page title font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '80px',
				'line-height'    => '1.16667',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.single-page-header .entry-title, .page .page .entry-title',
				),
			),
		),
		'typo_page_subtitle'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Page Subtitle', 'konte' ),
			'description' => esc_html__( 'Customize the page subtitle font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '24px',
				'line-height'    => '1.55556',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.single-page-header .entry-subtitle',
				),
			),
		),
	);

	// Typography - posts.
	$settings['typo_posts'] = array(
		'typo_blog_header_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Blog Header Title', 'konte' ),
			'description' => esc_html__( 'Customize the font of blog header', 'konte' ),
			'default'     => array(
				'font-family'    => 'Crimson Text',
				'variant'        => '600',
				'font-size'      => '44px',
				'line-height'    => '1.2',
				'text-transform' => 'uppercase',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.blog-header-content .header-title',
				),
			),
		),
		'typo_blog_post_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Blog Post Title', 'konte' ),
			'description' => esc_html__( 'Customize the font of blog post title', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '30px',
				'line-height'    => '1.33333',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.hfeed .hentry .entry-title',
				),
			),
		),
		'typo_blog_post_excerpt'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Blog Post Excerpt', 'konte' ),
			'description' => esc_html__( 'Customize the font of blog post excerpt', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '16px',
				'line-height'    => '1.375',
				'text-transform' => 'none',
				'color'          => '#838889',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.hfeed .hentry .entry-summary',
				),
			),
		),
	);

	// Typography - widgets.
	$settings['typo_widget'] = array(
		'typo_widget_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Widget Title', 'konte' ),
			'description' => esc_html__( 'Customize the widget title font', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '600',
				'font-size'      => '16px',
				'text-transform' => 'uppercase',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.widget-title',
				),
			),
		),
	);

	// Typography - footer.
	$settings['typo_footer'] = array(
		'typo_footer_extra'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Footer Extra', 'konte' ),
			'description' => esc_html__( 'Customize the font of footer extra', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '16px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.footer-extra',
				),
			),
		),
		'typo_footer_widgets'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Footer Widgets', 'konte' ),
			'description' => esc_html__( 'Customize the font of footer widgets', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '14px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.footer-widgets',
				),
			),
		),
		'typo_footer_main'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Footer Main', 'konte' ),
			'description' => esc_html__( 'Customize the font of footer main', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '14px',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices'   => konte_customizer_fonts_choices(),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.footer-main',
				),
			),
		),
	);

	// Layout settings.
	$settings['layout'] = array(
		'layout_default'   => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Default Layout', 'konte' ),
			'description' => esc_html__( 'Default layout of blog and other pages', 'konte' ),
			'default'     => 'sidebar-right',
			'choices'     => array(
				'no-sidebar'    => $images_uri . '/sidebars/empty.png',
				'sidebar-left'  => $images_uri . '/sidebars/single-left.png',
				'sidebar-right' => $images_uri . '/sidebars/single-right.png',
			),
		),
		'layout_post'      => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Post Layout', 'konte' ),
			'description' => esc_html__( 'Default layout of single post', 'konte' ),
			'default'     => 'no-sidebar',
			'choices'     => array(
				'no-sidebar'    => $images_uri . '/sidebars/empty.png',
				'sidebar-left'  => $images_uri . '/sidebars/single-left.png',
				'sidebar-right' => $images_uri . '/sidebars/single-right.png',
			),
		),
		'layout_custom_hr' => array(
			'type'     => 'custom',
			'default'  => '<hr />',
			'priority' => 100,
		),
		'sidebar_sticky'   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Sticky Sidebar', 'konte' ),
			'description' => esc_html__( 'Attachs the sidebar to the page when the user scrolls', 'konte' ),
			'default'     => true,
			'priority'    => 100,
		),
	);

	// Maintenance.
	$settings['maintenance'] = array(
		'maintenance_enable'    => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Maintenance Mode', 'konte' ),
			'description' => esc_html__( 'Put your site into maintenance mode', 'konte' ),
			'default'     => false,
		),
		'maintenance_mode'      => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Mode', 'konte' ),
			'description' => esc_html__( 'Select the correct mode for your site', 'konte' ),
			/* translators: %s link to an external documentation */
			'tooltip'     => wp_kses_post( sprintf( __( 'If you are putting your site into maintenance mode for a longer perior of time, you should set this to "Coming Soon". Maintenance will return HTTP 503, Comming Soon will set HTTP to 200. <a href="%s" target="_blank">Learn more</a>', 'konte' ), 'https://yoast.com/http-503-site-maintenance-seo/' ) ),
			'default'     => 'maintenance',
			'choices'     => array(
				'maintenance' => esc_attr__( 'Maintenance', 'konte' ),
				'coming_soon' => esc_attr__( 'Coming Soon', 'konte' ),
			),
		),
		'maintenance_page'      => array(
			'type'    => 'dropdown-pages',
			'label'   => esc_html__( 'Maintenance Page', 'konte' ),
			'default' => 0,
		),
		'maintenance_layout'    => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Maintenance Page Layout', 'konte' ),
			'tooltip' => esc_html__( 'This option is only applied for the page which is using the Default page template.', 'konte' ),
			'default' => 'default',
			'choices' => array(
				'default'    => esc_attr__( 'Default', 'konte' ),
				'split'      => esc_attr__( 'Splited Content', 'konte' ),
				'fullscreen' => esc_attr__( 'Fullscreen Background', 'konte' ),
			),
		),
		'maintenance_textcolor' => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color', 'konte' ),
			'default'         => 'dark',
			'choices'         => array(
				'dark'  => esc_attr__( 'Dark', 'konte' ),
				'light' => esc_attr__( 'Light', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'maintenance_layout',
					'operator' => '==',
					'value'    => 'fullscreen',
				),
			),
		),
	);

	// Colors.
	$settings['colors'] = array(
		'color_scheme_title'  => array(
			'type'  => 'custom',
			'label' => esc_html__( 'Color Scheme', 'konte' ),
		),
		'color_scheme'        => array(
			'type'            => 'color-palette',
			'default'         => '#161619',
			'choices'         => array(
				'colors' => array(
					'#161619',
					'#053399',
					'#3f51b5',
					'#7b1fa2',
					'#009688',
					'#388e3c',
					'#e64a19',
					'#b8a08d',
				),
				'style'  => 'round',
			),
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'color_scheme_custom',
					'operator' => '!=',
					'value'    => true,
				),
			),
		),
		'color_scheme_custom' => array(
			'type'      => 'checkbox',
			'label'     => esc_html__( 'Pick my favorite color', 'konte' ),
			'default'   => false,
			'transport' => 'postMessage',
		),
		'color_scheme_color'  => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Custom Color', 'konte' ),
			'default'         => '#161619',
			'active_callback' => array(
				array(
					'setting'  => 'color_scheme_custom',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
		),
	);

	// Header topbar.
	$settings['header_top'] = array(
		'topbar'            => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Topbar', 'konte' ),
			'description' => esc_html__( 'Display a bar on the top', 'konte' ),
			'default'     => false,
		),
		'topbar_height'     => array(
			'type'            => 'slider',
			'label'           => esc_html__( 'Height', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => '40',
			'choices'         => array(
				'min' => 30,
				'max' => 240,
			),
			'js_vars'         => array(
				array(
					'element'  => '.topbar',
					'property' => 'height',
					'units'    => 'px',
				),
				array(
					'element'  => '.topbar-menu > li > a',
					'property' => 'line-height',
					'units'    => 'px',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_background' => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Background', 'konte' ),
			'description'     => esc_html__( 'Select background color for the topbar', 'konte' ),
			'default'         => 'dark',
			'choices'         => array(
				'dark'  => esc_html__( 'Dark', 'konte' ),
				'light' => esc_html__( 'Light', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_left'       => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Left Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the left side of the topbar', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_topbar_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_center'       => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Center Items', 'konte' ),
			'description'     => esc_html__( 'Control items at center of the topbar', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_topbar_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_right'      => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Right Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the right side of the topbar', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_topbar_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_custom_hr'  => array(
			'type'            => 'custom',
			'default'         => '<hr />',
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'topbar_text'       => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Custom Text', 'konte' ),
			'description'     => esc_html__( 'The content of Custom Text item', 'konte' ),
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
	);

	// Header layout settings.
	$settings['header_layout'] = array(
		// 'header_position' => array(
		// 	'type'        => 'radio',
		// 	'label'       => esc_html__( 'Header Position', 'konte' ),
		// 	'description' => esc_html__( 'Select the position of site header. Not includes topbar.', 'konte' ),
		// 	'default'     => 'top',
		// 	'choices'     => array(
		// 		'top'  => esc_html__( 'Top', 'konte' ),
		// 		'left' => esc_html__( 'Left', 'konte' ),
		// 	),
		// ),
		'header_present' => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Present', 'konte' ),
			'description' => esc_html__( 'Select a prebuilt header or build your own', 'konte' ),
			'default'     => 'prebuild',
			'choices'     => array(
				'prebuild' => esc_html__( 'Use pre-build header', 'konte' ),
				'custom'   => esc_html__( 'Build my own', 'konte' ),
			),
			'partial_refresh' => array(
				'header_present' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_version' => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Prebuilt Header', 'konte' ),
			'description'     => esc_html__( 'Select a prebuilt header present', 'konte' ),
			'default'         => 'v3',
			'choices'         => array(
				'v1'  => esc_html__( 'Header V1', 'konte' ),
				'v2'  => esc_html__( 'Header V2', 'konte' ),
				'v3'  => esc_html__( 'Header V3', 'konte' ),
				'v4'  => esc_html__( 'Header V4', 'konte' ),
				'v5'  => esc_html__( 'Header V5', 'konte' ),
				'v6'  => esc_html__( 'Header V6', 'konte' ),
				'v7'  => esc_html__( 'Header V7', 'konte' ),
				'v8'  => esc_html__( 'Header V8', 'konte' ),
				'v9'  => esc_html__( 'Header V9', 'konte' ),
				'v10' => esc_html__( 'Header V10', 'konte' ),
			),
			'active_callback' => array(
				// array(
				// 	'setting'  => 'header_position',
				// 	'operator' => '==',
				// 	'value'    => 'top',
				// ),
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'prebuild',
				),
			),
			'partial_refresh' => array(
				'header_version_partial' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_width'   => array(
			'type'            => 'slider',
			'label'           => esc_html__( 'Width', 'konte' ),
			'description'     => esc_html__( 'Vertical header width', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => '360',
			'choices'         => array(
				'min' => 160,
				'max' => 800,
			),
			'js_vars'         => array(
				array(
					'element'  => '.header-v10 .header-main .header-left-items',
					'property' => 'width',
					'units'    => 'px',
				),
				array(
					'element'  => '.header-vertical .site',
					'property' => 'padding-left',
					'units'    => 'px',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'prebuild',
				),
				array(
					'setting'  => 'header_version',
					'operator' => '==',
					'value'    => 'v10',
				),
			),
		),
		'header_layout_hr_1'   => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'header_sticky'   => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Sticky Header', 'konte' ),
			'default'         => 'none',
			'choices'         => array(
				'none'   => esc_attr__( 'No sticky', 'konte' ),
				'normal' => esc_attr__( 'Normal sticky', 'konte' ),
				'smart'  => esc_attr__( 'Smart sticky', 'konte' ),
			),
		),
	);

	// Header background settings.
	$settings['header_background'] = array(
		'header_background'             => array(
			'type'      => 'select',
			'label'     => esc_html__( 'Background', 'konte' ),
			'transport' => 'postMessage',
			'default'   => 'light',
			'choices'   => array(
				'light'       => esc_html__( 'Light', 'konte' ),
				'dark'        => esc_html__( 'Dark', 'konte' ),
				'custom'      => esc_html__( 'Custom', 'konte' ),
			),
		),
		'header_background_color'       => array(
			'type'            => 'color',
			'default'         => '',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'header_background',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.site-header.custom',
					'property' => 'background-color',
				),
			),
		),
		'header_text_color'              => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_background',
					'operator' => 'in',
					'value'    => array( 'custom', 'transparent' ),
				),
			),
		),
		'header_background_hr_1'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'header_background_blog_custom' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Custom Background for Blog', 'konte' ),
			'default' => true,
		),
		'header_background_blog'        => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Background for Blog', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light'       => esc_html__( 'Light', 'konte' ),
				'dark'        => esc_html__( 'Dark', 'konte' ),
				'custom'      => esc_html__( 'Custom', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'header_background_blog_color'  => array(
			'type'            => 'color',
			'default'         => '',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'header_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'header_background_blog',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.blog-hfeed .site-header.custom',
					'property' => 'background-color',
				),
			),
		),
		'header_blog_textcolor'         => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color for Blog', 'konte' ),
			'description'     => esc_html__( 'Select header text color for blog pages', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'header_background_blog',
					'operator' => 'in',
					'value'    => array( 'custom', 'transparent' ),
				),
			),
		),
		'header_background_hr_99'       => array(
			'type'     => 'custom',
			'default'  => '<hr>',
			'priority' => 99,
		),
		'header_transparent_hover'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Header Hover', 'konte' ),
			'description' => esc_html__( 'Changes the header background when mouse over if it is transparent', 'konte' ),
			'default'     => true,
			'priority'    => 99,
		),
	);

	// Header main settings.
	$settings['header_main'] = array(
		'header_main_height' => array(
			'type'      => 'slider',
			'label'     => esc_html__( 'Height', 'konte' ),
			'transport' => 'postMessage',
			'default'   => '120',
			'choices'   => array(
				'min' => 50,
				'max' => 500,
			),
			'js_vars'   => array(
				array(
					'element'  => '.header-main',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		),
		'header_main_text' => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Custom Text', 'konte' ),
			'description'     => esc_html__( 'The content of the Custom Text item', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),
		'header_main_hr'     => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'header_main_left'   => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Left Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the left side of header main', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_main_left' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_main_center' => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Center Items', 'konte' ),
			'description'     => esc_html__( 'Control items at the center of header main', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_main_center' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_main_right'  => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Right Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the right of header main', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_main_right' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
	);

	// Header bottom settings.
	$settings['header_bottom'] = array(
		'header_bottom_height' => array(
			'type'      => 'slider',
			'label'     => esc_html__( 'Height', 'konte' ),
			'transport' => 'postMessage',
			'default'   => '90',
			'choices'   => array(
				'min' => 50,
				'max' => 500,
			),
			'js_vars'   => array(
				array(
					'element'  => '.header-bottom',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		),
		'header_bottom_text' => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Custom Text', 'konte' ),
			'description'     => esc_html__( 'The content of the Custom Text item', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),
		'header_bottom_hr'     => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'header_bottom_left'   => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Left Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the left side of header bottom', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_bottom_left' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_bottom_center' => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Center Items', 'konte' ),
			'description'     => esc_html__( 'Control items at the center of header bottom', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_bottom_center' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_bottom_right'  => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Right Items', 'konte' ),
			'description'     => esc_html__( 'Control items on the right of header bottom', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'          => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_header_items_option(),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'partial_refresh' => array(
				'header_bottom_right' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
	);

	// Campaign bar.
	$settings['header_campaign'] = array(
		'campaign_bar' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Campaign Bar', 'konte' ),
			'description' => esc_html__( 'Display a bar bellow site header. This bar will be hidden when the header background is transparent.', 'konte' ),
			'default'     => false,
		),
		'campaign_container' => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Container width', 'konte' ),
			'default' => 'konte-container-fluid',
			'choices' => array(
				'container'             => esc_attr__( 'Standard', 'konte' ),
				'konte-container'       => esc_attr__( 'Large', 'konte' ),
				'konte-container-fluid' => esc_attr__( 'Fluid', 'konte' ),
				'container-fluid'       => esc_attr__( 'Full Width', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'campaign_bar',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport' => 'postMessage',
		),
		'campaign_bgcolor' => array(
			'type'  => 'color',
			'label' => esc_html__( 'Background Color', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'campaign_bar',
					'operator' => '==',
					'value'    => true,
				),
			),
			'output'   => array(
				array(
					'element'  => '#campaign-bar',
					'property' => 'background-color',
				),
			),
			'transport' => 'postMessage',
		),
		'campaign_vertical_padding' => array(
			'type'            => 'slider',
			'label'           => esc_html__( 'Vertical Padding', 'konte' ),
			'default'         => 0,
			'choices'     => [
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			],
			'active_callback' => array(
				array(
					'setting'  => 'campaign_bar',
					'operator' => '==',
					'value'    => true,
				),
			),
			'output'   => array(
				array(
					'element'  => '#campaign-bar',
					'property' => 'padding-top',
					'units'    => 'px',
				),
				array(
					'element'  => '#campaign-bar',
					'property' => 'padding-bottom',
					'units'    => 'px',
				),
			),
			'transport' => 'postMessage',
		),
		'campaign_items'       => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Campaigns', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Campaign', 'konte' ),
			),
			'fields'          => array(
				'tag' => array(
					'type'    => 'text',
					'label'   => esc_html__( 'Tagline', 'konte' ),
				),
				'text' => array(
					'type'    => 'textarea',
					'label'   => esc_html__( 'Text', 'konte' ),
				),
				'button' => array(
					'type'    => 'text',
					'label'   => esc_html__( 'Button Text', 'konte' ),
					'default' => esc_html__( 'Shop Now', 'konte' ),
				),
				'link' => array(
					'type'    => 'text',
					'label'   => esc_html__( 'Button URL', 'konte' ),
				),
				'image' => array(
					'type'    => 'image',
					'label'   => esc_html__( 'Background', 'konte' ),
				),
				'bgcolor' => array(
					'type'    => 'color',
					'label'   => esc_html__( 'Background Color', 'konte' ),
				),
				'border' => array(
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Border', 'konte' ),
					'default' => true,
				),
				'color' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Text Color', 'konte' ),
					'default' => 'dark',
					'choices' => array(
						'dark'  => esc_html__( 'Dark', 'konte' ),
						'light' => esc_html__( 'White', 'konte' ),
					),
				),
				'layout' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Layout', 'konte' ),
					'default' => 'inline',
					'choices' => array(
						'standard' => esc_html__( 'Standard', 'konte' ),
						'inline'   => esc_html__( 'Inline', 'konte' ),
					),
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'campaign_bar',
					'operator' => '==',
					'value'    => true,
				),
			),
			'partial_refresh' => array(
				'campaign_items' => array(
					'selector'            => '#campaign-bar',
					'container_inclusive' => true,
					'render_callback'     => function() {
						get_template_part( 'template-parts/header/campaigns' );
					},
				),
			),
		),
	);

	// Logo.
	$settings['logo'] = array(
		'logo_type'      => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Logo Type', 'konte' ),
			'default' => 'image',
			'choices' => array(
				'image' => esc_html__( 'Image', 'konte' ),
				'text'  => esc_html__( 'Text', 'konte' ),
				'svg'   => esc_html__( 'SVG', 'konte' ),
			),
		),
		'logo_text'      => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Logo Text', 'konte' ),
			'default'         => get_bloginfo( 'name' ),
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'text',
				),
			),
		),
		'logo_font'      => array(
			'type'            => 'typography',
			'label'           => esc_html__( 'Logo Font', 'konte' ),
			'default'         => array(
				'font-family'    => '',
				'variant'        => '700',
				'font-size'      => '28px',
				'letter-spacing' => '0',
				'subsets'        => array( 'latin-ext' ),
				'text-transform' => 'uppercase',
			),
			'output'          => array(
				array(
					'element' => '.site-branding .logo-text',
				),
			),
			'transport'       => 'auto',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'text',
				),
			),
		),
		'logo_svg'       => array(
			'type'            => 'textarea',
			'label'           => esc_html__( 'Logo SVG', 'konte' ),
			'description'     => esc_html__( 'Paste SVG code of your logo here', 'konte' ),
			'output'          => array(
				array(
					'element' => '.site-branding .logo',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'svg',
				),
			),
		),
		'logo'           => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Logo', 'konte' ),
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_light'     => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Logo Light', 'konte' ),
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'logo_dimension' => array(
			'type'            => 'dimensions',
			'label'           => esc_html__( 'Logo Dimension', 'konte' ),
			'default'         => array(
				'width'  => 'auto',
				'height' => 'auto',
			),
			'active_callback' => array(
				array(
					'setting'  => 'logo_type',
					'operator' => '!=',
					'value'    => 'text',
				),
			),
		),
	);

	$settings['header_menu'] = array(
		'header_menu_caret' => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Menu Caret Arrow', 'konte' ),
			'default'         => false,
			'transport'       => 'postMessage',
			'partial_refresh' => array(
				'header_menu_caret' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_menu_caret_arrow' => array(
			'type'    => 'radio-buttonset',
			'default' => 'caret',
			'choices' => array(
				'caret'          => '<i class="fa fa-caret-down"></i>',
				'chevron'        => '<i class="fa fa-chevron-down"></i>',
				'arrow'         => '<i class="fa fa-arrow-down"></i>',
				'angle'          => '<i class="fa fa-angle-down"></i>',
				'chevron-circle' => '<i class="fa fa-chevron-circle-down"></i>',
				'arrow-circle'   => '<i class="fa fa-arrow-circle-down"></i>',
				'plus'           => '<i class="fa fa-plus"></i>',
				'plus_text'      => '+',
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_menu_caret',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'partial_refresh' => array(
				'header_menu_caret_arrow' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_menu_caret_submenu' => array(
			'type'            => 'checkbox',
			'label'           => esc_html__( 'Apply for sub-menu items', 'konte' ),
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'header_menu_caret',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'partial_refresh' => array(
				'header_menu_caret_submenu' => array(
					'selector'        => '#masthead',
					'render_callback' => 'konte_header',
				),
			),
		),
		'header_menu_hr_1' => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'header_vertical_submenu_toggle' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Sub-Menu Toggle', 'konte' ),
			'default' => 'flyout',
			'choices' => array(
				'flyout' => esc_html__( 'Fly Out', 'konte' ),
				'slidedown'  => esc_html__( 'Slide Down', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_present',
					'operator' => '==',
					'value'    => 'prebuild',
				),
				array(
					'setting'  => 'header_version',
					'operator' => '==',
					'value'    => 'v10',
				),
			),
		),
	);

	// Header search.
	$settings['header_search'] = array(
		'header_search_style'       => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Style', 'konte' ),
			'default'         => 'icon-modal',
			'choices'         => array(
				'form'       => esc_html__( 'Icon and search field', 'konte' ),
				'icon'       => esc_html__( 'Icon only (click to toggle search field)', 'konte' ),
				'icon-modal' => esc_html__( 'Icon only (click to open search modal)', 'konte' ),
			),
			'partial_refresh' => array(
				'header_search_partial' => array(
					'selector'            => '.header-search',
					'container_inclusive' => true,
					'render_callback'     => function() {
						get_template_part( 'template-parts/header/search' );
					},
				),
			),
		),
		'header_search_type'        => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Search For', 'konte' ),
			'default'         => 'product',
			'choices'         => array(
				''        => esc_html__( 'Search for everything', 'konte' ),
				'product' => esc_html__( 'Search for products', 'konte' ),
				'post'    => esc_html__( 'Search for posts', 'konte' ),
			),
			'partial_refresh' => array(
				'header_search_type_partial' => array(
					'selector'            => '.header-search',
					'container_inclusive' => true,
					'render_callback'     => function () {
						get_template_part( 'template-parts/header/search' );
					},
				),
			),
		),
		'header_search_ajax' => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'AJAX search', 'konte' ),
			'description'     => esc_html__( 'Instantly search on typing.', 'konte' ),
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'header_search_style',
					'operator' => '!=',
					'value'    => 'icon-modal',
				),
			),
		),
		'header_search_quick_links' => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Quick links', 'konte' ),
			'description'     => esc_html__( 'Display a list of links bellow the search field', 'konte' ),
			'default'         => false,
			'partial_refresh' => array(
				'header_search_quick_links_partial' => array(
					'selector'            => '.header-search',
					'container_inclusive' => true,
					'render_callback'     => function () {
						get_template_part( 'template-parts/header/search' );
					},
				),
			),
		),
		'header_search_links'       => array(
			'type'            => 'repeater',
			'label'           => esc_html__( 'Links', 'konte' ),
			'description'     => esc_html__( 'Add custom links of the quick links popup', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => array(),
			'row_label'       => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Link', 'konte' ),
				'field' => 'text',
			),
			'fields'          => array(
				'text' => array(
					'type'  => 'text',
					'label' => esc_html__( 'Text', 'konte' ),
				),
				'url'  => array(
					'type'  => 'text',
					'label' => esc_html__( 'URL', 'konte' ),
					'sanitize_callback' => 'esc_url_raw',
				),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_search_quick_links',
					'operator' => '==',
					'value'    => true,
				),
			),
			'partial_refresh' => array(
				'header_search_links_partial' => array(
					'selector'            => '.header-search',
					'container_inclusive' => true,
					'render_callback'     => function () {
						get_template_part( 'template-parts/header/search' );
					},
				),
			),
		),
	);

	// Header cart.
	$settings['header_cart'] = array(
		'header_cart_behaviour' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Cart Icon Behaviour', 'konte' ),
			'default' => 'panel',
			'choices' => array(
				'panel' => esc_attr__( 'Open the cart panel', 'konte' ),
				'link'  => esc_attr__( 'Open the cart page', 'konte' ),
			),
		),
	);

	// Header account.
	$settings['header_account'] = array(
		'header_account_display' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Account Icon Display', 'konte' ),
			'default' => 'text',
			'choices' => array(
				'text' => esc_attr__( 'Text', 'konte' ),
				'icon' => esc_attr__( 'Icon', 'konte' ),
			),
		),
		'header_account_behaviour' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Account Icon Behaviour', 'konte' ),
			'default' => 'panel',
			'choices' => array(
				'panel' => esc_attr__( 'Open the login panel', 'konte' ),
				'link'  => esc_attr__( 'Open My Account page', 'konte' ),
			),
		),
	);

	// Header hamburger.
	$settings['header_hamburger'] = array(
		'hamburger_background'             => array(
			'type'        => 'image',
			'label'       => esc_html__( 'Background', 'konte' ),
			'description' => esc_html__( 'Background image will be showed a half of screen.', 'konte' ),
		),
		'hamburger_logo'                   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Logo', 'konte' ),
			'description' => esc_html__( 'Display the site logo on top of screen', 'konte' ),
			'default'     => true,
		),
		'hamburger_social'                 => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Social Menu', 'konte' ),
			'description' => esc_html__( 'Display social menu', 'konte' ),
			'default'     => true,
		),
		'hamburger_currency'               => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Currency Switcher', 'konte' ),
			'description' => esc_html__( 'It requires currency switcher plugin installed.', 'konte' ),
			'default'     => true,
		),
		'hamburger_language'               => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Language Switcher', 'konte' ),
			'description' => esc_html__( 'It requires multilingual plugin installed.', 'konte' ),
			'default'     => true,
		),
		'hamburger_content_type'           => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Content Type', 'konte' ),
			'default' => 'menu',
			'choices' => array(
				'menu'    => esc_html__( 'Menu', 'konte' ),
				'widgets' => esc_html__( 'Widgets', 'konte' ),
			),
		),
		'hamburger_open_submenu_behaviour' => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Open Sub-Menu Behaviour', 'konte' ),
			'default'         => 'click',
			'choices'         => array(
				'click' => esc_html__( 'Click on the parent menu item', 'konte' ),
				'hover' => esc_html__( 'Hover on the parent menu item', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'hamburger_content_type',
					'operator' => '==',
					'value'    => 'menu',
				),
			),
		),
		'hamburger_content_animation'      => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Toggle Animation', 'konte' ),
			'description' => esc_html__( 'Fullscreen menu items animations', 'konte' ),
			'default'     => 'none',
			'choices'     => array(
				'none'  => esc_html__( 'No animation', 'konte' ),
				'fade'  => esc_html__( 'Fade', 'konte' ),
				'slide' => esc_html__( 'Slide Down', 'konte' ),
			),
		),
	);

	// Blog header.
	$post_objects_option = array(
		'blog' => esc_html__( 'Blog', 'konte' ),
		'post' => esc_html__( 'Single post', 'konte' ),
	);
	foreach ( get_object_taxonomies( 'post', 'objects' ) as $name => $taxonomy ) {
		if ( ! $taxonomy->public || ! $taxonomy->show_ui ) {
			continue;
		}

		$post_objects_option[ $name ] = $taxonomy->label;
	}

	$settings['blog_header'] = array(
		'blog_header'            => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Enable Blog Header', 'konte' ),
			'description' => esc_html__( 'Enable the blog header on blog pages.', 'konte' ),
			'default'     => true,
		),
		'blog_header_display'    => array(
			'type'            => 'multicheck',
			'label'           => esc_html__( 'Blog Header Display', 'konte' ),
			'description'     => esc_html__( 'Select pages you want to display blog header', 'konte' ),
			'default'         => array_diff( array_keys( $post_objects_option ), array( 'post' ) ),
			'choices'         => $post_objects_option,
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_content'    => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Blog Header Text', 'konte' ),
			'default'         => esc_html__( 'Konte Blog', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_search'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Blog Search', 'konte' ),
			'description'     => esc_html__( 'Display a search form on blog header', 'konte' ),
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_socials'    => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Social Icons', 'konte' ),
			'description'     => esc_html__( 'Display social menu on blog header', 'konte' ),
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_menu'       => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Blog Header Menu', 'konte' ),
			'description'     => esc_html__( 'Display the blog header menu. It is usually used to display categories.', 'konte' ),
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_image'      => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Header Image', 'konte' ),
			'description'     => esc_html__( 'Default image for blog header', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_header_text_color' => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Header Text Color', 'konte' ),
			'default'         => 'light',
			'choices'         => array(
				'dark'  => esc_attr__( 'Dark', 'konte' ),
				'light' => esc_attr__( 'Light', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'blog_header',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
	);

	// Blog archive.
	$settings['blog_archive'] = array(
		'blog_featured_content'       => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Featured Content', 'konte' ),
			'description' => esc_html__( 'Display the featured content section on blog page', 'konte' ),
			'default'     => true,
			'choices'     => array(
				''         => esc_attr__( 'Disable', 'konte' ),
				'slider'   => esc_attr__( 'Posts slider', 'konte' ),
				'carousel' => esc_attr__( 'Posts carousel', 'konte' ),
			),
		),
		'blog_featured_tag'           => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Featured Tag', 'konte' ),
			'description'     => esc_html__( 'Specify the tag you will use on posts to be displayed as Featured Content', 'konte' ),
			'default'         => 'featured',
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_content',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_featured_limit'         => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Number of Items', 'konte' ),
			'description'     => esc_html__( 'Maximum number of posts for featured content section', 'konte' ),
			'default'         => 6,
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_content',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_featured_display'       => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Featured Content Display', 'konte' ),
			'default'         => 'slider',
			'choices'         => array(
				'slider'   => esc_attr__( 'Posts slider', 'konte' ),
				'carousel' => esc_attr__( 'Posts carousel', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_content',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_featured_slider_effect' => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Slider Animation', 'konte' ),
			'default'         => 'fade',
			'choices'         => array(
				'fade'  => esc_attr__( 'Fade', 'konte' ),
				'slide' => esc_attr__( 'Slide', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_content',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'blog_featured_display',
					'operator' => '==',
					'value'    => 'slider',
				),
			),
		),
		'blog_featured_slider_height' => array(
			'type'            => 'slider',
			'label'           => esc_html__( 'Slider Height', 'konte' ),
			'default'         => '790',
			'choices'         => array(
				'min' => 400,
				'max' => 2400,
			),
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_content',
					'operator' => '==',
					'value'    => true,
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.featured-content-carousel, .featured-content-carousel .featured-item',
					'property' => 'height',
					'units'    => 'px',
				),
			),
			'transport'       => 'postMessage',
		),
		'blog_archive_custom_hr_1'    => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'blog_featured_posts'         => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Featured Posts', 'konte' ),
			'description' => esc_html__( 'Display a grid of featured posts.', 'konte' ),
			'default'     => false,
		),
		'blog_featured_posts_tag'     => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Featured Posts Tag', 'konte' ),
			'description'     => esc_html__( 'Specify the tag you will use on posts to be displayed as Featured Posts', 'konte' ),
			'default'         => 'featured',
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_featured_posts_limit'   => array(
			'type'            => 'number',
			'label'           => esc_html__( 'Number of Posts', 'konte' ),
			'description'     => esc_html__( 'Number of featured posts to be displayed', 'konte' ),
			'default'         => 4,
			'active_callback' => array(
				array(
					'setting'  => 'blog_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'blog_archive_custom_hr_2'    => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'blog_layout'                 => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Blog Layout', 'konte' ),
			'description' => esc_html__( 'The layout of blog posts', 'konte' ),
			'default'     => 'classic',
			'choices'     => array(
				'default' => esc_attr__( 'Default', 'konte' ),
				'classic' => esc_attr__( 'Classic', 'konte' ),
				'grid'    => esc_attr__( 'Grid', 'konte' ),
			),
		),
		'excerpt_length'              => array(
			'type'        => 'number',
			'label'       => esc_html__( 'Excerpt Length', 'konte' ),
			'description' => esc_html__( 'The number of words of the post excerpt', 'konte' ),
			'default'     => 30,
		),
		'blog_archive_custom_hr_3'    => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'blog_nav_type'               => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Navigation Type', 'konte' ),
			'default' => 'numeric',
			'choices' => array(
				'numeric'  => esc_attr__( 'Numeric', 'konte' ),
				'loadmore' => esc_attr__( 'Load more', 'konte' ),
			),
		),
		'blog_nav_ajax_url_change'               => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Change the URL after page loaded', 'konte' ),
			'default' => true,
			'active_callback' => array(
				array(
					'setting'  => 'blog_nav_type',
					'operator' => '!=',
					'value'    => 'numeric',
				),
			),
		),
	);

	// Blog single.
	$settings['blog_single'] = array(
		'post_author_box'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Author Box', 'konte' ),
			'description' => esc_html__( 'Display the post author box', 'konte' ),
			'default'     => false,
		),
		'post_navigation'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Post Navigation', 'konte' ),
			'description' => esc_html__( 'Display the next and previous posts', 'konte' ),
			'default'     => true,
		),
		'post_related_posts'   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Related Posts', 'konte' ),
			'description' => esc_html__( 'Display related posts', 'konte' ),
			'default'     => true,
		),
		'post_sharing'         => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Post Sharing', 'konte' ),
			'description' => esc_html__( 'Enable post sharing.', 'konte' ),
			'default'     => false,
		),
		'post_sharing_socials' => array(
			'type'            => 'sortable',
			'description'     => esc_html__( 'Select social media for sharing posts', 'konte' ),
			'default'         => array(
				'facebook',
				'twitter',
				'googleplus',
				'pinterest',
				'tumblr',
				'reddit',
				'telegram',
				'email',
			),
			'choices'         => array(
				'facebook'    => esc_html__( 'Facebook', 'konte' ),
				'twitter'     => esc_html__( 'Twitter', 'konte' ),
				'pinterest'   => esc_html__( 'Pinterest', 'konte' ),
				'tumblr'      => esc_html__( 'Tumblr', 'konte' ),
				'reddit'      => esc_html__( 'Reddit', 'konte' ),
				'linkedin'    => esc_html__( 'Linkedin', 'konte' ),
				'stumbleupon' => esc_html__( 'StumbleUpon', 'konte' ),
				'digg'        => esc_html__( 'Digg', 'konte' ),
				'telegram'    => esc_html__( 'Telegram', 'konte' ),
				'whatsapp'    => esc_html__( 'WhatsApp', 'konte' ),
				'vk'          => esc_html__( 'VK', 'konte' ),
				'email'       => esc_html__( 'Email', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'post_sharing',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'post_sharing_whatsapp_number' => array(
			'type'        => 'text',
			'description' => esc_html__( 'WhatsApp Phone Number', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'post_sharing',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'post_sharing_socials',
					'operator' => 'contains',
					'value'    => 'whatsapp',
				),
			),
		),
	);

	// Pages.
	$settings['pages'] = array(
		'page_title_display'       => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Page Title Display', 'konte' ),
			'description' => esc_html__( 'If a page has a thumbnail, you can select to display page title above or in front of the thumbnail image.', 'konte' ),
			'default'     => 'above',
			'choices'     => array(
				'above' => esc_html__( 'Above thumbnail', 'konte' ),
				'front' => esc_html__( 'In front of thumbnail', 'konte' ),
			),
		),
		'page_header_height_title' => array(
			'type'  => 'custom',
			'label' => esc_html__( 'Page Header Height', 'konte' ),
		),
		'page_header_height'       => array(
			'type'            => 'slider',
			'default'         => 800,
			'choices'         => array(
				'min' => '100',
				'max' => '2400',
			),
			'active_callback' => array(
				array(
					'setting'  => 'page_header_full_height',
					'operator' => '!=',
					'value'    => true,
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.page .page-header',
					'property' => 'height',
					'units'    => 'px',
				),
			),
			'transport'       => 'postMessage',
		),
		'page_header_full_height'  => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Page header full height', 'konte' ),
			'default' => false,
		),
	);

	// Footer layout.
	$settings['footer_layout'] = array(
		'footer_sections'  => array(
			'type'        => 'sortable',
			'label'       => esc_html__( 'Footer Sections', 'konte' ),
			'description' => esc_html__( 'Select and order footer contents', 'konte' ),
			'default'     => array( 'main' ),
			'choices'     => array(
				'extra'     => esc_attr__( 'Extra Content', 'konte' ),
				'widgets'   => esc_attr__( 'Footer Widgets', 'konte' ),
				'instagram' => esc_attr__( 'Instagram', 'konte' ),
				'main'      => esc_attr__( 'Footer Main', 'konte' ),
			),
		),
		'footer_layout_custom_hr_1'    => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'footer_container' => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Footer Container Width', 'konte' ),
			'description' => esc_html__( 'Select the width of footer container', 'konte' ),
			'default'     => 'konte-container',
			'choices'     => array(
				'container'             => esc_attr__( 'Standard', 'konte' ),
				'konte-container'       => esc_attr__( 'Large', 'konte' ),
				'konte-container-fluid' => esc_attr__( 'Fluid', 'konte' ),
				'container-fluid'       => esc_attr__( 'Full Width', 'konte' ),
			),
		),
		'footer_layout_custom_hr_2'    => array(
			'type'    => 'custom',
			'default' => '<hr/>',
		),
		'footer_gotop'  => array(
			'type'        => 'toggle',
			'label'       => esc_html__( '"Go to Top" Button', 'konte' ),
			'description' => esc_html__( 'Display a "Go to Top" button which stick at footer', 'konte' ),
			'default'     => false,
		),
	);

	// Footer background.
	$settings['footer_background'] = array(
		'footer_background'             => array(
			'type'      => 'select',
			'label'     => esc_html__( 'Background Scheme', 'konte' ),
			'default'   => 'light',
			'transport' => 'postMessage',
			'choices'   => array(
				'light'  => esc_attr__( 'Light', 'konte' ),
				'dark'   => esc_attr__( 'Dark', 'konte' ),
				'custom' => esc_attr__( 'Custom', 'konte' ),
			),
		),
		'footer_background_color'       => array(
			'type'            => 'color',
			'default'         => '#fff',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'footer_background',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.site-footer.custom',
					'property' => 'background-color',
				),
			),
		),
		'footer_textcolor'              => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),
		'footer_background_hr_1'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'footer_background_blog_custom' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Custom Background for Blog', 'konte' ),
			'default' => true,
		),
		'footer_background_blog'        => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Background for Blog', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light'  => esc_html__( 'Light', 'konte' ),
				'dark'   => esc_html__( 'Dark', 'konte' ),
				'custom' => esc_html__( 'Custom', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_background_blog_color'  => array(
			'type'            => 'color',
			'default'         => '',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'footer_background_blog',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.blog-hfeed .site-footer.custom',
					'property' => 'background-color',
				),
			),
		),
		'footer_blog_textcolor'         => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color for Blog', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_blog_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'footer_background_blog',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),
	);

	// Footer extra.
	$settings['footer_extra'] = array(
		'footer_extra_content' => array(
			'type'  => 'editor',
			'label' => esc_html__( 'Footer Extra Content', 'konte' ),
		),
	);

	// Footer Widgets.
	$settings['footer_widgets'] = array(
		'footer_widgets_layout' => array(
			'type'        => 'radio-image',
			'label'       => esc_html__( 'Footer Widgets Layout', 'konte' ),
			'description' => esc_html__( 'Select number of columns for displaying widgets', 'konte' ),
			'default'     => '4-columns',
			'choices'     => array(
				'2-columns'      => $images_uri . '/footer/2-columns.png',
				'3-columns'      => $images_uri . '/footer/3-columns.png',
				'4-columns'      => $images_uri . '/footer/4-columns.png',
				'4-columns-diff' => $images_uri . '/footer/4-columns-diff.png',
			),
		),
		'footer_widgets_flex'   => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Flex Column Width', 'konte' ),
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'footer_widgets_layout',
					'operator' => '!=',
					'value'    => '4-columns-diff',
				),
			),
		),
	);

	// Footer Instagram.
	$settings['footer_instagram'] = array(
		'footer_instagram_limit'        => array(
			'type'    => 'number',
			'label'   => esc_html__( 'Number of Photos', 'konte' ),
			'default' => 16,
		),
		'footer_instagram_columns'      => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Number of Columns', 'konte' ),
			'default' => '8',
			'choices' => array(
				'6' => esc_html__( '6 Columns', 'konte' ),
				'8' => esc_html__( '8 Columns', 'konte' ),
			),
		),
		'footer_instagram_orginal_size' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Use Original Image Size', 'konte' ),
			'description' => esc_html__( 'Display images in original size rather than the cropped version', 'konte' ),
			'default' => false,
		),
		'footer_instagram_profile_link' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Show Profile Link', 'konte' ),
			'description' => esc_html__( 'Show a link to the Instagram profile page.', 'konte' ),
			'default'     => true,
		),
	);

	// Footer Main.
	$settings['footer_main'] = array(
		'footer_main_left'     => array(
			'type'        => 'repeater',
			'label'       => esc_html__( 'Left Items', 'konte' ),
			'description' => esc_html__( 'Control left items of the footer', 'konte' ),
			'transport'   => 'postMessage',
			'default'     => array( array( 'item' => 'copyright' ) ),
			'row_label'   => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'      => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_footer_items_option(),
				),
			),
			'partial_refresh' => array(
				'footer_main_left' => array(
					'selector'        => '.footer-main',
					'container_inclusive' => true,
					'render_callback' => function() {
						get_template_part( 'template-parts/footer/footer' );
					},
				),
			),
		),
		'footer_main_center'   => array(
			'type'        => 'repeater',
			'label'       => esc_html__( 'Center Items', 'konte' ),
			'description' => esc_html__( 'Control center items of the footer', 'konte' ),
			'transport'   => 'postMessage',
			'default'     => array(),
			'row_label'   => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'      => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_footer_items_option(),
				),
			),
			'partial_refresh' => array(
				'footer_main_center' => array(
					'selector'        => '.footer-main',
					'container_inclusive' => true,
					'render_callback' => function() {
						get_template_part( 'template-parts/footer/footer' );
					},
				),
			),
		),
		'footer_main_right'    => array(
			'type'        => 'repeater',
			'label'       => esc_html__( 'Right Items', 'konte' ),
			'description' => esc_html__( 'Control right items of the footer', 'konte' ),
			'transport'   => 'postMessage',
			'default'     => array( array( 'item' => 'menu' ) ),
			'row_label'   => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'      => array(
				'item' => array(
					'type'    => 'select',
					'default' => 'copyright',
					'choices' => konte_footer_items_option(),
				),
			),
			'partial_refresh' => array(
				'footer_main_right' => array(
					'selector'        => '.footer-main',
					'container_inclusive' => true,
					'render_callback' => function() {
						get_template_part( 'template-parts/footer/footer' );
					},
				),
			),
		),
		'footer_main_divide_1' => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'footer_main_flow'     => array(
			'type'        => 'radio',
			'label'       => esc_html__( 'Items Display Direction', 'konte' ),
			'description' => esc_html__( 'Display footer items in horizontal or vertical direction', 'konte' ),
			'default'     => 'vertical',
			'choices'     => array(
				'vertical'   => esc_html__( 'Vertical', 'konte' ),
				'horizontal' => esc_html__( 'Horizontal', 'konte' ),
			),
		),
		'footer_main_border'   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Border Line', 'konte' ),
			'description' => esc_html__( 'Display a divide line on top', 'konte' ),
			'default'     => true,
		),
		'footer_main_divide_2' => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'footer_copyright'     => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Footer Copyright', 'konte' ),
			'description' => esc_html__( 'Display copyright info on the left side of footer', 'konte' ),
			'default'     => sprintf( '%s %s ' . esc_html__( 'All rights reserved', 'konte' ), '&copy;' . date( 'Y' ), get_bloginfo( 'name' ) ),
		),
		'footer_main_text'     => array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Custom Text', 'konte' ),
			'description' => esc_html__( 'The content of the Custom Text item', 'konte' ),
		),
	);

	// Mobile Header.
	$settings['mobile_header'] = array(
		'mobile_topbar' => array(
			'type'      => 'toggle',
			'label'     => esc_html__( 'Topbar', 'konte' ),
			'description' => esc_html__( 'Display topbar on mobile', 'konte' ),
			'default'   => false,
		),
		'mobile_topbar_section' => array(
			'type'      => 'select',
			'label'     => esc_html__( 'Topbar Items', 'konte' ),
			'default'   => 'left',
			'choices' => array(
				'left'   => esc_attr__( 'Keep left items', 'konte' ),
				'center' => esc_attr__( 'Keep center items', 'konte' ),
				'right'  => esc_attr__( 'Keep right items', 'konte' ),
				'both'   => esc_attr__( 'Keep both left and right items', 'konte' ),
				'all'    => esc_attr__( 'Keep all items', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'mobile_topbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'mobile_header_height' => array(
			'type'      => 'slider',
			'label'     => esc_html__( 'Header Height', 'konte' ),
			'transport' => 'postMessage',
			'default'   => '60',
			'choices'   => array(
				'min' => 40,
				'max' => 300,
			),
			'js_vars'   => array(
				array(
					'element'  => '.header-mobile',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		),
		'mobile_custom_logo'   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Mobile Custom Logo', 'konte' ),
			'description' => esc_html__( 'Use a different logo on mobile', 'konte' ),
			'default'     => false,
		),
		'mobile_logo'          => array(
			'type'            => 'image',
			'active_callback' => array(
				array(
					'setting'  => 'mobile_custom_logo',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'mobile_logo_dimension' => array(
			'type'        => 'dimensions',
			'label'       => esc_html__( 'Logo Dimension', 'konte' ),
			'description' => esc_html__( 'Specify the logo dimensions with units', 'konte' ),
			'default'     => array(
				'width'  => '',
				'height' => '',
			),
			'active_callback' => array(
				array(
					'setting'  => 'mobile_custom_logo',
					'operator' => '==',
					'value'    => true,
				),
			),
			'output'   => array(
				array(
					'element'  => '.header-mobile .logo img, .header-mobile .logo svg, .mobile-logo img',
				),
			),
			'transport' => 'postMessage',
		),
		'mobile_logo_position' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Logo Position', 'konte' ),
			'default' => 'center',
			'choices' => array(
				'center' => esc_attr__( 'Center', 'konte' ),
				'left'   => esc_attr__( 'Left', 'konte' ),
			),
		),
		'mobile_header_icons'  => array(
			'type'        => 'repeater',
			'label'       => esc_html__( 'Header Icons', 'konte' ),
			'description' => esc_html__( 'Control icons on the right side of mobile header', 'konte' ),
			'transport'   => 'postMessage',
			'default'     => array( array( 'item' => 'cart' ) ),
			'row_label'   => array(
				'type'  => 'field',
				'value' => esc_attr__( 'Item', 'konte' ),
				'field' => 'item',
			),
			'fields'      => array(
				'item' => array(
					'type'    => 'select',
					'choices' => konte_mobile_header_icons_option(),
				),
			),
		),
	);

	// Mobile Menu.
	$settings['mobile_menu'] = array(
		'mobile_menu_items'       => array(
			'type'    => 'sortable',
			'label'   => esc_html__( 'Mobile Menu Layout', 'konte' ),
			'tooltip' => esc_html__( 'Wishlist, currency switcher and language switcher require to have plugins installed in order to work', 'konte' ),
			'default' => array(
				'search',
				'menu',
				'divider1',
				'cart',
				'wishlist',
				'divider2',
				'currency',
				'language',
				'divider3',
				'account',
				'divider4',
				'socials',
			),
			'choices' => array(
				'search'   => esc_html__( 'Search Form', 'konte' ),
				'menu'     => esc_html__( 'Menu', 'konte' ),
				'cart'     => esc_html__( 'Shopping Cart', 'konte' ),
				'wishlist' => esc_html__( 'Wishlist', 'konte' ),
				'currency' => esc_html__( 'Currency Switcher', 'konte' ),
				'language' => esc_html__( 'Language Switcher', 'konte' ),
				'account'  => esc_html__( 'My Account', 'konte' ),
				'socials'  => esc_html__( 'Socials Menu', 'konte' ),
				'divider1' => '---------',
				'divider2' => '---------',
				'divider3' => '---------',
				'divider4' => '---------',
				'divider5' => '---------',
				'divider6' => '---------',
				'divider7' => '---------',
			),
		),
		'mobile_menu_search_type' => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Search Form', 'konte' ),
			'default' => 'product',
			'choices' => array(
				''        => esc_html__( 'Search for everything', 'konte' ),
				'product' => esc_html__( 'Search for products', 'konte' ),
				'post'    => esc_html__( 'Search for posts', 'konte' ),
			),
		),
	);

	// Mobile bottom bar.
	$settings['mobile_bottom_bar'] = array(
		'mobile_bottom_bar' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Bottom Navigation Bar', 'konte' ),
			'description' => esc_html__( 'Sticky bottom navigation for easy mobile access.', 'konte' ),
			'default'     => false,
		),
		'mobile_bottom_bar_items' => array(
			'type'    => 'sortable',
			'label'   => esc_html__( 'Items', 'konte' ),
			'tooltip' => esc_html__( 'The Wishlist item requires to have plugins installed in order to work', 'konte' ),
			'default' => array(
				'home',
				'search',
				'cart',
				'account',
			),
			'choices' => konte_mobile_bottom_bar_items_option(),
			'partial_refresh' => array(
				'mobile_bottom_bar_items' => array(
					'selector'            => '#mobile-bottom-bar',
					'container_inclusive' => true,
					'render_callback'     => 'konte_mobile_bottom_bar',
				),
			),
		),
		'mobile_bottom_bar_items_display' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Items display', 'konte' ),
			'default' => 'icon',
			'choices' => array(
				'icon' => esc_html__( 'Icon only', 'konte' ),
				'full' => esc_html__( 'Icon with label', 'konte' ),
			),
			'partial_refresh' => array(
				'mobile_bottom_bar_items_display' => array(
					'selector'            => '#mobile-bottom-bar',
					'container_inclusive' => true,
					'render_callback'     => 'konte_mobile_bottom_bar',
				),
			),
		),
	);

	return array(
		'theme'    => 'konte',
		'panels'   => apply_filters( 'konte_customize_panels', $panels ),
		'sections' => apply_filters( 'konte_customize_sections', $sections ),
		'settings' => apply_filters( 'konte_customize_settings', $settings ),
	);
}

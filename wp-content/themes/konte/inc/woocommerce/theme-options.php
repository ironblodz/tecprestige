<?php
/**
 * Theme options of WooCommerce.
 *
 * @package Konte
 */

/**
 * Adds theme options panels of WooCommerce.
 *
 * @param array $panels Theme options panels.
 *
 * @return array
 */
function konte_woocommerce_customize_panels( $panels ) {
	$panels['shop'] = array(
		'priority' => 300,
		'title'    => esc_html__( 'Shop', 'konte' ),
	);

	return $panels;
}

add_filter( 'konte_customize_panels', 'konte_woocommerce_customize_panels' );

/**
 * Adds theme options sections of WooCommerce.
 *
 * @param array $sections Theme options sections.
 *
 * @return array
 */
function konte_woocommerce_customize_sections( $sections ) {
	$sections = array_merge( $sections, array(
		'typo_product'      => array(
			'title'    => esc_html__( 'Product', 'konte' ),
			'priority' => 62,
			'panel'    => 'typography',
		),
		'typo_catalog'      => array(
			'title'    => esc_html__( 'Product Catalog', 'konte' ),
			'priority' => 64,
			'panel'    => 'typography',
		),
		'shop_product'        => array(
			'title'    => esc_html__( 'Product', 'konte' ),
			'priority' => 10,
			'panel'    => 'shop',
		),
		'shop_catalog'        => array(
			'title'    => esc_html__( 'Product Catalog', 'konte' ),
			'priority' => 20,
			'panel'    => 'shop',
		),
		'shop_catalog_header' => array(
			'title'    => esc_html__( 'Products Page Header', 'konte' ),
			'priority' => 30,
			'panel'    => 'shop',
		),
		'shop_products_toolbar' => array(
			'title'    => esc_html__( 'Products Toolbar', 'konte' ),
			'priority' => 40,
			'panel'    => 'shop',
		),
		'shop_cart'           => array(
			'title'    => esc_html__( 'Cart', 'konte' ),
			'priority' => 50,
			'panel'    => 'shop',
		),
		'shop_checkout'       => array(
			'title'    => esc_html__( 'Checkout', 'konte' ),
			'priority' => 60,
			'panel'    => 'shop',
		),
		'shop_badges'         => array(
			'title'    => esc_html__( 'Badges', 'konte' ),
			'priority' => 70,
			'panel'    => 'shop',
		),
		'shop_quickview'      => array(
			'title'    => esc_html__( 'Quick View', 'konte' ),
			'priority' => 80,
			'panel'    => 'shop',
		),
		'shop_notice'      => array(
			'title'    => esc_html__( 'Notifications', 'konte' ),
			'priority' => 90,
			'panel'    => 'shop',
		),
		'mobile_shop_catalog'  => array(
			'title'    => esc_html__( 'Product Catalog', 'konte' ),
			'priority' => 200,
			'panel'    => 'mobile',
		),
		'mobile_product'  => array(
			'title'    => esc_html__( 'Single Product', 'konte' ),
			'priority' => 210,
			'panel'    => 'mobile',
		),
		'mobile_shop_cart'      => array(
			'title'    => esc_html__( 'Shopping Cart', 'konte' ),
			'priority' => 220,
			'panel'    => 'mobile',
		),
	) );

	if ( class_exists( 'WOOCS' ) ) {
		$sections = array_merge( $sections, array(
			'currency_switcher'      => array(
				'title'    => esc_html__( 'Currencies', 'konte' ),
				'priority' => 100,
				'panel'    => 'shop',
			),
		) );
	}

	return $sections;
}

add_filter( 'konte_customize_sections', 'konte_woocommerce_customize_sections' );

/**
 * Adds theme options of WooCommerce.
 *
 * @param array $settings Theme options.
 *
 * @return array
 */
function konte_woocommerce_customize_settings( $settings ) {
	$images_uri = get_template_directory_uri() . '/images/options';

	// Site layout.
	$settings['layout'] = array_merge( (array) $settings['layout'], array(
		'layout_shop' => array(
			'type'            => 'radio-image',
			'label'           => esc_html__( 'Shop Layout', 'konte' ),
			'description'     => esc_html__( 'Default layout of shop pages', 'konte' ),
			'default'         => 'no-sidebar',
			'choices'         => array(
				'no-sidebar'    => $images_uri . '/sidebars/empty.png',
				'sidebar-left'  => $images_uri . '/sidebars/single-left.png',
				'sidebar-right' => $images_uri . '/sidebars/single-right.png',
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_layout',
					'operator' => '!=',
					'value'    => 'carousel',
				),
			),
		),
	) );

	// Typography - product.
	$settings['typo_product'] = array(
		'typo_product_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Product Name', 'konte' ),
			'description' => esc_html__( 'Customize the font of product name', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '32px',
				'line-height'    => '1.16667',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => konte_customizer_fonts_choices(),
			'transport' => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => '.woocommerce div.product .product_title',
				),
			),
		),
		'typo_product_short_desc'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Product Description', 'konte' ),
			'description' => esc_html__( 'Customize the font of product description', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '14px',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => konte_customizer_fonts_choices(),
			'transport' => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => '.woocommerce div.product .woocommerce-variation-description, .woocommerce div.product .woocommerce-product-details__short-description, .woocommerce-Tabs-panel--description',
				),
			),
		),
	);

	// Typography - catalog.
	$settings['typo_catalog'] = array(
		'typo_catalog_page_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Page Header Title', 'konte' ),
			'description' => esc_html__( 'Customize the font of page header title', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '500',
				'font-size'      => '20px',
				'line-height'    => '1.55556',
				'text-transform' => 'none',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => konte_customizer_fonts_choices(),
			'transport' => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => '.woocommerce-products-header .page-title',
				),
			),
		),
		'typo_catalog_product_title'              => array(
			'type'        => 'typography',
			'label'       => esc_html__( 'Product Name', 'konte' ),
			'description' => esc_html__( 'Customize the font of product name', 'konte' ),
			'default'     => array(
				'font-family'    => 'inherit',
				'variant'        => '400',
				'font-size'      => '16px',
				'line-height'    => '1.55556',
				'text-transform' => 'none',
				'color'          => '#161619',
				'subsets'        => array( 'latin-ext' ),
			),
			'choices' => konte_customizer_fonts_choices(),
			'transport' => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => 'ul.products li.product .woocommerce-loop-product__title a',
				),
			),
		),
	);

	// Header background.
	$settings['header_background'] = array_merge( (array) $settings['header_background'], array(
		'header_layout_hr_2'            => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'header_background_shop_custom' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Custom Background for Shop', 'konte' ),
			'default' => false,
		),
		'header_background_shop'        => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Background for Shop', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'light',
			'choices'         => array(
				'light'       => esc_html__( 'Light', 'konte' ),
				'dark'        => esc_html__( 'Dark', 'konte' ),
				'transparent' => esc_html__( 'Transparent', 'konte' ),
				'custom'      => esc_html__( 'Custom', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'header_background_shop_color'  => array(
			'type'            => 'color',
			'default'         => '',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'header_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'header_background_shop',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce .site-header.custom',
					'property' => 'background-color',
				),
			),
		),
		'header_shop_textcolor'         => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color for Shop', 'konte' ),
			'description'     => esc_html__( 'Select header text color for shop pages', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'header_background_shop',
					'operator' => 'in',
					'value'    => array( 'custom', 'transparent' ),
				),
			),
		),
	) );

	// Footer background.
	$settings['footer_background'] = array_merge( (array) $settings['footer_background'], array(
		'footer_background_hr_2'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'footer_background_shop_custom' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Custom Background for Shop', 'konte' ),
			'default' => false,
		),
		'footer_background_shop'        => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Background for Shop', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light'  => esc_html__( 'Light', 'konte' ),
				'dark'   => esc_html__( 'Dark', 'konte' ),
				'custom' => esc_html__( 'Custom', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'footer_background_shop_color'  => array(
			'type'            => 'color',
			'default'         => '',
			'transport'       => 'postMessage',
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'footer_background_shop',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce .site-footer.custom',
					'property' => 'background-color',
				),
			),
		),
		'footer_shop_textcolor'         => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color for Shop', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 'dark',
			'choices'         => array(
				'light' => esc_html__( 'Light', 'konte' ),
				'dark'  => esc_html__( 'Dark', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_background_shop_custom',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'footer_background_shop',
					'operator' => '==',
					'value'    => 'custom',
				),
			),
		),
	) );

	// Product page.
	$settings['shop_product'] = array(
		'product_layout'              => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Product Layout', 'konte' ),
			'default' => 'v6',
			'choices' => array(
				'v1' => esc_html__( 'Layout 1', 'konte' ),
				'v2' => esc_html__( 'Layout 2', 'konte' ),
				'v3' => esc_html__( 'Layout 3', 'konte' ),
				'v4' => esc_html__( 'Layout 4', 'konte' ),
				'v5' => esc_html__( 'Layout 5', 'konte' ),
				'v6' => esc_html__( 'Layout 6', 'konte' ),
				'v7' => esc_html__( 'Layout 7', 'konte' ),
			),
		),
		'product_side_products'       => array(
			'type'            => 'select',
			'label'           => esc_html__( 'Side Products', 'konte' ),
			'description'     => esc_html__( 'Display recommended products on the right side', 'konte' ),
			'default'         => 'best_selling_products',
			'choices'         => array(
				'best_selling_products' => esc_html__( 'Best selling products', 'konte' ),
				'featured_products'     => esc_html__( 'Featured products', 'konte' ),
				'related_products'      => esc_html__( 'Related products', 'konte' ),
				'upsell_products'       => esc_html__( 'Up-sell products', 'konte' ),
				'recent_products'       => esc_html__( 'Recent products', 'konte' ),
				'sale_products'         => esc_html__( 'Sale products', 'konte' ),
				'top_rated_products'    => esc_html__( 'Top rated products', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '==',
					'value'    => 'v7',
				),
			),
		),
		'product_side_products_title' => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Side Products Title', 'konte' ),
			'description'     => esc_html__( 'Section title of side products', 'konte' ),
			'default'         => esc_html__( 'Best Selling', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '==',
					'value'    => 'v7',
				),
			),
		),
		'product_side_products_limit' => array(
			'type'            => 'number',
			'description'     => esc_html__( 'Number of products', 'konte' ),
			'default'         => 6,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '==',
					'value'    => 'v7',
				),
			),
		),
		'product_sticky_summary'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Sticky Summary', 'konte' ),
			'description'     => esc_html__( 'Make the product summary sticky', 'konte' ),
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => 'in',
					'value'    => array( 'v2', 'v5' ),
				),
			),
		),
		'product_summary_sticky_mode' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Sticky Mode', 'konte' ),
			'default' => 'advanced',
			'choices' => array(
				'default'  => __( 'Default - uses native CSS', 'konte' ),
				'advanced' => __( 'Advanced - uses a JS library', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => 'in',
					'value'    => array( 'v2', 'v5' ),
				),
				array(
					'setting' => 'product_sticky_summary',
					'value'   => true,
				),
			),
		),
		'product_hr_1'                => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'product_auto_background'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Auto Background', 'konte' ),
			'description'     => esc_html__( 'Detect background color from product main image', 'konte' ),
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => 'in',
					'value'    => array( 'v1', 'v3' ),
				),
			),
		),
		'product_hr_2'                => array(
			'type'            => 'custom',
			'default'         => '<hr>',
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => 'in',
					'value'    => array( 'v1' ),
				),
			),
		),
		'product_breadcrumb'          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Breadcrumb', 'konte' ),
			'description' => esc_html__( 'Display breadcrumb on top of product page', 'konte' ),
			'default'     => true,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '!=',
					'value'    => 'v3',
				),
			),
		),
		'product_navigation'          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Navigation', 'konte' ),
			'description' => esc_html__( 'Display next & previous links on top of product page', 'konte' ),
			'default'     => true,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '!=',
					'value'    => 'v3',
				),
			),
		),
		'product_navigation_same_cat' => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Navigate to products in the same category', 'konte' ),
			'default'     => false,
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => 'in',
					'value'    => array( 'v1', 'v2', 'v4', 'v5', 'v6', 'v7' ),
				),
				array(
					'setting'  => 'product_navigation',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'product_hr_3'                => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'product_image_zoom'          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Image Zoom', 'konte' ),
			'description' => esc_html__( 'Zooms in where your cursor is on the image', 'konte' ),
			'default'     => false,
		),
		'product_image_lightbox'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Image Lightbox', 'konte' ),
			'description' => esc_html__( 'Opens your images against a dark backdrop', 'konte' ),
			'default'     => true,
		),
		'product_hr_4'                => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'product_v4_quantity_input_style'      => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Quantity Input', 'konte' ),
			'default'     => 'dropdown',
			'choices'     => array(
				'default'  => esc_attr__( 'Default', 'konte' ),
				'dropdown' => esc_attr__( 'Dropdown', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'value'    => 'v4',
				),
			),
		),
		'product_ajax_addtocart'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Ajax Add To Cart', 'konte' ),
			'description' => esc_html__( 'Using Ajax for add to cart button.', 'konte' ),
			'default'     => false,
		),
		'product_sticky_addtocart'      => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Sticky Add To Cart', 'konte' ),
			'description' => esc_html__( 'Display the sticky bar of the Add To Cart button', 'konte' ),
			'choices'     => array(
				''       => esc_html__( 'Disable', 'konte' ),
				'top'    => esc_html__( 'Stick on top', 'konte' ),
				'bottom' => esc_html__( 'Stick at bottom', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_layout',
					'operator' => '!=',
					'value'    => array( 'v3' ),
				),
			),
		),
		'product_hr_5'                => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'product_sharing'             => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Product Sharing', 'konte' ),
			'default' => true,
		),
		'product_sharing_socials'     => array(
			'type'            => 'multicheck',
			'description'     => esc_html__( 'Select social media for sharing products', 'konte' ),
			'default'         => array(
				'facebook',
				'twitter',
				'pinterest',
			),
			'choices'         => array(
				'facebook'   => esc_html__( 'Facebook', 'konte' ),
				'twitter'    => esc_html__( 'Twitter', 'konte' ),
				'pinterest'  => esc_html__( 'Pinterest', 'konte' ),
				'tumblr'     => esc_html__( 'Tumblr', 'konte' ),
				'telegram'   => esc_html__( 'Telegram', 'konte' ),
				'whatsapp'   => esc_html__( 'WhatsApp', 'konte' ),
				'email'      => esc_html__( 'Email', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_sharing',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'product_sharing_whatsapp_number' => array(
			'type'        => 'text',
			'description' => esc_html__( 'WhatsApp Phone Number', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'product_sharing',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'product_sharing_socials',
					'operator' => 'contains',
					'value'    => 'whatsapp',
				),
			),
		),
	);

	// Product catalog.
	$settings['shop_catalog'] = array(
		'shop_layout'        => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Catalog Layout', 'konte' ),
			'default' => 'standard',
			'choices' => array(
				'standard' => esc_attr__( 'Standard', 'konte' ),
				'masonry'  => esc_attr__( 'Masonry', 'konte' ),
				'carousel' => esc_attr__( 'Carousel slider', 'konte' ),
			),
		),
		'shop_catalog_hr_1'  => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_product_hover' => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Product Hover', 'konte' ),
			'description' => esc_html__( 'Product hover animation.', 'konte' ),
			'default'     => 'slider',
			'choices'     => array(
				'classic'     => esc_attr__( 'Classic', 'konte' ),
				'slider'      => esc_attr__( 'Slider', 'konte' ),
				'other_image' => esc_attr__( 'Fadein', 'konte' ),
				'zoom'        => esc_attr__( 'Zoom', 'konte' ),
				'simple'      => esc_attr__( 'Simple', 'konte' ),
			),
		),
		'shop_product_stars' => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Stars Rating', 'konte' ),
			'default' => false,
		),
		'shop_catalog_hr_2'  => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_nav'           => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Navigation Type', 'konte' ),
			'default'         => 'loadmore',
			'choices'         => array(
				'numeric'  => esc_attr__( 'Numeric', 'konte' ),
				'loadmore' => esc_attr__( 'Load More', 'konte' ),
				'infinite' => esc_attr__( 'Infinite Scroll', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_layout',
					'operator' => '!=',
					'value'    => 'carousel',
				),
			),
		),
		'shop_nav_ajax_url_change'               => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Change the URL after page loaded', 'konte' ),
			'default' => true,
			'active_callback' => array(
				array(
					'setting'  => 'shop_layout',
					'operator' => '!=',
					'value'    => 'carousel',
				),
				array(
					'setting'  => 'shop_nav',
					'operator' => '!=',
					'value'    => 'numeric',
				),
			),
		),
	);

	// Catalog page header.
	$settings['shop_catalog_header'] = array(
		'shop_page_header'                => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Page Header Style', 'konte' ),
			'default' => 'minimal',
			'choices' => array(
				''         => esc_attr__( 'No page header', 'konte' ),
				'standard' => esc_attr__( 'Standard (image and text)', 'konte' ),
				'minimal'  => esc_attr__( 'Minimal (text only)', 'konte' ),
			),
		),
		'shop_page_header_container'      => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Page Header Container', 'konte' ),
			'default'         => 'wrapped',
			'choices'         => array(
				'wrapped' => esc_attr__( 'Wrapped', 'konte' ),
				'fluid'   => esc_attr__( 'Full Width', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_page_header',
					'operator' => '==',
					'value'    => 'minimal',
				),
			),
		),
		'shop_catalog_header_hr_1'        => array(
			'type'            => 'custom',
			'default'         => '<hr>',
			'active_callback' => array(
				array(
					'setting'  => 'shop_page_header',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'shop_page_header_image'          => array(
			'type'            => 'image',
			'label'           => esc_html__( 'Page Header Image', 'konte' ),
			'default'         => '',
			'active_callback' => array(
				array(
					'setting'  => 'shop_page_header',
					'operator' => '==',
					'value'    => 'standard',
				),
			),
		),
		'shop_page_header_textcolor'      => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Text Color', 'konte' ),
			'default'         => 'light',
			'choices'         => array(
				'dark'  => esc_attr__( 'Dark', 'konte' ),
				'light' => esc_attr__( 'Light', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_page_header',
					'operator' => '==',
					'value'    => 'standard',
				),
			),
		),
		'shop_page_header_height'         => array(
			'type'            => 'slider',
			'label'           => esc_html__( 'Height', 'konte' ),
			'transport'       => 'postMessage',
			'default'         => 500,
			'choices'         => array(
				'min'  => 300,
				'max'  => 1000,
				'step' => 1,
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_page_header',
					'operator' => '==',
					'value'    => 'standard',
				),
			),
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce-products-header',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		),
	);

	// Products toolbar.
	$settings['shop_products_toolbar'] = array(
		'shop_toolbar'                    => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Products Toolbar', 'konte' ),
			'default' => true,
		),
		'shop_toolbar_layout'             => array(
			'type'            => 'select',
			'description'     => esc_html__( 'Products Toolbar Layout', 'konte' ),
			'default'         => 'v1',
			'choices'         => array(
				'v1' => esc_attr__( 'Layout V1', 'konte' ),
				'v2' => esc_attr__( 'Layout V2', 'konte' ),
				'v3' => esc_attr__( 'Layout V3', 'konte' ),
				'v4' => esc_attr__( 'Layout V4', 'konte' ),
				'v5' => esc_attr__( 'Layout V5', 'konte' ),
				'v6' => esc_attr__( 'Layout V6', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_toolbar',
					'operator' => '==',
					'value'    => true,
				),
			),
		),
		'shop_products_toolbar_hr_1'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_toolbar_columns'            => array(
			'type'    => 'multicheck',
			'label'   => esc_html__( 'Columns Switcher', 'konte' ),
			'default' => array( '2', '4', '6' ),
			'choices' => array(
				'2' => esc_attr__( '2 Columns', 'konte' ),
				'3' => esc_attr__( '3 Columns', 'konte' ),
				'4' => esc_attr__( '4 Columns', 'konte' ),
				'5' => esc_attr__( '5 Columns', 'konte' ),
				'6' => esc_attr__( '6 Columns', 'konte' ),
			),
		),
		'shop_products_toolbar_hr_2'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_toolbar_tabs'               => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Products Tabs', 'konte' ),
			'default' => 'group',
			'choices' => array(
				'group'    => esc_attr__( 'Groups', 'konte' ),
				'category' => esc_attr__( 'Categories', 'konte' ),
				'tag'      => esc_attr__( 'Tags', 'konte' ),
			),
		),
		'shop_toolbar_tabs_groups'        => array(
			'type'            => 'multicheck',
			'default'         => array( 'best_sellers', 'new', 'sale' ),
			'choices'         => array(
				'best_sellers' => esc_attr__( 'Best Sellers', 'konte' ),
				'featured'     => esc_attr__( 'Hot Products', 'konte' ),
				'new'          => esc_attr__( 'New Products', 'konte' ),
				'sale'         => esc_attr__( 'Sale Products', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_toolbar_tabs',
					'operator' => '==',
					'value'    => 'group',
				),
			),
		),
		'shop_toolbar_tabs_categories'    => array(
			'type'            => 'text',
			'description'     => esc_html__( 'Product categories. Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get limit number of top categories.', 'konte' ),
			'default'         => 3,
			'active_callback' => array(
				array(
					'setting'  => 'shop_toolbar_tabs',
					'operator' => '==',
					'value'    => 'category',
				),
			),
		),
		'shop_toolbar_tabs_subcategories' => array(
			'type'            => 'checkbox',
			'label'           => esc_html__( 'Replace by sub-categories', 'konte' ),
			'default'         => false,
			'active_callback' => array(
				array(
					'setting'  => 'shop_toolbar_tabs',
					'operator' => '==',
					'value'    => 'category',
				),
			),
		),
		'shop_toolbar_tabs_tags'          => array(
			'type'            => 'text',
			'description'     => esc_html__( 'Product tags. Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get limit number of top categories.', 'konte' ),
			'default'         => 3,
			'active_callback' => array(
				array(
					'setting'  => 'shop_toolbar_tabs',
					'operator' => '==',
					'value'    => 'tag',
				),
			),
		),
		'shop_products_toolbar_hr_3'        => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_toolbar_filter_open'        => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Products Filter', 'konte' ),
			'tooltip' => esc_html__( 'Add filter widgets in the "Products Filter" sidebar.', 'konte' ),
			'default' => 'off-canvas',
			'choices' => array(
				'off-canvas' => esc_attr__( 'Open filters on side', 'konte' ),
				'dropdown'   => esc_attr__( 'Open filters bellow', 'konte' ),
			),
		),
	);

	// Shop cart.
	$settings['shop_cart'] = array(
		'cart_open_after_added' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Open Cart Panel After Added', 'konte' ),
			'description' => esc_html__( 'Open the shopping cart panel after successful addition', 'konte' ),
			'default'     => false,
		),
		'cart_icon_source'      => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Cart Icon', 'konte' ),
			'default' => 'icon',
			'choices' => array(
				'icon'  => esc_attr__( 'Built-in Icon', 'konte' ),
				'image' => esc_attr__( 'Upload Image', 'konte' ),
				'svg'   => esc_attr__( 'SVG Code', 'konte' ),
			),
		),
		'cart_icon'             => array(
			'type'            => 'radio-image',
			'default'         => 'cart',
			'choices'         => array(
				'cart'       => $images_uri . '/cart/cart.svg',
				'shop-bag'   => $images_uri . '/cart/shop-bag.svg',
				'shop-bag-2' => $images_uri . '/cart/shop-bag-2.svg',
				'shop-bag-3' => $images_uri . '/cart/shop-bag-3.svg',
				'shop-bag-4' => $images_uri . '/cart/shop-bag-4.svg',
				'shop-bag-5' => $images_uri . '/cart/shop-bag-5.svg',
				'shop-cart'  => $images_uri . '/cart/shop-cart.svg',
			),
			'active_callback' => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'icon',
				),
			),
		),
		'cart_icon_image'       => array(
			'type'            => 'upload',
			'description'     => esc_html__( 'Normal icon', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'cart_icon_image_light' => array(
			'type'            => 'upload',
			'description'     => esc_html__( 'Light icon', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'cart_icon_width'       => array(
			'type'            => 'number',
			'description'     => esc_html__( 'Icon width', 'konte' ),
			'default'         => '20',
			'active_callback' => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'cart_icon_height'      => array(
			'type'            => 'number',
			'description'     => esc_html__( 'Icon Height', 'konte' ),
			'default'         => '20',
			'active_callback' => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'image',
				),
			),
		),
		'cart_icon_svg'         => array(
			'type'              => 'textarea',
			'description'       => esc_html__( 'Icon SVG code', 'konte' ),
			'sanitize_callback' => 'konte_sanitize_svg',
			'active_callback'   => array(
				array(
					'setting'  => 'cart_icon_source',
					'operator' => '==',
					'value'    => 'svg',
				),
			),
		),
		'cart_hr_1'          => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'cart_icon_floating'   => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Floating Cart Icon', 'konte' ),
			'description' => esc_html__( 'Display a sticky cart icon at the bottom.', 'konte' ),
			'default'     => false,
		),
	);

	// Checkout.
	$settings['shop_checkout'] = array(
		'checkout_layout'     => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Checkout Page Layout', 'konte' ),
			'default'         => '1-column',
			'choices'         => array(
				'1-column'  => esc_html__( '1 Column', 'konte' ),
				'2-columns' => esc_html__( '2 Columns', 'konte' ),
			),
		),
		'checkout_product_thumbnail'     => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Display the product thumbnail', 'konte' ),
			'default'         => false,
		),
	);

	// Shop badges.
	$settings['shop_badges'] = array(
		'shop_badge_shape'     => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Badge Shape', 'konte' ),
			'default'         => 'round',
			'choices'         => array(
				'round'     => esc_html__( 'Round', 'konte' ),
				'rectangle' => esc_html__( 'Rectangle', 'konte' ),
			),
		),
		'shop_badges_hr_1'          => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_badge_sale'          => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Sale Badge', 'konte' ),
			'description' => esc_html__( 'Display a badge for sale products.', 'konte' ),
			'default'     => true,
		),
		'shop_badge_sale_type'     => array(
			'type'            => 'radio',
			'label'           => esc_html__( 'Sale Badge Type', 'konte' ),
			'default'         => 'percent',
			'choices'         => array(
				'percent' => esc_html__( 'Percentage', 'konte' ),
				'text'    => esc_html__( 'Text', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_sale',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'shop_badge_sale_text'     => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Sale Badge Text', 'konte' ),
			'tooltip'         => esc_html__( 'Use {%} to display discount percentages, {$} to display discount amount.', 'konte' ),
			'default'         => esc_attr__( 'Sale', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_sale',
					'operator' => '=',
					'value'    => true,
				),
				array(
					'setting'  => 'shop_badge_sale_type',
					'operator' => '=',
					'value'    => 'text',
				),
			),
		),
		'shop_badge_sale_bg'  => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Sale Badge Background', 'konte' ),
			'default'         => '#3ee590',
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_sale',
					'operator' => '=',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce-badge.onsale',
					'property' => 'background-color',
				),
			),
		),
		'shop_badges_hr_2'          => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_badge_new'           => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'New Badge', 'konte' ),
			'description' => esc_html__( 'Display a badge for new products.', 'konte' ),
			'default'     => true,
		),
		'shop_badge_new_text'      => array(
			'type'            => 'text',
			'label'           => esc_html__( 'New Badge Text', 'konte' ),
			'default'         => esc_attr__( 'Novo', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_new',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'shop_badge_newness'       => array(
			'type'            => 'number',
			'description'     => esc_html__( 'Display the "New" badge for how many days?', 'konte' ),
			'tooltip'         => esc_html__( 'You can also add the NEW badge to each product in the Advanced setting tab of them.', 'konte' ),
			'default'         => 3,
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_new',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'shop_badge_new_bg'  => array(
			'type'            => 'color',
			'label'           => esc_html__( 'New Badge Background', 'konte' ),
			'default'         => '#ffb453',
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_new',
					'operator' => '=',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce-badge.new',
					'property' => 'background-color',
				),
			),
		),
		'shop_badges_hr_3'          => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_badge_featured'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Featured Badge', 'konte' ),
			'description' => esc_html__( 'Display a badge for featured products.', 'konte' ),
			'default'     => true,
		),
		'shop_badge_featured_text' => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Featured Badge Text', 'konte' ),
			'default'         => esc_attr__( 'Hot', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_featured',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'shop_badge_featured_bg'  => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Featured Badge Background', 'konte' ),
			'default'         => '#ff736c',
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_featured',
					'operator' => '=',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce-badge.featured',
					'property' => 'background-color',
				),
			),
		),
		'shop_badges_hr_4'          => array(
			'type'    => 'custom',
			'default' => '<hr>',
		),
		'shop_badge_soldout'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Sold Out Badge', 'konte' ),
			'description' => esc_html__( 'Display a badge for out of stock products.', 'konte' ),
			'default'     => false,
		),
		'shop_badge_soldout_text' => array(
			'type'            => 'text',
			'label'           => esc_html__( 'Sold Out Badge Text', 'konte' ),
			'default'         => esc_attr__( 'Sold Out', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_soldout',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'shop_badge_soldout_bg'  => array(
			'type'            => 'color',
			'label'           => esc_html__( 'Sold Out Badge Background', 'konte' ),
			'default'         => '#838889',
			'active_callback' => array(
				array(
					'setting'  => 'shop_badge_soldout',
					'operator' => '=',
					'value'    => true,
				),
			),
			'transport'       => 'postMessage',
			'js_vars'         => array(
				array(
					'element'  => '.woocommerce-badge.sold-out',
					'property' => 'background-color',
				),
			),
		),
	);

	// Quick view.
	$settings['shop_quickview'] = array(
		'product_quickview'            => array(
			'type'    => 'toggle',
			'label'   => esc_html__( 'Product Quick View', 'konte' ),
			'default' => true,
		),
		'product_quickview_style'            => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Quick View Style', 'konte' ),
			'default' => 'modal',
			'choices' => array(
				'modal' => esc_html__( 'Modal', 'konte' ),
				'panel' => esc_html__( 'Panel', 'konte' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'product_quickview',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'product_quickview_auto_background'  => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Dynamic Background Color', 'konte' ),
			'description'     => esc_html__( 'Detect background color from product main image', 'konte' ),
			'default'         => true,
			'active_callback' => array(
				array(
					'setting'  => 'product_quickview',
					'operator' => '=',
					'value'    => true,
				),
				array(
					'setting'  => 'product_quickview_style',
					'operator' => '=',
					'value'    => 'modal',
				),
			),
		),
		'product_quickview_detail_link'      => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Product Link', 'konte' ),
			'description' => esc_html__( 'Add a link to single product page to the quick-view modal', 'konte' ),
			'default'     => false,
			'active_callback' => array(
				array(
					'setting'  => 'product_quickview',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'product_quickview_detail_link_text' => array(
			'type'            => 'text',
			'description'     => esc_html__( 'Link text', 'konte' ),
			'default'         => esc_html__( 'View product details', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'product_quickview',
					'operator' => '=',
					'value'    => true,
				),
				array(
					'setting'  => 'product_quickview_detail_link',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
		'product_quickview_auto_close' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Auto close', 'konte' ),
			'description' => esc_html__( 'Auto close quick-view on successful product add to cart', 'konte' ),
			'default'     => true,
		),
	);

	// Shop notifications.
	$settings['shop_notice'] = array(
		'product_added_to_cart_notice' => array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Added to Cart Notification', 'konte' ),
			'description'     => esc_html__( 'Display a message when a product was added to cart.', 'konte' ),
			'default'         => true,
		),
		'product_added_to_cart_message' => array(
			'type'            => 'text',
			'description'     => esc_html__( 'Message', 'konte' ),
			'default'         => esc_attr__( 'Product was added to cart successfully', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'product_added_to_cart_notice',
					'operator' => '=',
					'value'    => true,
				),
			),
		),
	);

	if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
		$settings['shop_notice']['product_added_to_wishlist_notice'] = array(
			'type'            => 'toggle',
			'label'           => esc_html__( 'Added to Wishlist Notification', 'konte' ),
			'description'     => esc_html__( 'Display a message when a product was added to wishlist.', 'konte' ),
			'default'         => true,
		);
		$settings['shop_notice']['product_added_to_wishlist_message'] = array(
			'type'            => 'text',
			'description'     => esc_html__( 'Message', 'konte' ),
			'default'         => esc_attr__( 'Product was added to wishlist successfully', 'konte' ),
			'active_callback' => array(
				array(
					'setting'  => 'product_added_to_wishlist_notice',
					'operator' => '=',
					'value'    => true,
				),
			),
		);
	}

	// Currency Switcher
	$settings['currency_switcher'] = array(
		'currency_configuration' => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Currency Switcher', 'konte' ),
			'default' => 'name',
			'choices' => array(
				'name' => esc_html__( 'Displays the currency name only', 'konte' ),
				'flag' => esc_html__( 'Displays the currency flag only', 'konte' ),
				'both' => esc_html__( 'Displays both of name and flag', 'konte' ),
			),
		),
	);

	// Shop on mobile.
	$settings['mobile_shop_catalog'] = array(
		'mobile_shop_product_buttons' => array(
			'type'        => 'toggle',
			'label'       => esc_html__( 'Display Buttons', 'konte' ),
			'description' => esc_html__( 'Display product buttons on mobile', 'konte' ),
			'default'     => false,
		),
		'mobile_shop_columns' => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Products per row', 'konte' ),
			'description' => esc_html__( 'How many products should be shown per row on mobile', 'konte' ),
			'default'     => '2',
			'choices' => array(
				'1'  => esc_html__( '1 product', 'konte' ),
				'2' => esc_html__( '2 products', 'konte' ),
			),
		),
	);

	$settings['mobile_product'] = array(
		'mobile_product_gallery_layout' => array(
			'type'    => 'radio',
			'label'   => esc_html__( 'Product Gallery Layout', 'konte' ),
			'default' => 'dots',
			'choices' => array(
				'dots'       => esc_html__( 'Slideshow with dots pagination', 'konte' ),
				'thumbnails' => esc_html__( 'Slideshow with thumbnails pagination', 'konte' ),
			),
		),
	);

	// Shop on mobile.
	$settings['mobile_shop_cart'] = array(
		'mobile_shop_cart_panel_width' => array(
			'type'    => 'slider',
			'label'   => esc_html__( 'Cart Panel Width', 'konte' ),
			'default' => 100,
			'choices' => array(
				'min' => 0,
				'max' => 100,
				'step' => 1,
			),
			'transport' => 'postMessage',
			'js_vars'      => array(
				array(
					'element' => '.cart-panel .panel',
					'property' => 'width',
					'units'    => '%',
				),
			),
		),
	);

	return $settings;
}

add_filter( 'konte_customize_settings', 'konte_woocommerce_customize_settings' );

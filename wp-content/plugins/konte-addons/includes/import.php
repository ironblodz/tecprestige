<?php
/**
 * Register one click import demo data
 */

add_filter( 'soo_demo_packages', 'konte_addons_import_register' );

/**
 * Register demo data.
 *
 * @return array
 */
function konte_addons_import_register() {
	$active_tab = 'Elementor';

	if ( defined( 'WPB_VC_VERSION' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
		$active_tab = 'WPBakery Page Builder';
	}

	$options = array(
		'woocommerce_single_image_width'               => '840',
		'woocommerce_thumbnail_image_width'            => '680',
		'woocommerce_thumbnail_cropping'               => 'custom',
		'woocommerce_thumbnail_cropping_custom_width'  => '680',
		'woocommerce_thumbnail_cropping_custom_height' => '920',
		'elementor_disable_typography_schemes'         => 'yes',
		'elementor_disable_color_schemes'              => 'yes',
	);

	$menus = array(
		'primary'   => 'primary-menu',
		'secondary' => 'secondary-menu',
		'topbar'    => 'topbar-menu',
		'footer'    => 'footer-menu',
		'socials'   => 'socials',
	);

	return array(
		'active_tab' => $active_tab,

		// WPB.
		array(
			'name'    => 'Main Demo',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-1/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-1/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-1/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-1/customizer.dat',
			),
			'pages'      => array(
				'front_page'     => 'Home v1',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Minimal',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-2/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-2/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-2/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-2/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-2/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v2',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Modern',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-3/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-3/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-3/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-3/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-3/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v3',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Collections',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-4/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-4/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-4/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-4/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-4/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v4',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Classic',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-5/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-5/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-5/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-5/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-5/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v5',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Home & Gift',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-6/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-6/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-6/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-6/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-6/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v6',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Categories',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-7/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-7/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-7/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-7/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-7/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v7',
				'blog'           => 'Blog',
				'shop'           => 'Home v7',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Full Screen',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-8/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-8/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-8/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-8/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-8/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v8',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Dark Skin',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-9/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-9/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-9/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-9/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-9/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v9',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Clean',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-10/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-10/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-10/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-10/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-10/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v10',
				'blog'           => 'Blog',
				'shop'           => 'Home v10',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Lookbook',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-11/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-11/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-11/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-11/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-11/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v11',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Collection',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-12/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-12/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-12/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-12/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-12/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v12',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Instagram Shop',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-13/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-13/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-13/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-13/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-13/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v13',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Video Cover',
			'tab'     => 'WPBakery Page Builder',
			'preview' => 'https://uix.store/data/konte/wpb/home-14/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/wpb/home-14/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/wpb/home-14/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/wpb/home-14/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/wpb/home-14/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v14',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		// Elementor.
		array(
			'name'    => 'Main Demo',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-1/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-1/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-1/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-1/customizer.dat',
			),
			'pages'      => array(
				'front_page'     => 'Home v1',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Minimal',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-2/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-2/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-2/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-2/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-2/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v2',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Modern',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-3/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-3/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-3/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-3/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-3/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v3',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Collections',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-4/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-4/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-4/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-4/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-4/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v4',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Classic',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-5/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-5/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-5/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-5/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-5/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v5',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Home & Gift',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-6/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-6/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-6/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-6/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-6/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v6',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Categories',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-7/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-7/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-7/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-7/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-7/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v7',
				'blog'           => 'Blog',
				'shop'           => 'Home v7',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Full Screen',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-8/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-8/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-8/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-8/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-8/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v8',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Dark Skin',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-9/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-9/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-9/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-9/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-9/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v9',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Clean',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-10/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-10/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-10/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-10/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-10/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v10',
				'blog'           => 'Blog',
				'shop'           => 'Home v10',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Lookbook',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-11/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-11/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-11/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-11/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-11/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v11',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Collection',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-12/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-12/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-12/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-12/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-12/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v12',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Instagram Shop',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-13/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-13/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-13/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-13/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-13/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v13',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
		array(
			'name'    => 'Video Cover',
			'tab'     => 'Elementor',
			'preview' => 'https://uix.store/data/konte/elementor/home-14/preview.jpg',
			'files'   => array(
				'content'    => 'https://uix.store/data/konte/elementor/home-14/demo-content.xml',
				'widgets'    => 'https://uix.store/data/konte/elementor/home-14/widgets.wie',
				'customizer' => 'https://uix.store/data/konte/elementor/home-14/customizer.dat',
				'sliders'    => 'https://uix.store/data/konte/elementor/home-14/sliders.zip',
			),
			'pages'      => array(
				'front_page'     => 'Home v14',
				'blog'           => 'Blog',
				'shop'           => 'Shop',
				'cart'           => 'Cart',
				'checkout'       => 'Checkout',
				'my_account'     => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'      => 'Portfolio',
			),
			'menus'      => array(
				'primary'   => 'primary-menu',
				'secondary' => 'secondary-menu',
				'topbar'    => 'topbar-menu',
				'hamburger' => 'full-screen-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials-menu',
				'blog'      => 'blog-header-menu',
				'mobile'    => 'main-menu',
			),
			'options' => $options,
		),
	);
}

add_action( 'soodi_after_setup_pages', 'konte_addons_import_order_tracking' );

/**
 * Update more page options
 *
 * @param $pages
 */
function konte_addons_import_order_tracking( $pages ) {
	if ( isset( $pages['order_tracking'] ) ) {
		$order = get_page_by_title( $pages['order_tracking'] );

		if ( $order ) {
			update_option( 'order_tracking_page_id', $order->ID );
		}
	}

	if ( isset( $pages['portfolio'] ) ) {
		$portfolio = get_page_by_title( $pages['portfolio'] );

		if ( $portfolio ) {
			update_option( 'konte_portfolio_page_id', $portfolio->ID );
		}
	}
}

add_action( 'soodi_before_import_content', 'konte_addons_import_product_attributes' );

/**
 * Prepare product attributes before import demo content
 *
 * @param $file
 */
function konte_addons_import_product_attributes( $file ) {
	global $wpdb;

	if ( ! class_exists( 'WXR_Parser' ) ) {
		if ( ! file_exists( WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php' ) ) {
			return;
		}

		require_once WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php';
	}

	$parser      = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if ( empty( $import_data ) || is_wp_error( $import_data ) ) {
		return;
	}

	if ( isset( $import_data['posts'] ) ) {
		$posts = $import_data['posts'];

		if ( $posts && sizeof( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				if ( 'product' === $post['post_type'] ) {
					if ( ! empty( $post['terms'] ) ) {
						foreach ( $post['terms'] as $term ) {
							if ( strstr( $term['domain'], 'pa_' ) ) {
								if ( ! taxonomy_exists( $term['domain'] ) ) {
									$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

									// Create the taxonomy
									if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
										$attribute = array(
											'attribute_label'   => $attribute_name,
											'attribute_name'    => $attribute_name,
											'attribute_type'    => 'select',
											'attribute_orderby' => 'menu_order',
											'attribute_public'  => 0
										);
										$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
										delete_transient( 'wc_attribute_taxonomies' );
									}

									// Register the taxonomy now so that the import works!
									register_taxonomy(
										$term['domain'],
										apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
										apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										) )
									);
								}
							}
						}
					}
				}
			}
		}
	}
}
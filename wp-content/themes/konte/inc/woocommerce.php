<?php
/**
 * WooCommerce Compatibility file
 *
 * @link    https://woocommerce.com/
 *
 * @package Konte
 */


/**
 * WooCommerce setup.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 */
function konte_woocommerce_setup() {
	add_theme_support( 'woocommerce', array(
		'product_grid' => array(
			'default_rows'    => 4,
			'min_rows'        => 2,
			'max_rows'        => 20,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 6,
		),
		'variation_swatches' => array(
			'theme_style' => true,
			'shape'       => 'default',
			'size'        => array(
				'width'  => 18,
				'height' => 18,
			)
		),
		'wishlist' => array(
			'single_button_position' => 'theme',
			'loop_button_position'   => 'theme',
			'button_type'            => 'theme',
		),
	) );

	if ( konte_product_gallery_is_slider() ) {
		add_theme_support( 'wc-product-gallery-slider' );
	}

	if ( konte_get_option( 'product_image_zoom' ) ) {
		add_theme_support( 'wc-product-gallery-zoom' );
	}

	if ( konte_get_option( 'product_image_lightbox' ) ) {
		add_theme_support( 'wc-product-gallery-lightbox' );
	}
}

add_action( 'after_setup_theme', 'konte_woocommerce_setup' );

/**
 * Set the gallery thumbnail size.
 *
 * @param array $size Image size.
 *
 * @return array
 */
function konte_woocommerce_gallery_thumbnail_size( $size ) {
	$size['width']  = 120;
	$size['height'] = 140;
	$size['crop']   = 1;

	return $size;
}

add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'konte_woocommerce_gallery_thumbnail_size' );

/**
 * Register widget areas.
 */
function konte_woocommerce_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Shop Sidebar', 'konte' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Add widgets here in order to display on shop pages', 'konte' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Products Filter', 'konte' ),
		'id'            => 'product-filter',
		'description'   => esc_html__( 'Add product filter widgets here in order to display on shop toolbar', 'konte' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}

add_action( 'widgets_init', 'konte_woocommerce_widgets_init' );

/**
 * WooCommerce initialize.
 */
function konte_woocommerce_init() {
	Konte_WooCommerce_Template::init();
	Konte_WooCommerce_Template_Product::init();
	Konte_WooCommerce_Template_Catalog::init();
	Konte_WooCommerce_Template_Cart::init();
	Konte_WooCommerce_Template_Checkout::init();
	Konte_WooCommerce_Template_Account::init();

	if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
		Konte_WooCommerce_Template_Wishlist::init();
	}

	if ( class_exists( 'Konte_WooCommerce_Template_Compare' ) ) {
		Konte_WooCommerce_Template_Compare::init();
	}

	if ( is_admin() ) {
		Konte_WooCommerce_Settings::init();
	}
}

add_action( 'wp_loaded', 'konte_woocommerce_init' );

/**
 * Register theme assests for WooCommerce
 *
 * @since 2.3.5
 */
function konte_woocommerce_register_scripts() {
	$theme = is_child_theme() ? wp_get_theme()->parent() : wp_get_theme();
	$version = $theme->get( 'Version' );

	wp_register_style( 'konte-woocommerce', get_theme_file_uri( 'woocommerce.css' ), array(), $version );
	wp_register_style( 'konte-woocommerce-blocks', get_theme_file_uri( 'css/woocommerce-blocks.css' ), array(), $version );

	wp_register_script( 'zoom', get_theme_file_uri( 'js/jquery.zoom.min.js' ), array( 'jquery' ), '1.7.18', true );
	wp_register_script( 'swiper', get_theme_file_uri( 'js/swiper.min.js' ), array( 'jquery' ), '5.3.8', true );
	wp_register_script( 'jquery-quantity-dropdown', get_theme_file_uri( 'js/quantity-dropdown.js' ), array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'notify', get_theme_file_uri( 'js/notify.min.js' ), array( 'jquery' ), '0.4.2', true );
}

add_action( 'init', 'konte_woocommerce_register_scripts' );

// Product meta box.
add_filter( 'rwmb_meta_boxes', array( 'Konte_WooCommerce_Settings', 'product_meta_boxes' ) );

/**
 * Get IDs of the products that are set as new ones.
 *
 * @return array
 */
function konte_woocommerce_get_new_product_ids() {
	// Load from cache.
	$product_ids = get_transient( 'konte_woocommerce_products_new' );

	// Valid cache found.
	if ( false !== $product_ids ) {
		return apply_filters( 'konte_woocommerce_get_new_product_ids', $product_ids );
	}

	$product_ids = array();

	// Get products which are set as new.
	$meta_query   = WC()->query->get_meta_query();
	$meta_query[] = array(
		'key'   => '_is_new',
		'value' => 'yes',
	);
	$new_products = new WP_Query( array(
		'posts_per_page'   => -1,
		'post_type'        => 'product',
		'fields'           => 'ids',
		'suppress_filters' => true, // Get in all languages.
		'meta_query'       => $meta_query,
	) );

	if ( $new_products->have_posts() ) {
		$product_ids = array_merge( $product_ids, $new_products->posts );
	}

	// Get products after selected days.
	if ( konte_get_option( 'shop_badge_new' ) ) {
		$newness = intval( konte_get_option( 'shop_badge_newness' ) );

		if ( $newness > 0 ) {
			$new_products = new WP_Query( array(
				'posts_per_page'   => -1,
				'post_type'        => 'product',
				'fields'           => 'ids',
				'suppress_filters' => true, // Get in all languages.
				'date_query'       => array(
					'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) ),
				),
			) );

			if ( $new_products->have_posts() ) {
				$product_ids = array_merge( $product_ids, $new_products->posts );
			}
		}
	}

	set_transient( 'konte_woocommerce_products_new', $product_ids, DAY_IN_SECONDS );

	return apply_filters( 'konte_woocommerce_get_new_product_ids', $product_ids );
}

/**
 * Clear new product ids cache with the sale schedule which is run daily.
 */
function konte_woocommerce_clear_cache_daily() {
	delete_transient( 'konte_woocommerce_products_new' );
}

add_action( 'woocommerce_scheduled_sales', 'konte_woocommerce_clear_cache_daily' );
add_action( 'customize_save_after', 'konte_woocommerce_clear_cache_daily' );

/**
 * Clear new product ids cache when update/trash/delete products.
 *
 * @param int $post_id
 */
function konte_woocommerce_clear_cache( $post_id ) {
	if ( 'product' != get_post_type( $post_id ) ) {
		return;
	}

	do_action( 'konte_woocommerce_clear_cache' );
}

add_action( 'save_post', 'konte_woocommerce_clear_cache' );
add_action( 'wp_trash_post', 'konte_woocommerce_clear_cache' );
add_action( 'before_delete_post', 'konte_woocommerce_clear_cache' );
add_action( 'konte_woocommerce_clear_cache', 'konte_woocommerce_clear_cache_daily' );

// Load files.
require get_theme_file_path( '/inc/woocommerce/adjacent-products.php' );
require get_theme_file_path( '/inc/woocommerce/settings.php' );
require get_theme_file_path( '/inc/woocommerce/theme-options.php' );
require get_theme_file_path( '/inc/woocommerce/template.php' );
require get_theme_file_path( '/inc/woocommerce/template-product.php' );
require get_theme_file_path( '/inc/woocommerce/template-catalog.php' );
require get_theme_file_path( '/inc/woocommerce/template-cart.php' );
require get_theme_file_path( '/inc/woocommerce/template-checkout.php' );
require get_theme_file_path( '/inc/woocommerce/template-account.php' );

if ( class_exists( 'Soo_Wishlist' ) || class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
	require get_theme_file_path( '/inc/woocommerce/template-wishlist.php' );
}

if ( class_exists( '\WCBoost\ProductsCompare\Plugin' ) ) {
	require get_theme_file_path( '/inc/woocommerce/template-compare.php' );
}

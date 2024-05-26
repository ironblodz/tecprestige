<?php
/**
 * Konte functions and definitions
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Konte
 */

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function konte_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'konte_content_width', 1400 );
}

add_action( 'after_setup_theme', 'konte_content_width', 0 );

if ( ! function_exists( 'konte_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function konte_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'konte', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Enable support for common post formats
		add_theme_support( 'post-formats', array( 'gallery', 'video' ) );

		// Register menu locations
		register_nav_menus( array(
			'primary'   => esc_html__( 'Primary Menu', 'konte' ),
			'secondary' => esc_html__( 'Secondary Menu', 'konte' ),
			'topbar'    => esc_html__( 'Topbar Menu', 'konte' ),
			'hamburger' => esc_html__( 'Full Screen Menu', 'konte' ),
			'socials'   => esc_html__( 'Socials Menu', 'konte' ),
			'blog'      => esc_html__( 'Blog Header Menu', 'konte' ),
			'footer'    => esc_html__( 'Footer Menu', 'konte' ),
			'mobile'    => esc_html__( 'Mobile Menu', 'konte' ),
		) );

		// Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( array( 'css/style-editor.css', konte_fonts_url() ) );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Add support for font sizes
		add_theme_support( 'editor-font-sizes', array(
			array(
				'name'      => __( 'Small', 'konte' ),
				'shortName' => __( 'S', 'konte' ),
				'size'      => 12,
				'slug'      => 'small'
			),
			array(
				'name'      => __( 'Normal', 'konte' ),
				'shortName' => __( 'N', 'konte' ),
				'size'      => 18,
				'slug'      => 'normal'
			),
			array(
				'name'      => __( 'Medium', 'konte' ),
				'shortName' => __( 'M', 'konte' ),
				'size'      => 24,
				'slug'      => 'medium'
			),
			array(
				'name'      => __( 'Large', 'konte' ),
				'shortName' => __( 'L', 'konte' ),
				'size'      => 40,
				'slug'      => 'large'
			),
			array(
				'name'      => __( 'Huge', 'konte' ),
				'shortName' => __( 'XL', 'konte' ),
				'size'      => 64,
				'slug'      => 'huge'
			),
		) );

		// Add image sizes.
		set_post_thumbnail_size( 360, 210, true );
		add_image_size( 'konte-post-thumbnail-medium', 580, 400, true );
		add_image_size( 'konte-post-thumbnail-large', 750, 420, true );
		add_image_size( 'konte-post-thumbnail-navigation', 100, 68, true );
		add_image_size( 'konte-post-thumbnail-shortcode', 450, 300, true );
	}
endif;

add_action( 'after_setup_theme', 'konte_setup' );

/**
 * Setup theme instances
 */
function konte_init() {
	if ( is_admin() ) {
		Konte_Term_Edit::instance();
	}
}

add_action( 'init', 'konte_init', 20 );

/**
 * Register widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function konte_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'konte' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Add widgets here in order to display them on blog pages', 'konte' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Off Screen Sidebar', 'konte' ),
		'id'            => 'off-screen',
		'description'   => esc_html__( 'Add widgets here in order to display inside off-screen panel of hamburger icon', 'konte' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	for ( $i = 1; $i < 5; $i++ ) {
		register_sidebar( array(
			/* translators: %s the footer sidebar number */
			'name'          => sprintf( esc_html__( 'Footer Sidebar %s', 'konte' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => esc_html__( 'Add widgets here in order to display on footer', 'konte' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
}

add_action( 'widgets_init', 'konte_widgets_init' );

/**
 * Register elementor locations
 */
function konte_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
}

add_action( 'elementor/theme/register_locations', 'konte_register_elementor_locations' );

/**
 * Custom functions for this theme.
 */
require get_template_directory() . '/inc/functions/options.php';
require get_template_directory() . '/inc/functions/layout.php';
require get_template_directory() . '/inc/functions/style.php';
require get_template_directory() . '/inc/functions/header.php';
require get_template_directory() . '/inc/functions/menus.php';
require get_template_directory() . '/inc/functions/breadcrumbs.php';
require get_template_directory() . '/inc/functions/post.php';
require get_template_directory() . '/inc/functions/shop.php';
require get_template_directory() . '/inc/functions/footer.php';
require get_template_directory() . '/inc/functions/misc.php';

/**
 * Custom functions that act in the frontend.
 */
require get_template_directory() . '/inc/frontend/frontend.php';
require get_template_directory() . '/inc/frontend/header.php';
require get_template_directory() . '/inc/frontend/menus.php';
require get_template_directory() . '/inc/frontend/entry.php';
require get_template_directory() . '/inc/frontend/widgets.php';
require get_template_directory() . '/inc/frontend/footer.php';
require get_template_directory() . '/inc/frontend/maintenance.php';
require get_template_directory() . '/inc/frontend/mobile.php';

/**
 * Custom functions that act in widget editor
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Custom functions that act in the backend.
 */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/plugins.php';
	require get_template_directory() . '/inc/admin/meta-boxes.php';
	require get_template_directory() . '/inc/admin/term.php';
	require get_template_directory() . '/inc/admin/editor.php';
	require get_template_directory() . '/inc/admin/ajax.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( get_option( 'konte_portfolio' ) ) {
	require get_template_directory() . '/inc/portfolio.php';
}

/**
 * Customizer additions.
 */
if ( class_exists( 'Kirki' ) ) {
	require get_template_directory() . '/inc/customizer.php';
}

/**
 * WPML compatible
 */
if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE ) {
	require get_template_directory() . '/inc/wpml.php';
}

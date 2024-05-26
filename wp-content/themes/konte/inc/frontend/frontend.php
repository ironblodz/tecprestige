<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Konte
 */


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function konte_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( is_home() || is_tag() || is_category() || is_tax( get_object_taxonomies( 'post' ) ) ) {
		$classes[] = 'blog-hfeed';
	}

	// Adds a class of sidebar position.
	$layout = konte_get_layout();
	$classes[] = $layout;

	if ( 'no-sidebar' != $layout ) {
		if ( ! is_active_sidebar( konte_get_sidebar_id() ) ) {
			$classes[] = 'empty-sidebar';
		}
	}

	// Adds a class of blog layout.
	if ( is_home() ) {
		$classes[] = 'blog-' . konte_get_option( 'blog_layout' );
	}

	// Adds a class of post sharing.
	if ( is_singular( 'post' ) && konte_get_option( 'post_sharing' ) ) {
		$classes[] = 'post-shareable';
	}

	// Adds a class of top and bottom spacings.
	if ( is_page() ) {
		if ( 'none' == get_post_meta( get_the_ID(), 'top_spacing', true ) || ('transparent' == get_post_meta( get_the_ID(), 'header_background', true ) && ! is_page_template( 'templates/flex-posts.php' ) ) ) {
			$classes[] = 'no-top-spacing';
		}

		if ( 'none' == get_post_meta( get_the_ID(), 'bottom_spacing', true ) || 'transparent' == get_post_meta( get_the_ID(), 'footer_background', true ) ) {
			$classes[] = 'no-bottom-spacing';
		}

		if ( ! is_page_template() && ( $container = get_post_meta( get_the_ID(), 'content_container_width', true ) ) ) {
			$classes[] = 'content-area-' . $container;
		}
	} elseif ( is_singular( array( 'elementor_library' ) ) ) {
		$classes[] = 'no-top-spacing';
		$classes[] = 'no-bottom-spacing';
	}

	// Adds a class of header on side.
	if ( 'v10' == konte_get_header_layout() ) {
		$classes[] = 'header-vertical';
	}

	// Adds a class of sticky header.
	if ( 'none' !== konte_get_option( 'header_sticky' ) ) {
		$classes[] = 'header-sticky';
	}

	// Adds a class of mobile bottom bar.
	if ( konte_get_option( 'mobile_bottom_bar' ) ) {
		$classes[] = 'mobile-bottom-bar-enabled';
	}

	return $classes;
}

add_filter( 'body_class', 'konte_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function konte_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}

add_action( 'wp_head', 'konte_pingback_header' );

/**
 * Display custom color CSS.
 */
function konte_color_scheme_css() {
	$color = konte_get_option( 'color_scheme_custom' ) ? konte_get_option( 'color_scheme_color' ) : konte_get_option( 'color_scheme' );
	$color = $color ? $color : '#161619';

	if ( '#161619' === $color && ! is_customize_preview() ) {
		return;
	}

	// @codingStandardsIgnoreLine
	printf(
		'<%1$s id="custom-theme-colors" %2$s>%3$s</%1$s>',
		'style',
		is_customize_preview() ? 'data-color="' . esc_attr( $color ) . '"' : '',
		konte_custom_color_css()
	);
}

add_action( 'wp_head', 'konte_color_scheme_css' );

/**
 * Preload custom fonts: Function Pro
 *
 * @return void
 */
function konte_preload_resources() {
	// Preload font files.
	$fonts = array(
		'fonts/functionpro-light-webfont.woff2',
		'fonts/functionpro-book-webfont.woff2',
		'fonts/functionpro-medium-webfont.woff2',
		'fonts/functionpro-demi-webfont.woff2',
		'fonts/functionpro-bold-webfont.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>',
			esc_url( get_theme_file_uri( $font ) )
		);
	}
}

add_action( 'wp_head', 'konte_preload_resources', 1 );

/**
 * Enqueue scripts and styles.
 */
function konte_scripts() {
	$theme = wp_get_theme();
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_style( 'animate', get_template_directory_uri() . '/css/animate.css', array(), '3.5.2' );
	wp_register_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.7.0' );
	wp_register_style( 'bootstrap-grid', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.3.7' );

	$fonts_url = konte_fonts_url();

	if ( $fonts_url ) {
		wp_enqueue_style( 'konte-fonts', $fonts_url );
	}

	wp_enqueue_style( 'konte', get_template_directory_uri() . '/style.css', array(
		'animate',
		'font-awesome',
		'bootstrap-grid',
	), $theme->Version );


	wp_register_script( 'slick', get_template_directory_uri() . '/js/slick' . $debug . '.js', array( 'jquery' ), '1.8.1', true );
	wp_register_script( 'perfect-scrollbar', get_template_directory_uri() . '/js/perfect-scrollbar' . $debug . '.js', array( 'jquery' ), '1.5.0', true );
	wp_register_script( 'sticky-kit', get_template_directory_uri() . '/js/sticky-kit' . $debug . '.js', array( 'jquery' ), '1.1.3', true );
	wp_register_script( 'background-color-theif', get_template_directory_uri() . '/js/background-color-theif' . $debug . '.js', array(), '1.0', true );
	wp_register_script( 'scroll-trigger', get_template_directory_uri() . '/js/scrolltrigger.min.js', array(), '0.3.6', true );
	wp_register_script( 'headroom', get_template_directory_uri() . '/js/headroom.min.js', array(), '0.9.3', true );

	wp_register_script( 'konte', get_template_directory_uri() . '/js/scripts' . $debug . '.js', array(
		'jquery',
		'jquery-serialize-object',
		'background-color-theif',
		'imagesloaded',
		'slick',
		'perfect-scrollbar',
	), $theme->Version, true );

	wp_add_inline_style( 'konte', konte_get_inline_style() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( konte_get_option( 'sidebar_sticky' ) ) {
		wp_enqueue_script( 'sticky-kit' );
	}

	if ( is_page_template( 'templates/flex-posts.php' ) ) {
		wp_enqueue_script( 'jquery-masonry' );
		wp_enqueue_script( 'scroll-trigger' );
	}

	if ( ( is_post_type_archive( 'portfolio' ) || is_tax( get_object_taxonomies( 'portfolio' ) ) ) && 'masonry' == konte_get_option( 'portfolio_layout' ) ) {
		wp_enqueue_script( 'jquery-masonry' );
	}

	if ( is_page() && 'video' == get_post_meta( get_the_ID(), 'page_featured_content', true ) ) {
		wp_enqueue_script( 'youtube-player-api', 'https://www.youtube.com/iframe_api', array(), false, true );
	}

	if ( 'smart' == konte_get_option( 'header_sticky' ) ) {
		wp_enqueue_script( 'headroom' );
	}

	wp_enqueue_script( 'konte' );
	wp_localize_script( 'konte', 'konteData', array(
		'ajax_url'                          => admin_url( 'admin-ajax.php' ),
		'rtl'                               => is_rtl(),
		'preloader'                         => konte_get_option( 'preloader_enable' ),
		'sticky_header'                     => konte_get_option( 'header_sticky' ),
		'header_search_ajax'                => konte_get_option( 'header_search_ajax' ),
		'wishlist_count'                    => class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ? Konte_WooCommerce_Template_Wishlist::count_wishlist_items() : 0,
		'product_sticky_summary'            => konte_get_option( 'product_sticky_summary' ),
		'product_summary_sticky_mode'       => konte_get_option( 'product_summary_sticky_mode' ),
		'product_auto_background'           => konte_get_option( 'product_auto_background' ),
		'product_gallery_slider'            => konte_product_gallery_is_slider(),
		'product_image_lightbox'            => konte_get_option( 'product_image_lightbox' ),
		'product_image_zoom'                => konte_get_option( 'product_image_zoom' ),
		'product_quantity_input_style'      => 'v4' == konte_get_option( 'product_layout' ) ? konte_get_option( 'product_v4_quantity_input_style' ) : 'default',
		'product_ajax_addtocart'            => konte_get_option( 'product_ajax_addtocart' ),
		'cart_open_after_added'             => konte_get_option( 'cart_open_after_added' ),
		'product_quickview_auto_background' => 'modal' == konte_get_option( 'product_quickview_style' ) && konte_get_option( 'product_quickview_auto_background' ),
		'product_quickview_auto_close'      => konte_get_option( 'product_quickview_auto_close' ),
		'popup'                             => konte_get_option( 'popup_enable' ),
		'popup_frequency'                   => konte_get_option( 'popup_frequency' ),
		'popup_visible'                     => konte_get_option( 'popup_visible' ),
		'popup_visible_delay'               => konte_get_option( 'popup_visible_delay' ),
		'added_to_cart_notice'              => konte_get_option( 'product_added_to_cart_notice' ),
		'added_to_cart_message'             => konte_get_option( 'product_added_to_cart_message' ),
		'added_to_wishlist_notice'          => konte_get_option( 'product_added_to_wishlist_notice' ),
		'added_to_wishlist_message'         => konte_get_option( 'product_added_to_wishlist_message' ),
		'blog_nav_ajax_url_change'          => konte_get_option( 'blog_nav_ajax_url_change' ),
		'shop_nav_ajax_url_change'          => konte_get_option( 'shop_nav_ajax_url_change' ),
		'portfolio_nav_ajax_url_change'     => konte_get_option( 'portfolio_nav_ajax_url_change' ),
		'product_quickview_nonce'           => wp_create_nonce( 'konte-product-quickview' ),
		'product_search_nonce'              => wp_create_nonce( 'konte-product-search' ),
		'share_nonce'                       => wp_create_nonce( 'konte-fetch-share-count' ),
		'add_to_cart_nonce'                 => wp_create_nonce( 'konte-add-to-cart' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'konte_scripts' );

/**
 * Adds preloader container at the bottom of the site
 */
function konte_preloader() {
	if ( ! konte_get_option( 'preloader_enable' ) && ! is_customize_preview() ) {
		return;
	}

	get_template_part( 'template-parts/preloader/preloader' );
}

add_action( 'konte_before_site', 'konte_preloader', 1 );

/**
 * Add icon list as svg after <body> tag and hide it
 */
function konte_include_svg_icons() {
	$icons_file = get_parent_theme_file_path( '/images/svg-icons.svg' );

	// If it exists, include it.
	if ( file_exists( $icons_file ) ) {
		echo '<div id="svg-defs" class="svg-defs hidden" aria-hidden="true" tabindex="-1">';
		require_once( $icons_file );
		echo '</div>';
	}
}

add_action( 'konte_before_site', 'konte_include_svg_icons' );

/**
 * Change the content container class on some special pages.
 *
 * @param string $class The class name.
 *
 * @return string
 */
function konte_page_content_container_class( $class ) {
	if ( is_page_template( 'templates/split.php' ) ) {
		$class = '';
	} elseif ( is_page_template( 'templates/flex-posts.php' ) ) {
		$class = 'konte-container';
	} elseif ( is_page_template( 'templates/large-container.php' ) ) {
		$class = 'konte-container';
	} elseif ( konte_is_maintenance_page() ) {
		$class = '';

		if ( 'fullscreen' == konte_get_option( 'maintenance_layout' ) ) {
			$class = 'container text-' . konte_get_option( 'maintenance_textcolor' );
		}
	} elseif ( is_page() && ! is_page_template() && ( $container = get_post_meta( get_the_ID(), 'content_container_width', true ) ) ) {
		$classes = array(
			'standard' => 'container',
			'large'    => 'konte-container',
			'wide'     => 'konte-container-fluid',
			'wider'    => 'container-fluid',
			'full'     => 'konte-container-full',
		);

		if ( array_key_exists( $container, $classes ) ) {
			$class = $classes[ $container ];
		}
	}

	return $class;
}

add_filter( 'konte_content_container_class', 'konte_page_content_container_class' );

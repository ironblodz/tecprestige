<?php
/**
 * General template hooks.
 *
 * @package Konte
 */

/**
 * Class of general template.
 */
class Konte_WooCommerce_Template {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Disable the default WooCommerce stylesheet.
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ), 20 );
		add_action( 'enqueue_block_assets', array( __CLASS__, 'block_assets' ) );

		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'konte_custom_colors_css', array( __CLASS__, 'custom_color_css' ), 10, 2 );

		// Change the header layout if meta key is set.
		add_filter( 'konte_get_header_layout', array( __CLASS__, 'header_layout' ) );

		// Change the site content container.
		add_filter( 'konte_content_container_class', array( __CLASS__, 'content_container_class' ), 20 );

		// Remove default WooCommerce wrapper.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		add_action( 'woocommerce_before_main_content', array( __CLASS__, 'wrapper_before' ) );
		add_action( 'woocommerce_after_main_content', array( __CLASS__, 'wrapper_after' ) );

		// Change the markup of sale flash.
		add_filter( 'woocommerce_sale_flash', array( __CLASS__, 'sale_flash' ), 10, 3 );

		// Change star rating HTML.
		add_filter( 'woocommerce_get_star_rating_html', array( __CLASS__, 'star_rating_html' ), 10, 3 );

		// Change price order.
		add_filter( 'woocommerce_format_sale_price', array( __CLASS__, 'sale_price_html' ), 10, 3 );

		// Edit breadcrum.
		add_filter( 'woocommerce_breadcrumb_defaults', array( __CLASS__, 'breadcrumb_args' ) );

		// Update counter via ajax.
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'cart_link_fragment' ) );

		// Edit the quantity input.
		add_filter( 'konte_woocommerce_quantity_class', array( __CLASS__, 'quantity_class' ), 10, 3 );
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'quantity_input_args' ) );

		// Handle header search in via AJAX.
	 	add_filter( 'konte_ajax_header_search_products', array( __CLASS__, 'header_search_products' ), 10, 2 );
	 	add_filter( 'konte_ajax_header_search_product_item', array( __CLASS__, 'header_search_product_item' ) );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @return void
	 */
	public static function scripts() {
		if ( wp_style_is( 'select2', 'registered' ) ) {
			wp_enqueue_style( 'select2' );
		}

		wp_enqueue_style( 'konte-woocommerce' );

		$css            = self::get_inline_style();
		$product_layout = konte_get_option( 'product_layout' );

		if ( $css ) {
			wp_add_inline_style( 'konte-woocommerce', $css );
		}

		if ( is_rtl() ) {
			wp_enqueue_style( 'konte-woocommerce-rtl', get_template_directory_uri() . '/woocommerce-rtl.css' );
		}

		// Prevent initially open the product tab panel.
		wp_add_inline_script( 'wc-single-product', '
			jQuery( function( $ ) {
				$( document.body ).off( "click", ".woocommerce-tabs.panels-offscreen .wc-tabs li a, .woocommerce-tabs.panels-offscreen ul.tabs li a" );
			} );
		' );

		if ( wp_script_is( 'flexslider', 'registered' ) ) {
			wp_enqueue_script( 'flexslider' );
		}

		if ( wp_script_is( 'select2', 'registered' ) ) {
			wp_enqueue_script( 'select2' );
		}

		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		if ( is_product() && in_array( $product_layout, array( 'v2', 'v5', 'v7' ) ) ) {
			wp_enqueue_script( 'sticky-kit' );
		}

		if ( 'zoom' == konte_get_option( 'shop_product_hover' ) ) {
			wp_enqueue_script( 'zoom' );
		}

		if ( 'masonry' == konte_get_option( 'shop_layout' ) ) {
			wp_enqueue_script( 'jquery-masonry' );
		}

		if ( is_product() || 'carousel' == konte_get_option( 'shop_layout' ) ) {
			wp_enqueue_script( 'swiper' );
		}

		if ( konte_get_option( 'product_added_to_cart_notice' ) || konte_get_option( 'product_added_to_wishlist_notice' ) ) {
			wp_enqueue_script( 'notify' );
		}

		wp_enqueue_script( 'wc-cart-fragments' );
		wp_enqueue_script( 'jquery-quantity-dropdown' );
	}

	/**
	 * Enqueue block assets
	 *
	 * @return void
	 */
	public static function block_assets() {
		wp_enqueue_style( 'konte-woocommerce-blocks' );
	}

	/**
	 * Get CSS code of settings for shop.
	 *
	 * @return string
	 */
	public static function get_inline_style() {
		$product_layout = konte_get_option( 'product_layout' );
		$header_height  = 0;
		$css            = '';

		// Typography
		$css .= self::typography_css();

		// Get header height.
		if (
			( is_product() && in_array( $product_layout, array( 'v1', 'v3', 'v5' ) ) )
			|| ( ( is_shop() || is_product_taxonomy() ) && 'standard' == konte_get_option( 'shop_page_header' ) )
		) {
			if ( 'custom' != konte_get_header_layout() ) {
				$header_height = absint( konte_get_option( 'header_main_height' ) );

				if ( in_array( konte_get_header_layout(), array( 'v8', 'v9' ) ) ) {
					$header_height += absint( konte_get_option( 'header_bottom_height' ) );
				}
			} else {
				$header_main = array_filter( array(
					'left'   => konte_get_option( 'header_main_left' ),
					'center' => konte_get_option( 'header_main_center' ),
					'right'  => konte_get_option( 'header_main_right' ),
				) );

				if ( ! empty( $header_main ) ) {
					$header_height += absint( konte_get_option( 'header_main_height' ) );
				}

				$header_bottom = array_filter( array(
					'left'   => konte_get_option( 'header_bottom_left' ),
					'center' => konte_get_option( 'header_bottom_center' ),
					'right'  => konte_get_option( 'header_bottom_right' ),
				) );

				if ( ! empty( $header_bottom ) ) {
					$header_height += absint( konte_get_option( 'header_bottom_height' ) );
				}
			}
		}

		// Product.
		if ( is_product() && in_array( $product_layout, array( 'v1', 'v3', 'v5' ) ) ) {
			if ( $header_height ) {
				if ( 'v5' != $product_layout ) {
					$css .= 'div.product { padding-top: ' . $header_height . 'px; }';
				} else {
					$css .= 'div.product .summary { padding-top: ' . $header_height . 'px; }';
				}

				$css .= '.woocommerce div.product.layout-v1 .woocommerce-product-gallery { margin-top: -' . ( $header_height + 80 ) . 'px; }';
				$css .= '.woocommerce div.product.layout-v1 .woocommerce-badges { top: ' . ( $header_height + 80 ) . 'px; }';
				$css .= '.single-product.product-v3 .product-toolbar { top: ' . $header_height . 'px; }';

				$css .= '@media (max-width: 1199px) { .woocommerce div.product.layout-v1 .woocommerce-product-gallery { margin-top: 0; } }';
			}

			if ( $mobile_header_height = absint( konte_get_option( 'mobile_header_height' ) ) ) {
				$css .= '@media (max-width: 1199px) { .woocommerce div.product.layout-v1 .woocommerce-badges { top: ' . ( $mobile_header_height + 80 ) . 'px; } }';
				$css .= '@media (max-width: 991px) { .woocommerce div.product.layout-v1 .woocommerce-badges { top: ' . ( $mobile_header_height + 40 ) . 'px; } }';
				$css .= '@media (max-width: 767px) { .woocommerce div.product.layout-v1 .woocommerce-badges { top: ' . ( $mobile_header_height ) . 'px; } }';
				$css .= '.single-product.product-v3 .product-toolbar { top: ' . $mobile_header_height . 'px; }';
			}

			if ( 'v1' == $product_layout || 'v3' == $product_layout ) {
				$background = get_post_meta( get_the_ID(), 'background_color', true );

				if ( $background ) {
					$css .= '.woocommerce div.product { background-color: ' . esc_attr( $background ) . ' }';
				}
			}
		}

		// Page header.
		if ( ( is_shop() || is_product_taxonomy() ) ) {
			if ( 'standard' == konte_get_option( 'shop_page_header' ) ) {
				$page_header_height = konte_get_option( 'shop_page_header_height' );
				$page_header_image  = konte_get_option( 'shop_page_header_image' );
				$header_class       = apply_filters( 'konte_header_class', array() );

				if ( is_product_taxonomy() ) {
					$term_id  = get_queried_object_id();
					$image_id = absint( get_term_meta( $term_id, 'page_header_image_id', true ) );

					if ( $image_id ) {
						$image             = wp_get_attachment_image_src( $image_id, 'full' );
						$page_header_image = $image ? $image[0] : $page_header_image;
					}
				}

				$css .= '.woocommerce-products-header {
					height: ' . intval( $page_header_height ) . 'px;
					background-image: url(' . esc_url( $page_header_image ) . ');
					padding-top: ' . ( in_array( 'transparent', $header_class, true ) ? $header_height : 0 ) . 'px
				}';

				$css .= '@media (max-width: 767px) { .woocommerce-products-header {
					height: ' . ( intval( $page_header_height ) / 2 ) . 'px;
					padding-top: ' . ( in_array( 'transparent', $header_class, true ) ? intval( konte_get_option( 'mobile_header_height' ) ) : 0 ) . 'px
				} }';
			}
		}

		// Display settings for shop page.
		if ( is_shop() ) {
			$shop_page_id = wc_get_page_id( 'shop' );

			if ( 'custom' == get_post_meta( $shop_page_id, 'header_background', true ) ) {
				$background = get_post_meta( $shop_page_id, 'header_background_color', true );

				if ( $background ) {
					$css .= '.woocommerce .site-header.custom { background-color: ' . $background . '; }';
				}
			}

			if ( 'custom' == get_post_meta( $shop_page_id, 'footer_background', true ) ) {
				$background = get_post_meta( $shop_page_id, 'footer_background_color', true );

				if ( $background ) {
					$css .= '.woocommerce .site-footer.custom { background-color: ' . $background . '; }';
				}
			}

			if ( 'custom' == get_post_meta( $shop_page_id, 'top_spacing', true ) ) {
				$top_padding = get_post_meta( $shop_page_id, 'top_padding', true );
				$css         .= $top_padding ? '.site-content { padding-top: ' . esc_attr( $top_padding ) . ' !important; }' : '';
			}

			if ( 'custom' == get_post_meta( $shop_page_id, 'bottom_spacing', true ) ) {
				$bottom_padding = get_post_meta( $shop_page_id, 'bottom_padding', true );
				$css            .= $bottom_padding ? '.site-content { padding-bottom: ' . esc_attr( $bottom_padding ) . ' !important; }' : '';
			}

			// Custom page CSS of WPB.
			$shop_page_css           = get_post_meta( $shop_page_id, '_wpb_post_custom_css', true );
			$shop_page_shortcode_css = get_post_meta( $shop_page_id, '_wpb_shortcodes_custom_css', true );

			if ( ! empty( $shop_page_css ) ) {
				$css .= $shop_page_css;
			}

			if ( ! empty( $shop_page_shortcode_css ) ) {
				$css .= $shop_page_shortcode_css;
			}
		}

		// Product badges.
		if ( ( $color = konte_get_option( 'shop_badge_sale_bg' ) ) ) {
			$css .= '.woocommerce-badge.onsale {background-color: ' . $color . '}';
		}

		if ( ( $color = konte_get_option( 'shop_badge_new_bg' ) ) ) {
			$css .= '.woocommerce-badge.new {background-color: ' . $color . '}';
		}

		if ( ( $color = konte_get_option( 'shop_badge_featured_bg' ) ) ) {
			$css .= '.woocommerce-badge.featured {background-color: ' . $color . '}';
		}

		if ( ( $color = konte_get_option( 'shop_badge_soldout_bg' ) ) ) {
			$css .= '.woocommerce-badge.sold-out {background-color: ' . $color . '}';
		}

		// Mobile shopping cart panel width.
		$mobile_cart_panel_width = konte_get_option( 'mobile_shop_cart_panel_width' );
		if ( 100 != $mobile_cart_panel_width ) {
			$css .= '@media (max-width: 767px) { .cart-panel .panel { width: ' . $mobile_cart_panel_width . '%; } }';
		}

		return apply_filters( 'konte_woocommerce_inline_style', $css );
	}

	/**
	 * Get typography CSS base on settings
	 */
	protected static function typography_css() {
		$settings = array(
			'typo_product_title'         => '.woocommerce div.product .product_title',
			'typo_product_short_desc'    => '.woocommerce div.product .woocommerce-variation-description, .woocommerce div.product .woocommerce-product-details__short-description, .woocommerce .woocommerce-Tabs-panel--description',
			'typo_catalog_page_title'    => '.woocommerce-products-header .page-title, .woocommerce-products-header.layout-standard .page-title',
			'typo_catalog_product_title' => 'ul.products li.product .woocommerce-loop-product__title a',
		);

		return konte_get_typography_css( $settings );
	}

	/**
	 * Customize WC elements with selected color scheme.
	 *
	 * @param  string $css
	 * @param  string $color
	 *
	 * @return string
	 */
	public static function custom_color_css( $css, $color ) {
		$css .= '
			table.cart .actions .button,
			.cart-panel .widget_shopping_cart_content .buttons .button {
				color: ' . $color . ';
			}

			.woocommerce div.product .single_add_to_cart_button,
			.woocommerce div.product.layout-v1 .product-share .sharing-icon,
			.woocommerce div.product.layout-v1 .product-share .socials,
			.woocommerce div.product.layout-v5 .product-share .sharing-icon,
			.woocommerce div.product.layout-v5 .product-share .socials,
			.cart-panel .widget_shopping_cart_content .buttons .button:hover,
			.cart-panel .widget_shopping_cart_content .buttons .checkout,
			.cart-collaterals .checkout-button,
			.woocommerce-checkout-payment .place-order .button {
				color: #fff;
				background-color: ' . $color . ';
			}

			.cart-panel .widget_shopping_cart_content .buttons .button:hover {
				border-color: ' . $color . ';
			}
		';

		return $css;
	}

	/**
	 * Add 'woocommerce-active' class to the body tag.
	 *
	 * @param  array $classes CSS classes applied to the body tag.
	 *
	 * @return array $classes modified to include 'woocommerce-active' class.
	 */
	public static function body_class( $classes ) {
		$classes[] = 'woocommerce-active';

		// Adds a class of product layout.
		if ( is_product() ) {
			$product_layout = konte_get_option( 'product_layout' );
			$classes[]      = 'product-' . $product_layout;

			if ( 'v3' == $product_layout ) {
				$classes[] = 'no-bottom-spacing';
			}

			if ( in_array( $product_layout, array( 'v2', 'v5' ) ) && konte_get_option( 'product_sticky_summary' ) ) {
				$classes[] = 'product-summary-sticky-' . konte_get_option( 'product_summary_sticky_mode' );
			}
		}

		if ( is_account_page() && ! is_user_logged_in() ) {
			$classes[] = 'woocommerce-account-login';
		}

		if ( konte_is_order_tracking_page() ) {
			$classes[] = 'woocommerce-order-tracking';
		}

		if ( is_shop() || is_product_taxonomy() ) {
			$classes[] = 'woocommerce-archive';
			$classes[] = 'shop-layout-' . konte_get_option( 'shop_layout' );

			if ( 'carousel' != konte_get_option( 'shop_layout' ) ) {
				$classes[] = 'woocommerce-nav-' . konte_get_option( 'shop_nav' );
			}

			$shop_page_header = konte_get_option( 'shop_page_header' );

			if ( 'standard' == $shop_page_header ) {
				$classes[] = 'no-top-spacing';
			} elseif ( 'minimal' == $shop_page_header && 'fluid' == konte_get_option( 'shop_page_header_container' ) ) {
				$classes[] = 'woocommerce-header--minimal-fluid';
			}
		}

		if ( is_shop() ) {
			$shop_page_id = wc_get_page_id( 'shop' );

			if ( 'none' == get_post_meta( $shop_page_id, 'top_spacing', true ) || 'transparent' == get_post_meta( $shop_page_id, 'header_background', true ) ) {
				$classes[] = 'no-top-spacing';
			}

			if ( 'none' == get_post_meta( $shop_page_id, 'bottom_spacing', true ) || 'transparent' == get_post_meta( $shop_page_id, 'footer_background', true ) ) {
				$classes[] = 'no-bottom-spacing';
			}
		}

		if ( is_checkout() ) {
			$classes[] = 'woocommerce-checkout-' . konte_get_option( 'checkout_layout' );
		}

		if ( konte_get_option( 'mobile_shop_product_buttons' ) ) {
			$classes[] = 'mobile-shop-buttons';
		}

		$classes[] = 'mobile-shop-columns-' . absint( konte_get_option( 'mobile_shop_columns' ) );

		return $classes;
	}

	/**
	 * Change the header layout based on display settings.
	 *
	 * @param string $header_layout
	 * @return string
	 */
	public static function header_layout( $header_layout ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$shop_page_id  = wc_get_page_id( 'shop' );
			$layout        = $shop_page_id ? get_post_meta( $shop_page_id, 'header_layout', true ) : '';
			$header_layout = $layout ? $layout : $header_layout;
		}

		return $header_layout;
	}

	/**
	 * Change the content container class for product and shop pages.
	 *
	 * @param string $class Container class.
	 *
	 * @return string
	 */
	public static function content_container_class( $class ) {
		if ( is_product() ) {
			$class = 'product-content-container konte-container';

			if ( 'v3' == konte_get_option( 'product_layout' ) ) {
				$class = 'product-content-container konte-container-fluid';
			}
		} elseif ( is_shop() || is_product_taxonomy() ) {
			$class = 'shop-content-container konte-container';
		}

		return $class;
	}

	/**
	 * Before Content.
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 */
	public static function wrapper_before() {
		?>
		<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
	}

	/**
	 * After Content.
	 * Closes the wrapping divs.
	 */
	public static function wrapper_after() {
		?>
		</main><!-- #main -->
		</div><!-- #primary -->
		<?php
	}

	/**
	 * Sale badge.
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public static function sale_flash( $output, $post, $product ) {
		if ( ! konte_get_option( 'shop_badge_sale' ) || 'grouped' == $product->get_type() ) {
			return '';
		}

		$type       = konte_get_option( 'shop_badge_sale_type' );
		$text       = konte_get_option( 'shop_badge_sale_text' );
		$percentage = 0;
		$saved      = 0;

		if ( 'percent' == $type || false !== strpos( $text, '{%}' ) || false !== strpos( $text, '{$}' ) ) {
			if ( $product->get_type() == 'variable' ) {
				$available_variations = $product->get_available_variations();
				$max_percentage       = 0;
				$max_saved            = 0;
				$total_variations     = count( $available_variations );

				for ( $i = 0; $i < $total_variations; $i++ ) {
					$variation_id        = $available_variations[ $i ]['variation_id'];
					$variable_product    = new WC_Product_Variation( $variation_id );
					$regular_price       = floatval( $variable_product->get_regular_price() );
					$sales_price         = floatval( $variable_product->get_sale_price() );
					$variable_saved      = $regular_price && $sales_price ? ( $regular_price - $sales_price ) : 0;
					$variable_percentage = $regular_price && $sales_price ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ) : 0;

					if ( $variable_saved > $max_saved ) {
						$max_saved = $variable_saved;
					}

					if ( $variable_percentage > $max_percentage ) {
						$max_percentage = $variable_percentage;
					}
				}

				$saved      = $max_saved ? $max_saved : $saved;
				$percentage = $max_percentage ? $max_percentage : $percentage;
			} elseif ( $product->get_regular_price() != 0 ) {
				$regular_price = floatval( $product->get_regular_price() );
				$sales_price   = floatval( $product->get_sale_price() );
				$saved         = $regular_price - $sales_price;
				$percentage    = round( ( $saved / $regular_price ) * 100 );
			}
		}

		if ( 'percent' == $type ) {
			$output = '<span class="onsale woocommerce-badge"><span>' . $percentage . '%</span></span>';
		} else {
			$text = str_replace( '{%}', $percentage . '%', $text );
			$text = str_replace( '{$}', wc_price( $saved ), $text );
			$output = '<span class="onsale woocommerce-badge"><span>' . wp_kses_post( $text ) . '</span></span>';
		}

		return $output;
	}

	/**
	 * Star rating HTML.
	 *
	 * @param string $html   Star rating HTML.
	 * @param int    $rating Rating value.
	 * @param int    $count  Rated count.
	 *
	 * @return string
	 */
	public static function star_rating_html( $html, $rating, $count ) {
		$html = '<span class="max-rating rating-stars">
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
				</span>';
		$html .= '<span class="user-rating rating-stars" style="width:' . ( ( $rating / 5 ) * 100 ) . '%">
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
					<span class="svg-icon star-icon"><svg><use xlink:href="#star"></use></svg></span>
				</span>';

		$html .= '<span class="screen-reader-text">';

		if ( 0 < $count ) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'konte' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
		} else {
			/* translators: %s: rating */
			$html .= sprintf( esc_html__( 'Rated %s out of 5', 'konte' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
		}

		$html .= '</span>';

		return $html;
	}

	/**
	 * Change the order of onsale products' price.
	 *
	 * @param string $price         The price HTML.
	 * @param float  $regular_price The regular price.
	 * @param float  $sale_price    The sale price.
	 *
	 * @return string
	 */
	public static function sale_price_html( $price, $regular_price, $sale_price ) {
		$price = '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins> <del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';

		return $price;
	}

	/**
	 * Edit add to wihslist button.
	 *
	 * @param string $button The wishlist button HTML.
	 * @param array  $args   Wishlish button argurments.
	 *
	 * @return string
	 */
	public static function wishlist_button( $button, $args ) {
		_deprecated_function(
			'Konte_WooCommerce_Template::wishlist_button',
			'2.2.5',
			'Konte_WooCommerce_Template_Wishlist::soo_wishlist_button'
		);

		if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			return Konte_WooCommerce_Template_Wishlist::soo_wishlist_button( $button, $args );
		}
	}

	/**
	 * Changes breadcrumb args.
	 *
	 * @param array $args The breadcrumb argurments.
	 *
	 * @return array
	 */
	public static function breadcrumb_args( $args ) {
		$args['delimiter']   = konte_svg_icon( 'icon=arrow-breadcrumb&echo=0&class=delimiter' );
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb breadcrumbs">';
		$args['wrap_after']  = '</nav>';

		return $args;
	}

	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 *
	 * @return array Fragments to refresh via AJAX.
	 */
	public static function cart_link_fragment( $fragments ) {
		$fragments['span.cart-counter']       = '<span class="counter cart-counter">' . intval( WC()->cart->get_cart_contents_count() ) . '</span>';
		$fragments['span.cart-panel-counter'] = '<span class="cart-panel-counter">(' . intval( WC()->cart->get_cart_contents_count() ) . ')</span>';

		return $fragments;
	}

	/**
	 * Adds class 'hidden' to the product quantity if the product is sole individually.
	 *
	 * @param  array $classes  Quantity classes
	 * @param  bool $readonly  If the input should be set to readonly mode.
	 * @param  string $type    The input type attribute.
	 *
	 * @return array
	 */
	public static function quantity_class( $classes, $readonly, $type ) {
		if ( $readonly || 'hidden' == $type ) {
			$classes[] = 'hidden';
		}

		return $classes;
	}

	/**
	 * Filter function to edit quantity arguments.
	 * Remove (empty) 'product_name' to fixed the input label to 'Quantity'.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function quantity_input_args( $args ) {
		$args['product_name'] = '';

		return $args;
	}

	/**
	 * Filter function to search for products
	 *
	 * @param  array  $result Array of product HTML. It is in format '<li class="product">...</li>.
	 * @param  string $term  Search keyword.
	 * @return array
	 */
	public static function header_search_products( $result, $term ) {
		$shortcode = new WC_Shortcode_Products( array(
			'limit' => 6,
		) );

		$query_args = $shortcode->get_query_args();

		$query_args['s']       = $term;
		$query_args['orderby'] = 'relevance';
		$query_args['order']   = 'DESC';

		// Set the query vars to support the filter funciton
		// of searching by SKU (in the Addons plugin).
		set_query_var( 's', $term );
		set_query_var( 'post_type', 'product' );

		$query = new WP_Query( $query_args );

		foreach ( $query->posts as $post_id ) {
			$_product = wc_get_product( $post_id );

			if ( ! $_product->is_visible() ) {
				continue;
			}

			$result[] =
				'<li class="product">
					<a href="' . esc_url( $_product->get_permalink() ) . '">' .
						$_product->get_image( 'thumbnail' ) . '
						<span class="post-title product-title">' . $_product->get_name() . '</span>
						<span class="price">' . $_product->get_price_html() . '</span>
					</a>
				</li>';
		}

		return $result;
	}

	/**
	 * Render the search result item list for the header ajax search.
	 *
	 * @param  string $html
	 * @return string
	 */
	public static function header_search_product_item( $html ) {
		$_product = wc_get_product();

		if ( ! $_product->is_visible() ) {
			return $html;
		}

		return '<li class="product">
					<a href="' . esc_url( $_product->get_permalink() ) . '">' .
						$_product->get_image( 'thumbnail' ) . '
						<span class="post-title product-title">' . $_product->get_name() . '</span>
						<span class="price">' . $_product->get_price_html() . '</span>
					</a>
				</li>';
	}
}

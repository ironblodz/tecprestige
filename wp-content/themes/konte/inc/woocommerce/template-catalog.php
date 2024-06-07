<?php
/**
 * Hooks of shop catalog.
 *
 * @package Konte
 */

class Konte_WooCommerce_Template_Catalog {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Parse query for shop columns.
		add_action( 'parse_request', array( __CLASS__, 'parse_request' ) );

		// Set as is_filtered with the theme's filter.
		add_filter( 'woocommerce_is_filtered', array( __CLASS__, 'is_filtered' ) );

		// Get products by group.
		add_action( 'pre_get_posts', array( __CLASS__, 'products_group_query' ) );

		// Disable redirect to product page while having only one search result
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

		// Change products columns.
		add_filter( 'loop_shop_columns', array( __CLASS__, 'columns' ) );

		// Change products per page.
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'products_per_page' ), 20 );

		// Change header background.
		add_filter( 'konte_header_class', array( __CLASS__, 'header_class' ), 20 );
		add_filter( 'konte_footer_class', array( __CLASS__, 'footer_class' ), 20 );

		// Add more class to loop start.
		add_filter( 'woocommerce_product_loop_start', array( __CLASS__, 'loop_start' ), 5 );
		add_filter( 'woocommerce_product_loop_end', array( __CLASS__, 'loop_end' ) );

		// Move subcategories to a new ul. The original categories have been removed in Konte Addons plugin.
		add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'maybe_show_product_subcategories' ), 50 );

		// Remove wrapper link and replace by other links.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Wrap product into a container.
		add_action( 'woocommerce_before_shop_loop_item', array( __CLASS__, 'product_wrapper_open' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'product_wrapper_close' ), 1000 );

		// Replace the default sale flash.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( 'Konte_WooCommerce_Template_Product', 'badges' ), 5 );

		// Change product thumbnail markup.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'product_thumbnail' ) );

		// Group elemnents bellow product thumbnaisl.
		add_action( 'woocommerce_shop_loop_item_title', array( __CLASS__, 'summary_wrapper_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'summary_wrapper_close' ), 1000 );

		// Remove star rating.
		if ( ! konte_get_option( 'shop_product_stars' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}

		// Change the product title markup.
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( __CLASS__, 'product_title' ) );

		// Add a class to add to cart button.
		add_filter( 'woocommerce_loop_add_to_cart_args', array( __CLASS__, 'add_to_cart_button_args' ) );

		// Group buttons.
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'buttons' ) );
		add_action( 'konte_woocommerce_shop_loop_buttons', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'konte_woocommerce_shop_loop_buttons', array( __CLASS__, 'quick_view_button' ), 20 );


		// Add to wishlist button on hover simple layout.
		if ( 'simple' == konte_get_option( 'shop_product_hover' ) && class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			add_action( 'woocommerce_after_shop_loop_item', array( 'Konte_WooCommerce_Template_Wishlist', 'loop_button' ) );
		}

		// Pagination.
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
		add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'pagination' ) );
		add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'pagination_args' ) );

		// Quick view modal.
		add_action( 'wp_ajax_nopriv_konte_get_product_quickview', array( __CLASS__, 'quick_view' ) );
		add_action( 'wp_ajax_konte_get_product_quickview', array( __CLASS__, 'quick_view' ) );
		add_action( 'wc_ajax_product_quick_view', array( __CLASS__, 'quick_view' ) );

		add_action( 'konte_woocommerce_before_product_quickview_summary', array( 'Konte_WooCommerce_Template_Product', 'badges' ), 5 );
		add_action( 'konte_woocommerce_before_product_quickview_summary', 'woocommerce_show_product_images' );
		add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_title' );
		add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_rating', 30 );
		add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_price', 40 );
		add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_add_to_cart', 50 );
		add_action( 'konte_woocommerce_product_quickview_summary', array( __CLASS__, 'quickview_detail_link' ), 70 );

		if ( 'modal' == konte_get_option( 'product_quickview_style' ) ) {
			add_action( 'konte_after_site', array( __CLASS__, 'quick_view_modal' ) );
			add_action( 'konte_woocommerce_product_quickview_summary', 'woocommerce_template_single_meta', 60 );
			if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
				add_action( 'konte_woocommerce_after_product_quickview_summary', array( 'Konte_WooCommerce_Template_Wishlist', 'loop_button' ), 10 );
			}
			add_action( 'konte_woocommerce_after_product_quickview_summary', array( 'Konte_WooCommerce_Template_Product', 'product_share' ), 20 );
		} else {
			add_action( 'konte_after_site', array( __CLASS__, 'quick_view_panel' ) );
		}

		// Page header.
		remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description' );
		add_action( 'woocommerce_archive_description', array( __CLASS__, 'product_archive_description' ) );

		add_filter( 'konte_woocomerce_products_header_classes', array( __CLASS__, 'page_header_class' ) );

		if ( konte_get_option( 'shop_page_header' ) ) {
			add_action( 'konte_before_content_wrapper', array( __CLASS__, 'page_header' ) );
			add_action( 'woocommerce_archive_description', array( 'Konte_Breadcrumbs', 'breadcrumbs' ), 20 );

			// From version 8.6, WooCommerce has a new hook for the page header.
			// It should be removed to prevent duplications.
			remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );
		}

		// Place shop page content bellow page header.
		add_action( 'konte_before_content_wrapper', array( __CLASS__, 'shop_page_content' ), 20 );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		if ( konte_get_option( 'shop_toolbar' ) ) {
			add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'products_toolbar' ), 40 );
		}

		switch ( konte_get_option( 'shop_toolbar_layout' ) ) {
			case 'v1':
				add_action( 'konte_woocommerce_before_products_toolbar', array( __CLASS__, 'products_quick_search' ) );
				add_action( 'konte_woocommerce_products_toolbar', 'woocommerce_catalog_ordering' );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'columns_switcher' ), 20 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'result_count' ), 30 );
				break;

			case 'v2':
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'result_count' ), 10 );
				add_action( 'konte_woocommerce_products_toolbar', 'woocommerce_catalog_ordering', 20 );
				break;

			case 'v3':
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_filter' ), 10 );
				add_action( 'konte_woocommerce_products_toolbar', 'woocommerce_catalog_ordering', 20 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'result_count' ), 30 );
				break;

			case 'v4':
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_tabs' ), 10 );
				add_action( 'konte_woocommerce_products_toolbar', 'woocommerce_catalog_ordering', 10 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_filter' ), 20 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'columns_switcher' ), 30 );
				break;

			case 'v5':
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_tabs' ), 10 );
				add_action( 'konte_woocommerce_products_toolbar', 'woocommerce_catalog_ordering', 10 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_filter' ), 20 );
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'result_count' ), 30 );
				break;

			case 'v6':
				add_action( 'konte_woocommerce_products_toolbar', array( __CLASS__, 'products_quick_search' ) );
				break;
		}

		add_filter( 'woocommerce_catalog_orderby', array( __CLASS__, 'catalog_orderby' ) );
	}

	/**
	 * Parse request to change the shop columns
	 */
	public static function parse_request() {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( apply_filters( 'konte_allow_products_columns_cookie', true ) && isset( $_REQUEST['products_columns'] ) ) {
			wc_setcookie( 'products_columns', intval( $_REQUEST['products_columns'] ) );
		}
	}

	/**
	 * Filter function to correct the is_filtered function with filters of the theme.
	 */
	public static function is_filtered( $is_filtered ) {
		if ( isset( $_GET['filter'] ) || isset( $_POST['filter_time'] ) ) {
			$is_filtered = true;
		}

		return $is_filtered;
	}

	/**
	 * Change the main query to get products by group
	 *
	 * @param object $query
	 */
	public static function products_group_query( $query ) {
		if ( is_admin() || empty( $_GET['products_group'] ) || ! is_woocommerce() || ! $query->is_main_query() ) {
			return;
		}

		switch ( $_GET['products_group'] ) {
			case 'featured':
				$tax_query   = $query->get( 'tax_query' );
				$tax_query   = $tax_query ? $tax_query : WC()->query->get_tax_query();
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				$query->set( 'tax_query', $tax_query );
				break;

			case 'sale':
				$query->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
				break;

			case 'new':
				$query->set( 'post__in', array_merge( array( 0 ), konte_woocommerce_get_new_product_ids() ) );
				break;

			case 'best_sellers':
				$query->set( 'meta_key', 'total_sales' );
				$query->set( 'order', 'DESC' );
				$query->set( 'orderby', 'meta_value_num' );
				break;
		}
	}

	/**
	 * Change the shop columns.
	 *
	 * @param  int $columns The default columns.
	 *
	 * @return int
	 */
	public static function columns( $columns ) {
		if ( is_search() ) {
			if ( isset( $_POST['search_columns'] ) ) {
				$columns = intval( $_REQUEST['search_columns'] );
			}
		} else {
			if ( 'standard' == konte_get_option( 'shop_layout' ) ) {
				if ( ! empty( $_REQUEST['products_columns'] ) ) {
					$columns = intval( $_REQUEST['products_columns'] );
				} elseif ( ! empty( $_COOKIE['products_columns'] ) ) {
					$columns = intval( $_COOKIE['products_columns'] );
				}
			} else {
				$columns = 4; // Fixed for carousel and masonry.
			}
		}

		return $columns;
	}

	/**
	 * Change number of products per page.
	 *
	 * @param int $limit Number of products per page.
	 *
	 * @return int
	 */
	public static function products_per_page( $limit ) {
		if ( 'masonry' == konte_get_option( 'shop_layout' ) ) {
			$limit = 10;
		}

		return $limit;
	}

	/**
	 * Change header background class of the shop page.
	 *
	 * @param array $classes Header classes.
	 *
	 * @return array
	 */
	public static function header_class( $classes ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return $classes;
		}

		if ( is_shop() ) {
			$shop_page_id = wc_get_page_id( 'shop' );
			$background   = get_post_meta( $shop_page_id, 'header_background', true );

			if ( $background ) {
				$classes   = array_diff( $classes, array( 'dark', 'light', 'custom' ) );
				$classes[] = $background;
			}

			if ( 'custom' == $background || 'transparent' == $background ) {
				$text_color = get_post_meta( $shop_page_id, 'header_textcolor', true );
				$text_color = $text_color ? $text_color : '';
			} else {
				$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : '' );
			}

			if ( $text_color ) {
				$classes   = array_diff( $classes, array( 'text-dark', 'text-light' ) );
				$classes[] = 'text-' . $text_color;
			}
		}

		// Remove transparent background if there is no page header.
		if ( is_search() && 'standard' != konte_get_option( 'shop_page_header' ) && in_array( 'transparent', $classes ) ) {
			$background = konte_get_option( 'header_background' );
			$text_color = 'dark' == $background ? 'light': ( 'light' == $background ? 'dark': konte_get_option( 'header_text_color' ) );

			$classes    = array_diff( $classes, array( 'transparent', 'text-light', 'text-dark' ) );
			$classes[]  = $background;
			$classes[]  = 'text-' . $text_color;
		}

		if ( is_product_taxonomy() ) {
			$term_id    = get_queried_object_id();
			$text_color = get_term_meta( $term_id, 'header_textcolor', true );

			if ( $text_color ) {
				$classes   = array_diff( $classes, array( 'text-dark', 'text-light' ) );
				$classes[] = 'text-' . $text_color;
			}
		}

		return $classes;
	}

	/**
	 * Change the background class of footer on shop page.
	 *
	 * @param array $classes Footer classes.
	 *
	 * @return array
	 */
	public static function footer_class( $classes ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return $classes;
		}

		if ( is_shop() ) {
			$shop_page_id = wc_get_page_id( 'shop' );
			$background   = get_post_meta( $shop_page_id, 'footer_background', true );

			if ( $background ) {
				$classes   = array_diff( $classes, array( 'dark', 'light', 'custom' ) );
				$classes[] = $background;
			}

			if ( 'custom' == $background || 'transparent' == $background ) {
				$text_color = get_post_meta( $shop_page_id, 'footer_textcolor', true );
				$text_color = $text_color ? $text_color : '';
			} else {
				$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : '' );
			}

			if ( $text_color ) {
				$classes   = array_diff( $classes, array( 'text-dark', 'text-light' ) );
				$classes[] = 'text-' . $text_color;
			}
		}

		return $classes;
	}

	/**
	 * Loop start.
	 *
	 * @param string $html Open loop wrapper with the <ul class="products"> tag.
	 *
	 * @return string
	 */
	public static function loop_start( $html ) {
		$html    = '';
		$classes = array(
			'products',
			'hover-' . konte_get_option( 'shop_product_hover' ),
		);

		if ( is_main_query() && ( is_shop() || is_product_taxonomy() ) ) {
			$classes[] = 'main-products';

			if ( is_search() ) {
				$classes[] = 'layout-standard';
			} else {
				$classes[] = 'layout-' . konte_get_option( 'shop_layout' );
			}

			if ( 'standard' == konte_get_option( 'shop_layout' ) || is_search() ) {
				$classes[] = 'columns-' . wc_get_loop_prop( 'columns' );
			}

			if ( 'carousel' == konte_get_option( 'shop_layout' ) && ! is_search() ) {
				$html      .= '<div class="products-carousel swiper-container" dir="' . ( is_rtl() ? 'rtl' : 'ltr' ) . '" style="opacity: 0;">';
				$classes[] = 'swiper-wrapper';
			}
		} else {
			$classes[] = 'columns-' . wc_get_loop_prop( 'columns' );
		}

		$html .= '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		return $html;
	}

	/**
	 * Loop end.
	 *
	 * @param string $html The HTML of loop end.
	 *
	 * @return string
	 */
	public static function loop_end( $html ) {
		$html = '</ul>';

		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return $html;
		}

		if ( is_main_query() && 'carousel' == konte_get_option( 'shop_layout' ) && ! is_search() ) {
			$html .= '<div class="products-carousel-scrollbar swiper-scrollbar"></div>';
			$html .= '</div><!-- .products-carousel -->';
		}

		return $html;
	}

	/**
	 * Display product subcategories.
	 *
	 * @use woocommerce_maybe_show_product_subcategories
	 */
	public static function maybe_show_product_subcategories() {
		// Don't show on quick search result
		if ( ! empty( $_GET['product_tag'] ) || ! empty( $_GET['product_category'] ) ) {
			return;
		}

		if ( has_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' ) ) {
			return;
		}

		$subcats = woocommerce_maybe_show_product_subcategories();

		echo empty( $subcats ) ? '' : '<ul class="products product-categories">' . $subcats . '</ul>';
	}

	/**
	 * Open product wrapper.
	 */
	public static function product_wrapper_open() {
		echo '<div class="product-inner">';
	}

	/**
	 * Close product wrapper.
	 */
	public static function product_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Product thumbnail.
	 */
	public static function product_thumbnail() {
		global $product;

		switch ( konte_get_option( 'shop_product_hover' ) ) {
			case 'slider':
				$image_ids  = $product->get_gallery_image_ids();
				$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );

				if ( $image_ids ) {
					echo '<div class="product-thumbnail product-thumbnails--slider">';
				} else {
					echo '<div class="product-thumbnail">';
				}

				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();

				foreach ( $image_ids as $image_id ) {
					$src = wp_get_attachment_image_src( $image_id, $image_size );

					if ( ! $src ) {
						continue;
					}

					woocommerce_template_loop_product_link_open();

					printf(
						'<img data-lazy="%s" width="%s" height="%s" alt="%s" class="slick-loading">',
						esc_url( $src[0] ),
						esc_attr( $src[1] ),
						esc_attr( $src[2] ),
						esc_attr( $product->get_title() )
					);

					woocommerce_template_loop_product_link_close();
				}

				echo '</div>';
				break;

			case 'zoom';
				echo '<div class="product-thumbnail">';
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

				if ( $image ) {
					$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
					echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link product-thumbnail-zoom" data-zoom_image="' . $image[0] . '">';
				} else {
					woocommerce_template_loop_product_link_open();
				}
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();
				echo '</div>';
				break;

			case 'other_image':
				$image_ids = $product->get_gallery_image_ids();

				if ( ! empty( $image_ids ) ) {
					echo '<div class="product-thumbnail product-thumbnails--hover">';
				} else {
					echo '<div class="product-thumbnail">';
				}

				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();

				if ( ! empty( $image_ids ) ) {
					$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
					echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail hover-image' ) );
				}

				woocommerce_template_loop_product_link_close();
				echo '</div>';
				break;

			default:
				echo '<div class="product-thumbnail">';
				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();
				echo '</div>';
				break;
		}
	}

	/**
	 * Product title.
	 * Add link to the product title.
	 */
	public static function product_title() {
		$tag = apply_filters( 'konte_woocommerce_shop_loop_item_title_tag', 'h2' );

		printf( '<%s class="woocommerce-loop-product__title">', esc_attr( $tag ) );
		woocommerce_template_loop_product_link_open();
		the_title();
		woocommerce_template_loop_product_link_close();
		printf( '</%s>', $tag );
	}

	/**
	 * Add a custom class to add to cart button.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function add_to_cart_button_args( $args ) {
		$args['class'] .= ' woocommerce-loop-product__button';

		return $args;
	}

	/**
	 * Open product summary wrapper.
	 */
	public static function summary_wrapper_open() {
		echo '<div class="product-summary">';
	}

	/**
	 * Close product summary wrapper.
	 */
	public static function summary_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Product buttons. Includes add-to-cart, quick view and wishlist buttons.
	 */
	public static function buttons() {
		echo '<div class="buttons clearfix">';

		do_action( 'konte_woocommerce_shop_loop_buttons' );

		echo '</div>';
	}

	/**
	 * Products pagination.
	 */
	public static function pagination() {
		// Display the default pagination for [products] shortcode.
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			woocommerce_pagination();
			return;
		}

		$nav_type = konte_get_option( 'shop_nav' );

		// Fixed nav type for carousel.
		if ( 'carousel' == konte_get_option( 'shop_layout' ) ) {
			$nav_type = 'loadmore';
		}

		if ( 'numeric' == $nav_type ) {
			woocommerce_pagination();
		} elseif ( get_next_posts_link() ) {
			$classes = array(
				'woocommerce-navigation',
				'next-posts-navigation',
				'ajax-navigation',
				'ajax-' . $nav_type,
			);

			if ( 'carousel' == konte_get_option( 'shop_layout' ) ) {
				$classes[] = 'hidden';
			}

			echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			echo '<div class="nav-links">';
			next_posts_link( esc_html__( 'Load more', 'konte' ) );
			echo '</div>';
			echo '</nav>';
		}
	}

	/**
	 * WooCommerce pagination arguments.
	 *
	 * @param array $args The pagination args.
	 *
	 * @return array
	 */
	public static function pagination_args( $args ) {
		$args['prev_text'] = konte_svg_icon( 'icon=left&echo=0' ) . esc_html__( 'Prev', 'konte' );
		$args['next_text'] = esc_html__( 'Next', 'konte' ) . konte_svg_icon( 'icon=right&echo=0' );

		return $args;
	}

	/**
	 * Quick view button.
	 */
	public static function quick_view_button() {
		if ( ! konte_get_option( 'product_quickview' ) ) {
			return;
		}

		printf(
			'<a href="%s" class="quick_view_button quick-view-button button" data-toggle="%s" data-target="%s" data-product_id="%s" rel="nofollow">
				%s
			</a>',
			is_customize_preview() ? '#' :esc_url( get_permalink() ),
			'modal' == konte_get_option( 'product_quickview_style' ) ? 'modal' : 'off-canvas',
			'modal' == konte_get_option( 'product_quickview_style' ) ? 'quick-view-modal' : 'quick-view-panel',
			esc_attr( get_the_ID() ),
			konte_svg_icon( 'icon=eye&echo=0' )
		);
	}

	/**
	 * Quick view modal.
	 */
	public static function quick_view_modal() {
		if ( ! konte_get_option( 'product_quickview' ) ) {
			return;
		}
		?>

		<div id="quick-view-modal" class="quick-view-modal modal">
			<div class="backdrop"></div>
			<div class="modal-content container woocommerce">
				<div class="hamburger-menu button-close active">
					<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
					<div class="hamburger-box">
						<div class="hamburger-inner"></div>
					</div>
				</div>
				<div class="product"></div>
				<span class="modal-loader"><span class="spinner"></span></span>
			</div>
		</div>

		<?php
	}

	/**
	 * Quick view modal.
	 */
	public static function quick_view_panel() {
		if ( ! konte_get_option( 'product_quickview' ) ) {
			return;
		}
		?>

		<div id="quick-view-panel" class="quick-view-panel offscreen-panel">
			<div class="backdrop"></div>
			<div class="panel woocommerce">
				<div class="hamburger-menu button-close active">
					<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
					<div class="hamburger-box">
						<div class="hamburger-inner"></div>
					</div>
				</div>
				<div class="product"></div>
				<span class="panel-loader"><span class="spinner"></span></span>
			</div>
		</div>

		<?php

	}

	/**
	 * Product quick view template.
	 *
	 * @return string
	 */
	public static function quick_view() {
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'konte' ) );
			exit;
		}

		$post_object = get_post( $_POST['product_id'] );
		if ( ! $post_object || ! in_array( $post_object->post_type, array( 'product', 'product_variation', true ) ) ) {
			wp_send_json_error( esc_html__( 'Invalid product.', 'konte' ) );
			exit;
		}

		if ( 'modal' == konte_get_option( 'product_quickview_style' ) && konte_get_option( 'product_quickview_auto_background' ) ) {
			$background = get_post_meta( $_POST['product_id'], 'background_color', true );
		} else {
			$background = false;
		}

		ob_start();
		wc_get_template( 'content-product-quickview.php', array(
			'post_object'      => $post_object,
			'background_color' => $background,
		) );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * View product details link on the quick view modal.
	 */
	public static function quickview_detail_link() {
		if ( ! konte_get_option( 'product_quickview_detail_link' ) ) {
			return;
		}

		$text = konte_get_option( 'product_quickview_detail_link_text' );

		if ( empty( $text ) ) {
			return;
		}

		echo '<span class="view-product-link"><a href="' . esc_url( get_permalink() ) . '" rel="nofollow">' . esc_html( $text ) . '</a></span>';
	}

	/**
	 * Use subtitle as the shop page description.
	 *
	 * @see woocommerce_product_archive_description
	 */
	public static function product_archive_description() {
		if ( is_search() ) {
			return;
		}

		if ( is_post_type_archive( 'product' ) && absint( get_query_var( 'paged' ) ) <= 1 ) {
			$shop_page_id = wc_get_page_id( 'shop' );

			if ( $shop_page_id ) {
				$description = get_post_meta( $shop_page_id, '_subtitle', true );

				if ( $description ) {
					echo '<div class="page-description">' . $description . '</div>'; // WPCS: XSS ok.
				}
			}
		}
	}

	/**
	 * Products header.
	 * Copied from template "archive-product.php".
	 */
	public static function page_header() {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		$classes = array(
			'layout-' . konte_get_option( 'shop_page_header' ),
		);

		if ( 'standard' == konte_get_option( 'shop_page_header' ) ) {
			$classes[] = 'text-' . konte_get_option( 'shop_page_header_textcolor' );
		}

		$classes = apply_filters( 'konte_woocomerce_products_header_classes', $classes );
		$fluid   = ( 'minimal' == konte_get_option( 'shop_page_header' ) && 'fluid' == konte_get_option( 'shop_page_header_container' ) );
		?>

		<header class="woocommerce-products-header <?php echo esc_attr( implode( ' ', $classes ) ) ?>">
			<div class="woocommerce-products-header__container <?php echo true === $fluid ? 'konte-container-fluid' : 'konte-container'; ?>">
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
					<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>

				<?php
				/**
				 * Hook: woocommerce_archive_description.
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				do_action( 'woocommerce_archive_description' );
				?>
			</div>
		</header>

		<?php
	}

	/**
	 * Change the text color class on product taxonomy pages.
	 *
	 * @param array $classes The default classes.
	 *
	 * @return array
	 */
	public static function page_header_class( $classes ) {
		if ( 'standard' == konte_get_option( 'shop_page_header' ) && is_product_taxonomy() ) {
			$term_id    = get_queried_object_id();
			$text_color = get_term_meta( $term_id, 'page_header_textcolor', true );

			if ( $text_color ) {
				$classes   = array_diff( $classes, array( 'text-dark', 'text-light' ) );
				$classes[] = 'text-' . $text_color;
			}

			$classes[] = 'products-header-text-' . $text_color;
		}

		return $classes;
	}

	/**
	 * Display shop page content bellow the page header.
	 */
	public static function shop_page_content() {
		if ( ! is_shop() || is_search() || is_filtered() ) {
			return;
		}

		$shop_page = get_post( wc_get_page_id( 'shop' ) );

		if ( ! $shop_page ) {
			return;
		}

		if ( empty( $shop_page->post_content ) ) {
			return;
		}

		if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $shop_page->ID, '_elementor_edit_mode', true ) ) {
			setup_postdata( $GLOBALS['post'] =& $shop_page );

			$shop_page_content = apply_filters( 'the_content', $shop_page->post_content );

			wp_reset_postdata();
		} else {
			$shop_page_content = wc_format_content( $shop_page->post_content );
		}
		?>

		<section id="shop-page-content" class="shop-page-content">
			<div class="<?php echo esc_attr( apply_filters( 'konte_content_container_class', 'container' ) ); ?>">
				<?php echo ! empty( $shop_page_content ) ? $shop_page_content : ''; ?>
			</div>
		</section>

		<?php
	}

	/**
	 * Catalog products toolbar.
	 */
	public static function products_toolbar() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}

		$layout = konte_get_option( 'shop_toolbar_layout' );
		?>

		<div class="products-toolbar layout-<?php echo esc_attr( $layout ); ?>">
			<?php
			/**
			 * Hook: konte_woocommerce_before_products_toolbar
			 */
			do_action( 'konte_woocommerce_before_products_toolbar' );
			?>

			<div class="products-tools clearfix">
				<?php
				/**
				 * Hook: konte_woocommerce_products_toolbar
				 */
				do_action( 'konte_woocommerce_products_toolbar' );
				?>
			</div>

			<?php
			/**
			 * Hook: konte_woocommerce_after_products_toolbar
			 */
			do_action( 'konte_woocommerce_after_products_toolbar' );
			?>
		</div>

		<?php
	}

	/**
	 * Products quick search.
	 */
	public static function products_quick_search() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}
		?>

		<div class="products-quick-search">
			<form class="products-quick-search-form" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ) ?>" method="get">
				<?php
				$tags = wp_dropdown_categories( array(
					'taxonomy'        => 'product_tag',
					'show_option_all' => esc_attr__( 'everything', 'konte' ),
					'hide_empty'      => false,
					'echo'            => false,
					'value_field'     => 'slug',
					'name'            => 'product_tag',
					'hide_if_empty'   => true,
					'orderby'         => 'count',
					'order'           => 'DESC',
					'selected'        => get_query_var( 'product_tag' ),
				) );

				$categories = wp_dropdown_categories( array(
					'taxonomy'        => 'product_cat',
					'show_option_all' => esc_attr__( 'all categories', 'konte' ),
					'hide_empty'      => true,
					'echo'            => false,
					'value_field'     => 'slug',
					'name'            => 'product_cat',
					'hide_if_empty'   => true,
					'selected'        => get_query_var( 'product_cat' ),
					'hierarchical'    => true,
				) );

				printf(
					'<span class="products-quick-search__intro-text">%s</span> %s %s %s',
					esc_html__( "You're looking for", 'konte' ),
					$tags ? $tags : esc_html__( 'everything', 'konte' ),
					esc_html__( 'in', 'konte' ),
					$categories
				);
				?>

				<button type="submit" value="<?php esc_attr_e( 'search', 'konte' ) ?>" class="filter-submit"><?php esc_html_e( 'search', 'konte' ); ?></button>

				<?php
				foreach ( $_GET as $key => $value ) {
					if ( in_array( $key, array( 'product_tag', 'product_cat', 'filter' ) ) ) {
						continue;
					}

					printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $key ), esc_attr( $value ) );
				}
				?>

				<input type="hidden" name="filter" value="1">
			</form>
		</div>

		<?php
	}

	/**
	 * Products columns swither.
	 */
	public static function columns_switcher() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		if ( in_array( konte_get_option( 'shop_layout' ), array( 'masonry', 'carousel' ) ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['products_columns'] ) ) {
			$current = intval( $_REQUEST['products_columns'] );
		} elseif ( ! empty( $_COOKIE['products_columns'] ) ) {
			$current = intval( $_COOKIE['products_columns'] );
		} else {
			$current = wc_get_loop_prop( 'columns' );
		}

		$columns   = apply_filters( 'konte_columns_switcher_options', konte_get_option( 'shop_toolbar_columns' ) );
		$columns[] = $current;
		$columns   = array_unique( $columns );
		$columns   = array_filter( $columns );
		asort( $columns );

		if ( count( $columns ) <= 1 ) {
			return;
		}
		?>

		<p class="columns-switcher">
			<?php
			foreach ( $columns as $column ) {
				$tag   = $column == $current ? 'span' : 'a';
				$class = 'column-seletor underline-hover ' . ( $column == $current ? 'active' : '' );

				printf(
					'<%1$s href="%2$s" class="%3$s" %4$s>%5$s</%1$s>',
					$tag,
					esc_url( add_query_arg( array( 'products_columns' => $column ) ) ),
					$class,
					'a' == $tag ? 'rel="nofollow"' : '',
					$column
				);
			}
			?>
		</p>

		<?php
	}

	/**
	 * Output the result count text.
	 */
	public static function result_count() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}

		$total = wc_get_loop_prop( 'total' );
		?>

		<p class="woocommerce-result-count">
			<?php
			/* translators: %d: total results */
			printf( _n( '%d Product', '%d Products', $total, 'konte' ), $total );
			?>
		</p>

		<?php
	}

	/**
	 * Products filter toggle.
	 */
	public static function products_filter() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$open = konte_get_option( 'shop_toolbar_filter_open' );
		?>
		<p class="products-filter-toggle">
			<a href="#products-filter" class="toggle-filters" data-toggle="<?php echo esc_attr( $open ); ?>" data-target="products-filter">
				<?php konte_svg_icon( 'icon=filter' ) ?>
				<?php esc_html_e( 'Filter By', 'konte' ); ?>
			</a>
		</p>

		<?php if ( 'off-canvas' == $open ) : ?>

			<div id="products-filter" class="products-filter-sidebar products-filter offscreen-panel">
				<div class="backdrop"></div>
				<div class="panel">
					<div class="hamburger-menu button-close active">
						<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
						<div class="hamburger-box">
							<div class="hamburger-inner"></div>
						</div>
					</div>

					<div class="panel-header">
						<div class="panel__title"><?php esc_html_e( 'Filter By', 'konte' ) ?></div>
					</div>

					<div class="panel-content filter-widgets">
						<?php dynamic_sidebar( 'product-filter' ); ?>
					</div>
				</div>
			</div>

		<?php else : ?>

			<div id="products-filter" class="products-filter-sidebar products-filter dropdown-panel">
				<div class="konte-container products-filter-container">
					<div class="hamburger-menu button-close active">
						<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
						<div class="hamburger-box">
							<div class="hamburger-inner"></div>
						</div>
					</div>

					<div class="panel-header">
						<div class="panel__title"><?php esc_html_e( 'Filter By', 'konte' ) ?></div>
					</div>

					<div class="filter-widgets">
						<?php dynamic_sidebar( 'product-filter' ); ?>
					</div>
				</div>
			</div>

		<?php endif; ?>
		<?php
	}

	/**
	 * Update ordering options.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public static function catalog_orderby( $options ) {
		$options['menu_order'] = esc_attr__( 'Sort by', 'konte' );

		return $options;
	}

	/**
	 * Display products tabs.
	 */
	public static function products_tabs() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$type   = konte_get_option( 'shop_toolbar_tabs' );
		$tabs   = array();
		$active = false;

		if ( is_product_taxonomy() ) {
			$queried  = get_queried_object();
			$base_url = get_term_link( $queried );
		} else {
			$base_url = wc_get_page_permalink( 'shop' );
		}

		if ( 'category' == $type || 'tag' == $type ) {
			$terms    = array();
			$taxonomy = 'category' == $type ? 'product_cat' : 'product_tag';
			$option   = 'category' == $type ? 'shop_toolbar_tabs_categories' : 'shop_toolbar_tabs_tags';

			$slugs = trim( konte_get_option( $option ) );

			// Get terms.
			if ( is_tax( $taxonomy ) && konte_get_option( 'shop_toolbar_tabs_subcategories' ) ) {
				$queried = $queried ? $queried : get_queried_object();

				$args = array(
					'taxonomy' => $taxonomy,
					'parent'   => $queried->term_id,
				);

				if ( is_numeric( $slugs ) ) {
					$args['orderby'] = 'count';
					$args['order']   = 'DESC';
					$args['number']  = intval( $slugs );
				}

				$terms = get_terms( $args );
			}

			// Keep get default tabs if there is no sub-categorys.
			if ( empty( $terms ) ) {
				if ( is_numeric( $slugs ) ) {
					$terms = get_terms( array(
						'taxonomy' => $taxonomy,
						'orderby'  => 'count',
						'order'    => 'DESC',
						'number'   => intval( $slugs ),
					) );
				} elseif ( ! empty( $slugs ) ) {
					$slugs = explode( ',', $slugs );

					if ( empty( $slugs ) ) {
						return;
					}

					$terms = get_terms( array(
						'taxonomy' => $taxonomy,
						'orderby' => 'slug__in',
						'slug' => $slugs,
					) );
				} else {
					$terms = get_terms( array(
						'taxonomy' => $taxonomy,
						'orderby'  => 'count',
						'order'    => 'DESC',
						'parent'   => 0,
					) );
				}
			}

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return;
			}

			foreach ( $terms as $term ) {
				if ( is_tax( $taxonomy, $term->slug ) ) {
					$active = true;
				}

				$tabs[] = sprintf(
					'<a href="%s" class="tab-%s underline-hover %s">%s</a>',
					esc_url( get_term_link( $term ) ),
					esc_attr( $term->slug ),
					is_tax( $taxonomy, $term->slug ) ? 'active' : '',
					esc_html( $term->name )
				);
			}
		} else {
			$groups = (array) konte_get_option( 'shop_toolbar_tabs_groups' );

			if ( empty( $groups ) ) {
				return;
			}

			$labels = array(
				'best_sellers' => esc_html__( 'Best Sellers', 'konte' ),
				'featured'     => esc_html__( 'Hot Products', 'konte' ),
				'new'          => esc_html__( 'New Products', 'konte' ),
				'sale'         => esc_html__( 'Sale Products', 'konte' ),
			);

			foreach ( $groups as $group ) {
				if ( isset( $_GET['products_group'] ) && $group == $_GET['products_group'] ) {
					$active = true;
				}

				$tabs[] = sprintf(
					'<a href="%s" class="tab-%s underline-hover %s">%s</a>',
					esc_url( add_query_arg( array( 'products_group' => $group ), $base_url ) ),
					esc_attr( $group ),
					isset( $_GET['products_group'] ) && $group == $_GET['products_group'] ? 'active' : '',
					$labels[ $group ]
				);
			}
		}

		if ( empty( $tabs ) ) {
			return;
		}

		array_unshift( $tabs, sprintf(
			'<a href="%s" class="tab-all underline-hover %s">%s</a></li>',
			'group' == $type ? esc_url( $base_url ) : esc_url( wc_get_page_permalink( 'shop' ) ),
			$active ? '' : 'active',
			esc_html__( 'All Products', 'konte' )
		) );

		echo '<p class="products-tabs">';

		foreach ( $tabs as $tab ) {
			echo trim( $tab );
		}

		echo '</p>';
	}
}

<?php
/**
 * Hooks of single product.
 *
 * @package Konte
 */

/**
 * Class of single product template.
 */
class Konte_WooCommerce_Template_Product {
	/**
	 * Product description tab data.
	 */
	static $description_tab;

	/**
	 * General data of a product that used by the theme.
	 *
	 * @var array
	 */
	static $data = array();

	/**
	 * Initialize.
	 */
	public static function init() { 
		// Change header background.
		add_filter( 'konte_header_class', array( __CLASS__, 'header_class' ), 20 );
		add_filter( 'konte_footer_class', array( __CLASS__, 'footer_class' ), 20 );

		// Adds a class of product layout to product page.
		add_filter( 'post_class', array( __CLASS__, 'product_class' ), 10, 3 );

		// Replace the default sale flash.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
		add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'badges' ), 10 );

		// Re-order the description.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 7 );

		// Remove product tab heading.
		add_filter( 'woocommerce_product_description_heading', '__return_null' );
		add_filter( 'woocommerce_product_reviews_heading', '__return_null' );
		add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

		// Adds a class of the lightbox to product gallery.
		add_filter( 'woocommerce_single_product_image_gallery_classes', array( __CLASS__, 'gallery_classes' ) );

		// Change lightbox options.
		add_filter( 'woocommerce_single_product_photoswipe_options', array( __CLASS__, 'photoswipe_options' ) );

		// Remore reviewer's avatar.
		remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar' );

		// Change the order of grouped product columns.
		add_filter( 'woocommerce_grouped_product_columns', array( __CLASS__, 'grouped_product_columns' ) );

		// Change related products args.
		add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'related_products_args' ) );

		// Ajax add to cart.
		if ( konte_get_option( 'product_ajax_addtocart' ) ) {
			add_action( 'wc_ajax_konte_ajax_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
			add_action( 'wc_ajax_nopriv_konte_ajax_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		}

		// Sticky add to cart.
		if ( konte_get_option( 'product_sticky_addtocart' ) && 'v3' != konte_get_option( 'product_layout' ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'sticky_add_to_cart' ), 99 );
		}

		// Handle product page layout.
		switch ( konte_get_option( 'product_layout' ) ) {
			case 'v1':
				// Wrap gallery and summary in a container.
				add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'open_gallery_summary_wrapper' ), 19 );
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'close_gallery_summary_wrapper' ), 1 );

				// Place breadcrumb into product toolbar then place product toolbar before product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'product_toolbar' ), 5 );

				// Change product image carousel options.
				add_filter( 'woocommerce_single_product_carousel_options', array( __CLASS__, 'product_carousel_options' ) );

				// Product sharing.
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'product_share' ), 15 );

				// Move product tabs into the summary.
				//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_data_tabs' ), 100 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'reorder_bundle_product_form' ), 90 );
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v2':
				// Change the gallery thumbnail size.
				add_filter( 'woocommerce_gallery_image_size', array( __CLASS__, 'gallery_image_size_large' ) );

				// Place breadcrumb into product toolbar then place product toolbar inside product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar' ), 2 );

				// Product sharing.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );

				// Move product tabs into the summary.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_data_tabs' ), 100 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'reorder_bundle_product_form' ), 90 );
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v3':
				// Place breadcrumb into product toolbar then place product toolbar before product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				if ( is_product() ) {
					add_action( 'woocommerce_before_main_content', array( __CLASS__, 'product_toolbar' ), 20 );
				}

				// Change position of product ribbons.
				remove_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'badges' ), 10 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_title_with_badges' ), 5 );

				// Move product tabs into the summary.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_data_tabs' ), 100 );

				// Move add to cart form to another container.
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'single_add_to_cart' ), 7 );

				// Change product image carousel options.
				add_filter( 'woocommerce_single_product_carousel_options', array( __CLASS__, 'product_carousel_options' ) );

				// Re-order the stars rating.
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );

				// Product sharing.
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'product_share' ), 15 );

				// Remove related products.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					remove_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', -1000 );
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v4':
				// Place breadcrumb into product toolbar then place product toolbar inside product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar' ), 2 );

				// Product sharing.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );

				// Move product tabs into the summary.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_data_tabs' ), 900 );

				// Place product description outside tabs.
				add_filter( 'woocommerce_product_tabs', array( __CLASS__, 'product_v4_tabs' ), 100 );
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'product_v4_description' ), 25 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Quantity style.
				if ( 'dropdown' == konte_get_option( 'product_v4_quantity_input_style' ) ) {
					add_filter( 'konte_woocommerce_quantity_class', array( __CLASS__, 'quantity_dropdown_class' ) );
				}

				if ( class_exists( 'WC_Bundles' ) ) {
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v5':
				// Wrap gallery and summary in a container.
				add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'open_gallery_summary_wrapper' ), 19 );
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'close_gallery_summary_wrapper' ), 1 );

				// Wrap product summary.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_summary_inner_open' ), 0 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_summary_inner_close' ), 1000 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_summary_inner_close' ), 1002 );

				// Move product tabs into the summary.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_data_tabs' ), 1001 );

				// Place breadcrumb into product toolbar then place product toolbar inside product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar' ), 2 );

				// Change position of product ribbons.
				remove_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'badges' ), 10 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'badges' ), 4 );

				// Change the gallery thumbnail size.
				add_filter( 'woocommerce_gallery_image_size', array( __CLASS__, 'gallery_image_size_large' ) );

				// Product sharing.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'reorder_bundle_product_form' ), 90 );
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v6':
				return;
				// Place breadcrumb into product toolbar then place product toolbar inside product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar' ), 2 );

				// Product sharing.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

			case 'v7':
				// Place breadcrumb into product toolbar then place product toolbar inside product summary.
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar' ), 2 );

				// Add a section for related products on the right side.
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'side_products' ), 5 );

				// Product sharing.
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );

				// Place related products outside product container.
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

				add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
				add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

				// Support bundle products.
				if ( class_exists( 'WC_Bundles' ) ) {
					remove_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', -1000 );
					add_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', 7 );
					add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
				}
				break;

				case 'vt':

					remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

					//remove product meta
					//remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

					//toolbar
					add_action( 'woocommerce_before_main_content', array( __CLASS__, 'product_toolbar_tecprestige' ), 900 );
						
					/** FODA-SE FINALMENTE WP DE MERDA */
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
					add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'productCustomTecPrestige' ), 900 );
					/** */
						

					// Place breadcrumb into product toolbar then place product toolbar inside product summary.
					
					// product title
					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_title_with_badges_tec_prestige' ), 5 );

					//add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_toolbar_tecprestige' ), 2 );
				

					//wwp button
					add_action( 'woocommerce_after_add_to_cart_button', 'custom_wpp_button', 15 );
	
					function custom_wpp_button() {
						global $product;
					
						// Link para WhatsApp
						$whatsapp_number = '+351 932639539';
						$product_name = $product->get_name();
						$whatsapp_message = urlencode("Olá, estou interessado no produto: " . $product_name);
						$whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . $whatsapp_message;
					
						// HTML do botão
						echo '<a href="' . esc_url( $whatsapp_url ) . '" class="button whatsapp-button" target="_blank">
						<small>APOIO COMERCIAL - ONLINE</small>
						<p>PRECISA DE AJUDA?</p>
						</a>';
					}

					// Product sharing.
					//add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'product_share' ), 35 );
	

					// Remover a descrição padrão do produto
					remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);


					// Place related products outside product container.
					//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	
					add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
					
					
						// Adicionar a descrição customizada
						//add_action('woocommerce_after_single_product_summary', 'custom_tp_description', 70);
						function custom_tp_description() {
						global $product;

						// Obter os atributos do produto
						$attributes = $product->get_attributes();
					
						echo '<div class="custom-two-columns" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 40px;">';
					
						// Coluna da esquerda - Descrição personalizada
						echo '<div class="minha-descricao-customizada" style="flex: 1; min-width: 300px;">';
						echo '<h2>Descrição Personalizada</h2>';
						echo '<p>Esta é a minha descrição personalizada para este produto.</p>';
						echo '</div>';
					
						// Coluna da direita - Atributos do produto
						echo '<div class="produto-atributos" style="flex: 1; min-width: 300px;">';
						echo '<h2>Atributos do Produto</h2>';
						if ( ! empty( $attributes ) ) {
							foreach ( $attributes as $attribute ) {
								if ( $attribute->get_variation() ) {
									continue;
								}
								$name = wc_attribute_label( $attribute->get_name() );
								$value = wc_display_product_attribute( $attribute );
								echo '<p><strong>' . $name . ':</strong> ' . $value . '</p>';
							}
						} else {
							echo '<p>Este produto não possui atributos adicionais.</p>';
						}
						echo '</div>';
					
						echo '</div>';
					}

					add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
	
					// Support bundle products.
					if ( class_exists( 'WC_Bundles' ) ) {
						add_filter( 'woocommerce_bundled_items_grid_layout_columns', array( __CLASS__, 'bundled_product_grid_columns' ) );
					}
					break;
					
		}
	}

	/**
	 * Make header transparent on product page if selected product layout is Version 1.
	 *
	 * @param array $classes Header classes.
	 *
	 * @return array
	 */
	public static function header_class( $classes ) {
		if ( ! is_product() ) {
			return $classes;
		}

		if ( in_array( konte_get_option( 'product_layout' ), array( 'v1', 'v3', 'v5' ) ) ) {
			$classes = array_diff( $classes, array( 'dark', 'light', 'custom', 'text-dark', 'text-light' ) );

			$classes[] = 'transparent';
			$classes[] = 'text-dark';
		} elseif ( in_array( 'transparent', $classes ) ) {
			$classes = array_diff( $classes, array( 'dark', 'transparent', 'custom', 'text-dark', 'text-light' ) );
			$classes[] = 'light';
			$classes[] = 'text-dark';
		}

		return $classes;
	}

	/**
	 * Make footer be transparent on product page v3
	 *
	 * @param array $classes Footer classes.
	 *
	 * @return array
	 */
	public static function footer_class( $classes ) {
		if ( ! is_product() ) {
			return $classes;
		}

		if ( 'v3' == konte_get_option( 'product_layout' ) ) {
			$classes = array_diff( $classes, array( 'dark', 'light', 'custom', 'text-dark', 'text-light' ) );

			$classes[] = 'transparent';
			$classes[] = 'text-dark';
		}

		return $classes;
	}

	/**
	 * Adds classes to products
	 *
	 * @param array  $classes Post classes.
	 * @param string $class   Post class.
	 * @param string $post_id Post ID.
	 *
	 * @return array
	 */
	public static function product_class( $classes, $class = '', $post_id = '' ) {
		global $product;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		$post_type = get_post_type( $post_id );

		// Ignore other post types.
		if ( ! in_array( $post_type, array( 'product', 'product_variation' ) ) ) {
			return $classes;
		}

		// Only adding classes to single product page.
		if ( ! is_single( $post_id ) || 'product' != $post_type ) {
			return $classes;
		}

		$classes[] = 'layout-' . konte_get_option( 'product_layout' );
		$classes[] = 'clearfix';

		if ( in_array( konte_get_option( 'product_layout' ), array( 'v1', 'v3' ) ) && get_post_meta( $post_id, 'background_color', true ) ) {
			$classes[] = 'background-set';
		}

		if ( ! $product->get_gallery_image_ids() ) {
			$classes[] = 'empty-gallery';
		}

		return $classes;
	}

	/**
	 * Product badges.
	 */
	public static function badges() {
		$badges = self::get_badges();

		if ( $badges ) {
			$shape = konte_get_option( 'shop_badge_shape' );
			printf( '<span class="woocommerce-badges woocommerce-badges--%s">%s</span>', esc_attr( $shape ), implode( '', $badges ) );
		}
	}

	/**
	 * Get product badges.
	 *
	 * @return array
	 */
	public static function get_badges() {
		global $product;

		$badges = array();

		if ( $product->is_on_sale() && konte_get_option( 'shop_badge_sale' ) ) {
			ob_start();
			woocommerce_show_product_sale_flash();
			$badges['sale'] = ob_get_clean();
		}

		if ( $product->is_featured() && konte_get_option( 'shop_badge_featured' ) ) {
			$text               = konte_get_option( 'shop_badge_featured_text' );
			$text               = empty( $text ) ? esc_html__( 'Hot', 'konte' ) : $text;
			$badges['featured'] = '<span class="featured woocommerce-badge"><span>' . esc_html( $text ) . '</span></span>';
		}

		if ( konte_get_option( 'shop_badge_new' ) && in_array( $product->get_id(), konte_woocommerce_get_new_product_ids() ) ) {
			$text          = konte_get_option( 'shop_badge_new_text' );
			$text          = empty( $text ) ? esc_html__( 'New', 'konte' ) : $text;
			$badges['new'] = '<span class="new woocommerce-badge"><span>' . esc_html( $text ) . '</span></span>';
		}

		if ( konte_get_option( 'shop_badge_soldout' ) && ! $product->is_in_stock() ) {
			$in_stock = false;

			// Double check if this is a variable product.
			if ( $product->is_type( 'variable' ) ) {
				$variations = $product->get_available_variations();

				foreach ( $variations as $variation ) {
					if( $variation['is_in_stock'] ) {
						$in_stock = true;
						break;
					}
				}
			}

			if ( ! $in_stock ) {
				$text               = konte_get_option( 'shop_badge_soldout_text' );
				$text               = empty( $text ) ? esc_html__( 'Sold Out', 'konte' ) : $text;
				$badges['sold-out'] = '<span class="sold-out woocommerce-badge"><span>' . esc_html( $text ) . '</span></span>';
			}
		}

		$badges = apply_filters( 'konte_product_badges', $badges, $product );
		ksort( $badges );

		return $badges;
	}

	/**
	 * Adds custom classes to product image gallery
	 *
	 * @param array $classes Gallery classes.
	 *
	 * @return array
	 */
	public static function gallery_classes( $classes ) {
		global $product;

		if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
			$classes[] = 'lightbox-support';
		}

		if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
			$classes[] = 'zoom-support';
		}

		$attachment_ids = $product->get_gallery_image_ids();

		if ( ! $attachment_ids ) {
			$classes[] = 'no-thumbnails';
		}

		$product_layout = konte_get_option( 'product_layout' );

		switch ( $product_layout ) {
			case 'v1':
				$classes[] = 'woocommerce-product-gallery--nav-numbers';
				break;

			case 'v3':
				$classes[] = 'woocommerce-product-gallery--nav-dots';
				break;

			case 'v4':
			case 'v6':
			case 'v7':
				$classes[] = 'woocommerce-product-gallery--nav-thumbnails';
				break;
		}

		$classes[] = 'woocommerce-product-gallery--mobile-nav-' . konte_get_option( 'mobile_product_gallery_layout' );

		return $classes;
	}

	/**
	 * Changes photoswipe options
	 *
	 * @param array $options Photoswipe script options.
	 *
	 * @return array
	 */
	public static function photoswipe_options( $options ) {
		$options['captionEl']             = false;
		$options['showHideOpacity']       = true;
		$options['showAnimationDuration'] = 400;
		$options['hideAnimationDuration'] = 400;

		return $options;
	}

	/**
	 * Re-order grouped product
	 *
	 * @param array $columns Columns of fields.
	 *
	 * @return array
	 */
	public static function grouped_product_columns( $columns ) {
		$key = array_search( 'label', $columns );

		if ( false !== $key ) {
			$label = $columns[ $key ];
			unset( $columns[ $key ] );
			array_unshift( $columns, $label );
		}

		return $columns;
	}

	/**
	 * Increase amount of related products to create carousel for it.
	 *
	 * @param array $args Related products query args.
	 *
	 * @return array
	 */
	public static function related_products_args( $args ) {
		$args['posts_per_page'] = $args['columns'] * 3;

		return $args;
	}

	/**
	 * Open gallery summary wrapper
	 */
	public static function open_gallery_summary_wrapper() {
		$container = '';

		if ( 'v1' == konte_get_option( 'product_layout' ) ) {
			$container = 'konte-container';
		}

		echo '<div class="product-gallery-summary clearfix ' . esc_attr( $container ) . '">';
	}

	/**
	 * Close gallery summary wrapper
	 */
	public static function close_gallery_summary_wrapper() {
		echo '</div>';
	}

	/**
	 * Displays product toolbar
	 */
	public static function product_toolbar_tecprestige() {
		$breadcrumb = konte_get_option( 'product_breadcrumb' );

		if ( ! $breadcrumb  ) {
			return;
		}
		
		?>
		<div class="tecprestige-product-toolbar product-toolbar clearfix">
			<?php
			if ( $breadcrumb ) {
				Konte_Breadcrumbs::breadcrumbs();
			}

			?>
		</div>
		<?php
	}
	

	/**
	 * Displays product toolbar
	 */
	public static function product_toolbar() {
		$breadcrumb = konte_get_option( 'product_breadcrumb' );
		$navigation = konte_get_option( 'product_navigation' );

		if ( ! $breadcrumb && ! $navigation  ) {
			return;
		}
		?>
		<div class="product-toolbar clearfix">
			<?php
			if ( $breadcrumb ) {
				Konte_Breadcrumbs::breadcrumbs();
			}

			if ( $navigation ) {
				$nav_args = array();

				if ( konte_get_option( 'product_navigation_same_cat' ) ) {
					$nav_args['in_same_term'] = true;
				}

				self::product_navigation( $nav_args );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Single product navigation
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	public static function product_navigation( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'in_same_term'       => false,
			'taxonomy'           => 'product_cat',
			'excluded_terms'     => '',
			'prev_text'          => esc_html__( 'Prev', 'konte' ),
			'next_text'          => esc_html__( 'Next', 'konte' ),
			'screen_reader_text' => esc_html__( 'Product navigation', 'konte' ),
		) );

		$next = new Konte_WooCommerce_Adjacent_Products( $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'] );
		$prev = new Konte_WooCommerce_Adjacent_Products( $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'], true );

		$next_product = $next->get_product();
		$prev_product = $prev->get_product();

		if ( ! $next_product && ! $prev_product ) {
			return;
		}
		?>
		<nav class="navigation post-navigation product-navigation" aria-label="<?php esc_attr_e( 'More products', 'konte' ) ?>">
			<?php if ( ! empty( $args['screen_reader_text'] ) ) :?>
				<span class="screen-reader-text"><?php echo esc_html( $args['screen_reader_text'] ) ?></span>
			<?php endif; ?>
			<div class="nav-links">
				<?php if ( $prev_product ) : ?>
					<div class="nav-previous">
						<a href="<?php echo esc_url( $prev_product->get_permalink() ); ?>" rel="prev">
							<?php echo esc_html( $args['prev_text'] ) ?>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( $next_product ) : ?>
					<div class="nav-next">
						<a href="<?php echo esc_url( $next_product->get_permalink() ); ?>" rel="next">
							<?php echo esc_html( $args['next_text'] ) ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</nav>
		<?php
	}

	/**
	 * Changes flex slider options
	 *
	 * @param array $options Gallery carousel options.
	 *
	 * @return array
	 */
	public static function product_carousel_options( $options ) {
		$options['animation']  = 'fade';
		$options['controlNav'] = 'thumbnails';

		return $options;
	}

	/**
	 * Changes flex slider options for product v5.
	 *
	 * @param array $options Gallery carousel options.
	 *
	 * @return array
	 */
	public static function product_v5_carousel_options( $options ) {
		_deprecated_function( __METHOD__, '2.3' );
		$options['controlNav'] = true;

		return $options;
	}

	/**
	 * Displays sharing buttons.
	 */
	public static function product_share() {
		if ( ! function_exists( 'konte_addons_share_link' ) ) {
			return;
		}

		if ( ! konte_get_option( 'product_sharing' ) ) {
			return;
		}

		$socials = konte_get_option( 'product_sharing_socials' );
		$whatsapp_number = konte_get_option( 'product_sharing_whatsapp_number' );

		if ( empty( $socials ) ) {
			return;
		}
		?>
		<div class="product-share share">
			<span class="svg-icon icon-socials sharing-icon">
				<svg><use href="#socials" xlink:href="#socials"></svg>
				<span><?php esc_html_e( 'Share', 'konte' ) ?></span>
			</span>
			<span class="socials">
				<?php
				foreach ( $socials as $social ) {
					echo konte_addons_share_link( $social, array( 'whatsapp_number' => $whatsapp_number ) );
				}
				?>
			</span>
		</div>
		<?php
	}

	public static function productCustomTecPrestige() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( get_the_ID() );
		}
		
		echo '</div>';
		echo '<div class="product-description-table">';
		echo '<div class="description-card">';
		echo '<div>';
		echo '<h3 class="product-desc-title tecprestige-product-description-title">Descrição</h3>';
		// Full Description
		$full_description = $product->get_description();

		echo '<div class="product-description tecprestige-product-description">' . wpautop($full_description) . '</div>';
	
		// Short Description
		$short_description = $product->get_short_description();
		echo '<div class="product-short-description">' . wpautop($short_description) . '</div>';
		echo '</div>';
		echo '</div>';


		echo '<div class="description-card">';
		echo '<div>';
		$attributes = $product->get_attributes();

		if ( $attributes ) {
			echo '<div class="product-attributes">';
			echo '<h3 class="product-desc-title">Informações adicionais</h3>';
			echo '<table>';

			foreach ( $attributes as $attribute ) {
				if ( $attribute->is_taxonomy() ) {
					
					// This is a taxonomy-based attribute
					$taxonomy = $attribute->get_taxonomy_object();
					
					$terms = wp_get_post_terms( $product->get_id(), $attribute->get_name(), 'all' );
					$tax_label = $taxonomy->attribute_name;
					
					echo '<tr>';
					echo '<th>' . esc_html( ucfirst($tax_label) ) . '</th>';
					
					foreach ( $terms as $term ) {
						echo '<td>' . esc_html( ucfirst($term->name) ) . '</td>';
					}
					echo '</tr>';
				} else {
					// This is a custom product attribute
					$name = $attribute->get_name();
					$value = $attribute->get_options();
					
					echo '<tr>';
					echo '<th>' . esc_html( ucfirst($name) ) . '</th>';
					echo '<td>' . esc_html(  ucfirst(implode( ', ',$value)) ) . '</td>';
					echo '</tr>';
				}
			}
			echo '</table>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
		/**
		 * Filter tabs and allow third parties to add their own.
		 *
		 * Each tab is an array containing title, callback and priority.
		 *
		 * @see woocommerce_default_product_tabs()
		 */

		 return;
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( ! empty( $tabs ) ) :
			?>

			<div class="woocommerce-tabs wc-tabs-wrapper panels-offscreen">
				<ul class="tabs wc-tabs" role="tablist">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="panels">
					<div class="backdrop"></div>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						
						<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
							<div class="hamburger-menu button-close active">
								<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>

								<div class="hamburger-box">
									<div class="hamburger-inner"></div>
								</div>
							</div>
							<div class="panel-header">
								<div class="panel__title"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></div>
							</div>
							<div class="panel-content">
								<?php if ( isset( $tab['callback'] ) ) {
									call_user_func( $tab['callback'], $key, $tab );
								} ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

		<?php
		endif;
	}

	/**
	 * Product data tabs.
	 */
	public static function product_data_tabs() {
		
		/**
		 * Filter tabs and allow third parties to add their own.
		 *
		 * Each tab is an array containing title, callback and priority.
		 *
		 * @see woocommerce_default_product_tabs()
		 */
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( ! empty( $tabs ) ) :
			?>

			<div class="woocommerce-tabs wc-tabs-wrapper panels-offscreen">
				<ul class="tabs wc-tabs" role="tablist">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="panels">
					<div class="backdrop"></div>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						
						<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
							<div class="hamburger-menu button-close active">
								<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>

								<div class="hamburger-box">
									<div class="hamburger-inner"></div>
								</div>
							</div>
							<div class="panel-header">
								<div class="panel__title"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></div>
							</div>
							<div class="panel-content">
								<?php if ( isset( $tab['callback'] ) ) {
									call_user_func( $tab['callback'], $key, $tab );
								} ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

		<?php
		endif;
	}

	/**
	 * Change the image size of product v2.
	 *
	 * @param string $size Image size name.
	 *
	 * @return string
	 */
	public static function gallery_image_size_large( $size ) {
		return 'woocommerce_single';
	}

	/**
	 * Remove add-to-wishlist button.
	 */
	public static function remove_wishlist_button() {
		_deprecated_function(
			'Konte_WooCommerce_Template_Product::remove_wishlist_button',
			'2.2.5',
			'Konte_WooCommerce_Template_Wishlist::remove_plugin_button'
		);

		if ( ! is_product() ) {
			return;
		}

		if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
			Konte_WooCommerce_Template_Wishlist::remove_plugin_button();
		}
	}

	/**
	 * Display product title with the badges.
	 */
	public static function product_title_with_badges() {
		echo '<h1 class="product_title entry-title">';
		the_title();
		echo implode( '', self::get_badges() );
		echo '</h1>';
	}

	public static function product_title_with_badges_tec_prestige() {
		echo '<h1 class="product_title entry-title">';
		the_title();
		echo implode( '', self::get_badges() );
		echo '</h1>';
	}

	/**
	 * Product cart form.
	 */
	public static function single_add_to_cart() {
		echo '<div class="woocommerce-product-cart">';
		woocommerce_template_single_add_to_cart();
		echo '</div>';
	}

	/**
	 * Remove description for product tabs on product layout v4
	 *
	 * @param array $tabs Product tabs.
	 *
	 * @return array
	 */
	public static function product_v4_tabs( $tabs ) {
		if ( ! isset( $tabs['description'] ) ) {
			return $tabs;
		}

		self::$description_tab = $tabs['description'];
		unset( $tabs['description'] );

		return $tabs;
	}

	/**
	 * Place product descritpion outside on product layout v4
	 */
	public static function product_v4_description() {
		if ( empty( self::$description_tab ) || empty( self::$description_tab['callback'] ) ) {
			return;
		}

		echo '<div class="woocommerce-product-details__description product-description">';
		call_user_func( self::$description_tab['callback'], 'description', self::$description_tab );
		echo '</div>';
	}

	/**
	 * Open product summary inner.
	 */
	public static function product_summary_inner_open() {
		echo '<div class="summary-inner product-summary-inner"><div class="product-summary">';
	}

	/**
	 * Close product summary inner.
	 */
	public static function product_summary_inner_close() {
		echo '</div>';
	}

	/**
	 * Product thumbnails for product layout v5.
	 */
	public static function product_v5_thumbnails() {
		$lightbox_class = konte_get_option( 'product_image_lightbox' ) ? 'lightbox-support' : '';

		echo '<div class="product-gallery-thumbnails ' . esc_attr( $lightbox_class ) . '">';
		woocommerce_show_product_thumbnails();
		echo '</div>';
	}

	/**
	 * Display side products on prduct page v7.
	 */
	public static function side_products() {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return;
		}

		global $product;

		$limit = konte_get_option( 'product_side_products_limit' );
		$type  = konte_get_option( 'product_side_products' );

		if ( 'related_products' == $type ) {
			$query = new stdClass();

			$related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $limit, $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
			$related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );

			$query->posts = $related_products;
		} elseif ( 'upsell_products' == $type ) {
			$query = new stdClass();

			$upsells = array_filter( array_map( 'wc_get_product', $product->get_upsell_ids() ), 'wc_products_array_filter_visible' );
			$upsells = wc_products_array_orderby( $upsells, 'rand', 'desc' );
			$upsells = array_slice( $upsells, 0, $limit );

			if ( ! empty( $upsells ) ) {
				$query->posts = $upsells;
			} else {
				$related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $limit, $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
				$related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );

				$query->posts = $related_products;
			}
		} else {
			$atts  = array(
				'per_page'     => intval( $limit ),
				'category'     => '',
				'cat_operator' => 'IN',
			);

			switch ( $type ) {
				case 'sale_products':
				case 'top_rated_products':
					$atts = array_merge( array(
						'orderby' => 'title',
						'order'   => 'ASC',
					), $atts );
					break;

				case 'recent_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
					), $atts );
					break;

				case 'featured_products':
					$atts = array_merge( array(
						'orderby' => 'date',
						'order'   => 'DESC',
						'orderby' => 'rand',
					), $atts );
					$atts['visibility'] = 'featured';
					break;
			}

			$args  = new WC_Shortcode_Products( $atts, $type );
			$args  = $args->get_query_args();
			$query = new WP_Query( $args );
		}

		echo '<div class="side-products">';
		echo '	<h2>' . esc_html( konte_get_option( 'product_side_products_title' ) ) . '</h2>';
		echo '	<ul class="products ' . esc_attr( str_replace( '_', '-', $type ) ) . '">';

		foreach ( $query->posts as $product_id ) {
			$_product = is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id;
			?>

			<li>
				<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
					<?php echo wp_kses_post( $_product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
					<span class="product-info">
						<span class="product-title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
						<span class="product-price"><?php echo wp_kses_post( $_product->get_price_html() ); ?></span>
					</span>
				</a>
			</li>

			<?php
		}

		wp_reset_postdata();

		echo '	</ul>';
		echo '</div>';
	}

	/**
	 * Add to wishlist button
	 */
	public static function add_to_wishlist_button() {
		_deprecated_function(
			'Konte_WooCommerce_Template_Product::add_to_wishlist_button',
			'2.2.5',
			'Konte_WooCommerce_Template_Wishlist::single_button'
		);

		if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {

			if ( is_single() && ! wc_get_loop_prop( 'loop' ) ) {
				Konte_WooCommerce_Template_Wishlist::single_button();
			} else {
				Konte_WooCommerce_Template_Wishlist::loop_button();
			}

		}
	}

	/**
	 * Reorder bundled product form
	 *
	 * @return void
	 */
	public static function reorder_bundle_product_form() {
		global $product;

		if ( ! function_exists( 'wc_pb_is_product_bundle' ) ) {
			return;
		}

		if ( ! wc_pb_is_product_bundle() ) {
			return;
		}

		if ( 'after_summary' != $product->get_add_to_cart_form_location() ) {
			return;
		}

		remove_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', -1000 );

		add_filter( 'woocommerce_product_get_add_to_cart_form_location', array( __CLASS__, 'bundled_product_form_location_default' ) );
		wc_pb_template_add_to_cart();
		add_filter( 'woocommerce_product_get_add_to_cart_form_location', array( __CLASS__, 'bundled_product_form_location_reset' ) );
	}

	/**
	 * Filter function to change the bundled product form location to 'default'
	 *
	 * @param  string $location
	 * @return string
	 */
	public static function bundled_product_form_location_default( $location ) {
		self::$data['bundled_form_location'] = $location;

		return 'default';
	}

	/**
	 * Reset the form location of the bundled product.
	 *
	 * @param  string $location
	 * @return string
	 */
	public static function bundled_product_form_location_reset( $location ) {
		if ( isset( self::$data['bundled_form_location'] ) ) {
			return self::$data['bundled_form_location'];
		}

		return $location;
	}

	/**
	 * Change the bundled product grid columns
	 *
	 * @return int
	 */
	public static function bundled_product_grid_columns() {
		global $product;

		$product_layout = konte_get_option( 'product_layout' );

		if ( in_array( $product_layout, array( 'v4', 'v6' ) ) ) {
			return 4;
		} elseif ( 'v7' == $product_layout && 'after_summary' == $product->get_add_to_cart_form_location() ) {
			return 3;
		}

		return 2;
	}

	/**
	 * Ajax add to cart
	 *
	 * @return void
	 */
	public static function ajax_add_to_cart() {
		// Disable redirecting.
		remove_all_filters( 'woocommerce_add_to_cart_redirect' );
		add_filter( 'pre_option_woocommerce_cart_redirect_after_add', '__return_null' );

		// Correct the 'add-to-cart' param.
		if ( isset( $_REQUEST['konte-add-to-cart'] ) ) {
			$_REQUEST['add-to-cart'] = $_REQUEST['konte-add-to-cart'];
			unset( $_REQUEST['konte-add-to-cart'] );
		}

		WC_Form_Handler::add_to_cart_action();

		// Support Facebook Pixel tracking.
		if ( class_exists( 'WC_Facebookcommerce_Pixel' ) ) {
			$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : $_REQUEST['add-to-cart'];
			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		}

		// Send the ajax response.
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'ajax_add_to_cart_fragments' ) );
		WC_AJAX::get_refreshed_fragments();
	}

	/**
	 * Add notices to ajax add to cart fragments
	 *
	 * @param array $fragments
	 * @return array
	 */
	public static function ajax_add_to_cart_fragments( $fragments ) {
		$fragments['notices_html'] = wc_print_notices( true );

		return $fragments;
	}

	/**
	 * Sticky cart form
	 *
	 * @return void
	 */
	public static function sticky_add_to_cart() {
		global $product;

		if ( 'bundle' === $product->get_type() ) {
			return;
		}

		wc_set_loop_prop( 'is_sticky_addtocart', true );

		$position = konte_get_option( 'product_sticky_addtocart' );
		?>
		<div class="sticky-cart-form sticky-cart-form--<?php echo esc_attr( $position ) ?>" data-position="<?php echo esc_attr( $position ) ?>" aria-hidden="true">
			<div class="konte-container sticky-cart-form__container">
				<div class="sticky-cart-form__product-summary">
					<?php
					$post_thumbnail_id = $product->get_image_id();
					$thumbnail_size    = apply_filters( 'konte_sticky_add_to_cart_image_size', 'gallery_thumbnail' );

					if ( $post_thumbnail_id ) {
						$thumbnail_src = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
						$alt_text      = trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
					} else {
						$thumbnail_src = wc_placeholder_img_src( $thumbnail_size );
						$alt_text      = esc_html__( 'Awaiting product image', 'woocommerce' );
					}

					printf( '<div class="sticky-cart-form__product-image"><img src="%s" alt="%s" data-o_src="%s"></div>', esc_url( $thumbnail_src[0] ), esc_attr( $alt_text ), esc_url( $thumbnail_src[0] ) );
					the_title( '<p class="sticky-cart-form__product-title">', '</p>' );
					woocommerce_template_single_price();
					?>
				</div>

				<?php woocommerce_template_single_add_to_cart(); ?>

				<button type="button" class="button sticky-cart-form__mobile-button" data-product_type="<?php echo esc_attr( $product->get_type() ) ?>">
					<?php echo esc_html( $product->add_to_cart_text() ); ?>
				</button>
			</div>
		</div>
		<?php

		wc_set_loop_prop( 'is_sticky_addtocart', false );
	}

	/**
	 * Add a class of quantity dropdown
	 *
	 * @param array $classes
	 * @return array
	 */
	public static function quantity_dropdown_class( $classes ) {
		if ( ! wc_get_loop_prop( 'is_sticky_addtocart' ) && is_product() ) {
			$classes[] = 'quantity--dropdown';
		}

		return $classes;
	}
}

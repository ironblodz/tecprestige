<?php
/**
 * Display product quickview.
 *
 * @author        UIX Themes
 * @package       Konte
 * @version       1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$GLOBALS['post'] = $post_object;
wc_setup_product_data( $post_object );
?>

	<div <?php post_class( 'product-quickview clearfix ' . ( $background_color ? 'background-set' : '' ) ) ?> <?php echo ! empty( $background_color ) ? 'style="background-color: ' . esc_attr( $background_color ) . '"' : ''; ?>>
		<?php
		/**
		 * Hook: konte_woocommerce_before_product_quickview_summary
		 *
		 * @hooked woocommerce_show_product_sale_flash - 5
		 * @hooked woocommerce_show_product_images - 10
		 */
		do_action( 'konte_woocommerce_before_product_quickview_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: konte_woocommerce_product_quickview_summary
			 *
			 * @hooked woocommerce_template_single_title - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_rating - 30
			 * @hooked woocommerce_template_single_price - 40
			 * @hooked woocommerce_template_single_add_to_cart - 50
			 * @hooked woocommerce_template_single_meta - 60
			 */
			do_action( 'konte_woocommerce_product_quickview_summary' );
			?>
		</div>

		<?php
		/**
		 * Hook: konte_woocommerce_after_product_quickview_summary
		 *
		 * @hooked Konte_WooCommerce_Template_Catalog::add_to_wishlist_button - 10
		 * @hooked Konte_WooCommerce_Template_Product::product_share - 20
		 * @hooked Konte_WooCommerce_Template_Catalog::quickview_detail_link - 30
		 */
		do_action( 'konte_woocommerce_after_product_quickview_summary' );
		?>
	</div>

<?php
wp_reset_postdata();
wc_setup_product_data( $GLOBALS['post'] );

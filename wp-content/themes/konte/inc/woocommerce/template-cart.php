<?php
/**
 * Hooks of cart.
 *
 * @package Konte
 */

/**
 * Class of cart template.
 */
class Konte_WooCommerce_Template_Cart {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Empty cart.
		add_action( 'woocommerce_cart_actions', array( __CLASS__, 'empty_cart_button' ) );
		add_action( 'template_redirect', array( __CLASS__, 'empty_cart_action' ) );

		// Add image to empty cart message.
		add_filter( 'wc_empty_cart_message', array( __CLASS__, 'empty_cart_message' ) );

		// Move cross sell to bottom.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		add_filter( 'woocommerce_cross_sells_columns', array( __CLASS__, 'cross_sells_columns' ) );

		// Change the quantity format of the cart widget.
		add_filter( 'woocommerce_widget_cart_item_quantity', array( __CLASS__, 'widget_cart_item_quantity' ), 10, 3 );

		// Ajax update mini cart.
		add_action( 'wc_ajax_update_cart_item', array( __CLASS__, 'update_cart_item' ) );

		// Display the floating cart icon.
		if ( konte_get_option( 'cart_icon_floating' ) ) {
			konte_set_theme_prop( 'panels', 'cart' );

			// Set piority as 5 to display after go-top button.
			add_action( 'konte_after_site', array( __CLASS__, 'floating_cart_icon' ), 20 );
		}

		// Display fixed quanitity for individual purchasable items.
		add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'cart_item_quantity' ), 10, 3 );
	}

	/**
	 * Empty cart button.
	 */
	public static function empty_cart_button() {
		?>
		<button type="submit" class="button empty-cart-button" name="empty_cart" value="<?php esc_attr_e( 'Clear cart', 'konte' ); ?>"><?php esc_html_e( 'Clear cart', 'konte' ); ?></button>
		<?php
	}

	/**
	 * Empty cart.
	 */
	public static function empty_cart_action() {
		if ( ! empty( $_POST['empty_cart'] ) && wp_verify_nonce( wc_get_var( $_REQUEST['woocommerce-cart-nonce'] ), 'woocommerce-cart' ) ) {
			WC()->cart->empty_cart();
			wc_add_notice( esc_html__( 'Cart is cleared.', 'konte' ) );

			$referer = wp_get_referer() ? remove_query_arg( array(
				'remove_item',
				'add-to-cart',
				'added-to-cart',
			), add_query_arg( 'cart_emptied', '1', wp_get_referer() ) ) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;
		}
	}

	/**
	 * Display empty bad image.
	 *
	 * @param string $message
	 * @return string
	 */
	public static function empty_cart_message( $message ) {
		$message = '<img src="' . esc_url( get_theme_file_uri( 'images/empty-bag.svg' ) ) . '" width="150" alt="' . esc_attr__( 'Cart is empty', 'konte' ) . '">' . $message;

		return $message;
	}

	/**
	 * Change the cross sells columns.
	 *
	 * @return int
	 */
	public static function cross_sells_columns() {
		return 4;
	}

	/**
	 * Change the quantity HTML of widget cart.
	 *
	 * @param string $product_quantity
	 * @param array  $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public static function widget_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		$_product      = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

		if ( $_product->is_sold_individually() ) {
			$quantity = '<span class="quantity">1</span>';
		} else {
			$quantity = woocommerce_quantity_input( array(
				'input_name'   => "cart[{$cart_item_key}][qty]",
				'input_value'  => $cart_item['quantity'],
				'max_value'    => $_product->get_max_purchase_quantity(),
				'min_value'    => '0',
				'product_name' => $_product->get_name(),
			), $_product, false );
		}

		return sprintf(
			'<div class="woocommerce-mini-cart-item__qty" data-nonce="%s"><span class="label">%s</span>%s<span class="price">%s</span></div>',
			esc_attr( wp_create_nonce( 'konte-update-mini-cart-qty--' . $cart_item_key ) ),
			esc_html__( 'Qty', 'konte' ),
			$quantity,
			$product_price
		);
	}

	/**
	 * Update a single cart item.
	 */
	public static function update_cart_item() {
		if ( empty( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
			wp_send_json_error();
			exit;
		}

		$cart_item_key = wc_clean( $_POST['cart_item_key'] );
		$qty           = floatval( $_POST['qty'] );

		ob_start();

		WC()->cart->set_quantity( $cart_item_key, $qty );

		if ( $cart_item_key && false !== WC()->cart->set_quantity( $cart_item_key, $qty ) ) {
			WC_AJAX::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Display the floating cart icon.
	 */
	public static function floating_cart_icon() {
		?>
		<a href="<?php echo esc_url( wc_get_cart_url() ) ?>" data-toggle="off-canvas" data-target="cart-panel" class="floating-cart-icon">
			<?php konte_shopping_cart_icon(); ?>
			<span class="counter cart-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
		</a>
		<?php
	}

	/**
	 * Display the cart quantity as "1" for limited purchasable items.
	 *
	 * @param  string $product_quantity
	 * @param  string $cart_item_key
	 * @param  array $cart_item
	 * @return string
	 */
	public static function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product->is_sold_individually() ) {
			$product_quantity .= '<span class="quantity--individual">' . _x( '1', 'Product quantity', 'konte' ) . '</span>';
		}

		return $product_quantity;
	}
}

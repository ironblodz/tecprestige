<?php
/**
 * Template part for displaying the cart icon
 *
 * @package Konte
 */
if ( ! function_exists( 'WC' ) ) {
	return;
}
?>

<div class="header-cart">
	<a href="<?php echo esc_url( wc_get_cart_url() ) ?>" data-toggle="<?php echo 'panel' == konte_get_option( 'header_cart_behaviour' ) && ! is_cart() && ! is_checkout() ? 'off-canvas' : 'link'; ?>" data-target="cart-panel">
		<?php konte_shopping_cart_icon(); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'konte' )?></span>
		<span class="counter cart-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
	</a>
</div>

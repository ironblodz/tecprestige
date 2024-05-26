<?php
/**
 * Template part for displaying the wishlist icon
 *
 * @package Konte
 */
if ( ! function_exists( 'WC' ) ) {
	return;
}
if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
	printf(
		'<div class="header-wishlist">
			<a href="%s" class="wishlist-contents">
				%s
				<span class="screen-reader-text">%s</span>
				<span class="counter wishlist-counter">%s</span>
			</a>
		</div>',
		esc_url( Konte_WooCommerce_Template_Wishlist::get_wishlist_url() ),
		Konte_WooCommerce_Template_Wishlist::get_icon(),
		esc_html__( 'Wishlist', 'konte' ),
		Konte_WooCommerce_Template_Wishlist::count_wishlist_items()
	);
}

<?php
/**
 * Custom template tags of footer
 *
 * @package Konte
 */

 /**
  * Footer item template
  *
  * @param  string $item
  *
  * @return void
  */
function konte_footer_item( $item ) {
	switch ( $item ) {
		case 'copyright':
			echo '<div class="copyright">' . do_shortcode( wp_kses_post( konte_get_option( 'footer_copyright' ) ) ) . '</div>';
			break;

		case 'menu':
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array(
					'container'      => 'nav',
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'menu_class'     => 'footer-menu nav-menu menu',
					'depth'          => 1,
				) );
			}
			break;

		case 'social':
			if ( has_nav_menu( 'socials' ) ) {
				wp_nav_menu( array(
					'theme_location'  => 'socials',
					'container_class' => 'socials-menu ',
					'menu_id'         => 'footer-socials',
					'depth'           => 1,
				) );
			}
			break;

		case 'currency':
			konte_currency_switcher( array(
				'label'     => esc_html__( 'Currency', 'konte' ),
				'direction' => 'up',
			) );
			break;

		case 'language':
			konte_language_switcher( array(
				'label'     => esc_html__( 'Language', 'konte' ),
				'direction' => 'up',
			) );
			break;

		case 'currency_language':
			echo '<div class="switchers">';

			konte_currency_switcher( array(
				'label'     => esc_html__( 'Currency', 'konte' ),
				'direction' => 'up',
			) );
			konte_language_switcher( array(
				'label'     => esc_html__( 'Language', 'konte' ),
				'direction' => 'up',
			) );

			echo '</div>';
			break;

		case 'text':
			if ( $footer_custom_text = konte_get_option( 'footer_main_text' ) ) {
				echo '<div class="custom-text">' . do_shortcode( wp_kses_post( $footer_custom_text ) ) . '</div>';
			}
			break;

		default:
			do_action( 'konte_footer_footer_main_item', $item );
			break;
	}
}

/**
 * Mobile bottom bar item
 *
 * @since  2.3.0
 *
 * @param  string $item
 *
 * @return void
 */
function konte_mobile_bottom_bar_item( $item ) {

	switch( $item ) {
		case 'home':
			?>
			<a href="<?php echo esc_url( home_url() ); ?>" rel="home" role="button">
				<?php konte_svg_icon( 'icon=home' ); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Home', 'konte' ); ?></span>
			</a>
			<?php
			break;

		case 'search':
			konte_set_theme_prop( 'modals', 'search' );
			?>
			<a href="#" role="button" data-toggle="modal" data-target="search-modal">
				<?php konte_svg_icon( 'icon=search' ); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Search', 'konte' )?></span>
			</a>
			<?php
			break;

		case 'cart':
			$cart_toggle = konte_get_option( 'header_cart_behaviour' );

			if ( 'panel' == $cart_toggle ) {
				konte_set_theme_prop( 'panels', 'cart' );
			}
			?>
			<a href="<?php echo esc_url( wc_get_cart_url() ) ?>" data-toggle="<?php echo 'panel' == $cart_toggle && ! is_cart() && ! is_checkout() ? 'off-canvas' : 'link'; ?>" data-target="cart-panel">
				<?php konte_shopping_cart_icon(); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Cart', 'konte' )?></span>
				<span class="counter cart-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			</a>
			<?php
			break;

		case 'wishlist':
			if ( ! class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
				break;
			}
			?>
			<a href="<?php echo esc_url( Konte_WooCommerce_Template_Wishlist::get_wishlist_url() ); ?>" class="wishlist-contents">
				<?php konte_svg_icon( 'icon=heart-o' ); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Wishlist', 'konte' ); ?></span>
				<span class="counter wishlist-counter"><?php echo Konte_WooCommerce_Template_Wishlist::count_wishlist_items(); ?></span>
			</a>
			<?php
			break;

		case 'account':
			?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>">
				<?php konte_svg_icon( 'icon=account' ); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Account', 'konte' )?></span>
			</a>
			<?php
			break;

		case 'menu':
			?>
			<a href="#" role="button" data-toggle="off-canvas" data-target="mobile-menu">
				<?php konte_svg_icon( 'icon=menu' ); ?>
				<span class="mobile-bottom-bar-item__label"><?php esc_html_e( 'Menu', 'konte' ); ?></span>
			</a>
			<?php
			break;

		default:
			do_action( 'konte_mobile_bottom_bar_item', $item );
			break;

	}
}

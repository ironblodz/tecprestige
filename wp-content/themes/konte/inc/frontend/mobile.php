<?php
/**
 * Custom template functions that act on mobile
 *
 * @package Konte
 */


/**
 * Mobile header.
 */
function konte_mobile_header() {
	$classes = array(
		'logo-' . konte_get_option( 'mobile_custom_logo' ) ? 'custom' : 'default',
		'logo-' . konte_get_option( 'mobile_logo_position' ),
	);
	$classes = apply_filters( 'konte_mobile_header_class', $classes );
	?>

	<div class="header-mobile <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<div class="konte-container-fluid">
			<?php get_template_part( 'template-parts/mobile/header' ); ?>
		</div>
	</div>

	<?php
}

add_action( 'konte_header', 'konte_mobile_header', 99 );

/**
 * Mobile menu
 */
function konte_mobile_menu() {
	$items = konte_get_option( 'mobile_menu_items' );
	?>

	<div id="mobile-menu" class="mobile-menu-panel offscreen-panel">
		<div class="backdrop"></div>
		<div class="panel">
			<?php konte_mobile_header() ?>
			<?php
			foreach ( $items as $index => $item ) {
				switch ( $item ) {
					case 'divider1':
					case 'divider2':
					case 'divider3':
					case 'divider4':
					case 'divider5':
					case 'divider6':
					case 'divider7':
						echo '<hr class="mobile-menu__divider divider">';
						break;

					case 'search':
						?>
						<div class="mobile-menu__search-form">
							<form method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
								<label>
									<span class="screen-reader-text"><?php esc_html_e( 'Search', 'konte' ) ?></span>
									<?php konte_svg_icon( 'icon=search&class=search-icon' ); ?>
									<input type="text" name="s" class="search-field" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search', 'konte' ) ?>" autocomplete="off">
									<?php if ( $type = konte_get_option( 'mobile_menu_search_type' ) ) : ?>
										<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ) ?>">
									<?php endif; ?>
								</label>
							</form>
						</div>
						<?php
						break;

					case 'menu':
						$menu = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
						wp_nav_menu( array(
							'theme_location'  => $menu,
							'container'       => 'nav',
							'container_class' => 'mobile-menu__nav',
						) );
						break;

					case 'cart':
						if ( ! class_exists( 'WooCommerce' ) ) {
							break;
						}
						?>
						<div class="mobile-menu__cart">
							<a href="<?php echo esc_url( wc_get_cart_url() ) ?>">
								<span class="mobile-menu__cart-text"><?php esc_html_e( 'Shopping Cart', 'konte' ) ?></span>
								<span class="mobile-menu__cart-icon">
									<?php konte_shopping_cart_icon(); ?>
									<span class="counter cart-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
								</span>
							</a>
						</div>
						<?php
						break;

					case 'wishlist':
						if ( class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
							printf(
								'<div class="mobile-menu__wishlist">
									<a href="%s" class="wishlist-contents">
										<span class="mobile-menu__wishlist-text">%s</span>
										<span class="mobile-menu__wishlist-icon">
											%s
											<span class="counter wishlist-counter">%s</span>
										</span>
									</a>
								</div>',
								esc_url( Konte_WooCommerce_Template_Wishlist::get_wishlist_url() ),
								esc_html__( 'Wishlist', 'konte' ),
								konte_svg_icon( 'icon=heart-o&echo=0' ),
								Konte_WooCommerce_Template_Wishlist::count_wishlist_items()
							);
						}
						break;

					case 'currency':
						?>
						<?php konte_currency_switcher( array(
							'label'     => esc_html__( 'Currency', 'konte' ),
							'direction' => $index > 2 ? 'up' : 'down',
						) ); ?>
						<?php
						break;

					case 'language':
						?>
						<?php konte_language_switcher( array(
							'label'     => esc_html__( 'Language', 'konte' ),
							'direction' => $index > 2 ? 'up' : 'down',
						) ); ?>
						<?php
						break;

					case 'account':
						if ( ! class_exists( 'WooCommerce' ) ) {
							break;
						}

						if ( ! is_user_logged_in() ) {
							printf(
								'<div class="mobile-menu__account-login">
									<a href="%s">%s</a>
								</div>',
								esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
								esc_html__( 'Sign In', 'konte' )
							);
						} else {
							printf(
								'<div class="mobile-menu__account-dashboard">
									<a href="%s">%s</a>
								</div>',
								esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
								esc_html__( 'My Account', 'konte' )
							);

							printf(
								'<div class="mobile-menu__account-logout">
									<a href="%s">%s</a>
								</div>',
								esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ),
								esc_html__( 'Sign Out', 'konte' )
							);
						}
						break;

					case 'socials':
						if ( has_nav_menu( 'socials' ) ) {
							wp_nav_menu( array(
								'theme_location'  => 'socials',
								'container_class' => 'mobile-menu__socials socials-menu',
								'menu_id'         => 'footer-socials',
								'depth'           => 1,
							) );
						}
						break;

					default:
						do_action( 'konte_mobile_menu_items', $item );
						break;
				}
			}
			?>
		</div>
	</div>

	<?php
}

add_action( 'konte_after_site', 'konte_mobile_menu' );

/**
 * Display the bottom navigation bar sticky on mobile devices
 *
 * @since 2.3.0
 *
 * @return void
 */
function konte_mobile_bottom_bar() {
	if ( ! konte_get_option( 'mobile_bottom_bar' ) ) {
		return;
	}

	get_template_part( 'template-parts/mobile/bottom-bar' );
}

add_action( 'konte_after_site', 'konte_mobile_bottom_bar', 1 );

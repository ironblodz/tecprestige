<?php
/**
 * Hooks for nav menus
 */

/**
 * Modify the default page menu args.
 *
 * @param  array $args
 *
 * @return array
 */
function konte_page_menu_args( $args ) {
	$args['container']  = 'ul';
	$args['menu_class'] = 'menu nav-menu';
	$args['before']     = '';
	$args['after']      = '';

	return $args;
}

add_filter( 'wp_page_menu_args', 'konte_page_menu_args' );

/**
 * Add a walder object for all nav menus
 *
 * @since  1.0.0
 *
 * @param  array $args The default args
 *
 * @return array
 */
function konte_nav_menu_args( $args ) {
	if ( in_array( $args['theme_location'], array( 'primary', 'secondary' ) ) ) {
		// Not using mega menu for vertical menu.
		if ( 'v10' == konte_get_header_layout() ) {
			$args['menu_class'] .= ' nav-menu--submenu-' . konte_get_option( 'header_vertical_submenu_toggle' );

			return $args;
		}

		// Remove fallback to pages.
		$args['fallback_cb'] = false;

		// Only support mega menu if the Konte Addons plugin installed.
		if ( class_exists( 'Konte_Addons_Mega_Menu_Walker' ) ) {
			if ( 'mobile-menu__nav' != $args['container_class'] ) {
				$args['walker'] = new Konte_Addons_Mega_Menu_Walker;
			}
		}

		// Add custom class for carets.
		if ( konte_get_option( 'header_menu_caret_submenu' ) ) {
			$args['menu_class'] .= ' nav-menu--submenu-has-caret';
		}
	}

	return $args;
}

add_filter( 'wp_nav_menu_args', 'konte_nav_menu_args' );

/**
 * Add a caret icon to the main menu
 *
 * @param string $title
 * @param object $menu_item
 * @param object $args
 *
 * @return string
 */
function konte_menu_item_caret( $title, $menu_item, $args ) {
	// Only support main navigations.
	if ( ! in_array( $args->theme_location, array( 'primary', 'secondary' ) ) ) {
		return $title;
	}

	if ( 'mobile-menu__nav' == $args->container_class ) {
		return $title;
	}

	if ( ! konte_get_option( 'header_menu_caret' ) ) {
		return $title;
	}

	$caret = konte_get_option( 'header_menu_caret_arrow' );

	switch ( $caret ) {
		case 'plus_text':
			$caret = '+';
			break;

		case 'plus':
			$caret = '<i class="fa fa-plus"></i>';
			break;

		default:
			$caret = '<i class="fa fa-' . esc_attr( $caret ) . '-right"></i>';
			break;
	}

	if ( in_array( 'menu-item-has-children', $menu_item->classes ) ) {
		$title .= '<span class="caret">' . $caret . '</span>';
	}

	return $title;
}

add_filter( 'nav_menu_item_title', 'konte_menu_item_caret', 10, 3 );

if ( ! function_exists( 'konte_menu_social_icon' ) ) :

	/**
	 * Add SVG code of the social icon to the social menu.
	 * This function only adds icons which are not supported in FontAwesome v4.
	 *
	 * @param object $args
	 * @param object $item
	 *
	 * @return string
	 */
	function konte_menu_social_icon( $title, $item, $args ) {
		if ( 'socials' != $args->theme_location ) {
			return $title;
		}

		if ( preg_match( '/tiktok.com/i', $item->url ) ) {
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg>';
			$title = '<span class="svg-icon svg-icon--tiktok">' . $svg . '</span><span>' . $title . '</span>';
		} elseif ( preg_match( '/discord.com/i', $item->url ) || preg_match( '/discord.gg/i', $item->url ) ) {
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M524.531,69.836a1.5,1.5,0,0,0-.764-.7A485.065,485.065,0,0,0,404.081,32.03a1.816,1.816,0,0,0-1.923.91,337.461,337.461,0,0,0-14.9,30.6,447.848,447.848,0,0,0-134.426,0,309.541,309.541,0,0,0-15.135-30.6,1.89,1.89,0,0,0-1.924-.91A483.689,483.689,0,0,0,116.085,69.137a1.712,1.712,0,0,0-.788.676C39.068,183.651,18.186,294.69,28.43,404.354a2.016,2.016,0,0,0,.765,1.375A487.666,487.666,0,0,0,176.02,479.918a1.9,1.9,0,0,0,2.063-.676A348.2,348.2,0,0,0,208.12,430.4a1.86,1.86,0,0,0-1.019-2.588,321.173,321.173,0,0,1-45.868-21.853,1.885,1.885,0,0,1-.185-3.126c3.082-2.309,6.166-4.711,9.109-7.137a1.819,1.819,0,0,1,1.9-.256c96.229,43.917,200.41,43.917,295.5,0a1.812,1.812,0,0,1,1.924.233c2.944,2.426,6.027,4.851,9.132,7.16a1.884,1.884,0,0,1-.162,3.126,301.407,301.407,0,0,1-45.89,21.83,1.875,1.875,0,0,0-1,2.611,391.055,391.055,0,0,0,30.014,48.815,1.864,1.864,0,0,0,2.063.7A486.048,486.048,0,0,0,610.7,405.729a1.882,1.882,0,0,0,.765-1.352C623.729,277.594,590.933,167.465,524.531,69.836ZM222.491,337.58c-28.972,0-52.844-26.587-52.844-59.239S193.056,219.1,222.491,219.1c29.665,0,53.306,26.82,52.843,59.239C275.334,310.993,251.924,337.58,222.491,337.58Zm195.38,0c-28.971,0-52.843-26.587-52.843-59.239S388.437,219.1,417.871,219.1c29.667,0,53.307,26.82,52.844,59.239C470.715,310.993,447.538,337.58,417.871,337.58Z"/></svg>';
			$title = '<span class="svg-icon svg-icon--discord">' . $svg . '</span><span>' . $title . '</span>';
		} elseif ( preg_match( '/x.com/i', $item->url ) ) {
			$svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>';
			$title = '<span class="svg-icon svg-icon--x">' . $svg . '</span><span>' . $title . '</span>';
		} else {
			$title = '<span>' . $title . '</span>';
		}

		return $title;
	}

endif;

add_filter( 'nav_menu_item_title', 'konte_menu_social_icon', 10, 3 );

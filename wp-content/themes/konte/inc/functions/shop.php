<?php
/**
 * Functions for the shop pages
 */

if ( ! function_exists( 'konte_currency_switcher' ) ) :
	/**
	 * Print HTML of currency switcher
	 */
	function konte_currency_switcher( $args = array() ) {
		$currency_list = konte_get_currency_list();

		if ( empty( $currency_list ) ) {
			do_action( 'konte_currency_switcher', $args );

			return;
		}

		// The current currency is always the first item.
		$current_currency = reset( $currency_list );
		$current_currency = strip_tags( $current_currency );

		$args = wp_parse_args( $args, array(
			'label'     => '',
			'direction' => 'down',
			'class'     => '',
		) );

		$classes = array(
			'currency',
			'list-dropdown',
			$args['direction'],
			$args['class'],
		);

		$language_configuration = konte_get_option( 'currency_configuration' );

		if ( in_array( $language_configuration, array( 'name', 'both' ) ) ) {
			$classes[] = 'list-dropdown--show-name';
		}

		if ( in_array( $language_configuration, array( 'flag', 'both' ) ) ) {
			$classes[] = 'list-dropdown--show-flag';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ) ?>">
			<?php if ( ! empty( $args['label'] ) ) : ?>
				<span class="label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
			<div class="dropdown">
				<span class="current">
					<span class="selected"><?php echo esc_html( $current_currency ); ?></span>
					<?php konte_svg_icon( 'icon=arrow-dropdown&size=smaller&class=caret' ) ?>
				</span>
				<ul>
					<?php echo implode( "\n\t", $currency_list ); ?>
				</ul>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'konte_get_currency_list' ) ) :
	/**
	 * Get currency list
	 *
	 * @return array
	 */
	function konte_get_currency_list() {
		$currency_list = array();

		if ( class_exists( 'WOOCS' ) ) {
			$currencies = $GLOBALS['WOOCS']->get_currencies();

			foreach ( $currencies as $key => $currency ) {
				if ( $GLOBALS['WOOCS']->current_currency == $key ) {
					array_unshift( $currency_list, sprintf(
						'<li><a href="#" class="woocs_flag_view_item woocs_flag_view_item_current" data-currency="%s">
							<img src="%s" alt="%s" class="currency-dropdown__flag">
							<span class="name">%s</span>
						</a></li>',
						esc_attr( $currency['name'] ),
						esc_url( $currency['flag'] ),
						esc_attr( $currency['name'] ),
						esc_html( $currency['name'] )
					) );
				} else {
					$currency_list[] = sprintf(
						'<li><a href="#" class="woocs_flag_view_item" data-currency="%s">
							<img src="%s" alt="%s" class="currency-dropdown__flag">
							<span class="name">%s</span>
						</a></li>',
						esc_attr( $currency['name'] ),
						esc_url( $currency['flag'] ),
						esc_attr( $currency['name'] ),
						esc_html( $currency['name'] )
					);
				}
			}
		}

		return $currency_list;
	}
endif;

if ( ! function_exists( 'konte_language_switcher' ) ) :
	/**
	 * Print HTML of language switcher
	 * It requires plugin WPML installed
	 */
	function konte_language_switcher( $args = array() ) {
		$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 1 ) );

		if ( empty( $languages ) ) {
			return;
		}

		$args    = wp_parse_args( $args, array(
			'label'     => '',
			'direction' => 'down',
			'class'     => '',
		) );

		$classes = array(
			'language',
			'list-dropdown',
			$args['direction'],
			$args['class'],
		);

		$language_configuration = konte_get_option( 'language_configuration' );

		if ( in_array( $language_configuration, array( 'name', 'both' ) ) ) {
			$classes[] = 'list-dropdown--show-name';
		}

		if ( in_array( $language_configuration, array( 'flag', 'both' ) ) ) {
			$classes[] = 'list-dropdown--show-flag';
		}

		$list    = array();
		$current = '';

		foreach ( (array) $languages as $code => $language ) {
			$item = sprintf(
				'<li class="%s"><a href="%s"><img src="%s" alt="%s" class="language-dropdown__flag"><span class="name">%s</span></a></li>',
				esc_attr( $code ),
				esc_url( $language['url'] ),
				esc_url( $language['country_flag_url'] ),
				! empty( $language['translated_name'] ) ? esc_html( $language['translated_name'] ) : esc_html( $language['native_name'] ),
				! empty( $language['translated_name'] ) ? esc_html( $language['translated_name'] ) : esc_html( $language['native_name'] )
			);

			if ( ! $language['active'] ) {
				$list[] = $item;
			} else {
				$current = $language;
				array_unshift( $list, $item );
			}
		}

		?>

		<div class="<?php echo esc_attr( implode( ' ', $classes ) ) ?>">
			<?php if ( ! empty( $args['label'] ) ) : ?>
				<span class="label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
			<div class="dropdown">
				<span class="current">
					<span class="selected"><?php echo esc_html( $current['native_name'] ) ?></span>
					<?php konte_svg_icon( 'icon=arrow-dropdown&size=smaller&class=caret' ) ?>
				</span>
				<ul>
					<?php echo implode( "\n\t", $list ); ?>
				</ul>
			</div>
		</div>

		<?php
	}
endif;

if ( ! function_exists( 'konte_is_order_tracking_page' ) ) :
	/**
	 * Check if current page is order tracking page
	 *
	 * @return bool
	 */
	function konte_is_order_tracking_page() {
		$page_id = get_option( 'order_tracking_page_id' );
		$page_id = konte_get_translated_object_id( $page_id );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}
endif;

if ( ! function_exists( 'konte_shopping_cart_icon' ) ) {
	/**
	 * Get shopping cart icon HTML
	 */
	function konte_shopping_cart_icon( $echo = true ) {
		$source = konte_get_option( 'cart_icon_source' );
		$icon   = konte_svg_icon( 'icon=cart&echo=0' );

		if ( 'image' == $source ) {
			$width  = floatval( konte_get_option( 'cart_icon_width' ) );
			$height = floatval( konte_get_option( 'cart_icon_height' ) );

			$width  = $width ? ' width="' . $width . 'px"' : '';
			$height = $height ? ' height="' . $height . 'px"' : '';

			$dark  = konte_get_option( 'cart_icon_image' );
			$light = konte_get_option( 'cart_icon_image_light' );
			$light = $light ? $light : $dark;

			if ( $dark ) {
				$icon = sprintf(
					'<span class="shopping-cart-icon shopping-cart-icon--image icon-image"><img src="%1$s" alt="%2$s" %3$s class="icon-dark"><img src="%4$s" alt="%2$s" %3$s class="icon-light"></span>',
					esc_url( $dark ),
					esc_attr__( 'Shopping Cart', 'konte' ),
					$width . $height,
					esc_url( $light )
				);
			}
		} elseif ( 'svg' == $source ) {
			$svg = konte_get_option( 'cart_icon_svg' );

			if ( $svg ) {
				$icon = '<span class="shopping-cart-icon shopping-cart-icon--svg svg-code svg-icon">' . konte_sanitize_svg( $svg ) . '</span>';
			}
		} else {
			$svg  = konte_get_option( 'cart_icon' );
			$svg  = $svg ? $svg : 'cart';
			$icon = konte_svg_icon( 'icon=' . $svg . '&class=shopping-cart-icon' );
		}

		if ( ! $echo ) {
			return $icon;
		}

		echo ! empty( $icon ) ? $icon : '';
	}
}

/**
 * Check if product gallery is slider.
 *
 * @return bool
 */
function konte_product_gallery_is_slider() {
	$support = ! in_array( konte_get_option( 'product_layout' ), array( 'v2', 'v5' ) );

	return apply_filters( 'konte_product_gallery_is_slider', $support );
}

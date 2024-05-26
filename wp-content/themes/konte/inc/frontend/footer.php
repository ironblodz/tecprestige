<?php
/**
 * Custom functions that act on footer templates
 *
 * @package Konte
 */

/**
 * Adds classes of background and text color to footer
 *
 * @param array $classes
 *
 * @return array
 */
function konte_footer_classes( $classes ) {
	if ( konte_get_option( 'footer_background_blog_custom' ) && konte_is_blog_related_pages() ) {
		$background = konte_get_option( 'footer_background_blog' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'footer_blog_textcolor' ) );
	} elseif ( konte_get_option( 'footer_background_shop_custom' ) && function_exists( 'WC' ) && is_woocommerce() ) {
		$background = konte_get_option( 'footer_background_shop' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'footer_shop_color' ) );
	} else {
		$background = konte_get_option( 'footer_background' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'footer_textcolor' ) );
	}

	if ( is_page() && ( $page_background = get_post_meta( get_the_ID(), 'footer_background', true ) ) ) {
		$background = $page_background;

		if ( 'custom' == $background || 'transparent' == $background ) {
			$page_text_color = get_post_meta( get_the_ID(), 'footer_textcolor', true );
			$text_color      = $page_text_color ? $page_text_color : $text_color;
		} else {
			$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : $text_color );
		}
	}

	if ( is_page_template( 'templates/split.php' ) ) {
		$background = 'transparent';
		$text_color = get_post_meta( get_the_ID(), 'footer_textcolor', true );
		$text_color = $text_color ? $text_color : 'dark';
	}

	$classes[] = $background;
	$classes[] = 'text-' . $text_color;

	return $classes;
}

add_filter( 'konte_footer_class', 'konte_footer_classes' );

/**
 * Site footer
 */
function konte_footer() {
	$sections = konte_get_option( 'footer_sections' );

	if ( empty( $sections ) ) {
		return;
	}

	foreach ( (array) $sections as $section ) {
		if ( is_page_template( 'templates/split.php' ) && 'main' != $section ) {
			continue;
		}

		get_template_part( 'template-parts/footer/footer', $section );
	}
}

add_action( 'konte_footer', 'konte_footer' );

/**
 * Footer container class name.
 *
 * @param string $class
 *
 * @return string
 */
function konte_footer_container_class( $class ) {
	if ( is_page_template( 'templates/split.php' ) ) {
		$class = 'konte-container-fluid';
	}

	return $class;
}

add_filter( 'konte_footer_container_class', 'konte_footer_container_class' );

/**
 * Change the footer of split content template
 *
 * @param array $sections
 *
 * @return array
 */
function konte_split_content_footer_sections( $sections ) {
	if ( ! is_page_template( 'templates/split.php' ) ) {
		return $sections;
	}

	if ( ! get_post_meta( get_the_ID(), 'split_content_custom_footer', true ) ) {
		return $sections;
	}

	$sections['left'] = array(
		array( 'item' => get_post_meta( get_the_ID(), 'split_content_footer_left', true ) ),
	);

	$sections['right'] = array(
		array( 'item' => get_post_meta( get_the_ID(), 'split_content_footer_right', true ) ),
	);

	$sections['center'] = false;

	return $sections;
}

add_filter( 'konte_footer_main_sections', 'konte_split_content_footer_sections' );

/**
 * Print hamburger menu HTML on footer
 */
function konte_hamburger_menu() {
	$panels = konte_get_theme_prop( 'panels' );

	if ( ! in_array( 'hamburger', $panels ) ) {
		return;
	}

	$type      = konte_get_option( 'hamburger_content_type' );
	$logo      = konte_get_option( 'hamburger_logo' );
	$currency  = konte_get_option( 'hamburger_currency' );
	$language  = konte_get_option( 'hamburger_language' );
	$social    = konte_get_option( 'hamburger_social' );
	$behaviour = konte_get_option( 'hamburger_open_submenu_behaviour' );
	$animation = konte_get_option( 'hamburger_content_animation' );
	?>

	<div id="hamburger-fullscreen" class="hamburger-fullscreen content-<?php echo esc_attr( $type ) ?> content-animation-<?php echo esc_attr( $animation ); ?>">
		<div class="hamburger-fullscreen__header">
			<div class="hamburger-menu button-close">
				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
				<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
			</div>
			<?php
			if ( $logo ) {
				get_template_part( 'template-parts/header/logo' );
			}
			?>
		</div>


		<div class="hamburger-screen-inner">
			<div class="hamburger-screen-content">
				<?php if ( 'widgets' == konte_get_option( 'hamburger_content_type' ) ) : ?>
					<div class="fullscreen-widgets widget-area">
						<?php
						if ( is_active_sidebar( 'off-screen' ) ) {
							dynamic_sidebar( 'off-screen' );
						} elseif ( current_user_can( 'super admin' ) ) {
							the_widget( 'WP_Widget_Text', array(
								'text' => esc_html__( 'Please add widgets to Off Screen Sidebar to create content for this full screen menu.', 'konte' ),
							) );
						}
						?>
					</div>
				<?php else : ?>
					<nav id="fullscreen-menu" class="fullscreen-menu hamburger-navigation <?php echo esc_attr( $behaviour ) ?>-open">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'hamburger',
							'container'      => null,
							'depth'          => 3,
							'fallback_cb'    => 'wp_page_menu',
						) );
						?>
					</nav>
				<?php endif; ?>

				<?php if ( $currency || $language ) : ?>
					<div class="fullscreen-footer">
						<?php
						if ( $currency ) {
							konte_currency_switcher( array(
								'label'     => esc_html__( 'Currency', 'konte' ),
								'direction' => 'up',
							) );
						}

						if ( $language ) {
							konte_language_switcher( array(
								'label'     => esc_html__( 'Language', 'konte' ),
								'direction' => 'up',
							) );
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( $social && has_nav_menu( 'socials' ) ) : ?>
					<div class="social-icons">
						<?php
						wp_nav_menu( array(
							'theme_location'  => 'socials',
							'container_class' => 'socials-menu ',
							'menu_id'         => 'footer-socials',
							'depth'           => 1,
						) );
						?>
					</div>
				<?php endif; ?>
			</div><!-- .hamburger-screen-content -->

			<div class="hamburger-screen-background"></div>
		</div>
	</div>

	<?php
}

add_action( 'konte_after_site', 'konte_hamburger_menu' );

/**
 * Login panel
 */
function konte_login_panel() {
	$panels = konte_get_theme_prop( 'panels' );

	if ( ! in_array( 'account', $panels ) ) {
		return;
	}

	if ( is_user_logged_in() || ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	?>
	<div id="login-panel" class="login-panel offscreen-panel">
		<div class="backdrop"></div>
		<div class="panel">
			<div class="hamburger-menu button-close active">
				<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>

				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
			</div>

			<div class="panel-header">
				<div class="panel__title"><?php esc_html_e( 'Sign in', 'konte' ) ?></div>
			</div>

			<div class="panel-content">
				<form class="woocommerce-form woocommerce-form-login login" method="post" action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="form-row">
						<label for="panel_username"><?php esc_html_e( 'Username', 'konte' ); ?></label>
						<input type="text" class="input-text" name="username" id="panel_username" />
					</p>
					<p class="form-row">
						<label for="panel_password"><?php esc_html_e( 'Password', 'konte' ); ?></label>
						<input class="input-text" type="password" name="password" id="panel_password" autocomplete="off" />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<p class="form-row">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" value="forever" />
							<span><?php esc_html_e( 'Remember me', 'konte' ); ?></span>
						</label>
					</p>

					<p class="form-row">
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

						<input type="hidden" name="login" value="<?php esc_attr_e( 'Sign in', 'konte' ); ?>">
						<button type="submit" class="button large" value="<?php esc_attr_e( 'Sign in', 'konte' ); ?>" data-signing="<?php esc_attr_e( 'Siging in...', 'konte' ); ?>" data-signed="<?php esc_attr_e( 'Signed In', 'konte' ); ?>"><?php esc_html_e( 'Sign in', 'konte' ); ?></button>

						<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
							<span class="create-account button large alt"><?php esc_html_e( 'Create An Account', 'konte' ); ?></span>
						<?php endif; ?>
					</p>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

					<p class="lost_password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'konte' ); ?></a>
					</p>

				</form>

				<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

					<form method="post" class="woocommerce-form woocommerce-form-register register" action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" style="display: none;">

						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

							<p class="form-row form-row-wide">
								<label for="panel_reg_username"><?php esc_html_e( 'Username', 'konte' ); ?>
									<span class="required">*</span></label>
								<input type="text" class="input-text" name="username" id="panel_reg_username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
							</p>

						<?php endif; ?>

						<p class="form-row form-row-wide">
							<label for="panel_reg_email"><?php esc_html_e( 'Email address', 'konte' ); ?></label>
							<input type="email" class="input-text" name="email" id="panel_reg_email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
						</p>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

							<p class="form-row form-row-wide">
								<label for="panel_reg_password"><?php esc_html_e( 'Password', 'konte' ); ?></label>
								<input type="password" class="input-text" name="password" id="panel_reg_password" />
							</p>

						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<p class="form-row">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="button large" name="register" value="<?php esc_attr_e( 'Sign up', 'konte' ); ?>"><?php esc_html_e( 'Sign up', 'konte' ); ?></button>
						</p>

						<?php do_action( 'woocommerce_register_form_end' ); ?>

						<p class="already_registered">
							<a href="#" class="login"><?php esc_html_e( 'Already has an account', 'konte' ); ?></a>
						</p>

					</form>

				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'konte_after_site', 'konte_login_panel' );

/**
 * Shopping cart panel.
 */
function konte_cart_panel() {
	$panels = konte_get_theme_prop( 'panels' );

	if ( ! in_array( 'cart', $panels ) ) {
		return;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	?>
	<div id="cart-panel" class="cart-panel offscreen-panel">
		<div class="backdrop"></div>
		<div class="panel">
			<div class="hamburger-menu button-close active">
				<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>

				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
			</div>

			<div class="panel-header">
				<div class="panel__title"><?php esc_html_e( 'Cart', 'konte' ) ?>
					<span class="cart-panel-counter">(<?php echo WC()->cart->get_cart_contents_count(); ?>)</span></div>
			</div>

			<div class="panel-content woocommerce">
				<div class="widget_shopping_cart_content">
					<?php woocommerce_mini_cart(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'konte_after_site', 'konte_cart_panel' );

/**
 * Search modal.
 */
function konte_search_modal() {
	$modals = konte_get_theme_prop( 'modals' );

	if ( ! in_array( 'search', $modals ) ) {
		return;
	}
	?>

	<div id="search-modal" class="search-modal modal">
		<div class="modal-header">
			<div class="modal__title"><?php esc_html_e( 'Search', 'konte' ); ?></div>
			<div class="hamburger-menu button-close active">
				<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
			</div>
		</div>

		<div class="modal-content">
			<div class="search-form konte-container">
				<form method="get" class="instance-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<div class="search-fields">
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'konte' ); ?></span>
						<input type="text" name="s" class="search-field" placeholder="<?php esc_attr_e( 'Search&hellip;', 'konte' ) ?>" autocomplete="off">
						<?php if ( $type = konte_get_option( 'header_search_type' ) ) : ?>
							<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ) ?>">
						<?php endif; ?>

						<span class="spinner"></span>

						<button type="reset" class="search-reset">
							<?php konte_svg_icon( 'icon=close&class=close-icon' ); ?>
						</button>
					</div>
				</form>
			</div>

			<div class="search-result konte-container">
				<p class="label"><?php esc_html_e( 'Search Result', 'konte' ); ?></p>
				<div class="searched-items"></div>
				<div class="view-more">
					<a href="#" class="button alt"><?php esc_html_e( 'View All', 'konte' ) ?></a>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<div class="konte-container">
				<?php konte_search_quicklinks( konte_get_option( 'header_search_type' ) ); ?>
			</div>
		</div>
	</div>

	<?php
}

add_action( 'konte_after_site', 'konte_search_modal' );

/**
 * Add the popup HTML to footer
 *
 * @since 2.0
 */
function konte_popup() {
	if ( ! konte_get_option( 'popup_enable' ) && ! is_customize_preview() ) {
		return;
	}

	if ( konte_is_maintenance_page() ) {
		return;
	}

	$popup_frequency = intval( konte_get_option( 'popup_frequency' ) );

	if ( $popup_frequency > 0 && isset( $_COOKIE['konte_popup'] ) && ! is_customize_preview() ) {
		return;
	}

	get_template_part( 'template-parts/popup/popup' );
}

add_action( 'konte_after_site', 'konte_popup' );

/**
 * Display the "Go to top" button
 */
function konte_gotop_button() {
	if ( ! konte_get_option( 'footer_gotop' ) ) {
		return;
	}

	$button = '<a href="#page" id="gotop" class="gotop">' . konte_svg_icon( 'icon=arrow-down&echo=0' ) . '</a>';

	echo apply_filters( 'konte_gotop_button', $button );
}

add_action( 'konte_after_site', 'konte_gotop_button' );

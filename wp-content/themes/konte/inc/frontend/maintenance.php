<?php
/**
 * Custom functions for the maintenance mode.
 *
 * @package Konte
 */


/**
 * Redirect to the target page if the maintenance mode is enabled.
 */
function konte_maintenance_redirect() {
	if ( ! konte_get_option( 'maintenance_enable' ) ) {
		return;
	}

	if ( current_user_can( 'super admin' ) || current_user_can( 'administrator' ) ) {
		return;
	}

	$mode     = konte_get_option( 'maintenance_mode' );
	$page_id  = konte_get_option( 'maintenance_page' );
	$code     = 'maintenance' == $mode ? 503 : 200;
	$page_url = $page_id ? get_page_link( $page_id ) : '';

	// Use default message.
	if ( ! $page_id || ! $page_url ) {
		if ( 'coming_soon' == $mode ) {
			$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Coming Soon', 'konte' ), esc_html__( 'Our website is under construction. We will be here soon with our new awesome site.', 'konte' ) );
		} else {
			$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Website Under Maintenance', 'konte' ), esc_html__( 'Our website is currently undergoing scheduled maintenance. Please check back soon.', 'konte' ) );
		}

		wp_die( $message, get_bloginfo( 'name' ), array( 'response' => $code ) );
	}

	// Add body classes.
	add_filter( 'body_class', 'konte_maintenance_page_body_class' );

	// Additional check for special page
	$is_page = is_page( $page_id );

	if ( get_option( 'show_on_front' ) == 'page' && $page_id == get_option( 'page_for_posts' ) ) {
		$is_page = $is_page || ( is_home() && ! is_front_page() );
	}

	if ( get_option( 'konte_portfolio' ) && $page_id == get_option( 'konte_portfolio_page_id' ) ) {
		$is_page = $is_page || is_post_type_archive( 'portfolio' );
	}

	if ( class_exists( 'WooCommerce' ) && $page_id == wc_get_page_id( 'shop' ) ) {
		$is_page = $is_page || is_shop();
	}

	// Redirect to the correct page.
	if ( ! $is_page ) {
		wp_redirect( $page_url );
		exit;
	} else {
		if ( ! headers_sent() ) {
			status_header( $code );
		}

		remove_all_actions( 'konte_before_header' );
		remove_all_actions( 'konte_header' );
		remove_all_actions( 'konte_after_header' );

		remove_all_actions( 'konte_before_content_wrapper' );
		remove_all_actions( 'konte_after_content_wrapper' );

		remove_all_actions( 'konte_before_footer' );
		remove_all_actions( 'konte_footer' );
		remove_all_actions( 'konte_after_footer' );

		$layout = konte_get_option( 'maintenance_layout' );

		if ( 'default' != $layout ) {
			add_filter( 'the_content', 'konte_maintenance_page_content' );

			if ( 'fullscreen' == $layout ) {
				add_filter( 'konte_inline_style', 'konte_maintenance_page_background' );
				add_action( 'konte_before_header', 'konte_maintenance_page_header', 1 );
			}
		}
	}
}

add_action( 'template_redirect', 'konte_maintenance_redirect', 1 );

/**
 * Add classes for maintenance mode.
 *
 * @param array $classes
 *
 * @return array
 */
function konte_maintenance_page_body_class( $classes ) {
	if ( ! konte_get_option( 'maintenance_enable' ) ) {
		return $classes;
	}

	if ( current_user_can( 'super admin' ) ) {
		return $classes;
	}

	$classes[] = 'maintenance-mode';

	if ( konte_is_maintenance_page() ) {
		$classes[] = 'maintenance-page';

		if ( ! is_page_template() ) {
			$classes[] = 'maintenance-layout-' . konte_get_option( 'maintenance_layout' );
		}
	}

	return $classes;
}

/**
 * Edit the maintenance page layout.
 *
 * @param string $content
 *
 * @return string
 */
function konte_maintenance_page_content( $content ) {
	$layout = konte_get_option( 'maintenance_layout' );

	if ( 'split' == $layout && ! is_page_template( 'templates/split.php' ) ) {
		$featured = '<div class="split-page-featured"><div class="entry-header"><div class="entry-thumbnail" style="background-image: url(' . esc_url( get_the_post_thumbnail_url( null, 'full' ) ) . ')"></div></div></div>';
		$content  = '<div class="split-page-content"><div class="konte-container"><div class="entry-content">' . $content . '</div></div></div>';

		$content = $featured . $content;
	}

	return $content;
}

/**
 * Set the background image for the maintenance page layout Fullscreen.
 *
 * @param string $css
 *
 * @return string
 */
function konte_maintenance_page_background( $css ) {
	if ( has_post_thumbnail() ) {
		$css .= '.maintenance-page {background-image: url( ' . esc_url( get_the_post_thumbnail_url( null, 'full' ) ) . ' )}';
	}

	return $css;
}

/**
 * Konte
 *
 * @return void
 */
function konte_maintenance_page_header() {
	?>

	<div class="site-header maintenance-header transparent text-<?php echo esc_attr( konte_get_option( 'maintenance_textcolor' ) ) ?>">
		<div class="konte-container-fluid">
			<div class="header-items">
				<?php get_template_part( 'template-parts/header/logo' ); ?>

				<?php
				if ( has_nav_menu( 'socials' ) ) {
					wp_nav_menu( array(
						'theme_location'  => 'socials',
						'container_class' => 'socials-menu ',
						'menu_id'         => 'footer-socials',
						'depth'           => 1,
						'link_before'     => '<span>',
						'link_after'      => '</span>',
					) );
				}
				?>
			</div>
		</div>
	</div>

	<?php
}
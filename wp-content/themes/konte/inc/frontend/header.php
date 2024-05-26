<?php
/**
 * Custom functions that act on header templates
 *
 * @package Konte
 */

/**
 * Adds classes of background and text color to topbar
 *
 * @param array $classes Topbar classes.
 *
 * @return array
 */
function konte_topbar_classes( $classes ) {
	$background = konte_get_option( 'topbar_background' );
	$classes[]  = $background;

	if ( 'dark' == $background ) {
		$classes[] = 'text-light';
	} else {
		$classes[] = 'text-dark';
	}

	return $classes;
}

add_filter( 'konte_topbar_class', 'konte_topbar_classes' );

/**
 * Display header top bar
 */
function konte_topbar() {
	if ( ! konte_get_option( 'topbar' ) ) {
		return;
	}

	get_template_part( 'template-parts/header/topbar' );
}

add_action( 'konte_before_header', 'konte_topbar' );

/**
 * Adds classes of background and text color to header
 *
 * @param array $classes Header classes.
 *
 * @return array
 */
function konte_header_classes( $classes ) {
	if ( is_home() && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) && ( $blog_header_background = get_post_meta( $blog_page_id, 'header_background', true ) ) ) {
		$background = $blog_header_background;

		if ( 'custom' == $background || 'transparent' == $background ) {
			$blog_header_textcolor = get_post_meta( $blog_page_id, 'header_textcolor', true );
			$text_color            = $blog_header_textcolor ? $blog_header_textcolor : '';
		} else {
			$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : '' );
		}

		if ( ! $text_color ) {
			if ( konte_get_option( 'header_background_blog_custom' ) ) {
				$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'header_blog_textcolor' ) );
			} else {
				$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'header_text_color' ) );
			}
		}
	} elseif ( konte_get_option( 'header_background_blog_custom' ) && konte_is_blog_related_pages() ) {
		$background = konte_get_option( 'header_background_blog' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'header_blog_textcolor' ) );
	} elseif ( konte_get_option( 'header_background_shop_custom' ) && function_exists( 'WC' ) && is_woocommerce() ) {
		$background = konte_get_option( 'header_background_shop' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'header_shop_textcolor' ) );
	} else {
		$background = konte_get_option( 'header_background' );
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : konte_get_option( 'header_text_color' ) );
	}

	if ( is_page() && ( $page_background = get_post_meta( get_the_ID(), 'header_background', true ) ) ) {
		$background = $page_background;

		if ( 'custom' == $background || 'transparent' == $background ) {
			$page_text_color = get_post_meta( get_the_ID(), 'header_textcolor', true );
			$text_color      = $page_text_color ? $page_text_color : $text_color;
		} else {
			$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : $text_color );
		}
	}

	if ( is_page_template( 'templates/split.php' ) ) {
		$background = 'transparent';
		$text_color = get_post_meta( get_the_ID(), 'header_textcolor', true );
		$text_color = $text_color ? $text_color : 'dark';
	}

	$classes[] = $background;
	$classes[] = 'text-' . $text_color;

	if ( 'custom' != konte_get_header_layout() ) {
		$classes[] = 'header-prebuild';
		$classes[] = 'header-' . konte_get_header_layout();
	}

	if ( konte_get_option( 'header_transparent_hover' ) ) {
		$classes[] = 'transparent-hover';
	}

	$sticky = konte_get_option( 'header_sticky' );

	if ( $sticky && 'none' != $sticky ) {
		$classes[] = 'header-sticky--' . $sticky;
	}

	return $classes;
}

add_filter( 'konte_header_class', 'konte_header_classes' );

/**
 * Displays header content
 */
function konte_header() {
	$header_layout = konte_get_header_layout();

	if ( 'custom' != $header_layout ) {
		konte_prebuild_header( $header_layout );
	} else {
		// Header main.
		$sections = array(
			'left'   => konte_get_option( 'header_main_left' ),
			'center' => konte_get_option( 'header_main_center' ),
			'right'  => konte_get_option( 'header_main_right' ),
		);

		$classes = array( 'header-main', 'header-contents' );

		konte_set_theme_prop( 'header_section', 'main' );
		konte_header_contents( $sections, array( 'class' => $classes ) );

		// Header bottom.
		$sections = array(
			'left'   => konte_get_option( 'header_bottom_left' ),
			'center' => konte_get_option( 'header_bottom_center' ),
			'right'  => konte_get_option( 'header_bottom_right' ),
		);

		$classes = array( 'header-bottom', 'header-contents' );

		konte_set_theme_prop( 'header_section', 'bottom' );
		konte_header_contents( $sections, array( 'class' => $classes ) );

		// Reset header_section prop.
		konte_set_theme_prop( 'header_section', '' );
	}
}

add_action( 'konte_header', 'konte_header' );

/**
 * Display an empty space for sticky header.
 */
function konte_header_sticky_space() {
	$sticky = konte_get_option( 'header_sticky' );

	if ( ! $sticky || 'none' == $sticky ) {
		return;
	}

	$header_layout = konte_get_header_layout();

	if ( 'custom' != $header_layout ) {
		$sections = konte_get_prebuild_header( $header_layout );
	} else {
		$sections = array(
			'main' => array(
				'left'   => konte_get_option( 'header_main_left' ),
				'center' => konte_get_option( 'header_main_center' ),
				'right'  => konte_get_option( 'header_main_right' ),
			),
			'bottom' => array(
				'left'   => konte_get_option( 'header_bottom_left' ),
				'center' => konte_get_option( 'header_bottom_center' ),
				'right'  => konte_get_option( 'header_bottom_right' ),
			),
		);
	}

	$header_main = array_filter( $sections['main'] );
	$header_bottom = array_filter( $sections['bottom'] );
	?>

	<div class="site-header-space">
		<?php if ( ! empty( $header_main ) ) : ?>
			<div class="header-main"></div>
		<?php endif; ?>

		<?php if ( ! empty( $header_bottom ) ) : ?>
			<div class="header-bottom"></div>
		<?php endif; ?>

		<div class="header-mobile"></div>
	</div>
	<?php
}

add_action( 'konte_after_header', 'konte_header_sticky_space', 1 );

/**
 * Campaign bar
 *
 */
function konte_campaign_bar() {
	if ( ! konte_get_option( 'campaign_bar' ) ) {
		return;
	}

	get_template_part( 'template-parts/header/campaigns' );
}

add_action( 'konte_after_header', 'konte_campaign_bar', 10 );

/**
 * Display blog header
 */
function konte_blog_header() {
	if ( ! konte_has_blog_header() ) {
		return;
	}

	get_template_part( 'template-parts/post/blog-header' );
}

add_action( 'konte_before_content_wrapper', 'konte_blog_header' );

/**
 * Displays the single page header
 */
function konte_single_page_header() {
	if ( ! is_page() || is_archive() ) {
		return;
	}

	if ( is_page_template( 'templates/split.php' ) || is_page_template( 'templates/flex-posts.php' ) ) {
		return;
	}

	$content = get_post_meta( get_the_ID(), 'page_featured_content', true );

	if ( ! $content && ! has_post_thumbnail() ) {
		return;
	}

	get_template_part( 'template-parts/page/page-header' );
}

add_action( 'konte_before_content_wrapper', 'konte_single_page_header' );

/**
 * Change the archive title.
 * Remove the prefix.
 *
 * @param string $title Archive title.
 *
 * @return string
 */
function konte_archive_title( $title ) {
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_home() ) {
		$title = 'page' == get_option( 'show_on_front' ) ? get_the_title( get_option( 'page_for_posts' ) ) : esc_html__( 'Blog', 'konte' );
	} elseif ( is_search() ) {
		$title = esc_html__( 'Search Result', 'konte' );
	}

	return $title;
}

add_filter( 'get_the_archive_title', 'konte_archive_title' );

<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Konte
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'konte_before_site' ) ?>

<div id="page" class="site">

	<?php do_action( 'konte_before_header' ); ?>

	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
		<header id="masthead" class="site-header <?php echo esc_attr( implode( ' ', (array) apply_filters( 'konte_header_class', array() ) ) ); ?>">

			<?php do_action( 'konte_header' ); ?>

		</header><!-- #masthead -->
	<?php endif; ?>

	<?php do_action( 'konte_after_header' ); ?>

	<div id="content" class="site-content">

		<?php do_action( 'konte_before_content_wrapper' ); ?>

		<div class="site-content-container <?php echo esc_attr( apply_filters( 'konte_content_container_class', 'container' ) ) ?>">

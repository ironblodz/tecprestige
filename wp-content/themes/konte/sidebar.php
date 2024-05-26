<?php
/**
 * The sidebar containing the main widget area
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Konte
 */

if ( 'no-sidebar' == konte_get_layout() ) {
	return;
}

$sidebar = konte_get_sidebar_id();

if ( ! is_active_sidebar( $sidebar ) ) {
	return;
}

?>

<aside id="secondary" class="widget-area <?php echo esc_attr( $sidebar ); ?> <?php echo konte_get_option( 'sidebar_sticky' ) ? 'sticky-sidebar' : ''; ?>">
	<?php dynamic_sidebar( $sidebar ); ?>
</aside><!-- #secondary -->

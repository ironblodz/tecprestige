<?php
/**
 * Template part for displaying the custom text element
 *
 * @package Konte
 */

if ( 'main' == konte_get_theme_prop( 'header_section' ) ) {
	$text = konte_get_option( 'header_main_text' );
} elseif ( 'bottom' == konte_get_theme_prop( 'header_section' ) ) {
	$text = konte_get_option( 'header_bottom_text' );
}

if ( empty( $text ) ) {
	return;
}
?>
<div class="header-text">
	<?php echo do_shortcode( wp_kses_post( $text ) ) ?>
</div>

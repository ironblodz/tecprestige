<?php
/**
 * Template part for displaying footer extra content
 *
 * @package Konte
 */
?>

<?php if ( $footer_extra_content = konte_get_option( 'footer_extra_content' ) ) : ?>
	<div class="footer-extra">
		<div class="footer-container <?php echo esc_attr( apply_filters( 'konte_footer_container_class', konte_get_option( 'footer_container' ), 'extra' ) ); ?>">
			<?php echo do_shortcode( wp_kses_post( $footer_extra_content ) ) ?>
		</div>
	</div>
<?php endif; ?>
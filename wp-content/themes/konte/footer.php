<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Konte
 */

?>

</div><!-- .site-content-container -->

<?php do_action( 'konte_after_content_wrapper' ); ?>

</div><!-- #content -->

<?php do_action( 'konte_before_footer' ); ?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
	<footer id="colophon" class="site-footer <?php echo esc_attr( implode( ' ', (array) apply_filters( 'konte_footer_class', array() ) ) ); ?>">

		<?php do_action( 'konte_footer' ) ?>

	</footer><!-- #colophon -->
<?php endif; ?>

<?php do_action( 'konte_after_footer' ); ?>

</div><!-- #page -->

<?php do_action( 'konte_after_site' ) ?>

<?php wp_footer(); ?>

</body>
</html>

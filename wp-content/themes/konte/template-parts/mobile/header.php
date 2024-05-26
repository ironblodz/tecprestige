<?php
/**
 * Template file for displaying mobile header
 *
 * @package Konte
 */
?>

<div class="mobile-menu-hamburger">
	<button class="mobile-menu-toggle hamburger-menu" data-toggle="off-canvas" data-target="mobile-menu" aria-label="<?php esc_attr_e( 'Toggle Menu', 'konte' ) ?>">
		<span class="hamburger-box">
			<span class="hamburger-inner"></span>
		</span>
	</button>
</div>

<?php if ( konte_get_option( 'mobile_custom_logo' ) && ( $logo = konte_get_option( 'mobile_logo' ) ) ) : ?>
	<div class="mobile-logo site-branding">
		<a href="<?php echo esc_url( home_url() ) ?>" class="logo">
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		</a>
	</div>
<?php else : ?>
	<?php get_template_part( 'template-parts/header/logo' ); ?>
<?php endif; ?>

<div class="mobile-header-icons">
	<?php konte_mobile_header_icons(); ?>
</div>

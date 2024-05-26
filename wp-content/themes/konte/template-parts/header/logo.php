<?php
/**
 * Template part for displaying the logo
 *
 * @package Konte
 */

$logo_type = konte_get_option( 'logo_type' );

if ( 'text' == $logo_type ) :
	$logo = konte_get_option( 'logo_text' );
elseif ( 'svg' == $logo_type ) :
	$logo = konte_get_option( 'logo_svg' );
else:
	$logo       = konte_get_option( 'logo' );
	$logo_light = konte_get_option( 'logo_light' );

	if ( ! $logo && ! $logo_light ) {
		$logo       = $logo ? $logo : get_theme_file_uri( '/images/logo.svg' );
		$logo_light = $logo_light ? $logo_light : get_theme_file_uri( '/images/logo-light.svg' );
	} elseif ( ! $logo_light && $logo ) {
		$logo_light = $logo;
	} elseif ( ! $logo && $logo_light ) {
		$logo = $logo_light;
	}

	$dimension = konte_get_option( 'logo_dimension' );
	$width     = ( 0 < intval( $dimension['width'] ) ) ? ' width="' . intval( $dimension['width'] ) . '"' : '';
	$height    = ( 0 < intval( $dimension['width'] ) ) ? ' height="' . intval( $dimension['height'] ) . '"' : '';
endif;
?>
<div class="site-branding">
	<a href="<?php echo esc_url( apply_filters( 'konte_header_logo_link', home_url() ) ) ?>" class="logo">
		<?php if ( 'text' == $logo_type ) : ?>
			<span class="logo-text"><?php echo esc_html( $logo ) ?></span>
		<?php elseif ( 'svg' == $logo_type ) : ?>
			<span class="logo-svg"><?php echo konte_sanitize_svg( $logo ); ?></span>
		<?php else : ?>
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" class="logo-dark" <?php echo trim( $width . $height ); ?>>
			<img src="<?php echo esc_url( $logo_light ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" class="logo-light" <?php echo trim( $width . $height ); ?>>
		<?php endif; ?>
	</a>

	<?php konte_site_branding_title(); ?>
	<?php konte_site_branding_description(); ?>
</div>

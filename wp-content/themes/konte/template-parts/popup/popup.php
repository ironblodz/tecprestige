<?php
/**
 * The template part for displaying the popup.
 *
 * @package Konte
 */
?>

<div id="popup-modal" class="popup-modal modal popup-layout-<?php echo esc_attr( konte_get_option( 'popup_layout' ) ) ?>">
	<div class="backdrop popup-backdrop"></div>
	<div class="modal-content popup-modal-content">
		<div class="hamburger-menu button-close active">
			<span class="menu-text"><?php esc_html_e( 'Close', 'konte' ) ?></span>
			<div class="hamburger-box">
				<div class="hamburger-inner"></div>
			</div>
		</div>

		<div class="popup-image">
			<?php
			if ( $popup_banner = konte_get_option( 'popup_image' ) ) {
				if ( '1-column' == konte_get_option( 'popup_layout' ) ) {
					printf( '<div class="popup-image-holder" style="background-image: url(%s)"></div>', esc_url( $popup_banner ) );
				} else {
					$image_id = attachment_url_to_postid( $popup_banner );

					if ( $image_id ) {
						echo wp_get_attachment_image( $image_id, 'full' );
					} else {
						$image_info = pathinfo( $popup_banner );
						printf( '<img src="%s" alt="%s">', esc_url( $popup_banner ), esc_attr( basename( $popup_banner, '.' . $image_info['extension'] ) ) );
					}
				}
			}
			?>
		</div>

		<div class="popup-content">
			<div class="popup-content-wrapper">
				<?php echo do_shortcode( wp_kses_post( konte_get_option( 'popup_content' ) ) ); ?>
			</div>
		</div>

	</div>
</div>

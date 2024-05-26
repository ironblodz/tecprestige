<?php
/**
 * Template part for displaying footer Instagram feed
 *
 * @package Konte
 */

$limit   = absint( konte_get_option( 'footer_instagram_limit' ) );
$medias  = konte_get_instagram_images( $limit );
$size    = konte_get_option( 'footer_instagram_orginal_size' ) ? 'original': 'cropped';
?>

<div class="footer-instagram footer-instagram--<?php echo esc_attr( $size ) ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'konte_footer_container_class', konte_get_option( 'footer_container' ), 'instagram' ) ); ?>">
		<?php if ( is_wp_error( $medias ) ) : ?>
			<?php echo wp_kses_post( $medias->get_error_message() ); ?>
		<?php elseif ( is_array( $medias ) ) : ?>
			<?php $medias = array_slice( $medias, 0, $limit ); ?>
			<div class="instagram-feed columns-<?php echo esc_attr( konte_get_option( 'footer_instagram_columns' ) ); ?>">
				<ul>
					<?php foreach ( $medias as $index => $media ) : ?>
						<li><?php echo konte_instagram_image( $media, $size ); ?></li>
					<?php endforeach; ?>
				</ul>

				<?php if ( konte_get_option( 'footer_instagram_profile_link' ) ) : ?>
					<?php $instagram_user = konte_get_instagram_user(); ?>
					<a href="<?php echo esc_url( 'https://www.instagram.com/' . $instagram_user['username'] ) ?>" class="profile-link" target="_blank" rel="nofollow">
						<span>@<?php echo esc_html( $instagram_user['username'] ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
/**
 * Template file for displaying post author info
 *
 * @package Konte
 */
?>

<?php if ( get_the_author_meta( 'user_description' ) ) : ?>
	<div class="author-info clearfix">
		<div class="author-vcard">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), 80 ); ?>
				<span class="author-name"><?php echo get_the_author(); ?></span>
			</a>
		</div><!-- .author-avatar -->

		<div class="author-description">
			<?php echo wp_kses_post( get_the_author_meta( 'user_description' ) ); ?>
		</div>

		<div class="author-socials">
			<?php
			$socials = array( 'facebook', 'twitter', 'linkedin', 'instagram', 'pinterest' );
			foreach ( $socials as $social ) {
				$link = get_the_author_meta( $social );

				if ( empty( $link ) ) {
					continue;
				}

				printf(
					'<a href="%s" target="_blank" rel="nofollow"><i class="fa fa-%s" aria-hidden="true"></i></a>',
					esc_url( $link ),
					esc_attr( str_replace( array( 'pinterest' ), array( 'pinterest-p' ), $social ) )
				);
			}
			?>
		</div><!-- .author-description -->
	</div><!-- .author-info -->
<?php endif; ?>

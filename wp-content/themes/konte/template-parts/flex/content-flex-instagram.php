<?php
/**
 * Template part for displaying flex post - type Instagram
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

$design    = get_post_meta( get_the_ID(), 'flex_post_design', true );
$instagram = get_post_meta( get_the_ID(), 'flex_post_instagram', true );
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'flex-post--instagram' ); ?> <?php echo ! empty( $design['css'] ) ? 'style="' . esc_attr( wp_unslash( $design['css'] ) ) . '"' : '' ?>>
	<?php if ( ! empty( $design['background_image'] ) ) : ?>
		<div class="flex-post-background">
			<img src="<?php echo esc_url( $design['background_image'] ); ?>" alt="<?php the_title_attribute(); ?>">
		</div>
	<?php endif; ?>

	<div class="flex-post-content">
		<?php
		$tags = wp_get_post_terms( get_the_ID(), 'flex_post_tag', array( 'fields' => 'names' ) );
		if ( $tags && ! is_wp_error( $tags ) ) {
			echo '<span class="flex-tags" style="' . ( $design['tag_color'] ? 'color:' . esc_attr( $design['tag_color'] ) : '' ) . '">' . implode( ', ', $tags ) . '</span>';
		}

		if ( has_post_thumbnail() ) {
			echo ! empty( $instagram['link'] ) ? '<a href="' . esc_url( $instagram['link'] ) . '" target="_blank" rel="nofollow">' : '';
			the_post_thumbnail( 'full' );
			echo ! empty( $instagram['link'] ) ? '</a>' : '';
		}

		if ( ! empty( $instagram['caption'] ) ) {
			echo '<div class="instagram-caption">' . wp_kses_post( $instagram['caption'] ) . '</div>';
		}

		if ( ! empty( $instagram['user'] ) ) {
			echo '<span class="instagram-username"><i class="fa fa-instagram"></i> @' . ltrim( esc_html( $instagram['user'] ), '@' ) . '</span>';
		}
		?>
	</div>
</div><!-- #post-<?php the_ID(); ?> -->

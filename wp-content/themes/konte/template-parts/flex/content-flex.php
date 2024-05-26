<?php
/**
 * Template part for displaying flex post
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

$design = get_post_meta( get_the_ID(), 'flex_post_design', true );
$type   = get_post_meta( get_the_ID(), 'flex_content_type', true );
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'flex-post--' . ( $type ? $type : 'standard' ) ); ?> <?php echo ! empty( $design['css'] ) ? 'style="' . esc_attr( wp_unslash( $design['css'] ) ) . '"' : '' ?>>
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
		?>

		<?php the_content(); ?>
	</div>
</div><!-- #post-<?php the_ID(); ?> -->

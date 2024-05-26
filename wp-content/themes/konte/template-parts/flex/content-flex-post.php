<?php
/**
 * Template part for displaying flex post - type post
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

$design  = get_post_meta( get_the_ID(), 'flex_post_design', true );
$post_id = get_post_meta( get_the_ID(), 'flex_post_post', true );
$post_id = apply_filters( 'wpml_object_id', $post_id, 'post', false, null );

if ( has_post_thumbnail() ) {
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$ratio     = $thumbnail[1] / $thumbnail[2];

	if ( $ratio >= 1.3 ) {
		$class = 'thumbnail-landscape';
	} elseif ( $ratio <= 0.8 ) {
		$class = 'thumbnail-portrait';
	} else {
		$class = 'thumbnail-square';
	}
}

$thumbnail_id = get_post_thumbnail_id();

if ( ! $thumbnail_id && has_post_thumbnail( $post_id ) ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
}
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'flex-post--post' ); ?> <?php echo ! empty( $design['css'] ) ? 'style="' . esc_attr( wp_unslash( $design['css'] ) ) . '"' : '' ?>>
	<?php if ( ! empty( $design['background_image'] ) ) : ?>
		<div class="flex-post-background">
			<img src="<?php echo esc_url( $design['background_image'] ); ?>" alt="<?php the_title_attribute(); ?>">
		</div>
	<?php endif; ?>

	<div class="flex-post-content">
		<?php
		$tags = wp_get_post_terms( get_the_ID(), 'flex_post_tag', array( 'fields' => 'names' ) );
		if ( $tags && ! is_wp_error( $tags ) ) {
			echo '<span class="flex-tags">' . implode( ', ', $tags ) . '</span>';
		}
		?>
		<?php if ( ! empty( $thumbnail_id ) ) : ?>
			<a class="post-thumbnail" href="<?php the_permalink( $post_id ); ?>" aria-hidden="true">
				<?php echo wp_get_attachment_image( $thumbnail_id, 'konte-post-thumbnail-medium' ); ?>
			</a>
		<?php endif; ?>
		<div class="cat-links" style="<?php echo ! empty( $design['tag_color'] ) ? 'color:' . esc_attr( $design['tag_color'] ) : ''; ?>"><?php the_category( ', ', '', $post_id ); ?></div>
		<h3 class="post-title"><a href="<?php the_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a></h3>
		<div class="post-summary"><?php echo get_the_excerpt( $post_id ); ?></div>
		<a href="<?php the_permalink( $post_id ); ?>" class="read-more">
			<?php esc_html_e( 'Continue Reading', 'konte' ) ?>
			<span class="screen-reader-text"><?php echo get_the_title( $post_id ); ?></span>
		</a>
	</div>
</div><!-- #post-<?php the_ID(); ?> -->

<?php
/**
 * Template part for displaying flex post - type Custom
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

$design = get_post_meta( get_the_ID(), 'flex_post_design', true );
$data   = get_post_meta( get_the_ID(), 'flex_post_custom', true );
$class  = '';

if ( has_post_thumbnail() ) {
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$ratio     = $thumbnail ? ( $thumbnail[1] / $thumbnail[2] ) : 1;

	if ( $ratio >= 1.3 ) {
		$class = 'thumbnail-landscape';
	} elseif ( $ratio <= 0.8 ) {
		$class = 'thumbnail-portrait';
	} else {
		$class = 'thumbnail-square';
	}
}
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'flex-post--custom ' . $class ); ?> <?php echo ! empty( $design['css'] ) ? 'style="' . esc_attr( wp_unslash( $design['css'] ) ) . '"' : '' ?>>
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

		echo ! empty( $data['link'] ) ? '<a href="' . esc_url( $data['link'] ) . '">' : '';

		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'full' );
		}

		the_title( '<h3 class="flex-post-title">', '</h3>' );

		if ( $data['text'] ) {
			echo '<span class="read-more">' . esc_html( $data['text'] ) . '</span>';
		}
		echo ! empty( $data['link'] ) ? '</a>' : '';
		?>
	</div>
</div><!-- #post-<?php the_ID(); ?> -->

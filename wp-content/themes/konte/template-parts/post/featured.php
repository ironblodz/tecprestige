<?php
/**
 * Template part for displaying featured post carousel
 *
 * @package Konte
 */

$post_ids = konte_get_featured_post_ids( array(
	'tag'            => konte_get_option( 'blog_featured_tag' ),
	'posts_per_page' => intval( konte_get_option( 'blog_featured_limit' ) ),
) );

if ( empty( $post_ids ) ) {
	return;
}

$carousel_type = konte_get_option( 'blog_featured_display' );
?>

<div id="featured-content" class="featured-content posts-<?php echo esc_attr( $carousel_type ); ?>">
	<div class="konte-container">
		<div id="featured-content-carousel" class="featured-content-carousel <?php echo esc_attr( $carousel_type ); ?>" data-effect="<?php echo esc_attr( konte_get_option( 'blog_featured_slider_effect' ) ) ?>" <?php echo is_rtl() ? 'dir="rtl"' : ''; ?>>
			<?php foreach ( $post_ids as $post_id ) : ?>
				<?php
				$post_object = get_post( $post_id );
				setup_postdata( $GLOBALS['post'] =& $post_object );
				$thumbnail_url = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '';
				?>

				<div class="featured-item loading" <?php echo ! empty( $thumbnail_url ) ? 'data-lazy="' . esc_attr( $thumbnail_url ) . '"' : ''; ?>>
					<div class="entry-header">
						<div class="cat-links">
							<?php the_category( ', ', '' ); ?>
						</div>

						<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

						<a href="<?php the_permalink(); ?>" class="read-more">
							<?php esc_html_e( 'Continue reading', 'konte' ) ?> <span class="screen-reader-text"><?php the_title() ?></span>
						</a>
					</div>
				</div>

			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</div>
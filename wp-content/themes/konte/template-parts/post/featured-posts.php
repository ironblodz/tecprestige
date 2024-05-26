<?php
/**
 * Template part for displaying featured post grid
 *
 * @package Konte
 */

$limit      = intval( konte_get_option( 'blog_featured_posts_limit' ) );
$css_class  = ( $limit % 3 === 0 ) ? 'col-md-4' : 'col-md-3';
$query_args = array(
	'posts_per_page' => $limit,
	'tag'            => konte_get_option( 'blog_featured_posts_tag' ),
);

if ( ! apply_filters( 'konte_featured_posts_duplicate', false ) && konte_get_option( 'blog_featured_content' ) ) {
	$query_args['post__not_in'] = konte_get_featured_post_ids( array(
		'tag'            => konte_get_option( 'blog_featured_tag' ),
		'posts_per_page' => intval( konte_get_option( 'blog_featured_limit' ) ),
	) );
}

$post_ids = konte_get_featured_post_ids( $query_args );

if ( empty( $post_ids ) ) {
	return;
}
?>

<div id="featured-posts" class="featured-posts">
	<div class="container">
		<h2><?php esc_html_e( 'Featured Posts', 'konte' ) ?></h2>

		<div class="posts row">

			<?php foreach ( $post_ids as $post_id ) : ?>
				<?php
				$post_object = get_post( $post_id );
				setup_postdata( $GLOBALS['post'] =& $post_object );
				?>

				<div class="post col-sm-6 <?php echo esc_attr( $css_class ); ?>">

					<?php konte_post_thumbnail(); ?>

					<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h3>

					<?php konte_entry_posted_on(); ?>

				</div>

			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>

		</div>

		<hr class="divider" />
	</div>
</div>
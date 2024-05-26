<?php
/**
 * Template part for displaying related posts
 *
 * @package Konte
 */

$related_posts = new WP_Query( apply_filters( 'konte_related_posts_args', array(
	'post_type'              => 'post',
	'post_status'            => 'publish',
	'posts_per_page'         => 3,
	'orderby'                => 'rand',
	'category__in'           => wp_get_post_categories(),
	'post__not_in'           => array( get_the_ID() ),
	'no_found_rows'          => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false,
	'cache_results'          => false,
	'ignore_sticky_posts'    => true,
	'suppress_filters'       => false,
) ) );

if ( ! $related_posts->have_posts() ) {
	return;
}
?>

<div class="related-posts">
	<h2><?php esc_html_e( 'You might also like', 'konte' ) ?></h2>

	<div class="posts row">

		<?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>

			<div class="post-<?php the_ID() ?> post col-sm-4 col-md-4">
				<?php konte_post_thumbnail(); ?>
				<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
			</div>

		<?php endwhile; ?>
	</div>
</div>

<?php
wp_reset_postdata();

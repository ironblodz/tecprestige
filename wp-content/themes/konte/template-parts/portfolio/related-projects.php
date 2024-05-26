<?php
/**
 * Template part for displaying related projects
 *
 * @package Konte
 */

$related_projects = new WP_Query( apply_filters( 'konte_related_projects_args', array(
	'post_type'              => 'portfolio',
	'post_status'            => 'publish',
	'posts_per_page'         => 3,
	'orderby'                => 'rand',
	'post__not_in'           => array( get_the_ID() ),
	'no_found_rows'          => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false,
	'cache_results'          => false,
	'ignore_sticky_posts'    => true,
	'suppress_filters'       => false,
	'tax_query'              => array(
		array(
			'taxonomy' => 'portfolio_type',
			'field'    => 'term_id',
			'terms'    => wp_get_post_terms( get_the_ID(), 'portfolio_type', array( 'fields' => 'ids' ) ),
			'operator' => 'IN',
		),
	),
) ) );

if ( ! $related_projects->have_posts() ) {
	return;
}
?>

<div class="related-projects">
	<h2><?php esc_html_e( 'More from Category', 'konte' ) ?></h2>

	<div class="projects">

		<?php while ( $related_projects->have_posts() ) : $related_projects->the_post(); ?>

			<div class="post-<?php the_ID() ?> project">
				<a class="post-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'konte-portfolio-thumbnail' ); ?></a>
				<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
				<?php
				if ( $project_types = get_the_term_list( get_the_ID(), 'portfolio_type', '', ', ', '' ) ) {
					echo '<p class="project-types">' . $project_types . '</p>';
				}
				?>
			</div>

		<?php endwhile; ?>
	</div>
</div>

<?php
wp_reset_postdata();

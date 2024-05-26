<?php
/**
 * The template for displaying a single portfolio project.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Konte
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/portfolio/content-single', konte_get_option( 'project_layout' ) );

				if ( konte_get_option( 'project_navigation' ) ) {
					get_template_part( 'template-parts/portfolio/project-navigation' );
				}

				if ( konte_get_option( 'project_related' ) ) {
					get_template_part( 'template-parts/portfolio/related-projects' );
				}

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();

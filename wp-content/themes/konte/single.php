<?php
/**
 * The template for displaying all single posts
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

				get_template_part( 'template-parts/post/content-single', get_post_type() );

				if ( konte_get_option( 'post_author_box' ) && 'post' == get_post_type() ) {
					get_template_part( 'template-parts/post/biography' );
				}

				if ( konte_get_option( 'post_navigation' ) && 'post' == get_post_type() ) {
					get_template_part( 'template-parts/post/post-navigation' );
				}

				if ( konte_get_option( 'post_related_posts' ) && 'post' == get_post_type() ) {
					get_template_part( 'template-parts/post/related-posts' );
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

<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		if ( is_post_type_archive( 'portfolio' ) || is_tax( get_object_taxonomies( 'portfolio' ) ) ) {
			get_template_part( 'template-parts/archive', 'portfolio' );
		} else {
			get_template_part( 'template-parts/archive' );
		}
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
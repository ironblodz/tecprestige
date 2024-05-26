<?php
/**
 * Template part for displaying projects
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */
?>

<div id="project-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php the_permalink() ?>" rel="bookmark">
		<?php the_post_thumbnail( ( 'masonry' == konte_get_option( 'portfolio_layout' ) ) ? 'konte-portfolio-masonry' : 'konte-portfolio-thumbnail' ); ?>
	</a>
	<header class="project-header">
		<?php the_title( '<h3 class="project-title entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
		<?php if ( has_term( '', 'portfolio_type' ) ) : ?>
			<?php the_terms( get_the_ID(), 'portfolio_type', '<p class="project-types">' , ', ', '</p>' ); ?>
		<?php endif; ?>
	</header><!-- .project-header -->
</div><!-- #project-<?php the_ID(); ?> -->

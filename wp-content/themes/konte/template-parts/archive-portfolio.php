<?php
/**
 * Template part for displaying posts
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */
?>


<?php if ( have_posts() ) : ?>

	<?php do_action( 'konte_portfolio_before_loop' ); ?>

	<div class="portfolio-projects portfolio-projects--<?php echo esc_attr( konte_get_option( 'portfolio_layout' ) ) ?> portfolio-projects--columns-<?php echo esc_attr( konte_get_option( 'portfolio_columns' ) ) ?> posts-wrapper">

		<?php
		/* Start the Loop */
		while ( have_posts() ) : the_post();
			/*
			* Include the Post-Format-specific template for the content.
			* If you want to override this in a child theme, then include a file
			* called content-___.php (where ___ is the Post Format name) and that will be used instead.
			*/
			get_template_part( 'template-parts/portfolio/content', konte_get_option( 'portfolio_layout' ) );
		endwhile;
		?>

	</div>

	<?php do_action( 'konte_portfolio_after_loop' ); ?>

<?php endif; ?>
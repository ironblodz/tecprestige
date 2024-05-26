<?php
/**
 * Template part for displaying single post content
 *
 * @package Konte
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! is_singular( 'post' ) ) : ?>
		<header class="entry-header">
			<?php
			the_title( '<h1 class="entry-title">', '</h1>' );

			if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php konte_entry_meta(); ?>
				</div><!-- .entry-meta -->
			<?php
			endif; ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content( sprintf(
			wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'konte' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'konte' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php konte_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

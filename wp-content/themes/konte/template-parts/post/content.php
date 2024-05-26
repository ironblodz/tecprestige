<?php
/**
 * Template part for displaying posts
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php konte_post_thumbnail(); ?>

	<div class="entry">
		<header class="entry-header">
			<div class="cat-links">
				<?php the_category( ', ' ); ?>
			</div>

			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php konte_entry_meta(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

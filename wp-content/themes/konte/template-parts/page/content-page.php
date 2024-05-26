<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Konte
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ( empty( $GLOBALS['konte']['single_page'] ) || empty( $GLOBALS['konte']['single_page']['featured_image'] ) ) && 'none' != get_post_meta( get_the_ID(), 'page_title_display', true ) ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<?php if ( $subtitle = get_post_meta( get_the_ID(), '_subtitle', true ) ) : ?>
				<h4 class="entry-subtitle"><?php echo wp_kses_post( $subtitle ); ?></h4>
			<?php endif; ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'konte' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
</div><!-- #post-<?php the_ID(); ?> -->

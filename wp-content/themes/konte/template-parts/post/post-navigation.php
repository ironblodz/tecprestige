<?php
/**
 * Template part for displaying post navigation
 *
 * @package Konte
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( ! $next_post && ! $prev_post ) {
	return;
}
?>

<nav class="navigation post-navigation" role="navigation">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'konte' ) ?></h2>
	<div class="nav-titles">
		<?php if ( $prev_post ) : ?>
			<div class="nav-previous"><?php esc_html_e( 'Previous Post', 'konte' ); ?></div>
		<?php endif; ?>
		<?php if ( $next_post ) : ?>
			<div class="nav-next"><?php esc_html_e( 'Next Post', 'konte' ); ?></div>
		<?php endif; ?>
	</div>
	<div class="nav-links">
		<?php if ( $prev_post ) : ?>
			<div class="nav-previous">
				<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
					<?php
					if ( has_post_thumbnail( $prev_post ) ) {
						echo get_the_post_thumbnail( $prev_post, 'konte-post-thumbnail-navigation' );
					}
					echo esc_html( $prev_post->post_title );
					?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ( $next_post ) : ?>
			<div class="nav-next">
				<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
					<?php
					echo esc_html( $next_post->post_title );
					if ( has_post_thumbnail( $next_post ) ) {
						echo get_the_post_thumbnail( $next_post, 'konte-post-thumbnail-navigation' );
					}
					?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</nav>

<?php
/**
 * Template part for displaying portfolio project navigation
 *
 * @package Konte
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( ! $next_post && ! $prev_post ) {
	return;
}
?>

<nav class="navigation post-navigation project-navigation" role="navigation">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Project navigation', 'konte' ) ?></h2>
	<div class="nav-titles">
		<?php if ( $prev_post ) : ?>
			<div class="nav-previous"><?php esc_html_e( 'Previous Project', 'konte' ); ?></div>
		<?php endif; ?>
		<?php if ( $next_post ) : ?>
			<div class="nav-next"><?php esc_html_e( 'Next Project', 'konte' ); ?></div>
		<?php endif; ?>
	</div>
	<div class="nav-links">
		<?php if ( $prev_post ) : ?>
			<div class="nav-previous">
				<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
					<?php
					if ( has_post_thumbnail( $prev_post ) ) {
						echo get_the_post_thumbnail( $prev_post, 'thumbnail' );
					}
					echo '<span class="project-navigation__summary">';
					echo '<span class="project-title">' . esc_html( $prev_post->post_title ) . '</span>';

					$project_types = wp_get_post_terms( $prev_post->ID, 'portfolio_type', array( 'fields' => 'names' ) );

					if ( $project_types && ! is_wp_error( $project_types ) ) {
						echo '<span class="project-types">' . implode( ', ', $project_types ) . '</span>';
					}
					echo '</span>';
					?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ( $next_post ) : ?>
			<div class="nav-next">
				<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
					<?php
					echo '<span class="project-navigation__summary">';
					echo '<span class="project-title">' . esc_html( $next_post->post_title ) . '</span>';

					$project_types = wp_get_post_terms( $next_post->ID, 'portfolio_type', array( 'fields' => 'names' ) );

					if ( $project_types && ! is_wp_error( $project_types ) ) {
						echo '<span class="project-types">' . implode( ', ', $project_types ) . '</span>';
					}
					echo '</span>';

					if ( has_post_thumbnail( $next_post ) ) {
						echo get_the_post_thumbnail( $next_post, 'thumbnail' );
					}
					?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</nav>

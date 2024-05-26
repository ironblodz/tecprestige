<?php
/**
 * Template part for displaying portfolio pagination
 *
 * @package Konte
 */

if ( 'numeric' == konte_get_option( 'portfolio_nav_type' ) ) {
	the_posts_pagination( array(
		'prev_text' => konte_svg_icon( 'icon=left&size=small&echo=0' ) . esc_html__( 'Prev', 'konte' ),
		'next_text' => esc_html__( 'Next', 'konte' ) . konte_svg_icon( 'icon=right&size=small&echo=0' ),
	) );
} else {
	if ( $link = get_next_posts_link( esc_html__( 'Load More', 'konte' ) ) ) {
		$template = '
			<nav class="navigation next-posts-navigation next-projects-navigation">
				<h4 class="screen-reader-text">%s</h4>
				<div class="nav-links">%s</div>
			</nav>';

		printf( $template, esc_html__( 'Next projects navigation', 'konte' ), $link );
	}
}
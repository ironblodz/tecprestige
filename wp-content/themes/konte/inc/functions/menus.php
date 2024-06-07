<?php
/**
 * Custom template tags of nav menus
 *
 * @package Konte
 */

if ( ! function_exists( 'konte_posts_navigation' ) ) :
	/**
	 * Displays posts navigation
	 */
	function konte_posts_navigation( $type = '' ) {
		$type = $type ? $type : konte_get_option( 'blog_nav_type' );

		if ( is_home() && 'loadmore' == $type ) {
			$link = get_next_posts_link( esc_html__( 'Carregar mais', 'konte' ) );

			if ( $link ) {
				$template = '
					<nav class="navigation next-posts-navigation">
						<h4 class="screen-reader-text">%s</h4>
						<div class="nav-links">%s</div>
					</nav>';

				printf( $template, esc_html__( 'Next posts navigation', 'konte' ), $link );
			}
		} else {
			the_posts_pagination( array(
				'prev_text' => konte_svg_icon( 'icon=left&size=small&echo=0' ) . esc_html__( 'Prev', 'konte' ),
				'next_text' => esc_html__( 'Next', 'konte' ) . konte_svg_icon( 'icon=right&size=small&echo=0' ),
			) );
		}
	}
endif;

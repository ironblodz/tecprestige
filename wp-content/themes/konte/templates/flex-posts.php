<?php
/**
 * Template Name: Flex Posts
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div id="flex-posts" class="flex-posts">

				<?php
				if ( is_front_page() ) {
					$paged = max( 1, get_query_var( 'page' ) );
				} else {
					$paged = max( 1, get_query_var( 'paged' ) );
				}

				while ( have_posts() ) : the_post();
					$page_content = get_the_content();
				endwhile;

				if ( get_post_meta( get_the_ID(), 'flex_posts_content_as_title', true ) && 1 === $paged && $page_content ) {
					printf(
						'<div class="flex_post flex-posts-page-title">%s</div>',
						apply_filters( 'the_content', $page_content )
					);
				}

				query_posts( array(
					'post_type'        => 'flex_post',
					'posts_per_page'   => intval( get_post_meta( get_the_ID(), 'flex_posts_per_page', true ) ),
					'paged'            => $paged,
					'orderby'          => 'menu_order date',
					'order'            => 'DESC',
					'suppress_filters' => false,
				) );

				if ( have_posts() ) :

					while ( have_posts() ) : the_post();
						get_template_part( 'template-parts/flex/content-flex', get_post_meta( get_the_ID(), 'flex_content_type', true ) );
					endwhile;

				endif;
				?>

			</div>

			<?php
			konte_posts_navigation( 'loadmore' );

			wp_reset_query();
			wp_reset_postdata();

			if ( has_nav_menu( 'socials' ) ) {
				wp_nav_menu( array(
					'theme_location'  => 'socials',
					'container_id'    => 'sticky-socials',
					'container_class' => 'socials-menu sticky-socials',
					'menu_id'         => 'menu-socials-sticky',
					'depth'           => 1,
					'link_before'     => '<span>',
					'link_after'      => '</span>',
				) );
			}
			?>
			<span class="flex-posts-scroll-down sticky-scrolldown">
				<?php konte_svg_icon( 'icon=arrow-left&class=arrow-left-icon' ) ?>
				<?php esc_html_e( 'Scroll Down', 'konte' ); ?>
			</span>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
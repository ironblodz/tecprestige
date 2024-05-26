<?php
/**
 * Template file for displaying blog header
 *
 * @package Konte
 */
?>
<div id="blog-header" class="blog-header page-header">
	<div class="blog-header-content">
		<div class="container">
			<?php if ( konte_get_option( 'blog_header_search' ) ) : ?>
				<div class="blog-search-form">
					<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
						<label>
							<?php konte_svg_icon( 'icon=search&class=search-icon' ) ?>
							<input type="text" name="s" class="search-field" value="" placeholder="<?php esc_attr_e( 'Search the blog', 'konte' ) ?>">
							<input type="hidden" name="post_type" value="post">
						</label>
					</form>
				</div>
			<?php endif; ?>

			<div class="header-title">
				<span><a href="<?php echo get_post_type_archive_link( 'post' ); ?>"><?php echo wp_kses_post( konte_get_option( 'blog_header_content' ) ) ?></a></span>
			</div>

			<?php if ( konte_get_option( 'blog_header_socials' ) ) : ?>
				<div class="social-icons socials-menu">
					<?php if ( has_nav_menu( 'socials' ) ) : ?>
						<?php
						wp_nav_menu( array(
							'theme_location' => 'socials',
							'container'      => '',
							'depth'          => 1,
							'link_before'    => '<span>',
							'link_after'     => '</span>',
						) );
						?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( konte_get_option( 'blog_header_menu' ) ) : ?>
		<div class="blog-header-menu">
			<div class="container">
				<?php
				if ( has_nav_menu( 'blog' ) ) {
					wp_nav_menu( array(
						'theme_location'  => 'blog',
						'container'       => 'nav',
						'container_class' => 'blog-menu',
						'depth'           => 1,
					) );
				} else {
					echo '<ul class="post-category-list">';
					wp_list_categories( array(
						'order'    => 'DESC',
						'orderby'  => 'count',
						'number'   => 7,
						'title_li' => '',
					) );
					echo '</ul>';
				}
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( is_category() || is_tag() || is_tax( get_object_taxonomies( 'post' ) ) ) : ?>
		<div class="blog-header-main">
			<div class="konte-container">
				<div class="blog-header-title text-<?php echo esc_attr( konte_get_blog_header_text_color() ) ?>">
					<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
					<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

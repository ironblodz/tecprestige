<?php
/**
 * Hooks and functions for blog and other content types
 *
 * @package Konte
 */


/**
 * Change more string at the end of the excerpt
 *
 * @since  1.0
 *
 * @return string
 */
function konte_excerpt_more() {
	return '&hellip;';
}

add_filter( 'excerpt_more', 'konte_excerpt_more' );

/**
 * Change the length of post excerpt
 *
 * @since  1.0
 *
 * @param int $length
 *
 * @return int
 */
function konte_excerpt_length( $length ) {
	$excerpt_length = absint( konte_get_option( 'excerpt_length' ) );

	if ( $excerpt_length > 0 ) {
		return $excerpt_length;
	}

	return $length;
}

add_filter( 'excerpt_length', 'konte_excerpt_length' );

/**
 * Display the single post header
 */
function konte_single_post_header() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	?>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry-thumbnail konte-container">
				<?php the_post_thumbnail( 'full', array( 'loading' => false ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="post-info container">
			<div class="cat-links"><?php the_category( ', ' ); ?></div>
			<h1 class="entry-title"><?php single_post_title() ?></h1>
			<div class="entry-meta">
				<?php konte_entry_meta(); ?>
			</div>
		</div>
	</header>
	<?php
}

add_action( 'konte_before_content_wrapper', 'konte_single_post_header' );


/**
 * Filters the comment form default arguments.
 *
 * @param array $args
 *
 * @return array
 */
function konte_comment_form_defaults( $args ) {
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$html_req  = ( $req ? " required='required'" : '' );

	$args['comment_field'] = '<p class="comment-form-comment">' .
	                         '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required" placeholder="' . esc_attr__( 'Comment', 'konte' ) . '"></textarea></p>';

	$args['fields'] = array(
		'author' => '<p class="comment-form-author">' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245" placeholder="' . esc_attr__( 'Name', 'konte' ) . ( $req ? ' *' : '' ) . '" ' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email">' .
		            '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes" placeholder="' . esc_attr__( 'Email', 'konte' ) . ( $req ? ' *' : '' ) . '" ' . $aria_req . $html_req . ' /></p>',
	);

	return $args;
}

add_filter( 'comment_form_defaults', 'konte_comment_form_defaults' );

/**
 * Display featured content carousel on blog page
 */
function konte_blog_featured_content() {
	if ( ! is_home() || ! konte_get_option( 'blog_featured_content' ) || get_query_var( 'paged' ) > 1 ) {
		return;
	}

	get_template_part( 'template-parts/post/featured', 'content' );
}

add_action( 'konte_before_content_wrapper', 'konte_blog_featured_content', 20 );

/**
 * Display featured content posts grid
 */
function konte_blog_featured_posts() {
	if ( ! is_home() || ! konte_get_option( 'blog_featured_posts' ) || get_query_var( 'paged' ) > 1 ) {
		return;
	}

	get_template_part( 'template-parts/post/featured', 'posts' );
}

add_action( 'konte_before_content_wrapper', 'konte_blog_featured_posts', 30 );

/**
 * Flush caches while updating a post
 *
 * @param int $post_id
 */
function konte_flush_transients( $post_id ) {
	if ( 'post' == get_post_type( $post_id ) ) {
		delete_transient( 'konte_featured_post_ids' );
	}
}

add_action( 'save_post', 'konte_flush_transients' );
add_action( 'wp_trash_post', 'konte_flush_transients' );
add_action( 'before_delete_post', 'konte_flush_transients' );

/**
 * Setup global variable for blog page
 */
function konte_setup_blog_loop_data() {
	if ( ! is_home() ) {
		return;
	}

	konte_set_theme_prop( 'blog', array(
		'layout'   => konte_get_option( 'blog_layout' ),
		'nav_type' => konte_get_option( 'blog_nav_type' ),
		'sidebar'  => konte_get_option( 'layout_default' ),
	) );
}

add_action( 'template_redirect', 'konte_setup_blog_loop_data' );

/**
 * Add a class of blog layout to posts
 *
 * @param array $classes
 * @param array $class
 * @param int   $post_id
 *
 * @return mixed
 */
function konte_blog_post_classes( $classes, $class, $post_id ) {
	if ( ! is_home() || 'post' != get_post_type( $post_id ) || ! is_main_query() ) {
		return $classes;
	}

	global $wp_query, $konte;

	if ( 'classic' == $konte['blog']['layout'] ) {
		$current_post = $wp_query->current_post + 1;

		if ( 'loadmore' == $konte['blog']['nav_type'] ) {
			$paged        = get_query_var( 'paged' );
			$paged        = min( 0, $paged - 1 );
			$current_post += $paged * get_query_var( 'posts_per_page' );
		}

		if ( 0 === $current_post % 4 ) {
			$classes[] = 'post-large';
		}
	} elseif ( 'grid' == $konte['blog']['layout'] ) {
		$classes[] = 'col-sm-6';

		if ( 'no-sidebar' == $konte['blog']['sidebar'] ) {
			$classes[] = 'col-md-4';
		} else {
			if ( ! is_active_sidebar( konte_get_sidebar_id() ) ) {
				$classes[] = 'col-md-4';
			} else {
				$classes[] = 'col-md-6';
			}
		}
	}

	return $classes;
}

add_filter( 'post_class', 'konte_blog_post_classes', 10, 3 );

/**
 * Change the default thumbnail size for classic blog layout
 *
 * @param string $size
 *
 * @return string
 */
function konte_blog_classic_thumbnail_size( $size ) {
	if ( ! is_home() || ! is_main_query() ) {
		return $size;
	}

	global $wp_query, $konte;

	if ( 'classic' != $konte['blog']['layout'] ) {
		return $size;
	}

	$current_post = $wp_query->current_post + 1;

	if ( 'loadmore' == $konte['blog']['nav_type'] ) {
		$paged        = get_query_var( 'paged' );
		$paged        = min( 0, $paged - 1 );
		$current_post += $paged * get_query_var( 'posts_per_page' );
	}

	if ( 0 === $current_post % 4 ) {
		$size = 'konte-post-thumbnail-large';
	}

	return $size;
}

add_filter( 'konte_post_thumbnail_size', 'konte_blog_classic_thumbnail_size' );

/**
 * Filter function allowing svg in the post content.
 *
 * @param array $allowed
 * @param string $context
 *
 * @return array
 */
function konte_kses_post_allowed_html( $allowed, $context ) {
	if ( 'post' != $context ) {
		return $allowed;
	}

	// phpcs:ignore PHPCompatibility.PHP.NewFunctions.array_replace_recursiveFound
	return array_replace_recursive(
		$allowed,
		array(
			'svg'              => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
			),
			'g'                => array( 'fill'  => true ),
			'title'            => array( 'title' => true ),
			'path'             => array( 'd'     => true, 'fill'       => true,  ),
			'use'              => array( 'href'  => true, 'xlink:href' => true,  ),
		)
	);
}

add_filter( 'wp_kses_allowed_html', 'konte_kses_post_allowed_html', 10, 2 );

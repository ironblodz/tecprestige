<?php
/**
 * Custom template tags of post for this theme
 *
 * @package Konte
 */

if ( ! function_exists( 'konte_entry_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and categories.
	 */
	function konte_entry_posted_on() {
		$time_string = sprintf(
			'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date( get_option( 'date_format', 'd.m Y' ) ) )
		);

		$posted_on = is_singular() ? $time_string : '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.
	}
endif;

if ( ! function_exists( 'konte_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and categories.
	 */
	function konte_entry_meta() {
		konte_entry_posted_on();

		if ( konte_get_option( 'post_sharing' ) ) {
			$count = konte_get_share_count( false, is_single() );
			$total = false === $count ? '-' : sprintf( _n( '%s Share', '%s Shares', ( $count ? $count : 1 ), 'konte' ), $count );

			echo '<span class="total-shares ' . ( false === $count ? 'fetching' : '' ) . '" data-post_id="' . esc_attr( get_the_ID() ) . '">' . konte_svg_icon( 'icon=share&size=small&echo=0' ) . '<span class="count">' . $total . '</span></span>';
		}

		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			konte_svg_icon( 'icon=comment&size=small' );
			comments_popup_link();
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'konte_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for post tags.
	 */
	function konte_entry_footer() {
		if ( 'post' != get_post_type() ) {
			return;
		}

		$tags_list = get_the_tag_list( '', ' ' );

		if ( $tags_list ) {
			printf( '<span class="tags-links">%s</span>', $tags_list ); // WPCS: XSS OK.
		}

		if ( is_single() && function_exists( 'konte_addons_share_link' ) && konte_get_option( 'post_sharing' ) && ( $socials = konte_get_option( 'post_sharing_socials' ) ) ) {
			$visible_socials = array_splice( $socials, 0, 3 );
			$whatsapp_number = konte_get_option( 'post_sharing_whatsapp_number' );

			echo '<div class="post-sharing social-share">';

			foreach ( $visible_socials as $social ) {
				echo konte_addons_share_link( $social, array( 'whatsapp_number' => $whatsapp_number ) );
			}

			if ( $socials ) {
				echo '<span class="toggle-socials">';
				konte_svg_icon( 'icon=plus&size=small' );
				echo '<span class="social-list">';

				foreach ( $socials as $social ) {
					echo konte_addons_share_link( $social, array( 'whatsapp_number' => $whatsapp_number ) );
				}

				echo '</span>';
				echo '</span>';
			}

			echo '</div>';
		}
	}
endif;

if ( ! function_exists( 'konte_post_thumbnail' ) ) :
	/**
	 * Show entry thumbnail base on its format
	 *
	 * @since  1.0
	 */
	function konte_post_thumbnail() {
		if ( post_password_required() || ! has_post_thumbnail() ) {
			return;
		}

		$thumbnail_size = apply_filters( 'konte_post_thumbnail_size', 'post-thumbnail' );
		?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( $thumbnail_size, array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );

			switch ( get_post_format() ) {
				case 'video':
					konte_svg_icon( 'icon=video&class=post-format-icon' );
					break;

				case 'gallery':
					konte_svg_icon( 'icon=gallery&class=post-format-icon' );
					break;
			}
			?>
		</a>

		<?php
	}
endif;

/**
 * Get featured posts ids.
 * Use transitent to cache the result.
 * The transient will be deleted when updating a post.
 *
 * @see konte_flush_transients in "inc/frontend/entry.php"
 *
 * @param $args Query args
 *
 * @return array
 */
function konte_get_featured_post_ids( $args = array() ) {
	// Only allow getting by tag.
	if ( empty( $args['tag'] ) ) {
		return false;
	}

	$query_args = wp_parse_args( $args, array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 10,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'cache_results'          => false,
		'ignore_sticky_posts'    => true,
		'suppress_filters'       => false,
	) );

	$query_args['fields'] = 'ids';
	$query_hash           = md5( serialize( $query_args ) );
	$transient_key        = 'konte_featured_post_ids';
	$cache                = (array) get_transient( $transient_key );

	if ( ! isset( $cache[ $query_hash ] ) ) {
		// Allows an early filter for the best performance.
		$ids = apply_filters( 'konte_featured_post_ids_for_cache', null, $query_args );

		if ( ! $ids ) {
			$query = new WP_Query( $query_args );
			$ids   = $query->posts;
		}

		$cache[$query_hash] = $ids;

		set_transient( $transient_key, $cache, DAY_IN_SECONDS );
	}

	return apply_filters( 'konte_get_featured_post_ids', $cache[ $query_hash ] );
}

/**
 * Check if current post relates to blog.
 *
 * @return bool
 */
function konte_is_blog_related_pages() {
	return is_home() || is_singular( 'post' ) || is_category() || is_tax( get_object_taxonomies( 'post' ) );
}

/**
 * Fetch share count from ShareThis API.
 *
 * @param string $url
 *
 * @return array
 */
function konte_fetch_share_count( $url ) {
	$url     = preg_replace( '(^https?://)', '', $url );
	$request = add_query_arg( array( 'url' => $url ), 'https://count-server.sharethis.com/v2.0/get_counts' );
	$data    = wp_remote_get( $request );

	if ( ! $data || is_wp_error( $data ) ) {
		return false;
	}

	$data = wp_remote_retrieve_body( $data );

	return json_decode( $data, true );
}

/**
 * Get post count
 *
 * @param int $post_id
 *
 * @return int
 */
function konte_get_share_count( $post_id = false, $fetch = false ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$data    = get_post_meta( $post_id, '_share_count', true );

	if ( $data && $data['exp'] > strtotime( 'now' ) ) {
		if ( isset( $data['count'] ) ) {
			return $data['count'];
		} elseif ( isset( $data['data']['total'] ) && isset( $data['data']['total']['outbound'] ) ) {
			return $data['data']['total']['outbound'];
		}
	}

	if ( ! $fetch ) {
		return false;
	}

	$url  = get_permalink( $post_id );
	$data = konte_fetch_share_count( $url );

	if ( $data ) {
		$total = 0;

		if ( isset( $data['total'] ) ) {
			$total = $data['total'];
		} elseif ( isset( $data['shares'] ) && $data['shares']['all'] ) {
			$total = $data['shares']['all'];
		}

		update_post_meta( $post_id, '_share_count', array(
			'exp'  => strtotime( '+12 hours' ),
			'count' => $total,
		) );

		return $total;
	}

	return false;
}

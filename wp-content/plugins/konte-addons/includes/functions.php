<?php
/**
 * Functions that used in the theme and plugin.
 */

/**
 * Get the sharing URL of a social
 *
 * @param string $social
 * @param array $args
 *
 * @return string
 */
function konte_addons_share_link( $social, $args = array() ) {
	$url    = '';
	$text   = esc_html__( 'Share on', 'konte-addons' ) . ' ' . ucfirst( $social );
	$icon   = $social;

	switch ( $social ) {
		case 'facebook':
			$url = add_query_arg( array( 'u' => get_permalink() ), 'https://www.facebook.com/sharer.php' );
			break;

		case 'twitter':
			$url = add_query_arg( array( 'url' => get_permalink(), 'text' => get_the_title() ), 'https://twitter.com/intent/tweet' );
			break;

		case 'pinterest';
			$params         = array(
				'description' => get_the_title(),
				'media'       => get_the_post_thumbnail_url( null, 'full' ),
				'url'         => get_permalink(),
			);
			$url            = add_query_arg( $params, 'https://www.pinterest.com/pin/create/button/' );
			$icon           = 'pinterest-p';
			break;

		case 'linkedin':
			$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://www.linkedin.com/shareArticle' );
			break;

		case 'tumblr':
			$url = add_query_arg( array( 'url' => get_permalink(), 'name' => get_the_title() ), 'https://www.tumblr.com/share/link' );
			break;

		case 'reddit':
			$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://reddit.com/submit' );
			break;

		case 'stumbleupon':
			$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://www.stumbleupon.com/submit' );
			$text = esc_html__( 'Share On StumbleUpon', 'konte-addons' );
			break;

		case 'telegram':
			$url = add_query_arg( array( 'url' => get_permalink() ), 'https://t.me/share/url' );
			break;

		case 'whatsapp':
			$params = array( 'text' => urlencode( get_permalink() ) );

			$url = 'https://wa.me/';

			if ( ! empty( $args['whatsapp_number'] ) ) {
				$url .= urlencode( $args['whatsapp_number'] );
			}

			$url = add_query_arg( $params, $url );
			break;

		case 'pocket':
			$url = add_query_arg( array( 'url' => get_permalink(), 'title' => get_the_title() ), 'https://getpocket.com/save' );
			$text = esc_html__( 'Save On Pocket', 'konte-addons' );
			$icon = 'get-pocket';
			break;

		case 'digg':
			$url = add_query_arg( array( 'url' => get_permalink() ), 'https://digg.com/submit' );
			break;

		case 'vk':
			$url = add_query_arg( array( 'url' => get_permalink() ), 'https://vk.com/share.php' );
			break;

		case 'email':
			$url  = 'mailto:?subject=' . get_the_title() . '&body=' . __( 'Check out this site:', 'konte-addons' ) . ' ' . get_permalink();
			$text = esc_html__( 'Share Via Email', 'konte-addons' );
			$icon = 'envelope';
			break;
	}

	if ( ! $url ) {
		return;
	}

	return sprintf(
		'<a href="%s" target="_blank" class="social-share-link %s"><i class="fa fa-%s"></i><span>%s</span></a>',
		esc_url( $url ),
		esc_attr( $social ),
		esc_attr( $icon ),
		$text
	);
}

/**
 * Recursive merge user defined arguments into defaults array.
 *
 * @param array $args
 * @param array $default
 *
 * @return array
 */
function konte_addons_recurse_parse_args( $args, $default = array() ) {
	$args   = (array) $args;
	$result = $default;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $result[ $key ] ) ) {
			$result[ $key ] = konte_addons_recurse_parse_args( $value, $result[ $key ] );
		} else {
			$result[ $key ] = $value;
		}
	}

	return $result;
}

/**
 * Get translated object ID if the WPML plugin is installed
 * Return the original ID if this plugin is not installed
 *
 * @param int    $id            The object ID
 * @param string $type          The object type 'post', 'page', 'post_tag', 'category' or 'attachment'. Default is 'page'
 * @param bool   $original      Set as 'true' if you want WPML to return the ID of the original language element if the translation is missing.
 * @param bool   $language_code If set, forces the language of the returned object and can be different than the displayed language.
 *
 * @return mixed
 */
function konte_addons_get_translated_object_id( $id, $type = 'page', $original = true, $language_code = null ) {
	return apply_filters( 'wpml_object_id', $id, $type, $original, $language_code );
}

/**
 * Get terms array for select control
 *
 * @param string $taxonomy
 * @return array
 */
function konte_addons_get_terms_hierarchy( $taxonomy = 'category', $separator = '-' ) {
	$terms = get_terms( array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'update_term_meta_cache' => false,
	) );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	$taxonomy = get_taxonomy( $taxonomy );

	if ( $taxonomy->hierarchical ) {
		$terms = konte_addons_sort_terms_hierarchy( $terms );
		$terms = konte_addons_flatten_hierarchy_terms( $terms, $separator );
	}

	return $terms;
}

/**
 * Recursively sort an array of taxonomy terms hierarchically.
 *
 * @param array $terms
 * @param integer $parent_id
 * @return array
 */
function konte_addons_sort_terms_hierarchy( $terms, $parent_id = 0 ) {
	$hierarchy = array();

	foreach ( $terms as $term ) {
		if ( $term->parent == $parent_id ) {
			$term->children = konte_addons_sort_terms_hierarchy( $terms, $term->term_id );
			$hierarchy[] = $term;
		}
	}

	return $hierarchy;
}

/**
 * Flatten hierarchy terms
 *
 * @param array $terms
 * @param integer $depth
 * @return array
 */
function konte_addons_flatten_hierarchy_terms( $terms, $separator = '&mdash;', $depth = 0 ) {
	$flatted = array();

	foreach ( $terms as $term ) {
		$children = array();

		if ( ! empty( $term->children ) ) {
			$children = $term->children;
			$term->has_children = true;
			unset( $term->children );
		}

		$term->depth = $depth;
		$term->name = $depth && $separator ? str_repeat( $separator, $depth ) . ' ' . $term->name : $term->name;
		$flatted[] = $term;

		if ( ! empty( $children ) ) {
			$flatted = array_merge( $flatted, konte_addons_flatten_hierarchy_terms( $children, $separator, ++$depth ) );
			$depth--;
		}
	}

	return $flatted;
}

<?php
/**
 * Misc functions for this theme
 *
 * @package Konte
 */

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
function konte_get_translated_object_id( $id, $type = 'page', $original = true, $language_code = null ) {
	return apply_filters( 'wpml_object_id', $id, $type, $original, $language_code );
}

/**
 * Get Instagram images
 *
 * @param int $limit
 *
 * @return array|WP_Error
 */
function konte_get_instagram_images( $limit = 12 ) {
	$access_token = konte_get_option( 'api_instagram_token' );

	if ( empty( $access_token ) ) {
		return new WP_Error( 'instagram_no_access_token', esc_html__( 'No access token', 'konte' ) );
	}

	$user = konte_get_instagram_user();

	if ( ! $user || is_wp_error( $user ) ) {
		return $user;
	}

	$transient_key = 'konte_instagram_photos_' . sanitize_title_with_dashes( $user['username'] . '__' . $limit );
	$images = get_transient( $transient_key );

	if ( false === $images || empty( $images ) ) {
		$images = array();
		$next = false;

		while ( count( $images ) < $limit ) {
			if ( ! $next ) {
				$fetched = konte_fetch_instagram_media( $access_token );
			} else {
				$fetched = konte_fetch_instagram_media( $next );
			}

			if ( is_wp_error( $fetched ) ) {
				break;
			}

			$images = array_merge( $images, $fetched['images'] );
			$next = $fetched['paging'] ? $fetched['paging']['cursors']['after'] : false;

			if ( ! $next ) {
				break;
			}
		}

		if ( ! empty( $images ) ) {
			set_transient( $transient_key, $images, 2 * 3600 ); // Cache for 2 hours.
		}
	}

	if ( ! empty( $images ) ) {
		return $images;
	} else {
		return new WP_Error( 'instagram_no_images', esc_html__( 'Instagram did not return any images.', 'konte' ) );
	}
}

/**
 * Fetch photos from Instagram API
 *
 * @param  string $access_token
 * @return array
 */
function konte_fetch_instagram_media( $access_token ) {
	$url = add_query_arg( array(
		'fields'       => 'id,caption,media_type,media_url,permalink,thumbnail_url',
		'access_token' => $access_token,
	), 'https://graph.instagram.com/me/media' );

	$remote = wp_remote_retrieve_body( wp_remote_get( $url ) );
	$data   = json_decode( $remote, true );
	$images = array();

	if ( ! $data ) {
		return new WP_Error( 'instagram_error', esc_html__( 'No data', 'konte' ) );
	} elseif ( isset( $data['error'] ) ) {
		return new WP_Error( 'instagram_error', $data['error']['message'] );
	} else {
		foreach ( $data['data'] as $media ) {
			$images[] = array(
				'type'    => $media['media_type'],
				'caption' => isset( $media['caption'] ) ? $media['caption'] : $media['id'],
				'link'    => $media['permalink'],
				'images'  => array(
					'thumbnail' => ! empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : $media['media_url'],
					'original'  => $media['media_url'],
				),
			);
		}
	}

	return array(
		'images' => $images,
		'paging' => isset( $data['paging'] ) ? $data['paging'] : false,
	);
}

/**
 * Get user data
 *
 * @return bool|WP_Error|array
 */
function konte_get_instagram_user() {
	$access_token = konte_get_option( 'api_instagram_token' );

	if ( empty( $access_token ) ) {
		return new WP_Error( 'no_access_token', esc_html__( 'No access token', 'konte' ) );
	}

	$transient_key = md5( 'konte_instagram_user_' . $access_token );

	$user = get_transient( $transient_key );

	if ( false === $user ) {
		$url  = add_query_arg( array( 'fields' => 'id,username', 'access_token' => $access_token ), 'https://graph.instagram.com/me' );
		$data = wp_remote_get( $url );
		$data = wp_remote_retrieve_body( $data );

		if ( ! $data ) {
			return new WP_Error( 'no_user_data', esc_html__( 'No user data received', 'konte' ) );
		}

		$user = json_decode( $data, true );

		if ( isset( $user['error'] ) ) {
			return new WP_Error( 'no_user_data', $user['error']['message'] );
		}

		set_transient( $transient_key, $user, MONTH_IN_SECONDS );
	}

	return $user;
}

/**
 * Refresh Instagram Access Token
 */
function konte_refresh_instagram_access_token() {
	$access_token = konte_get_option( 'api_instagram_token' );

	if ( empty( $access_token ) ) {
		return new WP_Error( 'no_access_token', esc_html__( 'No access token', 'konte' ) );
	}

	$data = wp_remote_get( 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $access_token );
	$data = wp_remote_retrieve_body( $data );
	$data = json_decode( $data, true );

	if ( isset( $data['error'] ) ) {
		return new WP_Error( 'access_token_refresh', $data['error']['message'] );
	}

	$new_access_token = $data['access_token'];

	set_theme_mod( 'api_instagram_token', $new_access_token );

	return $new_access_token;
}


if ( ! function_exists( 'konte_instagram_image' ) ) {
	/**
	 * Get the output of an Instagram photo
	 */
	function konte_instagram_image( $media, $size = 'original' ) {
		if ( ! is_array( $media ) ) {
			return;
		}

		$srcset = array(
			$media['images']['thumbnail'] . ' 320w',
			$media['images']['original'] . ' 640w',
			$media['images']['original'] . ' 2x',
		);
		$sizes  = array(
			'(max-width: 1400px) 320px',
			'320px',
		);
		$caption = is_array( $media['caption'] ) && isset( $media['caption']['text'] ) ? $media['caption']['text'] : $media['caption'];

		$image  = sprintf(
			'<img src="%s" alt="%s" srcset="%s" sizes="%s">',
			esc_url( $media['images']['thumbnail'] ),
			esc_attr( $caption ),
			esc_attr( implode( ', ', $srcset ) ),
			esc_attr( implode( ', ', $sizes ) )
		);

		$style = '';

		if ( 'original' != $size ) {
			$style = 'style="background-image: url(' . esc_url( $media['images']['thumbnail'] ) . ')"';
		}

		return sprintf(
			'<a href="%s" target="_blank" rel="nofollow" %s>%s</a>',
			esc_url( $media['link'] ),
			$style,
			$image
		);
	}
}

/**
 * Sanitize SVG code.
 *
 * @param string $svg SVG code.
 *
 * @return string
 */
function konte_sanitize_svg( $svg ) {
	$allowed   = array();
	$whitelist = array(
		'a'              => array( 'class', 'clip-path', 'clip-rule', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'mask', 'opacity', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', 'href', 'xlink:href', 'xlink:title' ),
		'span'           => array( 'class' ),
		'circle'         => array( 'class', 'clip-path', 'clip-rule', 'cx', 'cy', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'mask', 'opacity', 'r', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform' ),
		'clipPath'       => array( 'class', 'clipPathUnits', 'id' ),
		'defs'           => array(),
		'style'          => array( 'type' ),
		'desc'           => array(),
		'ellipse'        => array( 'class', 'clip-path', 'clip-rule', 'cx', 'cy', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'mask', 'opacity', 'requiredFeatures', 'rx', 'ry', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform' ),
		'feGaussianBlur' => array( 'class', 'color-interpolation-filters', 'id', 'requiredFeatures', 'stdDeviation' ),
		'filter'         => array( 'class', 'color-interpolation-filters', 'filterRes', 'filterUnits', 'height', 'id', 'primitiveUnits', 'requiredFeatures', 'width', 'x', 'xlink:href', 'y' ),
		'foreignObject'  => array( 'class', 'font-size', 'height', 'id', 'opacity', 'requiredFeatures', 'style', 'transform', 'width', 'x', 'y' ),
		'g'              => array( 'class', 'clip-path', 'clip-rule', 'id', 'display', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'mask', 'opacity', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', 'font-family', 'font-size', 'font-style', 'font-weight', 'text-anchor' ),
		'image'          => array( 'class', 'clip-path', 'clip-rule', 'filter', 'height', 'id', 'mask', 'opacity', 'requiredFeatures', 'style', 'systemLanguage', 'transform', 'width', 'x', 'xlink:href', 'xlink:title', 'y', ),
		'line'           => array( 'class', 'clip-path', 'clip-rule', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'marker-end', 'marker-mid', 'marker-start', 'mask', 'opacity', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', 'x1', 'x2', 'y1', 'y2', ),
		'linearGradient' => array( 'class', 'id', 'gradientTransform', 'gradientUnits', 'requiredFeatures', 'spreadMethod', 'systemLanguage', 'x1', 'x2', 'xlink:href', 'y1', 'y2', ),
		'marker'         => array( 'id', 'class', 'markerHeight', 'markerUnits', 'markerWidth', 'orient', 'preserveAspectRatio', 'refX', 'refY', 'systemLanguage', 'viewBox', ),
		'mask'           => array( 'class', 'height', 'id', 'maskContentUnits', 'maskUnits', 'width', 'x', 'y' ),
		'metadata'       => array( 'class', 'id' ),
		'path'           => array( 'class', 'clip-path', 'clip-rule', 'd', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'marker-end', 'marker-mid', 'marker-start', 'mask', 'opacity', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', ),
		'pattern'        => array( 'class', 'height', 'id', 'patternContentUnits', 'patternTransform', 'patternUnits', 'requiredFeatures', 'style', 'systemLanguage', 'viewBox', 'width', 'x', 'xlink:href', 'y', ),
		'polygon'        => array( 'class', 'clip-path', 'clip-rule', 'id', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'id', 'class', 'marker-end', 'marker-mid', 'marker-start', 'mask', 'opacity', 'points', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', ),
		'polyline'       => array( 'class', 'clip-path', 'clip-rule', 'id', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'marker-end', 'marker-mid', 'marker-start', 'mask', 'opacity', 'points', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', ),
		'radialGradient' => array( 'class', 'cx', 'cy', 'fx', 'fy', 'gradientTransform', 'gradientUnits', 'id', 'r', 'requiredFeatures', 'spreadMethod', 'systemLanguage', 'xlink:href', ),
		'rect'           => array( 'class', 'clip-path', 'clip-rule', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'height', 'id', 'mask', 'opacity', 'requiredFeatures', 'rx', 'ry', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', 'width', 'x', 'y', ),
		'stop'           => array( 'class', 'id', 'offset', 'requiredFeatures', 'stop-color', 'stop-opacity', 'style', 'systemLanguage', ),
		'svg'            => array( 'class', 'clip-path', 'clip-rule', 'filter', 'id', 'mask', 'preserveaspectRatio', 'requiredfeatures', 'style', 'systemlanguage', 'viewbox', 'width', 'height', 'xmlns', 'xmlns:se', 'xmlns:xlink', 'x', 'y', 'enable-background', ),
		'switch'         => array( 'class', 'id', 'requiredFeatures', 'systemLanguage' ),
		'symbol'         => array( 'class', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'font-family', 'font-size', 'font-style', 'font-weight', 'id', 'opacity', 'preserveAspectRatio', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'transform', 'viewBox', ),
		'text'           => array( 'class', 'clip-path', 'clip-rule', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'font-family', 'font-size', 'font-style', 'font-weight', 'id', 'mask', 'opacity', 'requiredFeatures', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'text-anchor', 'transform', 'x', 'xml:space', 'y', ),
		'textPath'       => array( 'class', 'id', 'method', 'requiredFeatures', 'spacing', 'startOffset', 'style', 'systemLanguage', 'transform', 'xlink:href', ),
		'title'          => array(),
		'style'          => array(),
		'tspan'          => array( 'class', 'clip-path', 'clip-rule', 'dx', 'dy', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'font-family', 'font-size', 'font-style', 'font-weight', 'id', 'mask', 'opacity', 'requiredFeatures', 'rotate', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'systemLanguage', 'text-anchor', 'textLength', 'transform', 'x', 'xml:space', 'y', ),
		'use'            => array( 'class', 'clip-path', 'clip-rule', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'height', 'id', 'mask', 'stroke', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width', 'style', 'transform', 'width', 'x', 'xlink:href', 'y', ),
	);

	foreach ( $whitelist as $tag => $attributes ) {
		$allowed[ $tag ] = array();

		foreach ( $attributes as $attribute ) {
			$allowed[ $tag ][ $attribute ] = true;
		}
	}

	return wp_kses( $svg, $allowed );
}

/**
 * Get Youtube video ID.
 *
 * @see https://gist.github.com/ghalusa/6c7f3a00fd2383e5ef33
 */
function konte_get_youtube_video_id( $video_url ) {
	preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match );

	return $match[1];
}

/**
 * Conditional function to check if current page is the maintenance page.
 *
 * @return bool
 */
function konte_is_maintenance_page() {
	if ( ! konte_get_option( 'maintenance_enable' ) ) {
		return false;
	}

	if ( current_user_can( 'super admin' ) ) {
		return false;
	}

	$page_id = konte_get_option( 'maintenance_page' );

	if ( ! $page_id ) {
		return false;
	}

	return is_page( $page_id );
}

if ( ! function_exists( 'konte_svg_icon' ) ) :
	/**
	 * Return SVG markup.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function konte_svg_icon( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'icon'     => '',
			'size'     => 'normal',
			'class'    => '',
			'title'    => '',
			'desc'     => '',
			'fallback' => '',
			'echo'     => true,
		) );

		// Begin SVG markup.
		$svg = '<svg role="img">';

		// Display the title.
		if ( $args['title'] ) {
			$unique_id = uniqid();
			$svg       .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

			// Display the desc only if the title is already set.
			if ( $args['desc'] ) {
				$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
			}
		}

		$svg .= ' <use href="#' . esc_html( $args['icon'] ) . '" xlink:href="#' . esc_html( $args['icon'] ) . '"></use> ';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback ' . esc_attr( $args['fallback'] ) . '"></span>';
		}

		$svg .= '</svg>';

		$svg  = apply_filters( 'konte_svg_icon_code', $svg, $args );
		$code = $svg ? '<span class="svg-icon icon-' . esc_attr( $args['icon'] ) . ' size-' . esc_attr( $args['size'] ) . ' ' . esc_attr( $args['class'] ) . '">' . $svg . '</span>' : '';
		$code = apply_filters( 'konte_svg_icon', $code, $args );

		if ( ! $args['echo'] ) {
			return $code;
		}

		echo konte_sanitize_svg( $code ); // XSS: ignore.
	}
endif;

if ( ! function_exists( 'konte_remove_empty_p' ) ) :
	/**
	 * Remove empty paragraphs.
	 *
	 * @param string $content
	 * @return string
	 */
	function konte_remove_empty_p( $content ) {
		return preg_replace( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content );
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Adds backwards compatibility for wp_body_open() introduced with WordPress 5.2
	 *
	 * @since 1.6.3
	 * @see https://developer.wordpress.org/reference/functions/wp_body_open/
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * Setup the theme global variable.
 *
 * @param array $args
 */
function konte_setup_theme_prop( $args = array() ) {
	$default = array(
		'panels'      => array(),
		'modals'      => array(),
		'blog'        => array(),
		'single_page' => array(),
		'single_post' => array(),
	);

	if ( isset( $GLOBALS['konte'] ) ) {
		$default = array_merge( $default, $GLOBALS['konte'] );
	}

	$GLOBALS['konte'] = wp_parse_args( $args, $default );
}

/**
 * Reset the global variable.
 */
function konte_reset_theme_prop() {
	unset( $GLOBALS['konte'] );
}

/**
 * Get a propery from the global variable.
 *
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function konte_get_theme_prop( $prop, $default = '' ) {
	konte_setup_theme_prop(); // Ensure the global variable is setup.

	return isset( $GLOBALS['konte'], $GLOBALS['konte'][ $prop ] ) ? $GLOBALS['konte'][ $prop ] : $default;
}

/**
 * Sets a property in the global variable.
 *
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function konte_set_theme_prop( $prop, $value = '' ) {
	if ( ! isset( $GLOBALS['konte'] ) ) {
		konte_setup_theme_prop();
	}

	if ( ! isset( $GLOBALS['konte'][ $prop ] ) ) {
		$GLOBALS['konte'][ $prop ] = $value;

		return;
	}

	if ( is_array( $GLOBALS['konte'][ $prop ] ) ) {
		if ( is_array( $value ) ) {
			$GLOBALS['konte'][ $prop ] = array_merge( $GLOBALS['konte'][ $prop ], $value );
		} else {
			$GLOBALS['konte'][ $prop ][] = $value;
			array_unique( $GLOBALS['konte'][ $prop ] );
		}
	} else {
		$GLOBALS['konte'][ $prop ] = $value;
	}
}

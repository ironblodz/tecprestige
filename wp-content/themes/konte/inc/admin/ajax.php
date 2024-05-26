<?php
/**
 * Handle ajax requests
 */

/**
 * Authenticate a user, confirming the login credentials are valid.
 */
function konte_login_authenticate() {
	check_ajax_referer( 'woocommerce-login', 'security' );

	$creds = array(
		'user_login'    => trim( wp_unslash( $_POST['username'] ) ),
		'user_password' => $_POST['password'],
		'remember'      => isset( $_POST['rememberme'] ),
	);

	// Apply WooCommerce filters
	if ( class_exists( 'WooCommerce' ) ) {
		$validation_error = new WP_Error();
		$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

		if ( $validation_error->get_error_code() ) {
			wp_send_json_error( $validation_error->get_error_message() );
		}

		if ( empty( $creds['user_login'] ) ) {
			wp_send_json_error( esc_html__( 'Username is required.', 'konte' ) );
		}

		// On multisite, ensure user exists on current site, if not add them before allowing login.
		if ( is_multisite() ) {
			$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

			if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
				add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
			}
		}

		$creds = apply_filters( 'woocommerce_login_credentials', $creds );
	}

	$user = wp_authenticate( $creds['user_login'], $creds['user_password'] );

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( $user->get_error_message() );
	} else {
		wp_send_json_success( $user );
	}
}

add_action( 'wp_ajax_nopriv_konte_login_authenticate', 'konte_login_authenticate' );

/**
 * Get total share of given URL.
 */
function konte_get_total_shares() {
	check_ajax_referer( 'konte-fetch-share-count', 'security' );

	$post_id = intval( $_POST['post_id'] );

	if ( empty( $post_id ) ) {
		wp_send_json_error();
		exit;
	}

	$count = konte_get_share_count( $post_id, true );

	if ( false === $count ) {
		wp_send_json_error();
		exit;
	}

	wp_send_json_success( sprintf( _n( '%s Share', '%s Shares', ( $count ? $count : 1 ), 'konte' ), $count ) );
	exit;
}

add_action( 'wp_ajax_konte_get_total_shares', 'konte_get_total_shares' );
add_action( 'wp_ajax_nopriv_konte_get_total_shares', 'konte_get_total_shares' );

/**
 * Add 'monthly' cron interval
 *
 * @param  array $schedules
 * @return array
 */
function konte_add_cron_interval( $schedules ) {
	$schedules['monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => esc_html__( 'Once Monthly', 'konte' ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'konte_add_cron_interval' );

/**
 * Schedule events
 */
function konte_cron_events() {
	if ( ! wp_next_scheduled( 'konte_monthly_tasks' ) ) {
		wp_schedule_event( time(), 'monthly', 'konte_monthly_tasks' );
	}
}

add_action( 'konte_monthly_tasks', 'konte_refresh_instagram_access_token' );
add_action( 'wp', 'konte_cron_events' );

/**
 * Ajax search for header search form.
 */
function konte_ajax_header_search() {
	$term      = $_POST['s'];
	$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'any';
	$result    = array(
		'products' => array(),
		'posts'    => array(),
		'other'    => array(),
		'total'    => 0,
	);

	// Handle this search in Konte_WooCommerce_Template class.
	if ( 'product' == $post_type ) {
		$result['products'] = apply_filters( 'konte_ajax_header_search_products', array(), $term );
	} else {
		$query = new WP_Query( array(
			's'              => $term,
			'post_type'      => $post_type,
			'orderby'        => 'relevance',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		) );

		while ( $query->have_posts() ) {
			$query->the_post();

			if ( 'product' == get_post_type() ) {
				$result['products'][] = apply_filters( 'konte_ajax_header_search_product_item', '' );
			} else {
				$item = '
					<li class="' . esc_attr( get_post_type() ) . '">
						<a href="' . esc_url( get_permalink() ) . '">' .
							( has_post_thumbnail() ? get_the_post_thumbnail( null, 'thumbnail' ) : '' ) . '
							<span class="post-title">' . get_the_title() . '</span>' . '
						</a>
					</li>';

				if ( 'post' == get_post_type() ) {
					$result['posts'][] = $item;
				} else {
					$result['other'][] = $item;
				}
			}
		}
	}

	$products = implode( '', $result['products'] );
	$posts    = implode( '', $result['posts'] );
	$other    = implode( '', $result['other'] );

	$html = $products . $posts . $other;

	if ( $html ) {
		$search_link = get_search_link( $term );

		if ( 'any' != $post_type ) {
			$search_link = add_query_arg( 'post_type', $post_type, $search_link );
		}

		$html .= '<li class="view-more-results"><a href="' . esc_url( $search_link ) . '">' . esc_html__( 'View more', 'konte' ) . '</a></li>';
		$html = '<ul>' . $html . '</ul>';
	} else {
		$html = '<p class="not_found_message">' . esc_html__( 'No result found', 'konte' ) . '</p>';
	}

	wp_send_json_success( $html );
	exit;
}

add_action( 'wp_ajax_konte_header_search', 'konte_ajax_header_search' );
add_action( 'wp_ajax_nopriv_konte_header_search', 'konte_ajax_header_search' );

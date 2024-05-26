<?php
/**
 * Template tags for breadcrumbs
 */

/**
 * Breadcrumbs class
 */
class Konte_Breadcrumbs {
	/**
	 * Display the breadcrumbs
	 *
	 * @param array $args
	 */
	public static function breadcrumbs( $args = array() ) {
		if ( function_exists('yoast_breadcrumb') && class_exists( 'WPSEO_Options' ) ) {
			if ( WPSEO_Options::get( 'breadcrumbs-enable', false ) ) {
				return yoast_breadcrumb( '<nav class="breadcrumbs">','</nav>' );
			}
		} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) && \RankMath\Helpers\Conditional::is_breadcrumbs_enabled() ) {
			add_filter( 'rank_math/frontend/breadcrumb/args', array( __CLASS__, 'rankmath_breadcrumb_args' ) );
			return rank_math_the_breadcrumbs();
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			woocommerce_breadcrumb();
		} else {
			echo self::get_breadcrumbs( $args );
		}
	}

	/**
	 * Get breadcrumb HTML
	 *
	 * @param array $args
	 * @return string
	 */
	public static function get_breadcrumbs( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'separator'         => konte_svg_icon( 'icon=arrow-breadcrumb&echo=0&class=delimiter' ),
			'home_class'        => 'home',
			'before'            => '<nav class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">',
			'after'             => '</nav>',
			'before_item'       => '',
			'after_item'        => '',
			'taxonomy'          => 'category',
			'display_last_item' => true,
			'show_on_front'     => false,
			'labels'            => array(
				'home'      => esc_html__( 'Home', 'konte' ),
				'archive'   => esc_html__( 'Archives', 'konte' ),
				'blog'      => esc_html__( 'Blog', 'konte' ),
				'search'    => esc_html__( 'Search results for', 'konte' ),
				'not_found' => esc_html__( 'Not Found', 'konte' ),
				'author'    => esc_html__( 'Author Archives:', 'konte' ),
				'day'       => esc_html__( 'Daily Archives:', 'konte' ),
				'month'     => esc_html__( 'Monthly Archives:', 'konte' ),
				'year'      => esc_html__( 'Yearly Archives:', 'konte' ),
			),
		) );

		$args = apply_filters( 'konte_breadcrumbs_args', $args );

		if ( is_front_page() && ! $args['show_on_front'] ) {
			return;
		}

		$items    = array();
		$position = 1;

		// HTML template for each item
		$item_tpl      = $args['before_item'] . '
			<span itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement">
				<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>
				<meta itemprop="position" content="%d" />
			</span>
		' . $args['after_item'];
		$item_text_tpl = $args['before_item'] . '
			<span itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement">
				<span itemprop="name">%s</span>
				<meta itemprop="position" content="%d" />
			</span>' . $args['after_item'];

		// Home
		if ( ! $args['home_class'] ) {
			$items[] = sprintf( $item_tpl, get_home_url(), $args['labels']['home'], $position++ );
		} else {
			$items[] = sprintf(
				'%s<span class="%s" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement">
					<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>
					<meta itemprop="position" content="%d" />
				</span>%s',
				$args['before_item'],
				$args['home_class'],
				get_home_url(),
				$args['labels']['home'],
				$position++,
				$args['after_item']
			);
		}

		// Front page
		if ( is_front_page() ) {
			$items   = array();
			$position = 1;
			$items[] = sprintf( $item_text_tpl, $args['labels']['home'], $position++ );
		} // Blog
		elseif ( is_home() && ! is_front_page() ) {
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['blog'],
				$position++
			);
		} // Single
		elseif ( is_single() ) {
			if ( 'post' == get_post_type( get_the_ID() ) && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) ) {
				$items[] = sprintf( $item_tpl, get_page_link( $blog_page_id ), get_the_title( $blog_page_id ), $position++ );
			}

			// Terms
			$taxonomy = $args['taxonomy'];
			$terms    = get_the_terms( get_the_ID(), $taxonomy );

			if ( $terms ) {
				$term    = current( $terms );
				$terms   = self::get_term_parents( $term->term_id, $taxonomy );
				$terms[] = $term->term_id;

				foreach ( $terms as $term_id ) {
					$term    = get_term( $term_id, $taxonomy );
					$items[] = sprintf( $item_tpl, get_term_link( $term, $taxonomy ), $term->name, $position++ );
				}
			}

			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title(), $position++ );
			}
		} // Page
		elseif ( is_page() ) {
			$pages = self::get_post_parents( get_queried_object_id() );
			foreach ( $pages as $page ) {
				$items[] = sprintf( $item_tpl, get_permalink( $page ), get_the_title( $page ), $position++ );
			}
			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title(), $position++ );
			}
		} // Taxonomy
		elseif ( is_tax() || is_category() || is_tag() ) {
			$current_term = get_queried_object();

			if ( $current_term ) {
				if ( 'category' == $current_term->taxonomy && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) ) {
					$items[] = sprintf( $item_tpl, get_page_link( $blog_page_id ), get_the_title( $blog_page_id ), $position++ );
				}

				if ( is_object_in_taxonomy( 'portfolio', $current_term->taxonomy ) && ( $portfolio_page_id = get_option( 'konte_portfolio_page_id' ) ) ) {
					$items[] = sprintf( $item_tpl, get_page_link( $portfolio_page_id ), get_the_title( $portfolio_page_id ), $position++ );
				}

				$terms = self::get_term_parents( get_queried_object_id(), $current_term->taxonomy );
				if ( $terms ) {
					foreach ( $terms as $term_id ) {
						$term    = get_term( $term_id, $current_term->taxonomy );
						$items[] = sprintf( $item_tpl, get_category_link( $term_id ), $term->name, $position++ );
					}
				}

				if ( $args['display_last_item'] ) {
					$items[] = sprintf( $item_text_tpl, $current_term->name, $position++ );
				}
			}

		} // Search
		elseif ( is_search() ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['search'] . ' &quot;' . get_search_query() . '&quot;', $position++ );
		} // 404
		elseif ( is_404() ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['not_found'], $position++ );
		} // Author archive
		elseif ( is_author() ) {
			// Queue the first post, that way we know what author we're dealing with (if that is the case).
			the_post();
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['author'] . ' <span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>',
				$position++
			);
			rewind_posts();
		} // Day archive
		elseif ( is_day() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html( '%s %s' ), $args['labels']['day'], get_the_date() ),
				$position++
			);
		} // Month archive
		elseif ( is_month() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html( '%s %s' ), $args['labels']['month'], get_the_date( 'F Y' ) ),
				$position++
			);
		} // Year archive
		elseif ( is_year() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html( '%s %s' ), $args['labels']['year'], get_the_date( 'Y' ) ),
				$position++
			);
		} // Archive
		else {
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['archive'],
				$position++
			);
		}

		return $args['before'] . implode( $args['separator'], $items ) . $args['after'];
	}

	/**
	 * Searches for term parents' IDs of hierarchical taxonomies, including current term.
	 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
	 * Modified from Hybrid Framework
	 *
	 * @param int|string    $term_id  The term ID
	 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
	 *
	 * @return array Array of parent terms' IDs.
	 */
	public static function get_term_parents( $term_id = '', $taxonomy = 'category' ) {
		// Set up some default arrays.
		$list = array();

		// If no term ID or taxonomy is given, return an empty array.
		if ( empty( $term_id ) || empty( $taxonomy ) ) {
			return $list;
		}

		do {
			$list[] = $term_id;

			// Get next parent term
			$term    = get_term( $term_id, $taxonomy );
			$term_id = $term->parent;
		} while ( $term_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}

	/**
	 * Gets parent posts' IDs of any post type, include current post
	 *
	 * @param int|string $post_id ID of the post whose parents we want.
	 *
	 * @return array Array of parent posts' IDs.
	 */
	public static function get_post_parents( $post_id = '' ) {
		// Set up some default array.
		$list = array();

		// If no post ID is given, return an empty array.
		if ( empty( $post_id ) ) {
			return $list;
		}

		do {
			$list[] = $post_id;

			// Get next parent post
			$post    = get_post( $post_id );
			$post_id = $post->post_parent;
		} while ( $post_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}

	/**
	 * Add the class 'breadcrumbs' to the RankMath SEO's breadcrumb.
	 */
	public static function rankmath_breadcrumb_args( $args ) {
		$args['wrap_before'] = str_replace( ' class="', ' class="breadcrumbs ', $args['wrap_before'] );

		return $args;
	}
}

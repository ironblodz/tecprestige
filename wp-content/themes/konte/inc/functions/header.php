<?php
/**
 * Custom template tags of header
 *
 * @package Konte
 */

if ( ! function_exists( 'konte_topbar_items' ) ) :
	/**
	 * Display topbar items
	 */
	function konte_topbar_items( $items ) {
		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( konte_topbar_items_option() );

			switch ( $item['item'] ) {
				case 'menu':
					if ( has_nav_menu( 'topbar' ) ) {
						wp_nav_menu( array(
							'container'      => 'nav',
							'container_id'   => 'topbar-menu',
							'theme_location' => 'topbar',
							'menu_class'     => 'nav-menu topbar-menu menu',
						) );
					}
					break;

				case 'currency':
					konte_currency_switcher();
					break;

				case 'language':
					konte_language_switcher();
					break;

				case 'text':
					echo '<div class="topbar-text">' . do_shortcode( wp_kses_post( konte_get_option( 'topbar_text' ) ) ) . '</div>';
					break;

				case 'close':
					konte_svg_icon( 'icon=close&class=close-topbar&title=' . esc_html__( 'Close', 'konte' ) );
					break;

				case 'socials':
					if ( has_nav_menu( 'socials' ) ) {
						wp_nav_menu( array(
							'container'       => 'div',
							'container_class' => 'topbar-socials-menu socials-menu',
							'theme_location'  => 'socials',
							'menu_class'      => 'menu',
						) );
					}
					break;

				default:
					do_action( 'konte_header_topbar_item', $item['item'] );
					break;
			}
		}
	}
endif;

if ( ! function_exists( 'konte_header_items' ) ) :
	/**
	 * Display header items
	 */
	function konte_header_items( $items ) {
		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( konte_header_items_option() );
			$template_file = $item['item'];

			switch ( $item['item'] ) {
				case 'hamburger':
					konte_set_theme_prop( 'panels', $item['item'] );
					break;

				case 'cart':
					if ( ! class_exists( 'WooCommerce' ) ) {
						$template_file = '';
						break;
					}

					if ( 'panel' == konte_get_option( 'header_cart_behaviour' ) ) {
						konte_set_theme_prop( 'panels', $item['item'] );
					}

					break;

				case 'wishlist':
					if ( ! class_exists( 'Konte_WooCommerce_Template_Wishlist' ) ) {
						$template_file = '';
						break;
					}
					break;

				case 'search':
					if ( 'icon-modal' == konte_get_option( 'header_search_style' )  ) {
						konte_set_theme_prop( 'modals', $item['item'] );
					}
					break;

				case 'account':
					if ( 'panel' == konte_get_option( 'header_account_behaviour' ) ) {
						konte_set_theme_prop( 'panels', $item['item'] );
					}
					break;

				case 'language':
					$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 1 ) );

					if ( empty( $languages ) ) {
						$template_file = '';
					}
					break;
			}

			if ( $template_file ) {
				get_template_part( 'template-parts/header/' . $template_file );
			}
		}
	}
endif;

if ( ! function_exists( 'konte_header_contents' ) ) :
	/**
	 * Display header items
	 */
	function konte_header_contents( $sections, $atts = array() ) {
		if ( false == array_filter( $sections ) ) {
			return;
		}

		$classes = array();
		if ( isset( $atts['class'] ) ) {
			$classes = (array) $atts['class'];
			unset( $atts['class'] );
		}

		if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
			unset( $sections['left'] );
			unset( $sections['right'] );
		}

		if ( ! empty( $sections['center'] ) ) {
			$classes[]    = 'has-center';
			$center_items = wp_list_pluck( $sections['center'], 'item' );

			if ( in_array( 'logo', $center_items ) ) {
				$classes[] = 'logo-center';
			}

			if ( in_array( 'menu-primary', $center_items ) || in_array( 'menu-secondary', $center_items ) ) {
				$classes[] = 'menu-center';
			}

			if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
				$classes[] = 'no-sides';
			}
		} else {
			$classes[] = 'no-center';
			unset( $sections['center'] );

			if ( empty( $sections['left'] ) ) {
				unset( $sections['left'] );
			}

			if ( empty( $sections['right'] ) ) {
				unset( $sections['right'] );
			}
		}

		$attr = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';

		foreach ( $atts as $name => $value ) {
			$attr .= ' ' . $name . '="' . esc_attr( $value ) . '"';
		}
		?>
		<div <?php echo ! empty( $attr ) ? $attr : ''; ?>>
			<div class="<?php echo esc_attr( apply_filters( 'konte_header_container_class', 'konte-container-fluid' ) ); ?> site-header__container">
				<?php foreach ( $sections as $section => $items ) : ?>
					<?php
					$class      = '';
					$item_names = wp_list_pluck( $items, 'item' );

					if ( in_array( 'menu-primary', $item_names ) || in_array( 'menu-secondary', $item_names ) ) {
						$class = 'has-menu';
					}
					?>

					<div class="header-<?php echo esc_attr( $section ); ?>-items header-items <?php echo esc_attr( $class ) ?>">
						<?php konte_header_items( $items ); ?>
					</div>

				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'konte_prebuild_header' ) ) :
	/**
	 * Display pre-build header
	 *
	 * @param string $version
	 */
	function konte_prebuild_header( $version = 'v1' ) {
		$sections = konte_get_prebuild_header( $version );

		$classes = array( 'header-main', 'header-contents' );
		konte_set_theme_prop( 'header_section', 'main' );
		konte_header_contents( $sections['main'], array( 'class' => $classes ) );

		$classes = array( 'header-bottom', 'header-contents' );
		konte_set_theme_prop( 'header_section', 'bottom' );
		konte_header_contents( $sections['bottom'], array( 'class' => $classes ) );

		// Reset header_section prop.
		konte_set_theme_prop( 'header_section', '' );
	}
endif;

if ( ! function_exists( 'konte_get_prebuild_header' ) ) :
	/**
	 * Display pre-build header
	 *
	 * @param string $version
	 */
	function konte_get_prebuild_header( $version = 'v1' ) {
		switch ( $version ) {
			case 'v1':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'search' ),
					),
					'center' => array(
						array( 'item' => 'menu-primary' ),
						array( 'item' => 'logo' ),
						array( 'item' => 'menu-secondary' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v2':
				$main_sections   = array(
					'left'  => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'logo' ),
						array( 'item' => 'menu-primary' ),
					),
					'right' => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'wishlist' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v3':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'menu-primary' ),
					),
					'center' => array(
						array( 'item' => 'logo' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'wishlist' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v4':
				$main_sections   = array(
					'left'  => array(
						array( 'item' => 'logo' ),
						array( 'item' => 'menu-primary' ),
					),
					'right' => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'cart' ),
						array( 'item' => 'hamburger' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v5':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'search' ),
					),
					'center' => array(
						array( 'item' => 'logo' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v6':
				$main_sections   = array(
					'left'  => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'logo' ),
					),
					'right' => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'wishlist' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v7':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
					),
					'center' => array(
						array( 'item' => 'menu-primary' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			case 'v8':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'search' ),
					),
					'center' => array(
						array( 'item' => 'logo' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array(
					'center' => array(
						array( 'item' => 'menu-primary' ),
					),
				);
				break;

			case 'v9':
				$main_sections   = array(
					'center' => array(
						array( 'item' => 'logo' ),
					),
				);
				$bottom_sections = array(
					'left'   => array(
						array( 'item' => 'hamburger' ),
						array( 'item' => 'search' ),
					),
					'center' => array(
						array( 'item' => 'menu-primary' ),
					),
					'right'  => array(
						array( 'item' => 'account' ),
						array( 'item' => 'cart' ),
					),
				);
				break;

			case 'v10':
				$main_sections   = array(
					'left'  => array(
						array( 'item' => 'logo' ),
						array( 'item' => 'menu-primary' ),
					),
					'right' => array(
						array( 'item' => 'account' ),
						array( 'item' => 'search' ),
						array( 'item' => 'cart' ),
					),
				);
				$bottom_sections = array();
				break;

			default:
				$main_sections   = array();
				$bottom_sections = array();
				break;
		}

		return apply_filters( 'konte_prebuild_header', array( 'main' => $main_sections, 'bottom' => $bottom_sections ), $version );
	}
endif;

if ( ! function_exists( 'konte_has_blog_header' ) ) :
	/**
	 * Check if current page has blog header
	 *
	 * @return bool
	 */
	function konte_has_blog_header() {
		if ( ! konte_get_option( 'blog_header' ) ) {
			return false;
		}

		$allowed = (array) konte_get_option( 'blog_header_display' );

		if ( empty( $allowed ) ) {
			return false;
		}

		if ( ! is_home() && ! is_category() && ! is_tag() && ! is_tax() && ! is_singular( 'post' ) ) {
			return false;
		}

		if ( is_home() && ! in_array( 'blog', $allowed ) ) {
			return false;
		}
		if ( is_singular( 'post' ) && ! in_array( 'post', $allowed ) ) {
			return false;
		} elseif ( is_category() && ! in_array( 'category', $allowed ) ) {
			return false;
		} elseif ( is_tag() && ! in_array( 'post_tag', $allowed ) ) {
			return false;
		} elseif ( is_tax() && ! in_array( get_queried_object()->taxonomy, $allowed ) ) {
			return false;
		}

		return true;
	}
endif;

if ( ! function_exists( 'konte_get_blog_header_image' ) ) :
	/**
	 * Get blog header image URL
	 *
	 * @return string
	 */
	function konte_get_blog_header_image() {
		$image = konte_get_option( 'blog_header_image' );

		if ( is_category() || is_tag() || is_tax() ) {
			$image_id = get_term_meta( get_queried_object()->term_id, 'page_header_image_id', true );

			$image = $image_id ? wp_get_attachment_url( $image_id ) : $image;
		}

		return $image;
	}
endif;

if ( ! function_exists( 'konte_get_blog_header_text_color' ) ) :
	/**
	 * Get blog header text color
	 *
	 * @return string
	 */
	function konte_get_blog_header_text_color() {
		$color = konte_get_option( 'blog_header_text_color' );

		if ( is_tax( get_object_taxonomies( 'post' ) ) ) {
			$custom_color = get_term_meta( get_queried_object()->term_id, 'page_header_text_color', true );

			$color = $custom_color ? $custom_color : $color;
		}

		return $color;
	}
endif;

if ( ! function_exists( 'konte_search_quicklinks' ) ) :
	/**
	 * Display search quick links.
	 */
	function konte_search_quicklinks( $post_type = 'product' ) {
		if ( ! konte_get_option( 'header_search_quick_links' ) ) {
			return;
		}

		$links = apply_filters( 'konte_search_quicklinks', konte_get_option( 'header_search_links' ) );

		if ( empty( $links ) ) {
			return;
		}
		?>
		<div class="quick-links">
			<p class="label"><?php esc_html_e( 'Quick Links', 'konte' ); ?></p>

			<ul class="links">
				<?php
				foreach ( $links as $link ) {
					$url = $link['url'];

					if ( ! $url ) {
						$query = array( 's' => $link['text'] );

						if ( $post_type ) {
							$query['post_type'] = $post_type;
						}

						$url = add_query_arg( $query, home_url( '/' ) );
					}

					printf(
						'<li><a href="%s" class="underline-hover">%s</a>',
						esc_url( $url ),
						esc_html( $link['text'] )
					);
				}
				?>
			</ul>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'konte_mobile_header_icons' ) ) :
	/**
	 * Display mobile header icons
	 */
	function konte_mobile_header_icons() {
		$icons = konte_get_option( 'mobile_header_icons' );

		if ( empty( $icons ) ) {
			return;
		}

		foreach ( $icons as $icon ) {
			$icon['item'] = $icon['item'] ? $icon['item'] : key( konte_mobile_header_icons_option() );

			switch ( $icon['item'] ) {
				case 'cart':
					if ( 'panel' == konte_get_option( 'header_cart_behaviour' ) ) {
						konte_set_theme_prop( 'panels', 'cart' );
					}

					get_template_part( 'template-parts/header/cart' );
					break;

				case 'wishlist':
					get_template_part( 'template-parts/header/wishlist' );
					break;

				case 'search':
					konte_set_theme_prop( 'modals', 'search' );
					get_template_part( 'template-parts/mobile/header-search' );
					break;

				case 'account':
					konte_set_theme_prop( 'panels', 'account' );
					get_template_part( 'template-parts/mobile/header-account' );
					break;

				default:
					do_action( 'konte_mobile_header_icon', $icon['item'] );
					break;
			}
		}
	}
endif;

if ( ! function_exists( 'konte_campaign_item' ) ) :
	/**
	 * Display campaign bar item.
	 */
	function konte_campaign_item( $args ) {
		$args = wp_parse_args( $args, array(
			'tag'     => '',
			'text'    => '',
			'button'  => esc_html__( 'Shop Now', 'konte' ),
			'link'    => '#',
			'layout'  => 'inline',
			'image'   => '',
			'bgcolor' => '',
			'border'  => true,
			'color'   => 'dark',
		) );

		$css_class = array(
			'konte-promotion',
			'konte-promotion--' . $args['layout'],
			'layout-' . $args['layout'],
			'text-' . $args['color'],
		);

		if ( ! empty( $args['border'] ) ) {
			$css_class[] = 'konte-promotion--has-border';
		}

		$style = '';

		if ( ! empty( $args['bgcolor'] ) ) {
			$style .= 'background-color:' . $args['bgcolor'] . ';';
		}

		if ( ! empty( $args['image'] ) ) {
			$image = is_numeric( $args['image'] ) ? wp_get_attachment_image_url( $args['image'], 'full' ) : $args['image'];

			if ( $image ) {
				$style .= 'background-image: url("' . esc_url( $image ) . '");';
			}
		}

		$button = '';
		if ( ! empty( $args['button'] ) ) {
			$link = ! empty( $args['link'] ) ? $args['link'] : '#';
			$button = sprintf(
				'<a href="%s" class="konte-button %s">%s</a>',
				esc_url( $link ),
				'inline' == $args['layout'] ? 'button-underline underline-full small' : 'button button-normal text-' . $args['color'],
				esc_html( $args['button'] )
			);
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $css_class ) ) ?>" style="<?php echo esc_attr( $style ) ?>">
			<?php if ( ! empty( $args['tag'] ) ) : ?>
				<p class="konte-promotion__tagline"><?php echo wp_kses_post( $args['tag'] ) ?></p>
			<?php endif; ?>
			<p class="konte-promotion__text"><?php echo wp_kses_post( $args['text'] ) ?></p>
			<?php echo ! empty( $button ) ? $button : ''; ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'konte_campaign_bar' ) ) :
	/**
	 * Display campaign bar items
	 */
	function konte_campaign_items() {
		$campaigns = array_filter( (array) konte_get_option( 'campaign_items' ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		foreach ( $campaigns as $id => $args ) {
			$args = apply_filters( 'konte_campaign_item_args', $args, $id );

			konte_campaign_item( $args );
		}
	}
endif;

if ( ! function_exists( 'konte_site_branding_title' ) ) :
	/**
	 * Display the site branding title.
	 *
	 * @return string
	 */
	function konte_site_branding_title( $args = array() ) {
		// Don't render this title twice.
		if ( 'rendered' == konte_get_theme_prop( 'site_branding_title' ) ) {
			return '';
		}

		konte_set_theme_prop( 'site_branding_title', 'rendered' );

		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-title';

		// HTML tag for this title.
		$tag = is_front_page() || is_home() ? 'h1' : 'p';
		$tag = apply_filters( 'konte_site_branding_title_tag', $tag, $args );

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$title = sprintf(
			'<%1$s class="%2$s"><a href="%3$s" rel="home">%4$s</a></%1$s>',
			$tag,
			esc_attr( trim( $class ) ),
			esc_url( home_url( '/' ) ),
			get_bloginfo( 'name' )
		);

		if ( ! $args['echo'] ) {
			return $title;
		}

		echo apply_filters( 'konte_site_branding_title_html', $title );
	}
endif;

if ( ! function_exists( 'konte_site_branding_description' ) ) :
	/**
	 * Display the site branding description
	 *
	 * @param array $args
	 * @return void
	 */
	function konte_site_branding_description( $args = array() ) {
		// Don't render this description twice.
		if ( 'rendered' == konte_get_theme_prop( 'site_branding_description' ) ) {
			return '';
		}

		konte_set_theme_prop( 'site_branding_description', 'rendered' );

		$text = get_bloginfo( 'description', 'display' );

		if ( empty( $text ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-description';

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$description = sprintf(
			'<p class="%s">%s</p>',
			esc_attr( trim( $class ) ),
			wp_kses_post( $text )
		);

		if ( ! $args['echo'] ) {
			return $description;
		}

		echo apply_filters( 'site_branding_description_html', $description );
	}
endif;

if ( ! function_exists( 'konte_get_header_layout' ) ) :
	/**
	 * Get the header layout.
	 *
	 * @return string
	 */
	function konte_get_header_layout() {
		if ( is_page() ) {
			$header_layout = get_post_meta( get_the_ID(), 'header_layout', true );
		} elseif ( 'custom' == konte_get_option( 'header_present' ) ) {
			$header_layout = 'custom';
		}

		if ( empty( $header_layout ) ) {
			$header_present = konte_get_option( 'header_present' );
			$header_layout = 'prebuild' == $header_present ? konte_get_option( 'header_version' ) : 'custom';
		}

		return apply_filters( 'konte_get_header_layout', $header_layout );
	}
endif;


if ( ! function_exists( 'menu_top_navs' ) ) :
	/**
	 * Display the site branding title.
	 *
	 * @return string
	 */
	function menu_top_navs( $args = array() ) {
		if ( is_user_logged_in() ) {
			echo '<div class="action-menu-wrap"> 
					<a href="/cart" class="action-menu-btn"><img src="/wp-content/themes/konte/images/cart.png"> Carrinho</a>
					<a href="/my-account" class="action-menu-btn"><img src="/wp-content/themes/konte/images/account.png"> Perfil</a>
				</div>';
		} else {
			echo '<div class="action-menu-wrap"> 
					<a href="/cart" class="action-menu-btn"><img src="/wp-content/themes/konte/images/cart.png"> Carrinho</a>
					<a href="/login" class="action-menu-btn"><img src="/wp-content/themes/konte/images/account.png"> Entrar</a>
				</div>';
		}
	}
	
endif;

<?php
/**
 * Functions of stylesheets and CSS
 *
 * @package Konte
 */

if ( ! function_exists( 'konte_fonts_url' ) ) :
	/**
	 * Register fonts
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	function konte_fonts_url() {
		$fonts_url     = '';
		$query_args    = array();
		$font_families = array();
		$font_subsets  = array( 'latin', 'latin-ext' );

		// Loads the default font font for the blog header.
		if ( konte_has_blog_header() ) {
			$typography = konte_get_option( 'typo_blog_header_title' );

			if ( empty( $typography['font-family'] ) ) {
				/* Translators: If there are characters in your language that are not
				* supported by Crimson Text, translate this to 'off'. Do not translate
				* into your own language.
				*/
				if ( 'off' !== _x( 'on', 'Crimson Text font: on or off', 'konte' ) ) {
					$font_families['CrimsonText'] = 'Crimson Text:600';
				}
			}
		}

		// Get custom fonts from typography settings, excluding the blog header.
		// Form version 2.2.5, it is not necessary anymore. It is controlled by Kirki.
		if ( apply_filters( 'konte_get_fonts_from_settings', false ) ) {
			$settings = array(
				'typo_body',
				'typo_h1',
				'typo_h2',
				'typo_h3',
				'typo_h4',
				'typo_h5',
				'typo_h6',
				'typo_menu',
				'typo_submenu',
				'typo_page_title',
				'typo_page_subtitle',
				'typo_blog_post_title',
				'typo_blog_post_excerpt',
				'typo_widget_title',
				'typo_product_title',
				'typo_product_short_desc',
				'typo_catalog_page_title',
				'typo_catalog_product_title',
				'typo_footer_extra',
				'typo_footer_widgets',
				'typo_footer_main',
			);

			if ( konte_has_blog_header() ) {
				$settings[] = 'typo_blog_header_title';
			}

			foreach ( $settings as $setting ) {
				$typography = konte_get_option( $setting );

				if (
					! empty( $typography['font-family'] )
					&& ( 'function_pro' !== $typography['font-family'] )
					&& ( 'inherit' !== $typography['font-family'] )
					&& ! array_key_exists( $typography['font-family'], $font_families )
				) {
					$font_families[ $typography['font-family'] ] = trim( trim( $typography['font-family'] ), ',' );

					if ( isset( $typography['subsets'] ) ) {
						if ( is_array( $typography['subsets'] ) ) {
							$font_subsets = array_merge( $font_subsets, $typography['subsets'] );
						} else {
							$font_subsets[] = $typography['subsets'];
						}
					}
				}
			}
		}

		if ( ! empty( $font_families ) ) {
			$font_subsets = array_unique( $font_subsets );
			$query_args   = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( implode( ',', $font_subsets ) ),
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( apply_filters( 'konte_fonts_url', $fonts_url, $query_args ) );
	}

endif;

if ( ! function_exists( 'konte_typography_css' ) ) :
	/**
	 * Get typography CSS base on settings
	 */
	function konte_typography_css() {
		$settings = array(
			'typo_body'                  => 'body, .block-editor .editor-styles-wrapper',
			'typo_h1'                    => 'h1, .h1',
			'typo_h2'                    => 'h2, .h2',
			'typo_h3'                    => 'h3, .h3',
			'typo_h4'                    => 'h4, .h4',
			'typo_h5'                    => 'h5, .h5',
			'typo_h6'                    => 'h6, .h6',
			'typo_menu'                  => '.main-navigation a, .header-v8 .nav-menu > li > a, .header-v9 .nav-menu > li > a, .header-vertical .main-navigation .nav-menu > li > a',
			'typo_submenu'               => '.main-navigation li li a, .header-vertical .main-navigation .sub-menu a',
			'typo_page_title'            => '.single-page-header .entry-title, .page .page .entry-title',
			'typo_page_subtitle'         => '.single-page-header .entry-subtitle',
			'typo_blog_header_title'     => '.blog-header-content .header-title',
			'typo_blog_post_title'       => '.hfeed .hentry .entry-title a',
			'typo_blog_post_excerpt'     => '.hfeed .hentry .entry-summary',
			'typo_widget_title'          => '.widget-title',
			'typo_footer_extra'          => '.footer-extra',
			'typo_footer_widgets'        => '.footer-widgets',
			'typo_footer_main'           => '.footer-main',
		);

		return konte_get_typography_css( $settings );
	}
endif;

if ( ! function_exists( 'konte_get_typography_css' ) ) :
	/**
	 * Get typography CSS base on settings
	 */
	function konte_get_typography_css( $settings, $print_default = false ) {
		if ( empty( $settings ) ) {
			return '';
		}

		$css        = '';
		$properties = array(
			'font-family'    => 'font-family',
			'font-size'      => 'font-size',
			'variant'        => 'font-weight',
			'line-height'    => 'line-height',
			'letter-spacing' => 'letter-spacing',
			'color'          => 'color',
			'text-transform' => 'text-transform',
			'text-align'     => 'text-align',
			'font-weight'    => 'font-weight',
			'font-style'     => 'font-style',
		);

		foreach ( $settings as $setting => $selector ) {
			if ( ! is_string( $setting ) ) {
				continue;
			}

			$selector   = is_array( $selector ) ? implode( ',', $selector ): $selector;
			$typography = konte_get_option( $setting );
			$default    = (array) konte_get_option_default( $setting );
			$style      = '';

			// Correct the default values. Copy from Kirki_Field_Typography::sanitize
			if ( isset( $default['variant'] ) ) {
				if ( ! isset( $default['font-weight'] ) ) {
					$default['font-weight'] = filter_var( $default['variant'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
					$default['font-weight'] = ( 'regular' === $default['variant'] || 'italic' === $default['variant'] ) ? '400' : absint( $default['font-weight'] );
				}

				// Get font-style from variant.
				if ( ! isset( $default['font-style'] ) ) {
					$default['font-style'] = ( false === strpos( $default['variant'], 'italic' ) ) ? 'normal' : 'italic';
				}
			}

			if ( isset( $typography['variant'] ) && ( ! empty( $typography['font-weight'] ) || ! empty( $typography['font-style'] ) ) ) {
				unset( $typography['variant'] );
			}

			foreach ( $properties as $key => $property ) {
				if ( ! isset( $default[ $key ] ) ) {
					continue;
				}

				if ( isset( $typography[ $key ] ) && ! empty( $typography[ $key ] ) ) {
					if ( ! $print_default && strtoupper( $default[ $key ] ) == strtoupper( $typography[ $key ] ) ) {
						continue;
					}

					$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
					$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

					if ( $value ) {
						$style .= $property . ': ' . $value . ';';
					}
				}
			}

			if ( ! empty( $style ) ) {
				$css .= $selector . '{' . $style . '}';
			}
		}

		return $css;
	}
endif;

if ( ! function_exists( 'konte_custom_color_css' ) ) :
	/**
	 * Generate the CSS for the current custom color scheme.
	 */
	function konte_custom_color_css() {
		$color = konte_get_option( 'color_scheme_custom' ) ? konte_get_option( 'color_scheme_color' ) : konte_get_option( 'color_scheme' );
		$color = $color ? esc_attr( $color ) : '#161619';

		$css = '
/**
 * Konte: Color Scheme
 */
:root {
	--konte--primary-color:' . $color . ';
}

h1,
h2,
h3,
h4,
h5,
h6,
.text-default,
.color-scheme-default,
.text-default a,
.color-scheme-default a,
.konte-button.button-outline,
.konte-button.button-underline,
.konte-button.button-underline:hover,
.konte-product-grid__head:after,
.konte-post-grid .post-title a,
.konte-info-list .info-value a:hover,
.konte-cta:before,
.site-footer.light a,
.site-footer.light .footer-widgets-area,
.site-footer .mc4wp-form input[type=submit],
.site-footer .list-dropdown .current {
	color: ' . $color . ';
}

.konte-button.button-outline:hover,
.konte-carousel__arrow:hover {
	color: #fff;
	border-color: ' . $color . ';
	background-color: ' . $color . ';
}

button,
.button,
input[type="button"],
input[type="reset"],
input[type="submit"],
.konte-button.button-normal.text-default,
button.alt:hover,
.button.alt:hover,
input[type="button"].alt:hover,
input[type="reset"].alt:hover,
input[type="submit"].alt:hover {
	background-color: ' . $color . ';
	border-color: ' . $color . ';
}

button.alt,
.button.alt,
input[type="button"].alt,
input[type="reset"].alt,
input[type="submit"].alt {
	color: ' . $color . ';
	border-color: ' . $color . ';
	background-color: transparent;
}

.next-posts-navigation a:hover,
.site-footer .mc4wp-form input:focus,
.site-footer .mc4wp-form select:focus,
.site-footer .mc4wp-form textarea:focus,
.site-footer .mc4wp-form button:focus {
	border-color: ' . $color . ';
}

.konte-button.button-normal.text-default {
	color: #fff;
}

.next-posts-navigation a:after {
	border-top-color: ' . $color . '
}
	';

		return apply_filters( 'konte_custom_colors_css', $css, $color );
	}
endif;

if ( ! function_exists( 'konte_get_inline_style' ) ) :
	/**
	 * Get inline style data
	 */
	function konte_get_inline_style() {
		$css = '';

		// Typography CSS.
		// From version 2.2.5,it is controlled by Kirki.
		// $css .= konte_typography_css();

		// Header background.
		if ( is_home() && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) && 'custom' == get_post_meta( $blog_page_id, 'header_background', true ) ) {
			$background = get_post_meta( $blog_page_id, 'header_background_color', true );
		} elseif ( konte_get_option( 'header_background_blog_custom' ) && konte_is_blog_related_pages() && 'custom' == konte_get_option( 'header_background_blog' ) ) {
			$background = konte_get_option( 'header_background_blog_color' );
		} elseif ( konte_get_option( 'header_background_shop_custom' ) && function_exists( 'WC' ) && is_woocommerce() && 'custom' == konte_get_option( 'header_background_shop' ) ) {
			$background = konte_get_option( 'header_background_shop_color' );
		} elseif ( is_page() && 'custom' == get_post_meta( get_the_ID(), 'header_background', true ) ) {
			$background = get_post_meta( get_the_ID(), 'header_background_color', true );
		} elseif ( 'custom' == konte_get_option( 'header_background' ) ) {
			$background = konte_get_option( 'header_background_color' );
		}

		if ( ! empty( $background ) ) {
			if ( 'v10' == konte_get_header_layout() ) {
				$selector = '.header-v10.custom .header-main .header-left-items';
			} else {
				$selector = '.site-header.custom, .custom .header-search.icon .search-field:focus, .custom .header-search.icon .search-field.focused';
			}

			$css .= $selector . ' { background-color: ' . esc_attr( $background ) . '; }';
		}

		// Topbar height.
		if ( $height = konte_get_option( 'topbar_height' ) ) {
			$css .= '.topbar {height: ' . intval( $height ) . 'px}';
		}

		// Header height.
		$css .= ':root { --header-main-height: ' . intval( konte_get_option( 'header_main_height' ) ) . 'px}';
		$css .= ':root { --header-bottom-height: ' . intval( konte_get_option( 'header_bottom_height' ) ) . 'px}';
		$css .= '.header-main, .header-v10 .site-branding, .header-v10 .header-main .header-right-items { height: ' . intval( konte_get_option( 'header_main_height' ) ) . 'px; }';
		$css .= '.header-bottom { height: ' . intval( konte_get_option( 'header_bottom_height' ) ) . 'px; }';

		// Header width.
		if ( 'v10' == konte_get_header_layout() ) {
			$width = absint( konte_get_option( 'header_width' ) );
			$width = $width ? $width : 360;

			$css .= '.header-v10 .header-main .header-left-items { width: ' . $width . 'px; }';
			$css .= '.header-vertical .site, .header-vertical .sticky-cart-form { padding-left: ' . $width . 'px; }';
			$css .= '.header-vertical .site-footer.transparent { padding-left: ' . $width . 'px; }';

			$css .= '@media screen and (max-width: 1440px) { .header-v10 .header-main .header-left-items { width: ' . min( $width, 280 ) . 'px; } .header-vertical .site, .header-vertical .sticky-cart-form { padding-left: ' . min( $width, 280 ) . 'px; } }';
			$css .= '@media screen and (max-width: 1280px) { .header-v10 .header-main .header-left-items { width: ' . min( $width, 200 ) . 'px; } .header-vertical .site, .header-vertical .sticky-cart-form { padding-left: ' . min( $width, 200 ) . 'px; } }';
		}

		// Mobile header height.
		if ( $height = konte_get_option( 'mobile_header_height' ) ) {
			$css .= '.header-mobile {height: ' . intval( $height ) . 'px}';
		}

		// Logo dimension.
		$logo_dimension = konte_get_option( 'logo_dimension' );
		$logo_dimension = array_map( 'intval', (array) $logo_dimension );

		if ( $logo_dimension['width'] > 0 || $logo_dimension['height'] > 0 ) {
			$width = 0 < $logo_dimension['width'] ? 'width: ' . $logo_dimension['width'] . 'px;' : '';
			$height = 0 < $logo_dimension['height'] ? 'height: ' . $logo_dimension['height'] . 'px;' : '';
			$css .= '.logo img {' . $width . $height . '}';

			$width = $width ? $width : 'width: auto;';
			$height = $height ? $height : 'height: auto;';
			$css .= '.logo svg {' . $width . $height . '}';
		}

		// Top & Bottom spacings
		if ( is_page() && 'custom' == get_post_meta( get_the_ID(), 'top_spacing', true ) ) {
			$top_padding = get_post_meta( get_the_ID(), 'top_padding', true );

			if ( $top_padding ) {
				$css .= '.site-content { padding-top: ' . esc_attr( $top_padding ) . ' !important; }';
			}
		}

		if ( is_page() && 'custom' == get_post_meta( get_the_ID(), 'bottom_spacing', true ) ) {
			$bottom_padding = get_post_meta( get_the_ID(), 'bottom_padding', true );

			if ( $bottom_padding ) {
				$css .= '.site-content { padding-bottom: ' . esc_attr( $bottom_padding ) . ' !important; }';
			}
		}

		// Hamburger menu background
		if ( $image = konte_get_option( 'hamburger_background' ) ) {
			$css .= '.hamburger-screen-background { background-image: url(' . esc_url( $image ) . '); }';
		}

		// Blog header image
		if ( konte_has_blog_header() && ( $image = konte_get_blog_header_image() ) ) {
			$css .= '.blog-header-title { background-image: url(' . esc_url( $image ) . ')}';
		}

		// Featured content slider's height
		if ( is_home() && konte_get_option( 'blog_featured_content' ) ) {
			$height = konte_get_option( 'blog_featured_slider_height' );
			$ratio  = 1920 / $height;
			$css    .= '.featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( $height ) . 'px }';
			$css    .= '@media screen and (max-width: 1440px) { .featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( 1440 / $ratio ) . 'px } }';
			$css    .= '@media screen and (max-width: 1280px) { .featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( 1280 / $ratio ) . 'px } }';
			$css    .= '@media screen and (max-width: 1199px) { .featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( 1199 / $ratio ) . 'px } }';
			$css    .= '@media screen and (max-width: 991px) { .featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( 991 / $ratio ) . 'px } }';
			$css    .= '@media screen and (max-width: 767px) { .featured-content-carousel, .featured-content-carousel .featured-item { height: ' . absint( 767 / $ratio ) . 'px } }';
		}

		// Footer background
		if ( is_page() && 'custom' == get_post_meta( get_the_ID(), 'footer_background', true ) ) {
			$background = get_post_meta( get_the_ID(), 'footer_background_color', true );
		} elseif ( konte_get_option( 'footer_background_blog_custom' ) && konte_is_blog_related_pages() && 'custom' == konte_get_option( 'footer_background_blog' ) ) {
			$background = konte_get_option( 'footer_background_blog_color' );
		} elseif ( konte_get_option( 'footer_background_shop_custom' ) && function_exists( 'WC' ) && is_woocommerce() && 'custom' == konte_get_option( 'footer_background_shop' ) ) {
			$background = konte_get_option( 'footer_background_shop_color' );
		} elseif ( 'custom' == konte_get_option( 'footer_background' ) ) {
			$background = konte_get_option( 'footer_background_color' );
		}

		if ( ! empty( $background ) ) {
			$css .= '.site-footer.custom { background-color: ' . esc_attr( $background ) . '; }';
			unset( $background );
		}

		// Page header height.
		if ( is_page() ) {
			$height = get_post_meta( get_the_ID(), 'page_header_height', true );

			if ( ! $height ) {
				if ( ! konte_get_option( 'page_header_full_height' ) ) {
					$height = intval( konte_get_option( 'page_header_height' ) );

					$css .= '.page-header.title-front, .page-header .entry-thumbnail { height: ' . $height . 'px; }';
					$css .= '@media (max-width: 991px) { max.page-header.title-front, .page-header .entry-thumbnail { height: ' . ( $height * 0.5 ) . 'px; } }';
					$css .= '@media (max-width: 767px) { max.page-header.title-front, .page-header .entry-thumbnail { height: ' . ( $height * 0.375 ) . 'px; } }';
				}
			} elseif ( 'manual' == $height ) {
				$css .= '.page-header.title-front, .page-header .entry-thumbnail { height: ' . intval( get_post_meta( get_the_ID(), 'page_header_manual_height', true ) ) . 'px; }';
			}
		}

		// Preloader. Add default CSS to make it run as soon as page loads.
		if ( konte_get_option( 'preloader_enable' ) ) {
			$preloader_css = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999999;';

			$color = konte_get_option( 'preloader_background_color' );
			$color = $color ? $color : '#fff';
			$preloader_css .= 'background-color: ' . $color . ';';

			$css .= '.preloader { ' . $preloader_css . ' }';
		}

		return apply_filters( 'konte_inline_style', $css );
	}
endif;

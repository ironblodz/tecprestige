<?php
/**
 * Shortcodes.
 */

/**
 * Class for shortcodes.
 */
class Konte_Addons_Shortcodes {
	/**
	 * Testimonials for the carousel.
	 *
	 * @var array
	 */
	protected static $testimonials = array();

	/**
	 * Init shortcodes
	 */
	public static function init() {
		$shortcodes = array(
			'pricing_table',
			'map',
			'testimonial',
			'testimonial_carousel',
			'button',
			'chart',
			'message_box',
			'icon_box',
			'member',
			'carousel',
			'carousel_item',
			'post_grid',
			'post_carousel',
			'dash',
			'info_list',
			'countdown',
			'category_banner',
			'product_carousel',
			'product_carousel2',
			'product_tabs',
			'product_grid',
			'product_masonry',
			'product',
			'promotion',
			'cta',
			'banner_grid',
			'subscribe_box',
			'banner_countdown',
			'empty_space',
			'instagram',
			'instagram_carousel',
		);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( 'konte_' . $shortcode, array( __CLASS__, $shortcode ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_nopriv_konte_products_shortcode', array( __CLASS__, 'ajax_products_shortcode' ) );
		add_action( 'wp_ajax_konte_products_shortcode', array( __CLASS__, 'ajax_products_shortcode' ) );
		add_action( 'wc_ajax_konte_products_shortcode', array( __CLASS__, 'ajax_products_shortcode' ) );
	}

	/**
	 * Enqueue scripts.
	 */
	public static function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

		wp_register_script( 'jquery-countdown', KONTE_ADDONS_URL . 'assets/js/jquery.countdown' . $suffix . '.js', array( 'jquery' ), '2.2.0', true );
		wp_register_script( 'jquery-circle-progress', KONTE_ADDONS_URL . 'assets/js/circle-progress' . $suffix . '.js', array( 'jquery' ), '1.2.2', true );
		wp_register_script( 'slick', KONTE_ADDONS_URL . 'assets/js/slick' . $suffix . '.js', array( 'jquery' ), '1.8.1', true );

		wp_enqueue_script( 'konte-addons-shortcodes', KONTE_ADDONS_URL . 'assets/js/shortcodes.js', array(
			'wp-util',
			'slick',
			'jquery-masonry',
			'jquery-countdown',
			'jquery-circle-progress',
		), '20180620', true );
	}

	/**
	 * Pricing table.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function pricing_table( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'         => '',
			'image_source'  => 'media_library',
			'image'         => '',
			'image_src'     => '',
			'image_width'   => '',
			'price'         => '',
			'currency'      => '$',
			'recurrence'    => esc_html__( 'Per Month', 'konte-addons' ),
			'button_text'   => esc_html__( 'Get Started', 'konte-addons' ),
			'button_link'   => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-pricing-table',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'external_link' == $atts['image_source'] ) {
			$image_url = $atts['image_src'];
		} else {
			$image = wp_get_attachment_image_src( $atts['image'], 'full' );
			$image_url = $image ? $image[0] : false;
		}

		if ( $image_url ) {
			$image = sprintf( '<img src="%s" alt="%s" width="%s" class="konte-pricing-table__image">',
				esc_url( $image_url ),
				esc_attr( $atts['title'] ),
				esc_attr( $atts['image_width'] )
			);
		} else {
			$image = '';
		}

		return sprintf(
			'<div class="%s">
				%s
				<h4 class="konte-pricing-table__title">%s</h4>
				<div class="konte-pricing-table__description">%s</div>
				<div class="konte-pricing-table__price"><span class="currency">%s</span>%s</div>
				<div class="konte-pricing-table__recurrence">%s</div>
				<a href="%s" class="button konte-pricing-table__button">%s</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			esc_html( $atts['title'] ),
			$content,
			esc_html( $atts['currency'] ),
			esc_html( $atts['price'] ),
			esc_html( $atts['recurrence'] ),
			esc_url( $atts['button_link'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Google Map
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function map( $atts, $content ) {
		$atts = shortcode_atts( array(
			'marker'        => '',
			'address'       => '',
			'lat'           => '',
			'lng'           => '',
			'width'         => '100%',
			'height'        => '600px',
			'zoom'          => 12,
			'color'         => 'blue',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		if ( ! function_exists( 'konte_get_option' ) ) {
			return esc_html__( 'This shortcode only works with the Konte theme', 'konte-addons' );
		}

		$api_key = konte_get_option( 'api_google_map' );

		if ( empty( $api_key ) ) {
			return esc_html__( 'Google map requires API Key in order to work.', 'konte-addons' );
		}

		if ( empty( $atts['address'] ) && empty( $atts['lat'] ) && empty( $atts['lng'] ) ) {
			return esc_html__( 'No address', 'konte-addons' );
		}

		if ( ! empty( $atts['lat'] ) && ! empty( $atts['lng'] ) ) {
			$coordinates = array(
				'lat' => $atts['lat'],
				'lng' => $atts['lng'],
			);
		} else {
			$coordinates = self::get_coordinates($atts['address'], $api_key);
		}

		if ( ! empty( $coordinates['error'] ) ) {
			return $coordinates['error'];
		}

		$css_class = array(
			'konte-map',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$style = array();
		if ( $atts['width'] ) {
			$style[] = 'width: ' . $atts['width'];
		}

		if ( $atts['height'] ) {
			$style[] = 'height: ' . intval( $atts['height'] ) . 'px';
		}

		$marker = '';

		if ( $atts['marker'] ) {
			if ( filter_var( $atts['marker'], FILTER_VALIDATE_URL ) ) {
				$marker = $atts['marker'];
			} else {
				$attachment_image = wp_get_attachment_image_src( intval( $atts['marker'] ), 'full' );
				$marker           = $attachment_image ? $attachment_image[0] : '';
			}
		}

		$zoom = absint( $atts['zoom'] );
		$zoom = $zoom ? $zoom : 12;

		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key );

		return sprintf(
			'<div class="%s" style="%s" data-zoom="%s" data-lat="%s" data-lng="%s" data-color="%s" data-marker="%s">%s</div>',
			implode( ' ', $css_class ),
			implode( ';', $style ),
			esc_attr( $zoom ),
			esc_attr( $coordinates['lat'] ),
			esc_attr( $coordinates['lng'] ),
			esc_attr( $atts['color'] ),
			esc_attr( $marker ),
			$content
		);
	}

	/**
	 * Testimonial
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function testimonial( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'name'          => '',
			'company'       => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-testimonial',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['image'] ) {
			$image = self::get_image( $atts['image'], '200x200', 'medium' );
		} else {
			$image = '<img src="' . KONTE_ADDONS_URL . 'assets/images/person.jpg" alt="' . esc_attr( $atts['name'] ) . '" width="200" height="200">';
		}

		$author = array(
			'<span class="konte-testimonial__name">' . esc_html( $atts['name'] ) . '</span>',
			'<span class="konte-testimonial__company">' . esc_html( $atts['company'] ) . '</span>',
		);

		self::$testimonials[] = array(
			'image'   => $atts['image'],
			'name'    => $atts['name'],
			'company' => $atts['company'],
			'text'    => $content,
		);

		return sprintf(
			'<div class="%s">
				<div class="konte-testimonial__photo">%s</div>
				<div class="konte-testimonial__entry">
					<div class="konte-testimonial__content">%s</div>
					<div class="konte-testimonial__author">%s</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			$content,
			implode( '<span class="konte-testimonial__author-separator">-</span>', $author )
		);
	}

	/**
	 * Button
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function button( $atts, $content ) {
		$atts = shortcode_atts( array(
			'link'          => '',
			'style'         => 'normal',
			'color'         => 'default',
			'shape'         => 'square',
			'line_width'    => 'full',
			'line_position' => 'left',
			'size'          => 'normal',
			'align'         => 'inline',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$attributes = array();

		$css_class = array(
			'konte-button',
			'button-' . $atts['style'],
			'text-' . $atts['color'],
			'align-' . $atts['align'],
			$atts['size'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'underline' == $atts['style'] ) {
			$css_class[] = 'underline-' . $atts['line_width'];

			if ( 'small' == $atts['line_width'] ) {
				$css_class[] = 'underline-' . $atts['line_position'];
			}
		} else {
			$css_class[] = 'button';
			$css_class[] = $atts['shape'];
		}

		if ( ! empty( $atts['link'] ) ) {
			$link = self::build_link( $atts['link'] );

			if ( ! empty( $link['url'] ) ) {
				$attributes['href'] = $link['url'];
			}

			if ( ! empty( $link['title'] ) ) {
				$attributes['title'] = $link['title'];
			}

			if ( ! empty( $link['target'] ) ) {
				$attributes['target'] = $link['target'];
			}

			if ( ! empty( $link['rel'] ) ) {
				$attributes['rel'] = $link['rel'];
			}
		}

		$attributes['class'] = implode( ' ', $css_class );
		$attr                = array();

		foreach ( $attributes as $name => $value ) {
			$attr[] = $name . '="' . esc_attr( $value ) . '"';
		}

		$button = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			empty( $attributes['href'] ) ? 'span' : 'a',
			implode( ' ', $attr ),
			esc_html( $content )
		);

		if ( 'center' == $atts['align'] ) {
			return '<p class="konte-button-wrapper text-center">' . $button . '</p>';
		}

		return $button;
	}

	/**
	 * Chart
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function chart( $atts ) {
		$atts = shortcode_atts( array(
			'value'         => 100,
			'size'          => 300,
			'thickness'     => 10,
			'label_source'  => 'auto',
			'label'         => '',
			'color'         => '#161619',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-chart',
			'konte-chart-' . $atts['value'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$color = $atts['color'] ? $atts['color'] : '#161619';
		$label = 'custom' == $atts['label_source'] ? $atts['label'] : '<span class="konte-chart__unit">%</span>' . esc_html( $atts['value'] );

		return sprintf(
			'<div class="%s" data-value="%s" data-size="%s" data-thickness="%s" data-fill="%s">
				<div class="konte-chart__text" %s>%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( intval( $atts['value'] ) / 100 ),
			esc_attr( $atts['size'] ),
			esc_attr( $atts['thickness'] ),
			esc_attr( json_encode( array( 'color' => $color ) ) ),
			$atts['color'] ? 'style="color: ' . esc_attr( $atts['color'] ) . '"' : '',
			wp_kses_post( $label )
		);
	}

	/**
	 * Message Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function message_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'type'          => 'success',
			'closeable'     => true,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-message-box',
			$atts['type'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['closeable'] ) {
			$css_class[] = 'closeable';
		}

		$icon = str_replace( array( 'info', 'danger' ), array( 'information', 'error' ), $atts['type'] );

		return sprintf(
			'<div class="%s">
				<span class="konte-message-box__icon svg-icon icon-%s"><svg width="40" height="40"><use xlink:href="#%s"></use></svg></span>
				<div class="konte-message-box__content">%s</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $icon ),
			esc_attr( $icon ),
			do_shortcode( $content ),
			$atts['closeable'] ? '<a class="konte-message-box__close close" href="#"><span class="svg-icon icon-close"><svg width="24" height="24"><use xlink:href="#close"></use></svg></span></a>' : ''
		);
	}

	/**
	 * Icon Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function icon_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'icon_type'        => 'fontawesome',
			'icon_fontawesome' => 'fa fa-adjust',
			'icon_openiconic'  => 'vc-oi vc-oi-dial',
			'icon_typicons'    => 'typcn typcn-adjust-brightness',
			'icon_entypo'      => 'entypo-icon entypo-icon-note',
			'icon_linecons'    => 'vc_li vc_li-heart',
			'icon_monosocial'  => 'vc-mono vc-mono-fivehundredpx',
			'icon_material'    => 'vc-material vc-material-cake',
			'image'            => '',
			'image_link'       => '',
			'align'            => 'left',
			'title'            => esc_html__( 'I am Icon Box', 'konte-addons' ),
			'title_size'       => '',
			'css_animation'    => '',
			'el_class'         => '',
			'css'              => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-icon-box',
			'icon-type-' . $atts['icon_type'],
			'box-align-' . $atts['align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		if ( 'image' == $atts['icon_type'] ) {
			$image = wp_get_attachment_image_src( $atts['image'], 'full' );
			$icon  = $image ? sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) ) : '';
		} elseif ( 'external' == $atts['icon_type'] ) {
			$icon = $atts['image_link'] ? sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $atts['image_link'] ) ) : '';
		} else {
			if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
				vc_icon_element_fonts_enqueue( $atts['icon_type'] );
			}

			$icon = '<i class="' . esc_attr( $atts[ 'icon_' . $atts['icon_type'] ] ) . '"></i>';
		}

		$title_size = '';

		if ( ! empty( $atts['title_size'] ) ) {
			$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
			preg_match( $pattern, $atts['title_size'], $matches );
			$font_size = isset( $matches[1] ) ? (float) $matches[1] : (float) $atts['title_size'];
			$unit = isset( $matches[2] ) ? $matches[2] : 'px';

			if ( 'px' == $unit ) {
				$font_size = ( $font_size / 18 ) . 'em';
			} else {
				$font_size = $font_size . $unit;
			}

			$title_size = $font_size ? 'style="font-size:' . $font_size . '"' : '';
		}

		return sprintf(
			'<div class="%s">
				<div class="konte-icon-box__icon">%s</div>
				<h4 class="konte-icon-box__title" %s>%s</h4>
				<div class="konte-icon-box__content">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$icon,
			$title_size,
			esc_html( $atts['title'] ),
			function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content, true ) : $content
		);
	}

	/**
	 * Team member
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function member( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => 'full',
			'name'          => '',
			'job'           => '',
			'facebook'      => '',
			'twitter'       => '',
			'google'        => '',
			'pinterest'     => '',
			'linkedin'      => '',
			'youtube'       => '',
			'instagram'     => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-team-member',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['image'] ) {
			$image = self::get_image( $atts['image'], $atts['image_size'] );
		} else {
			$image = KONTE_ADDONS_URL . 'assets/images/person.jpg';
			$image = sprintf( '<img src="%s" alt="%s" width="288" height="352">',
				esc_url( $image ),
				esc_attr( $atts['name'] )
			);
		}

		$socials = array( 'facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'youtube', 'instagram' );
		$links   = array();

		foreach ( $socials as $social ) {
			if ( empty( $atts[ $social ] ) ) {
				continue;
			}

			$icon = str_replace( array( 'google', 'pinterest', 'youtube' ), array(
				'google-plus',
				'pinterest-p',
				'youtube-play',
			), $social );

			$links[] = sprintf( '<a href="%s" target="_blank"><i class="fa fa-%s"></i></a>', esc_url( $atts[ $social ] ), esc_attr( $icon ) );
		}

		return sprintf(
			'<div class="%s">
				%s
				<div class="konte-team-member__info">
					<h5 class="konte-team-member__name">%s</h5>
					<span class="konte-team-member__job">%s</span>
					<span class="konte-team-member__socials">%s</span>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			esc_html( $atts['name'] ),
			esc_html( $atts['job'] ),
			implode( '', $links )
		);
	}

	/**
	 * Carousel
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function carousel( $atts, $content ) {
		$atts = shortcode_atts( array(
			'infinite'   => true,
			'free'       => false,
			'dots'       => 'center',
			'arrows'     => '',
			'slide'      => 2,
			'scroll'     => 1,
			'gap'        => '40',
			'show_index' => false,
			'el_class'   => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-carousel',
			'konte-carousel--gap-' . $atts['gap'],
			$atts['el_class'],
		);

		$data = array(
			'dots'           => false,
			'arrows'         => false,
			'infinite'       => (bool) $atts['infinite'],
			'slidesToShow'   => intval( $atts['slide'] ),
			'slidesToScroll' => intval( $atts['scroll'] ),
		);

		if ( ! empty( $atts['dots'] ) && 'no' != $atts['dots'] ) {
			$data['dots'] = true;
			$css_class[] = 'konte-carousel--dots-' . $atts['dots'];
		}

		if ( ! empty( $atts['arrows'] ) && 'no' != $atts['arrows'] ) {
			$data['arrows'] = true;
			$css_class[] = 'konte-carousel--arrows-' . $atts['arrows'];
		}

		if ( $atts['free'] ) {
			$data['variableWidth'] = true;
			$css_class[] = 'konte-carousel--free-mode';
		}

		if ( $atts['show_index'] ) {
			$css_class[] = 'konte-carousel--show-index';
		}

		return sprintf(
			'<div class="%s" data-slick="%s" %s>%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( json_encode( $data ) ),
			is_rtl() ? 'dir="rtl"' : '',
			do_shortcode( $content )
		);
	}

	/**
	 * Carousel item.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function carousel_item( $atts ) {
		$atts = shortcode_atts(array(
			'image_source' => 'media_library',
			'image' => '',
			'image_size' => '1000x640',
			'image_src' => '',
			'title' => '',
			'link' => '',
			'button_text' => esc_html__( 'Shop Now', 'konte-addons' ),
			'el_class' => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'konte-carousel-item',
			$atts['el_class'],
		);

		$image = '';
		$link = self::build_link( $atts['link'] );

		if ( 'media_library' == $atts['image_source'] && $atts['image'] ) {
			$image = self::get_image($atts['image'], $atts['image_size']);
		} elseif ( 'external_link' == $atts['image_source'] && $atts['image_src'] ) {
			$image = '<img src="' . esc_url( $atts['image_src'] ) . '" alt="' . esc_attr( $atts['title'] ) . '">';
		}

		if ( ! empty( $link['url'] ) ) {
			$link_open = '<a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '">';
			$link_close = '</a>';
		} else {
			$link_open = '';
			$link_close = '';
		}

		$button = '';
		if ( ! empty( $atts['button_text'] ) ) {
			$button = '<span class="konte-button button-underline underline-small underline-left large">' . esc_html( $atts['button_text'] ) . '</span>';
		}

		return sprintf(
			'<div class="%s">
				%s
				%s
				%s
				%s
				%s
			</div>',
			esc_attr(implode(' ', $css_class)),
			$link_open,
			$image,
			! empty( $atts['title'] ) ? '<h4 class="konte-carousel-item__title">' . esc_html( $atts['title'] ) . '</h4>' : '',
			$button,
			$link_close
		);
	}

	/**
	 * Testimonial carousel
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function testimonial_carousel( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'    => esc_html__( 'Testimonials', 'konte-addons' ),
			'el_class' => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-testimonial-carousel',
			$atts['el_class'],
		);

		self::$testimonials = array();
		do_shortcode( $content );

		if ( empty( self::$testimonials ) ) {
			return '';
		}

		$avatars = array();
		$testimonials = array();

		foreach ( self::$testimonials as $testimonial ) {
			$image = wp_get_attachment_image_src( $testimonial['image'], 'full' );

			if ( $image ) {
				$avatars[] = sprintf( '<img alt="%s" src="%s">',
					esc_attr( $testimonial['image'] ),
					esc_url( $image[0] )
				);
			} else {
				$avatars[] = sprintf( '<img alt="%s" src="%s">',
					esc_attr( $testimonial['image'] ),
					esc_url( KONTE_ADDONS_URL . '/assets/images/person.jpg' )
				);
			}

			$testimonials[] = sprintf(
				'<div class="konte-testimonial">
					<div class="konte-testimonial__content">%s</div>
					<div class="konte-testimonial__author">
						<span class="konte-testimonial__name">%s</span>
						<span class="konte-testimonial__company">%s</span>
					</div>
				</div>',
				wp_kses_post( $testimonial['text'] ),
				wp_kses_post( $testimonial['name'] ),
				wp_kses_post( $testimonial['company'] )
			);
		}

		$photos_class = uniqid( 'testimonials-photos-' );
		$testimonials_class = uniqid( 'testimonials-' );

		return sprintf(
			'<div class="%s">
				<div id="%s" class="konte-testimonial-carousel__photos" data-slick="%s" %s>%s</div>
				<div class="konte-testimonial-carousel__content">
					%s
					<div id="%s" class="konte-testimonial-carousel__testimonials" data-slick="%s">%s</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $photos_class ),
			esc_attr( json_encode( array( 'asNavFor' => '#' . $testimonials_class ) ) ),
			is_rtl() ? 'dir="rtl"' : '',
			implode( ' ', $avatars ),
			$atts['title'] ? '<div class="konte-testimonials-title">' . esc_html( $atts['title'] ) . '</div>' : '',
			esc_attr( $testimonials_class ),
			esc_attr( json_encode(array( 'asNavFor' => '#' . $photos_class ) ) ),
			implode( ' ', $testimonials )
		);
	}

	/**
	 * Post grid.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function post_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 3,
			'columns'       => 3,
			'category'      => '',
			'gap'       => 40,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-post-grid',
			'post-grid',
			'clearfix',
			'columns-' . $atts['columns'],
			'columns-gap-' . intval( $atts['gap'] ),
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$output = array();

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $atts['per_page'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => false,
		);

		if ( $atts['category'] ) {
			$args['category_name'] = trim( $atts['category'] );
		}

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return '';
		}

		while ( $posts->have_posts() ) : $posts->the_post();
			$thumbnail  = '';

			if ( has_post_thumbnail() ) :
				$thumbnail = sprintf(
					'<a href="%s" class="post-thumbnail">%s</a>',
					esc_url( get_permalink() ),
					get_the_post_thumbnail( get_the_ID(), 'konte-post-thumbnail-shortcode' )
				);
			endif;

			$output[] = sprintf(
				'<div class="%s">
					%s
					<div class="konte-post-grid__summary">
						<h5 class="post-title"><a href="%s" rel="bookmark">%s</a></h5>
						<div class="post-summary">%s</div>
						<a class="button alt" href="%s">%s</a>
					</div>
				</div>',
				esc_attr( implode( ' ', get_post_class() ) ),
				$thumbnail,
				esc_url( get_permalink() ),
				get_the_title(),
				get_the_excerpt(),
				esc_url( get_permalink() ),
				esc_html__( 'Continue reading', 'konte-addons' )
			);
		endwhile;

		wp_reset_postdata();

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Post carousel.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function post_carousel( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 9,
			'category'      => '',
			'infinite'      => true,
			'dots_position' => 'left',
			'slide'         => 3,
			'scroll'        => 3,
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-post-carousel',
			'konte-post-grid',
			'dots-' . $atts['dots_position'],
			$atts['el_class'],
		);

		$output = array();

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $atts['per_page'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => false,
		);

		if ( $atts['category'] ) {
			$args['category_name'] = trim( $atts['category'] );
		}

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return '';
		}

		while ( $posts->have_posts() ) : $posts->the_post();
			$thumbnail  = '';

			if ( has_post_thumbnail() ) :
				$thumbnail = sprintf(
					'<a href="%s" class="post-thumbnail">%s</a>',
					esc_url( get_permalink() ),
					get_the_post_thumbnail( get_the_ID(), 'konte-post-thumbnail-shortcode' )
				);
			endif;

			$output[] = sprintf(
				'<div class="%s">
					%s
					<div class="konte-post-grid__summary">
						<h5 class="post-title"><a href="%s" rel="bookmark">%s</a></h5>
						<div class="post-summary">%s</div>
						<a class="button alt" href="%s">%s</a>
					</div>
				</div>',
				esc_attr( implode( ' ', get_post_class() ) ),
				$thumbnail,
				esc_url( get_permalink() ),
				get_the_title(),
				get_the_excerpt(),
				esc_url( get_permalink() ),
				esc_html__( 'Continue reading', 'konte-addons' )
			);
		endwhile;

		wp_reset_postdata();

		$data = array(
			'dots'           => true,
			'arrows'         => false,
			'infinite'       => (bool) $atts['infinite'],
			'slidesToShow'   => intval( $atts['slide'] ),
			'slidesToScroll' => intval( $atts['scroll'] ),
		);

		return sprintf(
			'<div class="%s" data-slick="%s" %s>%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( json_encode( $data ) ),
			is_rtl() ? 'dir="rtl"' : '',
			implode( '', $output )
		);
	}

	/**
	 * Post grid.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function dash( $atts ) {
		$atts = shortcode_atts( array(
			'text'          => '',
			'color'         => 'default',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-dash',
			'text-' . $atts['color'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		return sprintf(
			'<span class="%s"><span class="konte-dash__line"></span>%s</span>',
			esc_attr( implode( ' ', $css_class ) ),
			! empty( $atts['text'] ) ? '<span class="konte-dash__text">' . esc_html( $atts['text'] ) . '</span>' : ''
		);
	}

	/**
	 * Info List
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function info_list( $atts ) {
		$atts = shortcode_atts( array(
			'info' => urlencode( json_encode( array(
				array(
					'label' => esc_html__( 'Address', 'konte-addons' ),
					'value' => '9606 North MoPac Expressway Suite 700 Austin, TX 78759',
				),
				array(
					'label' => esc_html__( 'Phone', 'konte-addons' ),
					'value' => '+1 248-785-8545',
				),
				array(
					'label' => esc_html__( 'Email', 'konte-addons' ),
					'value' => 'kontetheme@google.com',
				),
			) ) ),
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$info = (array) vc_param_group_parse_atts( $atts['info'] );
		} else {
			$info = json_decode( urldecode( $atts['info'] ), true );
		}

		$css_class = array(
			'konte-info-list',
			$atts['el_class'],
		);

		$animation = self::get_css_animation( $atts['css_animation'] );

		$list = array();
		foreach ( $info as $item ) {
			$value = wp_kses_post( $item['value'] );

			if ( is_email( $value ) ) {
				$value = sprintf( '<a href="mailto:%s" class="konte-info-list__email-link">%s</a>', $value, $value );
			}

			$list[] = sprintf(
				'<li class="%s">
					<span class="info-name">%s</span>
					<span class="info-value">%s</span>
				</li>',
				$animation,
				esc_html( $item['label'] ),
				$value // Escaped before
			);
		}

		if ( ! $list ) {
			return '';
		}

		return sprintf( '<div class="%s"><ul>%s</ul></div>', esc_attr( implode( ' ', $css_class ) ), implode( '', $list ) );
	}

	/**
	 * Count down
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function countdown( $atts ) {
		$atts = shortcode_atts( array(
			'date'          => '',
			'type'          => 'full',
			'align'         => 'inline',
			'color'         => 'default',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		if ( empty( $atts['date'] ) ) {
			return '';
		}

		$css_class = array(
			'konte-countdown',
			'konte-countdown--align-' . $atts['align'],
			'text-' . $atts['color'],
			'type-' . $atts['type'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$labels = array(
			'day'    => esc_html__( 'Days', 'konte-addons' ),
			'hour'   => esc_html__( 'Hours', 'konte-addons' ),
			'minute' => esc_html__( 'Minutes', 'konte-addons' ),
			'second' => esc_html__( 'Seconds', 'konte-addons' ),
		);

		if ( 'small' == $atts['type'] ) {
			$labels['minute'] = esc_html__( 'Mins', 'konte-addons' );
			$labels['second'] = esc_html__( 'Sec', 'konte-addons' );
		}

		$output   = array();
		$output[] = sprintf( '<div class="timers" data-date="%s">', esc_attr( $atts['date'] ) );
		$output[] = sprintf( '<div class="timer-day konte-countdown__box"><span class="time day"></span><span class="konte-countdown__box-label">%s</span></div>', $labels['day'] );
		$output[] = sprintf( '<div class="timer-hour konte-countdown__box"><span class="time hour"></span><span class="konte-countdown__box-label">%s</span></div>', $labels['hour'] );
		$output[] = sprintf( '<div class="timer-min konte-countdown__box"><span class="time min"></span><span class="konte-countdown__box-label">%s</span></div>', $labels['minute'] );
		$output[] = sprintf( '<div class="timer-secs konte-countdown__box"><span class="time secs"></span><span class="konte-countdown__box-label">%s</span></div>', $labels['second'] );
		$output[] = '</div>';

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Category banner.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function category_banner( $atts ) {
		$atts = shortcode_atts( array(
			'category'       => '',
			'image_source'   => 'media_library',
			'image'          => '',
			'image_src'      => '',
			'image_size'     => 'full',
			'sub_image'      => '',
			'sub_image_src'  => '',
			'align'          => 'left',
			'link'           => '',
			'title'          => '',
			'title_position' => 'bottom',
			'button_text'    => esc_html__( 'Explore Now', 'konte-addons' ),
			'css_animation'  => '',
			'el_class'       => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-category-banner',
			'align-' . $atts['align'],
			'title-' . $atts['title_position'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$width = false;
		$image = '';

		if ( 'media_library' == $atts['image_source'] ) {
			if ( $atts['image'] ) {
				if ( ! function_exists( 'wpb_getImageBySize' ) ) {
					$image_url = wp_get_attachment_image_src($atts['image'], $atts['image_size']);
					$width = $image_url ? $image_url[1] : false;
				}

				$image = self::get_image( $atts['image'], $atts['image_size'] );
			}

			if ( $atts['sub_image'] ) {
				$css_class[] = 'has-sub-image';

				$sub_image = self::get_image( $atts['sub_image'], '200x200', 'medium' );
			}
		} else {
			if ( $atts['image_src'] ) {
				$image = '<img src="' . esc_url( $atts['image_src'] ) . '" alt="' . esc_attr( $atts['title'] ) . '">';
			}

			if ( $atts['sub_image_src'] ) {
				$sub_image = '<img src="' . esc_url( $atts['sub_image_src'] ) . '" alt="' . esc_attr( $atts['category'] ) . '">';
			}
		}

		$link = self::build_link( $atts['link'] );

		if ( ! $width ) {
			$size = explode( 'x', $atts['image_size'] );
			$width = count( $size ) > 1 ? intval( $size[0] ) . 'px' : false;
		} else {
			$width = $width . 'px';
		}

		if ( ! $width ) {
			$css_class[] = 'auto-width';
		}

		return sprintf(
			'<div class="%s">
				<a href="%s" class="konte-category-banner__link" target="%s" rel="%s" %s>
					<span class="konte-category-banner__category">%s</span>
					<span class="konte-category-banner__image">%s%s</span>
					<span class="konte-category-banner__content">
						<h4 class="konte-category-banner__title">%s</h4>
						<span class="konte-category-banner__button">%s</span>
					</span>
				</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$link['url'] ? esc_url( $link['url'] ) : '#',
			$link['target'] ? esc_attr( $link['target'] ) : '_self',
			$link['rel'] ? esc_attr( $link['rel'] ) : 'bookmark',
			$width && 'middle' != $atts['title_position'] ? 'style="width:' . $width . '"' : '',
			esc_html( $atts['category'] ),
			$image,
			isset( $sub_image ) ? '<span class="konte-category-banner__sub-image">' . $sub_image . '</span>' : '',
			esc_html( $atts['title'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Product carousel
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function product_carousel( $atts ) {
		$atts = shortcode_atts( array(
			'limit'         => 15,
			'columns'       => 4,
			'type'          => 'recent',
			'category'      => '',
			'autoplay'      => 5000,
			'loop'          => false,
			'nav_style'     => 'arrow',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-products',
			'konte-product-carousel',
			'konte-product-carousel__nav-' . $atts['nav_style'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$data = array(
			'autoplay'       => intval( $atts['autoplay'] ) ? true: false,
			'autoplaySpeed'  => absint( $atts['autoplay'] ),
			'infinite'       => (bool) $atts['loop'],
			'slidesToShow'   => intval( $atts['columns'] ),
			'slidesToScroll' => intval( $atts['columns'] ),
		);

		return sprintf(
			'<div class="%s" data-slick="%s" data-nav_style="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( json_encode( $data ) ),
			esc_attr( $atts['nav_style'] ),
			self::products_shortcode( $atts )
		);
	}

	/**
	 * Product carousel 2
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function product_carousel2($atts) {
		$atts = shortcode_atts(array(
			'limit'         => 10,
			'type'          => 'recent',
			'category'      => '',
			'tag'           => '',
			'image_size'    => '350x440',
			'autoplay'      => 5000,
			'loop'          => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'woocommerce',
			'konte-products',
			'konte-product-carousel2',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( class_exists( 'WC_Shortcode_Products' ) ) {
			$type = 'recent';

			$types = array(
				'recent'       => 'recent_products',
				'sale'         => 'sale_products',
				'best_selling' => 'best_selling_products',
				'top_rated'    => 'top_rated_products',
				'featured'     => 'featured_products',
				'by_attribute' => 'product_attribute',
				'product'      => 'product',
			);

			if ( ! empty( $atts['type'] ) ) {
				$type = $atts['type'];

				// Fix incorrect type
				$type = isset( $types[ $type ] ) ? $types[ $type ] : $type;
			}

			if ( 'featured_products' == $type ) {
				$atts['visibility'] = 'featured';
			}

			$shortcode = new WC_Shortcode_Products( $atts, $type );
			$args = $shortcode->get_query_args();
		} else {
			$args = self::get_query_args( $atts );
		}

		$args['fields'] = 'ids';

		$query = new WP_Query( $args );
		$product_ids = $query->posts;

		$products = '';

		foreach ( $product_ids as $product_id ) {
			$_product = wc_get_product( $product_id );

			$button_class = implode( ' ', array(
				'underline-hover',
				'short-line',
				'add-to-cart',
				$_product->is_purchasable() && $_product->is_in_stock() ? 'add_to_cart_button' : '',
				$_product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			) );
			$button = sprintf(
				'<a href="%s" data-product_id="%s" data-product_sku="%s" aria-label="%s" data-quantity="1" rel="nofollow" class="%s">%s</a>',
				esc_url( $_product->add_to_cart_url() ),
				esc_attr( $_product->get_id() ),
				esc_attr( $_product->get_sku() ),
				esc_attr( $_product->add_to_cart_description() ),
				esc_attr( $button_class ),
				esc_html__( 'Buy Now', 'konte-addons' )
			);

			$products .= sprintf(
				'<li class="product %s">
					<a href="%s" class="product-link">
						%s
						<span class="product-summary">
							<h3 class="product-title">%s</h3>
							<span class="product-price">%s</span>
						</span>
					</a>
					%s
				</li>',
				esc_attr( 'product-type-' . $_product->get_type() ),
				esc_url( $_product->get_permalink() ),
				self::get_image( $_product->get_image_id(), $atts['image_size'], 'woocommerce_thumbnail' ),
				$_product->get_title(),
				$_product->get_price_html(),
				$button
			);
		}

		$data = array(
			'autoplay' => intval($atts['autoplay']) ? true : false,
			'autoplaySpeed' => absint($atts['autoplay']),
			'infinite' => (bool) $atts['loop'],
		);

		return sprintf(
			'<div class="%s" data-slick="%s"><ul class="products">%s</ul></div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( json_encode( $data ) ),
			$products
		);
	}

	/**
	 * Product tabs shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_tabs( $atts ) {
		$atts = shortcode_atts( array(
			'limit'         => 8,
			'columns'       => 4,
			'tab_type'      => 'category',
			'tab_all'       => false,
			'category'      => '',
			'tag'           => '',
			'groups'        => urlencode( json_encode( array(
				array(
					'type'  => 'best_selling',
					'title' => esc_html__( 'Best Selling', 'konte-addons' ),
				),
				array(
					'type'  => 'recent',
					'title' => esc_html__( 'Recent Products', 'konte-addons' ),
				),
				array(
					'type'  => 'sale',
					'title' => esc_html__( 'Sale Products', 'konte-addons' ),
				),
			) ) ),
			'carousel'      => false,
			'autoplay'      => 5000,
			'loop'          => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$tabs      = array();
		$index     = 1;
		$query     = null;
		$css_class = array(
			'konte-product-tabs',
			'konte-product-tabs__' . $atts['tab_type'],
			'konte-tabs',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['tab_all'] ) {
			$query             = self::parse_query_atts( $atts );
			$query['category'] = '';
			$query['tag']      = '';

			$tabs['all'] = sprintf( '<li data-target="%s" data-atts="%s" class="active">%s</li>', $index++, esc_attr( json_encode( $query ) ), esc_html__( 'All Products', 'konte-addons' ) );
		}

		switch ( $atts['tab_type'] ) {
			case 'category':
			case 'tag':
				$taxonomy = 'category' == $atts['tab_type'] ? 'product_cat': 'product_tag';
				$field    = 'category' == $atts['tab_type'] ? 'category':    'tag';
				$slugs    = explode( ',', $atts[ $field ] );
				$args     = self::parse_query_atts( $atts );
				$terms    = get_terms( array(
					'taxonomy' => $taxonomy,
					'orderby' => 'slug__in',
					'slug' => $slugs,
				) );

				foreach ( $terms as $term ) {
					$args['category'] = $args['tag'] = '';
					$args[ $field ]   = $term->slug;
					$query            = $query ? $query : $args; // Check if this is the all products tab then store the query

					$tabs[ $term->slug ] = sprintf( '<li data-target="%s" data-atts="%s" class="%s">%s</li>', $index++, esc_attr( json_encode( $args ) ), (2 === $index ? 'active' : ''), $term->name );
				}
				break;

			case 'groups':
				$groups = json_decode( urldecode( $atts['groups'] ), true );

				if ( empty( $groups ) ) {
					break;
				}

				foreach ( $groups as $i => $group ) {
					$args             = self::parse_query_atts( $atts );
					$args['category'] = $args['tag'] = '';
					$args['type']     = $group['type'];
					$query            = $query ? $query : $args; // Check if this is the all products tab then store the query

					$tabs[ $group['type'] . $i ] = sprintf( '<li data-target="%s" data-atts="%s" class="%s">%s</li>', $index++, esc_attr( json_encode( $args ) ), (2 === $index ? 'active' : ''), $group['title'] );
				}

				break;
		}

		if ( empty( $tabs ) ) {
			return '';
		}

		$panel_css_class = array( 'konte-products' );

		if ( $atts['carousel'] ) {
			$css_class[] = 'tabs-carousel';
			$panel_css_class[] = 'konte-product-carousel';
			$panel_css_class[] = 'konte-product-carousel__nav-angle';

			$data = array(
				'autoplay'       => intval( $atts['autoplay'] ) ? true: false,
				'autoplaySpeed'  => absint( $atts['autoplay'] ),
				'infinite'       => (bool) $atts['loop'],
				'slidesToShow'   => intval( $atts['columns'] ),
				'slidesToScroll' => intval( $atts['columns'] ),
			);
		} else {
			$panel_css_class[] = 'konte-product-grid';
		}

		return sprintf(
			'<div class="%s">
				<ul class="konte-product-tabs__tabs konte-tabs__nav">%s</ul>
				<div class="konte-product-tabs__panels konte-tabs__panels">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( "\n\t", $tabs ),
			sprintf(
				'<div class="%s konte-product-tabs__panel konte-tabs__panel active" data-panel="1" %s>%s</div>',
				esc_attr( implode( ' ', $panel_css_class ) ),
				$atts['carousel'] ? 'data-slick="' . esc_attr( json_encode( $data ) ) . '" data-nav_style="angle"' : '',
				self::products_shortcode( $query )
			)
		);
	}

	/**
	 * Product grid
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function product_grid($atts, $content) {
		$atts = shortcode_atts(array(
			'title'         => '',
			'limit'         => 7,
			'columns'       => 4,
			'type'          => 'recent',
			'order'         => 'ASC',
			'orderby'       => '',
			'category'      => '',
			'tag'           => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'konte-product-grid',
			'konte-products',
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		$grid = self::products_shortcode( $atts );

		if ( ! empty( $atts['title'] ) && ! empty( $content ) ) {
			$css_class[] = 'has-heading';

			$title = '<li class="product konte-product-grid__head"><h2 class="konte-product-grid__title">' . $atts['title'] . '</h2><div class="konte-product-grid__description">' . $content . '</div></li>';
			$grid = preg_replace( '/<ul class=["\']products[^>]+>/i', '$0' . $title, $grid );
		}

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr(implode(' ', $css_class)),
			$grid
		);
	}

	/**
	 * Product grid
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function product_masonry($atts, $content) {
		$atts = shortcode_atts(array(
			'title'         => esc_html__( 'Explore our products', 'konte-addons' ),
			'limit'         => 4,
			'type'          => 'recent',
			'order'         => 'ASC',
			'orderby'       => '',
			'category'      => '',
			'tag'           => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'konte-product-masonry',
			'konte-products',
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		$grid = self::products_shortcode( $atts );

		if ( ! empty( $atts['title'] ) ) {
			$css_class[] = 'has-heading';

			$desc = ! empty( $content ) ? '<div class="konte-product-masonry__description">' . $content . '</div>' : '';
			$title = '<li class="product konte-product-masonry__head"><div class="konte-dash text-default"><span class="konte-dash__line"></span></div><h2 class="konte-product-masonry__title">' . $atts['title'] . '</h2>' . $desc . '</li>';
			$grid = preg_replace( '/<ul class=["\']products[^>]+>/i', '$0' . $title, $grid );
		}

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr(implode(' ', $css_class)),
			$grid
		);
	}

	/**
	 * Product
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function product( $atts ) {
		$atts = shortcode_atts(array(
			'id'            => '',
			'image_source'  => 'media_library',
			'image'         => '',
			'image_size'    => 'full',
			'image_src'     => '',
			'button_text'   => '',
			'text_color'    => 'light',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'konte_' . __FUNCTION__);

		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$css_class = array(
			'woocommerce',
			'konte-product',
			'text-' . $atts['text_color'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		$_product = wc_get_product( $atts['id'] );
		$_product = $_product ? $_product : wc_get_product( self::get_default_product_id() );

		if ( ! $_product ) {
			return '';
		}

		$css_class[] = 'product-type-' . $_product->get_type();

		if (function_exists('vc_shortcode_custom_css_class')) {
			$css_class[] = vc_shortcode_custom_css_class($atts['css']);
		}

		if ( 'external_link' == $atts['image_source'] && $atts['image_src'] ) {
			$image = '<img src="' . esc_url( $atts['image_src'] ) . '" alt="' . esc_attr( $_product->get_title() ) . '">';
		} elseif ( 'media_library' == $atts['image_source'] ) {
			$image_id = ! empty( $atts['image'] ) ? $atts['image'] : $_product->get_image_id();
			$image = self::get_image( $image_id );
		} else {
			$image = '';
		}

		if ( ! empty( $image ) ) {
			$image = '<a href="' . esc_url( $_product->get_permalink() ) . '" class="product-image">' . $image . '</a>';
		}

		$button_class = implode( ' ', array(
			'underline-hover',
			'short-line',
			'add-to-cart',
			$_product->is_purchasable() && $_product->is_in_stock() ? 'add_to_cart_button' : '',
			$_product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
		) );

		$output = '';
		$output .= '<a href="' . esc_url( $_product->get_permalink() ) . '" class="konte-product__hidden-url" rel="nofollow">' . $_product->get_title() . '</a>';
		$output .= get_the_term_list( $_product->get_id(), 'product_cat', '<span class="product-cats">', ',', '</span>' );
		$output .= '<h3 class="product-title"><a href="' . esc_url( $_product->get_permalink() ) . '">' . $_product->get_title() . '</a></h3>';
		$output .= '<p class="product-price">' . $_product->get_price_html() . '</p>';
		$output .= sprintf(
			'<a href="%s" data-product_id="%s" data-product_sku="%s" aria-label="%s" data-quantity="1" rel="nofollow" class="%s">%s</a>',
			esc_url($_product->add_to_cart_url()),
			esc_attr($_product->get_id()),
			esc_attr($_product->get_sku()),
			esc_attr($_product->add_to_cart_description()),
			esc_attr($button_class),
			empty($atts['button_text']) ? $_product->add_to_cart_text() : esc_html($atts['button_text'])
		);

		return sprintf(
			'<div class="%s">%s<div class="konte-product__wrapper">%s</div></div>',
			esc_attr(implode(' ', $css_class)),
			$image,
			$output
		);
	}

	/**
	 * Promotion
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function promotion($atts) {
		$atts = shortcode_atts(array(
			'layout' => 'standard',
			'text' => '',
			'link' => '',
			'button_text' => '',
			'color' => 'default',
			'css_animation' => '',
			'el_class' => '',
			'css' => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'konte-promotion',
			'layout-' . $atts['layout'],
			'text-' . $atts['color'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		if (function_exists('vc_shortcode_custom_css_class')) {
			$css_class[] = vc_shortcode_custom_css_class($atts['css']);
		}

		$link = self::build_link( $atts['link'] );

		$button = '';
		if ( ! empty( $link ) && ! empty( $atts['button_text'] ) ) {
			$button = sprintf(
				'<a href="%s" target="%s" rel="%s" title="%s" class="konte-button button-underline underline-full small">%s</a>',
				esc_url( $link['url'] ),
				esc_attr( $link['target'] ),
				esc_attr( $link['rel'] ),
				esc_attr( $link['title'] ),
				esc_html( $atts['button_text'] )
			);
		}

		return sprintf(
			'<div class="%s"><p class="konte-promotion__text">%s</p>%s</div>',
			esc_attr(implode(' ', $css_class)),
			wp_kses_post( $atts['text'] ),
			$button
		);
	}

	/**
	 * Promotion
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function cta( $atts, $content ) {
		$atts = shortcode_atts(array(
			'heading'       => esc_attr__( 'Mid Season Sale', 'konte-addons' ),
			'link'          => '',
			'button_text'   => esc_attr__( 'Shop Now', 'konte-addons' ),
			'note'          => '',
			'color'         => 'default',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'konte_' . __FUNCTION__);

		$css_class = array(
			'konte-cta',
			'text-' . $atts['color'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		if (function_exists('vc_shortcode_custom_css_class')) {
			$css_class[] = vc_shortcode_custom_css_class($atts['css']);
		}

		$heading = $atts['heading'] ? '<h5 class="konte-cta__heading">' . esc_html( $atts['heading'] ) . '</h5>' : '';
		$note = $atts['note'] ? '<p class="konte-cta__note">' . esc_html( $atts['note'] ) . '</p>' : '';
		$content = function_exists('wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content ) : $content;

		$link = self::build_link( $atts['link'] );
		$button = '';

		if ( ! empty( $link ) && ! empty( $link['url'] ) && ! empty( $atts['button_text'] ) ) {
			$button = sprintf(
				'<div class="konte-cta__button"><a href="%s" target="%s" rel="%s" title="%s" class="konte-button button-outline button">%s</a></div>',
				esc_url( $link['url'] ),
				esc_attr( $link['target'] ),
				esc_attr( $link['rel'] ),
				esc_attr( $link['title'] ),
				esc_html( $atts['button_text'] )
			);
		}

		return sprintf(
			'<div class="%s">
				<div class="konte-cta__content">
					%s<div class="konte-cta__text">%s</div>%s%s
				</div>
			</div>',
			esc_attr(implode(' ', $css_class)),
			$heading,
			$content,
			$button,
			$note
		);
	}

	/**
	 * Banner grid
	 *
	 * @param  array $atts
	 *
	 * @return string
	 */
	public static function banner_grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'height' => 840,
				'gap' => 4,
				'css_animation' => '',
				'el_class' => '',

				'banner1_image_source' => 'media_library',
				'banner1_image' => '',
				'banner1_image_src' => '',
				'banner1_image_position' => 'center center',
				'banner1_image_position_mobile' => 'center center',
				'banner1_image_position_custom' => '50% 50%',
				'banner1_image_position_mobile_custom' => '50% 50%',
				'banner1_link' => '',
				'banner1_button' => esc_html__( 'Shop Now', 'konte-addons' ),
				'banner1_tagline' => '',
				'banner1_text' => '',
				'banner1_text_position' => 'top-left',
				'banner1_text_color' => 'dark',

				'banner2_image_source' => 'media_library',
				'banner2_image' => '',
				'banner2_image_src' => '',
				'banner2_image_position' => 'center center',
				'banner2_image_position_mobile' => 'center center',
				'banner2_image_position_custom' => '50% 50%',
				'banner2_image_position_mobile_custom' => '50% 50%',
				'banner2_link' => '',
				'banner2_button' => esc_html__( 'Shop Now', 'konte-addons' ),
				'banner2_tagline' => '',
				'banner2_text' => '',
				'banner2_text_position' => 'top-left',
				'banner2_text_color' => 'dark',

				'banner3_image_source' => 'media_library',
				'banner3_image' => '',
				'banner3_image_src' => '',
				'banner3_image_position' => 'center center',
				'banner3_image_position_mobile' => 'center center',
				'banner3_image_position_custom' => '50% 50%',
				'banner3_image_position_mobile_custom' => '50% 50%',
				'banner3_link' => '',
				'banner3_button' => esc_html__( 'Shop Now', 'konte-addons' ),
				'banner3_tagline' => '',
				'banner3_text' => '',
				'banner3_text_position' => 'top-left',
				'banner3_text_color' => 'dark',

				'banner4_image_source' => 'media_library',
				'banner4_image' => '',
				'banner4_image_src' => '',
				'banner4_image_position' => 'center center',
				'banner4_image_position_mobile' => 'center center',
				'banner4_image_position_custom' => '50% 50%',
				'banner4_image_position_mobile_custom' => '50% 50%',
				'banner4_link' => '',
				'banner4_button' => esc_html__( 'Shop Now', 'konte-addons' ),
				'banner4_tagline' => '',
				'banner4_text' => '',
				'banner4_text_position' => 'top-left',
				'banner4_text_color' => 'dark',
			),
			$atts,
			'konte_' . __FUNCTION__
		);

		$css             = '';
		$banners         = '';
		$class_name      = uniqid( 'konte-banner-grid__' );
		$animation_class = self::get_css_animation($atts['css_animation']);

		$css_class       = array(
			$class_name,
			'konte-banner-grid',
			'gap-' . $atts['gap'],
			$animation_class,
			$atts['el_class'],
		);

		// Banner 1.
		if ( 'external_link' == $atts['banner1_image_source'] ) {
			$image_url = $atts['banner1_image_src'];
		} elseif ( is_numeric( $atts['banner1_image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['banner1_image'], 'full' );
			$image_url = $image ? $image[0] : '';
		} else {
			$image_url = $atts['banner1_image'];
		}

		$css .= self::get_css_background( '.' . $class_name . ' .konte-banner-grid__banner1 .banner-image', array(
			'image' => $image_url,
			'position' => $atts['banner1_image_position'],
			'position_custom' => $atts['banner1_image_position_custom'],
			'position_mobile' => $atts['banner1_image_position_mobile'],
			'position_mobile_custom' => $atts['banner1_image_position_mobile_custom'],
		) );
		$banners .= self::banner_grid__banner( array(
			'image'         => $atts['banner1_image'],
			'link'          => $atts['banner1_link'],
			'button'        => $atts['banner1_button'],
			'tagline'       => $atts['banner1_tagline'],
			'text'          => $atts['banner1_text'],
			'text_position' => $atts['banner1_text_position'],
			'text_color'    => $atts['banner1_text_color'],
			'animation'     => $atts['css_animation'],
			'el_class'      => array( 'konte-banner-grid__banner1' ),
		) );

		// Banner 2.
		if ( 'external_link' == $atts['banner2_image_source'] ) {
			$image_url = $atts['banner2_image_src'];
		} elseif ( is_numeric( $atts['banner2_image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['banner2_image'], 'full' );
			$image_url = $image ? $image[0] : '';
		} else {
			$image_url = $atts['banner2_image'];
		}

		$css .= self::get_css_background( '.' . $class_name . ' .konte-banner-grid__banner2 .banner-image', array(
			'image' => $image_url,
			'position' => $atts['banner2_image_position'],
			'position_custom' => $atts['banner2_image_position_custom'],
			'position_mobile' => $atts['banner2_image_position_mobile'],
			'position_mobile_custom' => $atts['banner2_image_position_mobile_custom'],
		) );
		$banners .= self::banner_grid__banner( array(
			'image'         => $atts['banner2_image'],
			'link'          => $atts['banner2_link'],
			'button'        => $atts['banner2_button'],
			'tagline'       => $atts['banner2_tagline'],
			'text'          => $atts['banner2_text'],
			'text_position' => $atts['banner2_text_position'],
			'text_color'    => $atts['banner2_text_color'],
			'animation'     => $atts['css_animation'],
			'el_class'      => array( 'konte-banner-grid__banner2' ),
		) );

		// Banner 3.
		if ( 'external_link' == $atts['banner3_image_source'] ) {
			$image_url = $atts['banner3_image_src'];
		} elseif ( is_numeric( $atts['banner3_image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['banner3_image'], 'full' );
			$image_url = $image ? $image[0] : '';
		} else {
			$image_url = $atts['banner3_image'];
		}

		$css .= self::get_css_background( '.' . $class_name . ' .konte-banner-grid__banner3 .banner-image', array(
			'image' => $image_url,
			'position' => $atts['banner3_image_position'],
			'position_custom' => $atts['banner3_image_position_custom'],
			'position_mobile' => $atts['banner3_image_position_mobile'],
			'position_mobile_custom' => $atts['banner3_image_position_mobile_custom'],
		) );
		$banners .= self::banner_grid__banner( array(
			'image'         => $atts['banner3_image'],
			'link'          => $atts['banner3_link'],
			'button'        => $atts['banner3_button'],
			'tagline'       => $atts['banner3_tagline'],
			'text'          => $atts['banner3_text'],
			'text_position' => $atts['banner3_text_position'],
			'text_color'    => $atts['banner3_text_color'],
			'animation'     => $atts['css_animation'],
			'el_class'      => array( 'konte-banner-grid__banner3' ),
		) );

		// Banner 4.
		if ( 'external_link' == $atts['banner4_image_source'] ) {
			$image_url = $atts['banner4_image_src'];
		} elseif ( is_numeric( $atts['banner4_image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['banner4_image'], 'full' );
			$image_url = $image ? $image[0] : '';
		} else {
			$image_url = $atts['banner4_image'];
		}

		$css .= self::get_css_background( '.' . $class_name . ' .konte-banner-grid__banner4 .banner-image', array(
			'image' => $image_url,
			'position' => $atts['banner4_image_position'],
			'position_custom' => $atts['banner4_image_position_custom'],
			'position_mobile' => $atts['banner4_image_position_mobile'],
			'position_mobile_custom' => $atts['banner4_image_position_mobile_custom'],
		) );
		$banners .= self::banner_grid__banner( array(
			'image'         => $atts['banner4_image'],
			'link'          => $atts['banner4_link'],
			'button'        => $atts['banner4_button'],
			'tagline'       => $atts['banner4_tagline'],
			'text'          => $atts['banner4_text'],
			'text_position' => $atts['banner4_text_position'],
			'text_color'    => $atts['banner4_text_color'],
			'animation'     => $atts['css_animation'],
			'el_class'      => array( 'konte-banner-grid__banner4' ),
		) );

		// CSS for responsive height.
		$css .= "
			.$class_name { height: " . ( floatval( $atts['height'] ) + $atts['gap'] ) . "px }
			@media (max-width: 1199px) { .$class_name { height: " . floatval( $atts['height'] ) * 0.7 . "px } }
			@media (max-width: 991px) { .$class_name { height: " . floatval( $atts['height'] ) * 0.6 . "px } }
			@media (max-width: 767px) { .$class_name { height: " . floatval( $atts['height'] ) * 0.6 * 2 . "px } }
			";

		return sprintf(
			'<style type="text/css">%s</style><div class="%s">%s</div>',
			$css,
			esc_attr( implode( ' ', $css_class ) ),
			$banners
		);
	}

	/**
	 * Single banner inside a banner grid.
	 *
	 * @param array $atts
	 * @return string
	 */
	protected static function banner_grid__banner( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'image'         => '',
			'link'          => '',
			'button'        => esc_html__( 'Shop Now', 'konte-addons' ),
			'tagline'       => '',
			'text'          => '',
			'text_position' => 'top-left',
			'text_color'    => 'dark',
			'animation'     => '',
			'el_class'      => '',
		) );

		$link      = self::build_link( $atts['link'] );

		$css_class = array_merge( $atts['el_class'], array(
			'konte-banner-grid__banner',
			'text-position-' . $atts['text_position'],
			'text-' . $atts['text_color'],
			self::get_css_animation( $atts['animation'] )
		) );

		$line_align = str_replace( array( 'top-', 'bottom-' ), '', $atts['text_position'] );
		$tagline = $atts['tagline'] ? '<span class="konte-banner-grid__banner-tagline">' . esc_html( $atts['tagline'] ) . '</span>' : '';
		$button = $atts['button'] ? $button = '<span class="konte-button button-underline underline-small underline-' . esc_attr( $line_align ) . '">' . esc_html( $atts['button'] ) . '</span>': '';

		return sprintf(
			'<div class="%s">
				<a href="%s" target="%s" rel="%s" title="%s" class="konte-banner-grid__banner-link">
					<span class="konte-banner-grid__banner-image banner-image"></span>
					<span class="konte-banner-grid__banner-content banner-content">
						%s
						<span class="konte-banner-grid__banner-text banner-title">%s</span>
						%s
					</span>
				</a>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			$tagline,
			wp_kses_post( $atts['text'] ),
			$button
		);
	}

	/**
	 * Subscribe box
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function subscribe_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'         => '',
			'style'         => 'style1',
			'form_id'       => '',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'konte_' . __FUNCTION__ );

		if ( ! $atts['form_id'] ) {
			return '';
		}

		$css_class = array(
			'konte-subscribe-box',
			'konte-subscribe-box--' . $atts['style'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		$title = empty( $atts['title'] ) ? '' : '<h2 class="konte-subscribe-box__title">' . esc_html( $atts['title'] ) . '</h2>';
		$content = function_exists('wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content ) : do_shortcode( $content );
		$content = $content ? '<div class="konte-subscribe-box__desc">' . $content . '</div>' : '';

		return sprintf(
			'<div class="%s">
				%s
				%s
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$title,
			$content,
			do_shortcode( '[mc4wp_form id="' . $atts['form_id'] . '"]' )
		);
	}

	/**
	 * Banner with countdown clock inside.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function banner_countdown( $atts ) {
		$atts = shortcode_atts( array(
			'image_source' => 'media_library',
			'image' => '',
			'image_size' => 'full',
			'image_src' => '',
			'tagline' => '',
			'text' => '',
			'link' => '',
			'button_text' => esc_html__( 'Shop Now', 'konte-addons' ),
			'date' => '',
			'color' => 'default',
			'css_animation' => '',
			'el_class' => '',
			'css' => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-banner-countdown',
			'text-' . $atts['color'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		$tagline = $atts['tagline'] ? '<p class="konte-banner-countdown__tagline">' . esc_html( $atts['tagline'] ) . '</p>' : '';
		$link = self::build_link($atts['link']);
		$button = '';
		$image = '';

		if ( ! empty( $link['url'] ) && ! empty( $atts['button_text'] ) ) {
			$button = '<a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '" class="konte-button button-underline underline-small underline-center medium">' . esc_html( $atts['button_text'] ) . '</a>';
		}

		if ( 'media_library' == $atts['image_source'] && $atts['image'] ) {
			$image = self::get_image( $atts['image'], $atts['image_size'] );
		} elseif ( 'external_link' == $atts['image_source'] && $atts['image_src'] ) {
			$image = '<img src="' . esc_url( $atts['image_src'] ) . '" alt="' . esc_attr( $atts['text'] ) . '">';
		}

		if ( ! empty( $link['url'] ) && $image ) {
			$image = '<a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '">' . $image . '</a>';
		}

		return sprintf(
			'<div class="%s">
				%s
				<div class="konte-banner-countdown__banner">
					%s
					<div class="konte-banner-countdown__text">%s</div>
					%s
				</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			$tagline,
			esc_html( $atts['text'] ),
			$button,
			$atts['date'] ? do_shortcode( '[konte_countdown date="' . $atts['date'] . '" type="small" color="' . $atts['color'] . '"]' ) : ''
		);
	}

	/**
	 * Empty space
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function empty_space( $atts ) {
		$atts = shortcode_atts( array(
			'height' => '32px',
			'height_xs' => '',
			'height_md' => '',
			'height_lg' => '',
			'hidden_xs' => '',
			'hidden_md' => '',
			'hidden_lg' => '',
			'el_class' => '',
		), $atts, 'konte_' . __FUNCTION__ );

		$css_class = array(
			'konte-empty-space',
			$atts['el_class'],
		);

		if ( $atts['hidden_xs'] ) {
			$css_class[] = 'hidden-xs';
		}

		if ( $atts['hidden_md'] ) {
			$css_class[] = 'hidden-md';
		}

		if ( $atts['hidden_lg'] ) {
			$css_class[] = 'hidden-lg';
		}

		if ( empty( $atts['height_xs'] ) && empty( $atts['height_md'] ) && empty( $atts['height_lg'] ) ) {
			return sprintf(
				'<div class="%s" style="height: %s"></div>',
				esc_attr( implode( ' ', $css_class ) ),
				esc_attr( $atts['height'] )
			);
		}

		$height = trim( $atts['height'] );
		$height = is_numeric( $height ) ? $height . 'px' : $height;
		$height_xs = empty( $atts['height_xs'] ) ? $height : ( is_numeric( $atts['height_xs'] ) ?  $atts['height_xs'] . 'px' :  $atts['height_xs'] );
		$height_md = empty( $atts['height_md'] ) ? $height : ( is_numeric( $atts['height_md'] ) ?  $atts['height_md'] . 'px' :  $atts['height_md'] );
		$height_lg = empty( $atts['height_lg'] ) ? $height : ( is_numeric( $atts['height_lg'] ) ?  $atts['height_lg'] . 'px' :  $atts['height_lg'] );

		return
			'<div class="' . esc_attr( implode( ' ', $css_class ) ) . '" aria-hidden="true">' .
				'<div class="konte-empty-space__xs visible-xs" style="height:' . esc_attr( $height_xs ) . '"></div>' .
				'<div class="konte-empty-space__sm visible-sm" style="height:' . esc_attr( $height ) . '"></div>' .
				'<div class="konte-empty-space__md visible-md" style="height:' . esc_attr( $height_md ) . '"></div>' .
				'<div class="konte-empty-space__lg visible-lg" style="height:' . esc_attr( $height_lg ) . '"></div>' .
			'</div>';
	}

	/**
	 * Instagram
	 */
	public static function instagram( $atts ) {
		$atts = shortcode_atts(
			array(
				'limit'         => 16,
				'columns'       => '8',
				'size'          => 'cropped',
				'css_animation' => '',
				'el_class'      => '',
			), $atts
		);

		if ( ! function_exists( 'konte_get_instagram_images' ) || ! function_exists( 'konte_instagram_image' ) ) {
			return esc_html__( 'This shortcode only works with the Konte theme', 'konte-addons' );
		}

		$limit = absint( $atts['limit'] );
		$columns = absint( $atts['columns'] );

		$css_class = [
			'konte-instagram',
			'konte-instagram--' . $atts['size'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		];

		$medias  = konte_get_instagram_images( $limit );
		$output = [];

		if ( is_wp_error( $medias ) ) {
			$output[] = wp_kses_post( $medias->get_error_message() );
		} elseif ( is_array( $medias ) ) {
			$medias = array_slice( $medias, 0, $limit );
			$output[] = '<ul class="konte-instagram__list columns-' . esc_attr( $columns ) . '">';

			foreach ( $medias as $media ) {
				$output[] = '<li class="konte-instagram__item">' . konte_instagram_image( $media, $atts['size'] ) . '</li>';
			}

			$output[] = '</ul>';
		}

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Instagram Carousel
	 */
	public static function instagram_carousel( $atts ) {
		$atts = shortcode_atts(
			array(
				'limit'        => 16,
				'slide'        => 6,
				'scroll'       => 3,
				'infinite'     => '1',
				'dots'         => false,
				'arrows'       => '1',
				'size'          => 'cropped',
				'css_animation' => '',
				'el_class'     => '',
			), $atts
		);

		if ( ! function_exists( 'konte_get_instagram_images' ) || ! function_exists( 'konte_instagram_image' ) ) {
			return esc_html__( 'This shortcode only works with the Konte theme', 'konte-addons' );
		}

		$limit = absint( $atts['limit'] );

		$css_class = [
			'konte-instagram-carousel',
			'konte-instagram--' . $atts['size'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		];

		$medias  = konte_get_instagram_images( $limit );

		$output = [];

		if ( is_wp_error( $medias ) ) {
			$output[] = wp_kses_post( $medias->get_error_message() );
		} elseif ( is_array( $medias ) ) {
			$medias = array_slice( $medias, 0, $limit );
			$output[] = '<div class="konte-instagram__list">';

			foreach ( $medias as $media ) {
				$output[] = '<div class="konte-instagram__item">' . konte_instagram_image( $media, $atts ) . '</div>';
			}

			$output[] = '</div>';
		}

		$direction = is_rtl() ? 'rtl' : 'ltr';

		$data = [
			'slidesToShow'   => absint( $atts['slide'] ),
			'slidesToScroll' => absint( $atts['scroll'] ),
			'dots'           => (bool) $atts['dots'],
			'arrows'         => (bool) $atts['arrows'],
			'infinite'       => (bool) $atts['infinite']
		];

		return sprintf(
			'<div class="%s" dir="%s" data-slick="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $direction ),
			esc_attr( wp_json_encode( $data ) ),
			implode( '', $output )
		);
	}

	/**
	 * Get CSS classes for animation
	 *
	 * @param string $css_animation
	 *
	 * @return string
	 */
	public static function get_css_animation( $css_animation ) {
		$output = '';

		if ( '' !== $css_animation && 'none' !== $css_animation ) {
			wp_enqueue_script( 'waypoints' );
			wp_enqueue_style( 'animate-css' );
			$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
		}

		return $output;
	}

	/**
	 * Get background image and position CSS
	 *
	 * @param string $selector
	 * @param array  $args
	 *
	 * @return string
	 */
	protected static function get_css_background( $selector, $args = array() ) {
		$args = wp_parse_args($args, array(
			'image' => '',
			'position' => 'center center',
			'position_custom' => 'center center',
			'position_mobile' => 'center center',
			'position_mobile_custom' => 'center center',
		));

		$position = 'custom' == $args['position'] ? $args['position_custom'] : $args['position'];
		$position_mobile = 'custom' == $args['position_mobile'] ? $args['position_mobile_custom'] : $args['position_mobile'];

		$css = $bgimage = '';

		if ($args['image']) {
			$bgimage = 'background-image: url(' . esc_attr($args['image']) . ')';
		}

		$css = "
			$selector {
				$bgimage;
				background-position: $position;
			}
			@media (max-width: 767px) {
				$selector {
					background-position: $position_mobile;
				}
			}
		";

		return $css;
	}

	/**
	 * Get coordinates
	 *
	 * @param string $address
	 * @param bool   $refresh
	 *
	 * @return array
	 */
	public static function get_coordinates( $address, $key = '', $refresh = false ) {
		$address_hash = md5( $address );
		$coordinates  = get_transient( $address_hash );
		$results      = array( 'lat' => '', 'lng' => '' );

		if ( $refresh || $coordinates === false ) {
			$args     = array( 'address' => urlencode( $address ), 'sensor' => 'false', 'key' => $key );
			$url      = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );
			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) ) {
				$results['error'] = esc_html__( 'Can not connect to Google Maps APIs.', 'konte-addons' ) . ' ' . $response->get_error_message();

				return $results;
			}

			$data = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $data ) ) {
				$results['error'] = esc_html__( 'Can not connect to Google Maps APIs', 'konte-addons' );

				return $results;
			}

			if ( $response['response']['code'] == 200 ) {
				$data = json_decode( $data );

				if ( $data->status === 'OK' ) {
					$coordinates = $data->results[0]->geometry->location;

					$results['lat']     = $coordinates->lat;
					$results['lng']     = $coordinates->lng;
					$results['address'] = (string) $data->results[0]->formatted_address;

					// cache coordinates for 3 months
					set_transient( $address_hash, $results, 3600 * 24 * 30 * 3 );
				} elseif ( $data->status === 'ZERO_RESULTS' ) {
					$results['error'] = esc_html__( 'No location found for the entered address.', 'konte-addons' );
				} elseif ( $data->status === 'INVALID_REQUEST' ) {
					$results['error'] = esc_html__( 'Invalid request. Did you enter an address?', 'konte-addons' );
				} else {
					$results['error'] = $data->error_message;
				}
			} else {
				$results['error'] = esc_html__( 'Unable to contact Google API service.', 'konte-addons' );
			}
		} else {
			$results = $coordinates; // return cached results
		}

		return $results;
	}

	/**
	 * Parse query args from shortcode attributes
	 *
	 * @param  array $atts Shortcode's attributes
	 *
	 * @return array
	 */
	protected static function parse_query_atts( $atts = array() ) {
		return shortcode_atts( array(
			'type'     => 'recent',
			'limit'    => 10,
			'columns'  => '',
			'category' => '',
			'tag'      => '',
		), $atts );
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @param array $atts Shortcode attributes
	 * @return string
	 */
	protected static function products_shortcode( $atts ) {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return self::product_loop( $atts );
		}

		$type = 'products';

		$types = array(
			'recent'       => 'recent_products',
			'sale'         => 'sale_products',
			'best_selling' => 'best_selling_products',
			'top_rated'    => 'top_rated_products',
			'featured'     => 'featured_products',
			'by_attribute' => 'product_attribute',
			'product'      => 'product',
		);

		if ( ! empty( $atts['type'] ) ) {
			$type = $atts['type'];

			// Fix incorrect type
			$type = isset( $types[ $type ] ) ? $types[ $type ] : $type;
		}

		switch ( $type ) {
			case 'recent_products':
				$atts['order']        = 'DESC';
				$atts['orderby']      = 'date';
				$atts['cat_operator'] = 'IN';
				break;

			case 'top_rated_products':
				$atts['orderby']      = 'title';
				$atts['order']        = 'ASC';
				$atts['cat_operator'] = 'IN';
				break;

			case 'sale_products':
			case 'best_selling_products':
				$atts['cat_operator'] = 'IN';
				break;

			case 'featured_products':
				$atts['cat_operator'] = 'IN';
				$atts['visibility']   = 'featured';
				break;

			case 'product':
				$atts['skus']  = isset( $atts['sku'] ) ? $atts['sku'] : '';
				$atts['ids']   = isset( $atts['id'] ) ? $atts['id'] : '';
				$atts['limit'] = '1';
				break;
		}

		// Use the default product order setting.
		if ( empty( $atts['orderby'] ) ) {
			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'DESC';

			if ( in_array( $orderby, array( 'menu_order', 'price' ) ) ) {
				$order = 'ASC';
			}

			$atts['orderby'] = strtolower( $orderby );
			$atts['order'] = strtoupper( $order );
		}

		$shortcode = new WC_Shortcode_Products( $atts, $type );

		return $shortcode->get_content();
	}

	/**
	 * Loop over found products.
	 *
	 * @param  array  $atts
	 * @param  string $loop_name
	 *
	 * @return string
	 * @internal param array $columns
	 */
	protected static function product_loop( $atts, $loop_name = 'konte_products_shortcode' ) {
		global $woocommerce_loop;

		$query_args = self::get_query_args( $atts );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$products = new WP_Query( $query_args );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$woocommerce_loop['name'] = $loop_name;
		$columns                  = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : null;

		if ( $columns ) {
			$woocommerce_loop['columns'] = $columns;
		}

		ob_start();

		if ( $products->have_posts() ) {
			woocommerce_product_loop_start();

			while ( $products->have_posts() ) : $products->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile; // end of the loop.

			woocommerce_product_loop_end();
		}

		$output = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

		if ( isset( $atts['load_more'] ) && $atts['load_more'] && $products->max_num_pages > 1 ) {
			$paged = max( 1, $products->get( 'paged' ) );
			$type  = isset( $atts['type'] ) ? $atts['type'] : 'recent';

			if ( $paged < $products->max_num_pages ) {
				$query_args['paged']++;

				$button = sprintf(
					'<div class="load-more text-center">
						<a href="#" class="button ajax-load-products" rel="nofollow" data-query="%s">
							<span class="button-text">%s</span>
							<span class="loading-icon">
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
							</span>
						</a>
					</div>',
					esc_attr( json_encode( $query_args ) ),
					esc_html__( 'Load More', 'konte-addons' )
				);

				$output .= $button;
			}
		}

		wc_reset_loop();
		wp_reset_postdata();

		return $output;
	}

	/**
	 * Build query args from shortcode attributes
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	protected static function get_query_args( $atts ) {
		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'orderby'                => get_option( 'woocommerce_default_catalog_orderby' ),
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => 1,
			'posts_per_page'         => $atts['limit'],
			'meta_query'             => WC()->query->get_meta_query(),
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => false,
		);

		if( version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query();
		}

		// Ordering
		if ( 'menu_order' == $args['orderby'] || 'price' == $args['orderby'] ) {
			$args['order'] = 'ASC';
		}

		if ( 'price-desc' == $args['orderby'] ) {
			$args['orderby'] = 'price';
		}

		if ( method_exists( WC()->query, 'get_catalog_ordering_args' ) ) {
			$ordering_args   = WC()->query->get_catalog_ordering_args( $args['orderby'], $args['order'] );
			$args['orderby'] = $ordering_args['orderby'];
			$args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$args['meta_key'] = $ordering_args['meta_key'];
			}
		}

		// Improve performance
		if ( ! isset( $atts['load_more'] ) || ! $atts['load_more'] ) {
			$args['no_found_rows'] = true;
		}

		if ( ! empty( $atts['category'] ) ) {
			$args['product_cat'] = $atts['category'];
			unset( $args['update_post_term_cache'] );
		}

		if ( ! empty($atts['tag'] ) ) {
			$args['product_tag'] = $atts['tag'];
			unset( $args['update_post_term_cache'] );
		}

		if ( ! empty( $atts['page'] ) ) {
			$args['paged'] = absint( $atts['page'] );
		}

		if ( isset( $atts['type'] ) ) {
			switch ( $atts['type'] ) {
				case 'featured':
					if( version_compare( WC()->version, '3.0.0', '<' ) ) {
						$args['meta_query'][] = array(
							'key'   => '_featured',
							'value' => 'yes',
						);
					} else {
						$args['tax_query'][] = array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'featured',
							'operator' => 'IN',
						);
					}

					unset( $args['update_post_meta_cache'] );
					break;

				case 'sale':
					$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
					break;

				case 'best_selling':
					$args['meta_key'] = 'total_sales';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					unset( $args['update_post_meta_cache'] );

					add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
					break;

				case 'new':
					if ( function_exists( 'konte_woocommerce_get_new_product_ids' ) ) {
						$args['post__in'] = array_merge( array( 0 ), konte_woocommerce_get_new_product_ids() );
					} else {
						$newness = intval( konte_get_option( 'shop_badge_newness' ) );

						if ( $newness > 0 ) {
							$args['date_query'] = array(
								'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) )
							);
						} else {
							$args['meta_query'][] = array(
								'key'   => '_is_new',
								'value' => 'yes',
							);
						}
					}
					break;

				case 'top_rated':
					unset( $args['product_cat'] );
					$args          = self::_maybe_add_category_args( $args, $atts['category'] );
					$args['order'] = 'DESC';
					break;
			}
		}

		return $args;
	}

	/**
	 * Adds a tax_query index to the query to filter by category.
	 *
	 * @param array $args
	 * @param string $category
	 *
	 * @return array;
	 */
	protected static function _maybe_add_category_args( $args, $category ) {
		if ( ! empty( $category ) ) {
			if ( empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => array_map( 'sanitize_title', explode( ',', $category ) ),
					'field'    => 'slug',
					'operator' => 'IN',
				),
			);
		}

		return $args;
	}

	/**
	 * WP Core doens't let us change the sort direction for invidual orderby params - https://core.trac.wordpress.org/ticket/17065.
	 *
	 * This lets us sort by meta value desc, and have a second orderby param.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public static function order_by_popularity_post_clauses( $args ) {
		global $wpdb;
		$args['orderby'] = "$wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_date DESC";
		return $args;
	}

	/**
	 * Get image
	 *
	 * @param int $image_id
	 * @param string $size
	 * @return string
	 */
	public static function get_image( $image_id, $size = 'full', $fallback_size = false, $attr = array() ) {
		if ( ! is_numeric( $image_id ) ) {
			return '<img src="' . esc_url( $image_id ) . '">';
		}

		$output = '';

		if ( function_exists( 'wpb_getImageBySize' ) ) {
			$image = wpb_getImageBySize( array(
				'attach_id'  => $image_id,
				'thumb_size' => $size,
			) );

			$output = $image['thumbnail'];
		} else {
			$size       = $fallback_size ? $fallback_size : $size;
			$size_array = explode( 'x' , $size );
			$size       = count( $size_array ) > 1 ? array_map( 'intval', $size_array ) : $size;

			$output = wp_get_attachment_image( $image_id, $size, false, $attr );
		}

		return $output;
	}

	/**
	 * Ajax function for displaying products.
	 */
	public static function ajax_products_shortcode() {
		if (empty($_POST['atts'])) {
			wp_send_json_error(esc_html__('No query data.', 'konte-addons'));
			exit;
		}

		$output = self::products_shortcode( $_POST['atts'] );

		wp_send_json_success( $output );
	}

	/**
	 * Build links from WPB Page Builder link attribute
	 *
	 * @param string $string
	 * @return array
	 */
	protected static function build_link( $string ) {
		if ( function_exists( 'vc_build_link' ) ) {
			return vc_build_link( $string );
		} else {
			$result       = array( 'url' => '', 'title' => '', 'target' => '', 'rel' => '' );
			$params_pairs = explode( '|', $string );

			if ( empty( $params_pairs ) ) {
				return $result;
			}

			foreach ( $params_pairs as $pair ) {
				$param = preg_split( '/\:/', $pair );

				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$result[ $param[0] ] = rawurldecode( $param[1] );
				}
			}

			return $result;
		}
	}

	/**
	 * Get product id default
	 */
	protected static function get_default_product_id() {
		$product_ids = get_posts('post_type=product&numberposts=1&fields=ids');

		return ! empty( $product_ids ) ? $product_ids[0] : 0;
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $image
 * @var $image_size
 * @var $desc
 * @var $text_position
 * @var $button_text
 * @var $button_size
 * @var $link
 * @var $google_fonts
 * @var $font_container
 * @var $el_class
 * @var $css
 * @var $font_container_data - returned from $this->getAttributes
 * @var $google_fonts_data   - returned from $this->getAttributes
 * Shortcode class
 * @var $this                WPBakeryShortCode_Konte_Banner
 */
$source = $image = $image_size = $custom_src = $button_type = $button_text = $link = $google_fonts = $font_container = $el_class = $css = $font_container_data = $google_fonts_data = '';
// This is needed to extract $font_container_data and $google_fonts_data
extract( $this->getAttributes( $atts ) );

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

extract( $this->getStyles( $el_class . $this->getCSSAnimation( $css_animation ), $css, $google_fonts_data, $font_container_data, $atts ) );

$settings = get_option( 'wpb_js_google_fonts_subsets' );
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
} else {
	$subsets = '';
}

if ( isset( $google_fonts_data['values']['font_family'] ) ) {
	wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
}

if ( ! empty( $styles ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
} else {
	$style = '';
}

$link = vc_build_link( $link );
if ( 'external_link' == $source ) {
	$image = sprintf( '<img alt="%s" src="%s">',
		esc_attr( $content ),
		esc_url( $custom_src )
	);
} elseif ( $image ) {
	$size = apply_filters( 'konte_banner_size', $atts['image_size'], $atts, 'konte_banner' );

	if ( function_exists( 'wpb_getImageBySize' ) ) {
		$image = wpb_getImageBySize( array(
			'attach_id'  => $atts['image'],
			'thumb_size' => $size,
		) );

		$image = $image['thumbnail'];
	} else {
		$size_array = explode( 'x', $size );
		$size       = count( $size_array ) == 1 ? $size : $size_array;

		$image = wp_get_attachment_image_src( $atts['image'], $size );

		if ( $image ) {
			$image = sprintf( '<img alt="%s" src="%s">',
				esc_attr( $atts['image'] ),
				esc_url( $image[0] )
			);
		}
	}
}

switch ( $button_type ) {
	case 'outline':
		$button = '<span class="konte-banner__button konte-button button-outline button ' . esc_attr( $button_size ) . ' square">' . esc_html( $button_text ) . '</span>';
		break;

	case 'underline':
		$line_align = str_replace( array( 'top-', 'bottom-' ), '', $text_position );
		$line_align = $line_align && 'under-image' != $line_align ? $line_align : ( is_rtl() ? 'right' : 'left' );
		$button     = '<span class="konte-banner__button konte-button button-underline underline-small underline-' . esc_attr( $line_align . ' ' . $button_size ) . '">' . esc_html( $button_text ) . '</span>';
		break;

	default:
		$button = '<span class="konte-banner__button konte-button button-normal button ' . esc_attr( $button_size ) . ' square">' . esc_html( $button_text ) . '</span>';
		break;
}

if ( ! empty( $button_visibility ) ) {
	$css_class .= ' konte-banner--button-visible-' . $button_visibility;
}

if ( $use_theme_fonts && ! empty( $content_weight ) ) {
	$css_class .= ' konte-banner--font-' . $content_weight;
}

$output = '';

$output .= '<div class="' . esc_attr( $css_class ) . '">';
if ( ! empty( $link ) && ! empty( $link['url'] ) ) {
	$output .= '<a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '">';
}
$output .= $image;
$output .= '<span class="konte-banner__content">';
if ( $tagline ) {
	$output .= '<span class="konte-banner__tagline">' . $tagline . '</span>';
}
$output .= '<span class="konte-banner__text" ' . $style . '>' . $content . '</span>';
if ( $desc ) {
	$output .= '<span class="konte-banner__description">' . $desc . '</span>';
}
$output .= $button;
$output .= '</span>';
if ( ! empty( $link ) && ! empty( $link['url'] ) ) {
	$output .= '</a>';
}
$output .= '</div>';

echo ! empty( $output ) ? $output : '';

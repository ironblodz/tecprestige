<?php
namespace KonteAddons\Elementor;

class Utils {
	/**
	 * Get terms array for select control
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public static function get_terms_options( $taxonomy = 'category' ) {
		$terms = konte_addons_get_terms_hierarchy( $taxonomy, '&#8212;' );

		if ( empty( $terms ) ) {
			return [];
		}

		$options = wp_list_pluck( $terms, 'name', 'slug' );

		return $options;
	}

	/**
	 * The carousel pagination
	 *
	 * @param boolean $echo
	 * @return void|string
	 */
	public static function carousel_pagination( $echo = true ) {
		$pagination = '<div class="konte-carousel__pagination swiper-pagination"></div>';

		if ( $echo ) {
			echo $pagination;
		} else {
			return $pagination;
		}
	}

	/**
	 * The carousel navigation
	 *
	 * @param string $type
	 * @param boolean $echo
	 * @return void|array
	 */
	public static function carousel_navigation( $type = 'angle', $echo = true ) {
		switch ( $type ) {
			case 'arrow':
				$left  = 'arrow-left';
				$right = 'arrow-left';
				break;

			default:
				$left  = 'left';
				$right = 'right';
				break;
		}

		$arrows = [
			'left' => '<div class="konte-carousel__arrow konte-carousel-navigation--' . esc_attr( $type ) . ' konte-carousel-navigation--prev">
						<span class="svg-icon icon-' . esc_attr( $left ) . '"><svg><use xlink:href="#' . esc_attr( $left ) . '"></use></svg></span>
					</div>',
			'right' => '<div class="konte-carousel__arrow konte-carousel-navigation--' . esc_attr( $type ) . ' konte-carousel-navigation--next">
						<span class="svg-icon icon-' . esc_attr( $right ) . '"><svg><use xlink:href="#' . esc_attr( $right ) . '"></use></svg></span>
					</div>',
		];

		if ( $echo ) {
			echo implode( '', $arrows );
		} else {
			return $arrows;
		}
	}
}
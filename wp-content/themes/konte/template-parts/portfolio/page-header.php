<?php
/**
 * Template part for displaying portfolio page header
 *
 * @package Konte
 */
?>

<div class="page-header portfolio-page-header portfolio-page-header--<?php echo esc_attr( konte_get_option( 'portfolio_page_header_content' ) ) ?>">
	<div class="portfolio-page-header__container <?php echo esc_attr( apply_filters( 'konte_content_container_class', 'container' ) ) ?>">

		<?php
		if ( is_post_type_archive( 'portfolio' ) ) {
			switch ( konte_get_option( 'portfolio_page_header_content' ) ) {
				case 'page_content':
					if ( $portfolio_page_id = get_option( 'konte_portfolio_page_id' ) ) {
						$portfolio_page_id = konte_get_translated_object_id( $portfolio_page_id );
						echo apply_filters( 'the_content', get_post_field( 'post_content', $portfolio_page_id ) );
					}
					break;

				case 'page_title':
					if ( $portfolio_page_id = get_option( 'konte_portfolio_page_id' ) ) {
						$portfolio_page_id = konte_get_translated_object_id( $portfolio_page_id );
						echo '<h1 class="page-title">' . get_post_field( 'post_title', $portfolio_page_id ) . '</h1>';
					}
					break;

				case 'custom':
					echo do_shortcode( wp_kses_post( konte_get_option( 'portfolio_page_header_custom' ) ) );
					break;
			}
		} else {
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
		}
		?>

	</div>
</div>
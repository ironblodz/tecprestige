<?php
/**
 * Template file for displaying single page header
 *
 * @package Konte
 */

$page_header_content = get_post_meta( get_the_ID(), 'page_featured_content', true );

if ( 'hidden' == $page_header_content ) {
	return;
}

if ( ! ( $page_header_height = get_post_meta( get_the_ID(), 'page_header_height', true ) ) ) {
	$page_header_height = konte_get_option( 'page_header_height' );

	if ( konte_get_option( 'page_header_full_height' ) ) {
		$page_header_height = 'full';
	}
}

if ( ! ( $page_title_display = get_post_meta( get_the_ID(), 'page_title_display', true ) ) ) {
	$page_title_display = konte_get_option( 'page_title_display' );
}

$page_header_class = array(
	'entry-header',
	'page-header',
	'single-page-header',
	'title-' . $page_title_display,
);

if ( 'full' == $page_header_height ) {
	$page_header_class[] = 'full-height';
}

if ( 'front' == $page_title_display ) {
	$page_header_textcolor = get_post_meta( get_the_ID(), 'page_title_color', true );
	$page_header_textcolor = $page_header_textcolor ? $page_header_textcolor : 'light';

	$page_header_class[] = 'text-' . $page_header_textcolor;
}
?>

<header class="<?php echo esc_attr( implode( ' ', $page_header_class ) ); ?>">
	<?php if ( 'none' != $page_title_display ) : ?>
		<div class="page-titles">
			<div class="container">
				<h1 class="entry-title"><?php single_post_title(); ?></h1>

				<?php if ( $subtitle = get_post_meta( get_the_ID(), '_subtitle', true ) ) : ?>
					<h4 class="entry-subtitle"><?php echo wp_kses_post( $subtitle ); ?></h4>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	switch ( $page_header_content ) {
		case 'video':
			$video_url = get_post_meta( get_the_ID(), 'page_featured_video', true );

			if ( $video_url ) {
				$video_id = konte_get_youtube_video_id( $video_url );

				if ( $video_id ) {
					konte_set_theme_prop( 'single_page', array( 'featured_image' => 'video' ) );
					printf(
						'<div class="entry-thumbnail"><div id="video-%s" class="video-background" data-video_id="%s" data-mute="%s"></div><div class="video-overlay"></div></div>',
						esc_attr( $video_id ),
						esc_attr( $video_id ),
						esc_attr( get_post_meta( get_the_ID(), 'page_featured_video_mute', true ) )
					);
				}
			}
			break;

		case 'map':
			$map_api_key = konte_get_option( 'api_google_map' );
			$map_address = get_post_meta( get_the_ID(), 'page_featured_map_address', true );
			$coordinates = get_post_meta( get_the_ID(), 'page_featured_map_coordinates', true );
			$coordinates = $coordinates ? explode( ',', $coordinates ) : array();
			$latlng      = 1 < count( $coordinates ) ? 'lat="' . floatval( $coordinates[0] ) . '" lng="' . floatval( $coordinates[1] ) . '"' : '';
			$map_zoom    = intval( get_post_meta( get_the_ID(), 'page_featured_map_zoom', true ) );
			$map_zoom    = $map_zoom ? $map_zoom : '';
			$marker      = '';

			if ( empty( $map_api_key ) || empty( $latlng ) && empty( $map_address ) ) {
				break;
			}

			if ( get_post_meta( get_the_ID(), 'page_featured_map_marker', true ) ) {
				$marker = get_theme_file_uri( '/images/map-marker.png' );
				$marker = ' marker="' . esc_url( $marker ) . '"';
			}

			if ( shortcode_exists( 'konte_map' ) ) {
				konte_set_theme_prop( 'single_page', array( 'featured_image' => 'map' ) );

				$shortcode = sprintf( '[konte_map address="%s" zoom="%s" %s]', $map_address, $map_zoom, $latlng . $marker );
				echo '<div class="entry-thumbnail">' . do_shortcode( $shortcode ) . '</div>';
			}
			break;

		case 'hidden':
			break;

		default:
			if ( has_post_thumbnail() ) {
				konte_set_theme_prop( 'single_page', array( 'featured_image' => 'image' ) );
				echo '<div class="entry-thumbnail" style="background-image: url(' . esc_url( get_the_post_thumbnail_url( null, 'full' ) ) . ')"></div>';
			}
			break;
	}
	?>

	<?php if ( 'full' == $page_header_height && 'front' == $page_title_display ) : ?>
		<span class="scroll"><?php esc_html_e( 'Scroll', 'konte' ); ?></span>
	<?php endif; ?>
</header>

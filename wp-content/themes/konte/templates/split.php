<?php
/**
 * Template Name: Splited Content
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class( 'split-page content-' . get_post_meta( get_the_ID(), 'split_content_position', true ) ); ?>>
					<div class="split-page-featured">
						<div class="entry-header">
							<?php
							switch ( get_post_meta( get_the_ID(), 'page_featured_content', true ) ) {
								case 'video':
									$video_url = get_post_meta( get_the_ID(), 'page_featured_video', true );

									if ( $video_url ) {
										$video_id = konte_get_youtube_video_id( $video_url );

										if ( $video_id ) {
											wp_enqueue_script( 'youtube-player-api', 'https://www.youtube.com/iframe_api' );
											printf(
												'<div id="video-%s" class="video-background" data-video_id="%s" data-mute="%s"></div><div class="video-overlay"></div>',
												esc_attr( $video_id ),
												esc_attr( $video_id ),
												esc_attr( get_post_meta( get_the_ID(), 'page_featured_video_mute', true ) )
											);
										}
									}
									break;

								case 'map':
									$api_key     = konte_get_option( 'api_google_map' );
									$address     = get_post_meta( get_the_ID(), 'page_featured_map_address', true );
									$coordinates = get_post_meta( get_the_ID(), 'page_featured_map_coordinates', true );
									$coordinates = $coordinates ? explode( ',', $coordinates ) : array();
									$latlng      = 1 < count( $coordinates ) ? 'lat="' . floatval( $coordinates[0] ) . '" lng="' . floatval( $coordinates[1] ) . '"' : '';
									$zoom        = intval( get_post_meta( get_the_ID(), 'page_featured_map_zoom', true ) );
									$zoom        = $zoom ? $zoom : '';
									$marker      = '';

									if ( empty( $api_key ) || empty( $address ) && empty( $latlng ) ) {
										break;
									}

									if ( get_post_meta( get_the_ID(), 'page_featured_map_marker', true ) ) {
										$marker = get_theme_file_uri( '/images/map-marker.png' );
										$marker = ' marker="' . esc_url( $marker ) . '"';
									}

									if ( shortcode_exists( 'konte_map' ) ) {
										$shortcode = sprintf( '[konte_map address="%s" zoom="%s" %s]', $address, $zoom, $latlng . $marker );
										echo do_shortcode( $shortcode );
									}
									break;

								case 'hidden':
									// Show nothing.
									break;

								default:
									if ( has_post_thumbnail() ) {
										echo '<div class="entry-thumbnail" style="background-image: url(' . esc_url( get_the_post_thumbnail_url( null, 'full' ) ) . ')"></div>';
									}
									break;
							}
							?>
						</div>
					</div><!-- .split-page-featured -->

					<div class="split-page-content">
						<div class="konte-container">
							<div class="entry-content">
								<?php
								the_content();

								wp_link_pages( array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'konte' ),
									'after'  => '</div>',
								) );
								?>
							</div>
						</div>
					</div><!-- .entry-content -->
				</div><!-- #post-<?php the_ID(); ?> -->

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

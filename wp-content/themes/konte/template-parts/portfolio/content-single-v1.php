<?php
/**
 * Template part for display single portfolio project content with layout v1
 *
 * @package Konte
 */
?>

<div id="project-<?php the_ID() ?>" <?php post_class( 'portfolio-project portfolio-project--layout-v1' ) ?>>
	<header class="project-header entry-header">
		<?php the_title( '<h1 class="project-title entry-title">', '</h1>' ) ?>
	</header>

	<div class="project-content entry-content">
		<?php the_content(); ?>
		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'konte' ),
			'after'  => '</div>',
		) );
		?>
	</div>

	<?php if ( function_exists( 'konte_addons_share_link' ) && konte_get_option( 'project_share' ) && ( $socials = konte_get_option( 'project_sharing_socials' ) ) ) : ?>
		<footer class="project-footer entry-footer">
			<div class="project-share social-share">
				<?php
				$visible_socials = array_splice( $socials, 0, 3 );

				foreach ( $visible_socials as $social ) {
					echo konte_addons_share_link( $social );
				}

				if ( $socials ) {
					echo '<span class="toggle-socials">';
					konte_svg_icon( 'icon=plus&size=small' );
					echo '<span class="social-list">';

					foreach ( $socials as $social ) {
						echo konte_addons_share_link( $social );
					}

					echo '</span>';
					echo '</span>';
				}
				?>
			</div>
		</footer>
	<?php endif; ?>
</div>
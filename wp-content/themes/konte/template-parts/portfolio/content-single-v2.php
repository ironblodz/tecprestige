<?php
/**
 * Template part for display single portfolio project content with layout v2
 *
 * @package Konte
 */

$text_color = get_post_meta( get_the_ID(), 'project_header_textcolor', true );
?>

<div id="project-<?php the_ID() ?>" <?php post_class( 'portfolio-project portfolio-project--layout-v2' ) ?>>
	<header class="project-header entry-header alignfull <?php echo ! empty( $text_color ) ? 'text-' . esc_attr( $text_color ) : 'text-dark' ?>" <?php echo has_post_thumbnail() ? 'style="background-image: url(' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ) . ')"' : '' ?>>
		<div class="project-header__container konte-container">
			<?php the_title( '<h1 class="project-title entry-title">', '</h1>' ) ?>
			<?php if ( has_excerpt() ) : ?>
				<div class="project-exerpt"><?php the_excerpt(); ?></div>
			<?php endif; ?>
			<div class="project-meta">
				<?php if ( has_term( '', 'portfolio_type' ) ) : ?>
					<p class="project-types">
						<span class="project-meta__label"><?php esc_html_e( 'Category', 'konte' ) ?></span>
						<span class="project-meta__colons">:</span>
						<span class="project-meta__value"><?php echo the_terms( get_the_ID(), 'portfolio_type', '' , ', ', '' ); ?></span>
					</p>
				<?php endif; ?>
				<p class="project-date">
					<span class="project-meta__label"><?php esc_html_e( 'Date', 'konte' ) ?></span>
					<span class="project-meta__colons">:</span>
					<span class="project-meta__value"><?php the_time( 'Y' ); ?></span>
				</p>
				<?php if ( has_term( '', 'portfolio_tag' ) ) : ?>
					<p class="project-tags">
						<span class="project-meta__label"><?php esc_html_e( 'Tags', 'konte' ) ?></span>
						<span class="project-meta__colons">:</span>
						<span class="project-meta__value"><?php echo the_terms( get_the_ID(), 'portfolio_tag', '' , ', ', '' ); ?></span>
					</p>
				<?php endif; ?>
			</div>
		</div>
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
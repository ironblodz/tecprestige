<?php
/**
 * Template part for displaying footer main
 *
 * @package Konte
 */

$sections = array(
	'left'   => konte_get_option( 'footer_main_left' ),
	'center' => konte_get_option( 'footer_main_center' ),
	'right'  => konte_get_option( 'footer_main_right' ),
);

/**
 * Hook: konte_footer_main_sections
 *
 * @hooked: konte_split_content_custom_footer - 10
 */
$sections = apply_filters( 'konte_footer_main_sections', $sections );

$sections = array_filter( $sections );

if ( empty( $sections ) ) {
	return;
}
?>
<div class="footer-main site-info items-<?php echo esc_attr( konte_get_option( 'footer_main_flow' ) ) ?>">
	<?php if ( konte_get_option( 'footer_main_border' ) ) : ?>
		<div class="footer-container <?php echo esc_attr( apply_filters( 'konte_footer_container_class', konte_get_option( 'footer_container' ), 'main' ) ); ?>">
			<hr class="divider">
		</div>
	<?php endif; ?>

	<div class="footer-container <?php echo esc_attr( apply_filters( 'konte_footer_container_class', konte_get_option( 'footer_container' ), 'main' ) ); ?>">
		<?php foreach ( $sections as $section => $items ) : ?>

			<div class="footer-items footer-<?php echo esc_attr( $section ); ?>">
				<?php
				foreach ( $items as $item ) {
					$item['item'] = $item['item'] ? $item['item'] : key( konte_footer_items_option() );
					konte_footer_item( $item['item'] );
				}
				?>
			</div>

		<?php endforeach; ?>
	</div>
</div>

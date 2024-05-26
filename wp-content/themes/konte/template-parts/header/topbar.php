<?php
/**
 * Template part for displaying the topbar
 *
 * @package Konte
 */

$left_items   = array_filter( (array) konte_get_option( 'topbar_left' ) );
$right_items  = array_filter( (array) konte_get_option( 'topbar_right' ) );
$center_items = array_filter( (array) konte_get_option( 'topbar_center' ) );
$topbar_class = array();

if ( ! empty( $center_items ) ) {
	$topbar_class[] = 'topbar--has-center';
}

if ( konte_get_option( 'mobile_topbar' ) ) {
	$topbar_class[] = 'topbar-mobile';
	$topbar_class[] = 'topbar-mobile--keep-' . konte_get_option( 'mobile_topbar_section' );
}
?>
<div id="topbar" class="topbar <?php echo esc_attr( implode( ' ', (array) apply_filters( 'konte_topbar_class', $topbar_class ) ) ); ?>">
	<div class="konte-container-fluid">
		<?php if ( ! empty( $left_items ) || ! empty( $center_items ) ) : ?>

		<?php endif; ?>
		<div class="topbar-items topbar-left-items">
			<?php konte_topbar_items( $left_items ); ?>
		</div>

		<?php if ( ! empty( $center_items ) ) : ?>
			<div class="topbar-items topbar-center-items">
				<?php konte_topbar_items( $center_items ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $right_items ) || ! empty( $center_items ) ) : ?>

		<?php endif; ?>
		<div class="topbar-items topbar-right-items">
			<?php konte_topbar_items( $right_items ); ?>
		</div>
	</div>
</div>

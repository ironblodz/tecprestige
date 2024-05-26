<?php
/**
 * Template part for displaying footer widgets
 *
 * @package Konte
 */



$layout      = konte_get_option( 'footer_widgets_layout' );
$columns     = intval( $layout );
$flex        = false;
$has_widgets = false;

for ( $i = 1; $i <= $columns; $i++ ) {
	$has_widgets = $has_widgets || is_active_sidebar( 'footer-' . $i );
}

if ( ! $has_widgets ) {
	return;
}

if ( '4-columns-diff' != $layout && konte_get_option( 'footer_widgets_flex' ) ) {
	$flex = true;
}
?>

<div class="footer-widgets widgets-area widgets-<?php echo esc_attr( $layout ) ?> <?php echo ! empty( $flex ) ? 'footer-widgets-flex' : '' ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'konte_footer_container_class', konte_get_option( 'footer_container' ), 'widgets' ) ); ?>">
		<div class="row">

			<?php
			for ( $i = 1; $i <= $columns; $i++ ) {
				$column_class = 12 / $columns;

				if ( '4-columns-diff' == $layout ) {
					if ( in_array( $i, array( 1, 4 ) ) ) {
						$column_class = 'col-xs-12 col-sm-12 col-md-4';
					} else {
						$column_class = 'col-xs-6 col-sm-6 col-md-2';
					}
				} else {
					$column_class = 'col-xs-12 col-sm-6 col-md-' . $column_class;
				}
				?>

				<div class="footer-widgets-area-<?php echo esc_attr( $i ) ?> footer-widgets-area <?php echo esc_attr( $column_class ) ?>">
					<?php dynamic_sidebar( 'footer-' . $i ); ?>
				</div>
				<?php
			}
			?>

		</div>
	</div>
</div>
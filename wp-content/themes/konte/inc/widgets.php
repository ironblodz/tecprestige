<?php
/**
 * Edit widgets fields
 *
 * @package Konte
 */

/**
 * Add more options to all widgets
 *
 * @param WP_Widget $widget
 */
function konte_widget_background_option( $widget, $return, $instance ) {
	$background = isset( $instance['_konte_background'] ) ? $instance['_konte_background'] : '';
	?>

	<p class="konte-widget-background-field">
		<label for="<?php echo esc_attr( $widget->get_field_id( '_konte_background' ) ); ?>"><?php esc_html_e( 'Background Color', 'konte' ) ?>:</label><br>
		<input type="text" id="<?php echo esc_attr( $widget->get_field_id( '_konte_background' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( '_konte_background' ) ); ?>" value="<?php echo esc_attr( $background ); ?>">
	</p>

	<?php
}

add_action( 'in_widget_form', 'konte_widget_background_option', 10, 3 );

/**
 * Save widget background setting
 *
 * @param array $instance
 * @param array $new_instance
 *
 * @return array
 */
function konte_widget_update_background( $instance, $new_instance ) {
	$instance['_konte_background'] = isset( $new_instance['_konte_background'] ) ? $new_instance['_konte_background'] : '';

	return $instance;
}

add_filter( 'widget_update_callback', 'konte_widget_update_background', 10, 2 );

/**
 * Enqueue scripts for widgets
 */
function konte_widget_scripts() {
	$screen = get_current_screen();

	if ( ! in_array( $screen->base, array( 'widgets', 'customize' ) ) ) {
		return;
	}

	wp_add_inline_style( 'common', '.konte-widget-background-field {display: none;} #blog-sidebar .konte-widget-background-field, [data-widget-area-id="blog-sidebar"] .konte-widget-background-field, #sub-accordion-section-sidebar-widgets-blog-sidebar .konte-widget-background-field {display: block;}' );
}

add_action( 'admin_enqueue_scripts', 'konte_widget_scripts' );

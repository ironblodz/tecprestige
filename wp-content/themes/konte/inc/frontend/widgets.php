<?php
/**
 * Load and register widgets
 *
 * @package Konte
 */

/**
 * Change markup of archive and category widget to include .count for post count
 *
 * @param string $output
 *
 * @return string
 */
function konte_widget_archive_count( $output ) {
	$output = preg_replace( '|\((\d+)\)|', '<span class="posts-count">\\1</span>', $output );

	return $output;
}

add_filter( 'wp_list_categories', 'konte_widget_archive_count' );
add_filter( 'get_archives_link', 'konte_widget_archive_count' );

/**
 * Apply the background setting to widgets in blog sidebar
 *
 * @param array $params
 *
 * @return array
 */
function konte_dynamic_sidebar_params( $params ) {
	if ( 'blog-sidebar' != $params[0]['id'] ) {
		return $params;
	}

	global $wp_registered_widgets;

	$widget_id  = $params[0]['widget_id'];
	$widget_obj = $wp_registered_widgets[ $widget_id ];
	$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
	$widget_num = $params[1]['number'];

	if ( empty( $widget_opt[ $widget_num ]['_konte_background'] ) ) {
		return $params;
	}

	$params[0]['before_widget'] = str_replace( 'class="', 'class="filled ', $params[0]['before_widget'] );
	$params[0]['before_widget'] = str_replace( '>', ' style="background-color: ' . esc_attr( $widget_opt[ $widget_num ]['_konte_background'] ) . '">', $params[0]['before_widget'] );

	return $params;
}

add_filter( 'dynamic_sidebar_params', 'konte_dynamic_sidebar_params' );

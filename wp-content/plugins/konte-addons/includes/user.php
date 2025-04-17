<?php
/**
 * Add more data for user
 */

/**
 * Add more contact methods for users.
 *
 * @param array $methods
 *
 * @return array
 */
function konte_addons_user_contact_methods( $methods ) {
	$methods['facebook']  = esc_html__( 'Facebook', 'konte-addons' );
	$methods['twitter']   = esc_html__( 'Twitter', 'konte-addons' );
	$methods['linkedin']  = esc_html__( 'Linkedin', 'konte-addons' );
	$methods['pinterest'] = esc_html__( 'Pinterest', 'konte-addons' );
	$methods['instagram'] = esc_html__( 'Instagram', 'konte-addons' );

	return $methods;
}

add_filter( 'user_contactmethods', 'konte_addons_user_contact_methods' );
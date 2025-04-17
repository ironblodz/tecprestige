<?php
/**
 * Widget Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image widget class
 */
class Konte_Addons_Mega_Menu_HTML_Widget extends Konte_Addons_Mega_Menu_Widget {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'html';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'HTML', 'konte-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'text' => '',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		echo do_shortcode( $this->get_data( 'text' ) );
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type' => 'textarea',
			'name' => 'text',
			'label' => __( 'Content', 'konte-addons' ),
		) );
	}
}
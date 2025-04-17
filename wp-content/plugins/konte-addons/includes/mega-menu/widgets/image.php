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
class Konte_Addons_Mega_Menu_Image_Widget extends Konte_Addons_Mega_Menu_Widget {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'image';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Banner Image', 'konte-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'image'  => array( 'id' => '', 'url' => '' ),
			'link'   => array( 'url' => '', 'target' => '' ),
			'desc'   => '',
			'button' => '',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();

		$this->render_link_open( $data['link'] );
		$this->render_image( $data['image'], 'full', array( 'alt' => $data['desc'] ) );

		if ( $data['button'] ) {
			printf( '<span class="menu-widget-image__button" role="button">%s</span>',  esc_html( $data['button'] ) );
		}

		$this->render_link_close( $data['link'] );

		if ( $data['desc'] ) {
			echo '<div class="menu-widget-image__desc">' . wp_kses_post( $data['desc'] ) . '</div>';
		}
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type'  => 'image',
			'label' => __( 'Image', 'konte-addons' ),
			'name'  => 'image',
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type' => 'textarea',
			'label' => __( 'Description', 'konte-addons' ),
			'name' => 'desc',
		) );

		$this->add_control( array(
			'type' => 'text',
			'name' => 'button',
			'label' => __( 'Button', 'konte-addons' ),
		) );
	}
}

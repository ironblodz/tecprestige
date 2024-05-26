<?php
/**
 * Widget Heading
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading widget class
 */
class Konte_Addons_Mega_Menu_Heading_Widget extends Konte_Addons_Mega_Menu_Widget {

	/**
	 * Set the widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'heading';
	}

	/**
	 * Set the widget label
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Heading', 'konte-addons' );
	}

	/**
	 * Default widget options
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'text' => '',
			'link' => array( 'url' => '', 'target' => '' ),
			'tag'  => 'span',
		);
	}

	/**
	 * Render widget content
	 */
	public function render() {
		$data = $this->get_data();

		printf( '<%s class="menu-widget-heading">', esc_attr( $data['tag'] ) );

		if ( $data['link'] ) {
			$this->render_link_open( $data['link'] );
		}

		echo esc_html( $data['text'] );

		if ( $data['link'] ) {
			$this->render_link_close( $data['link'] );
		}

		printf( '</%s>', esc_attr( $data['tag'] ) );
	}

	/**
	 * Widget setting fields.
	 */
	public function add_controls() {
		$this->add_control( array(
			'type'  => 'text',
			'label' => __( 'Text', 'konte-addons' ),
			'name'  => 'text',
		) );

		$this->add_control( array(
			'type' => 'link',
			'name' => 'link',
		) );

		$this->add_control( array(
			'type'  => 'select',
			'label' => __( 'Tag', 'konte-addons' ),
			'name'  => 'tag',
			'options' => array(
				'h1'   => 'h1',
				'h2'   => 'h2',
				'h3'   => 'h3',
				'h4'   => 'h4',
				'h5'   => 'h5',
				'h6'   => 'h6',
				'span' => 'span',
				'p'    => 'p',
				'div'  => 'div',
			),
		) );
	}
}

<?php

/**
 * Abstract class of a mega menu item widget.
 */
abstract class Konte_Addons_Mega_Menu_Widget {
	/**
	 * Widget data
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Widget controls
	 *
	 * @var array
	 */
	protected $_controls = array();

	/**
	 * Constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$data = wp_parse_args( $data, $this->get_defaults() );

		$this->set_data( $data );
		$this->add_controls();
	}

	/**
	 * Retrives widget name.
	 *
	 * @return string
	 */
	public function get_name() {}

	/**
	 * Get wiget title.
	 *
	 * @return string
	 */
	public function get_lable() {}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	public function render() {}

	/**
	 * Render the widget setting fields.
	 *
	 * @return void
	 */
	public function form() {
		if ( empty( $this->_controls ) ) {
			return;
		}

		foreach ( $this->_controls as $args ) {
			$this->print_control( $args );
		}
	}

	/**
	 * Get the setting field name attribute.
	 *
	 * @param string $name
	 * @return string
	 */
	public function get_field_name( $name, $group = '' ) {
		$name = $group ? $group . '.' . $name : $name;
		return $name;
	}

	/**
	 * Set/update widget data.
	 *
	 * @param array $data
	 */
	public function set_data( $data = array() ) {
		$this->_data = array_merge_recursive( $this->_data, $data );
	}

	/**
	 * Recieve widget data.
	 *
	 * @param string $props
	 * @return mixed
	 */
	public function get_data( $props = '' ) {
		if ( empty( $props ) ) {
			return $this->_data;
		}

		$props = explode( '.', $props );
		$value = $this->_data;

		foreach ( $props as $prop ) {
			if ( ! isset( $value[ $prop ] ) ) {
				return null;
			}

			$value = $value[ $prop ];
		}

		return $value;
	}

	/**
	 * Return the default widget data.
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array();
	}

	/**
	 * Add widget controls
	 */
	public function add_controls() {}

	/**
	 * Add a control field to the widget
	 *
	 * @param array $args
	 */
	public function add_control( $args ) {
		$args = wp_parse_args( $args, array(
			'type'        => 'text',
			'name'        => '',
			'label'       => '',
			'description' => '',
			'class'       => '',
		) );

		if ( empty( $args['name'] ) ) {
			return;
		}

		$args['value'] = isset( $args['value'] ) ? $args['value'] : $this->get_data( $args['name'] );

		$this->_controls[ $args['name'] ] = $args;
	}

	/**
	 * Check if a control exists in the widget.
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has_control( $name ) {
		return array_key_exists( $name, $this->_controls );
	}

	/**
	 * Print a single field
	 *
	 * @param array $args
	 */
	public function print_control( $args ) {
		$type   = ! empty( $args['type'] ) && is_string( $args['type'] ) ? $args['type'] : '';
		$method = 'print_control_' . $type;

		if ( method_exists( $this, $method ) ) {
			call_user_func( array( $this, $method ), $args );
		}
	}

	/**
	 * Add a text control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_text( $args ) {
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<label>
				<?php $this->control_label_text( $args['label'] ); ?>
				<input type="text" data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" class="widefat">
				<?php $this->control_description( $args['description'] ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Add a textarea control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_textarea( $args ) {
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<label>
				<?php $this->control_label_text( $args['label'] ); ?>
				<textarea data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>" class="widefat" rows="3" cols="20"><?php echo esc_textarea( $args['value'] ); ?></textarea>
				<?php $this->control_description( $args['description'] ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Add a checkbox control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_checkbox( $args ) {
		if ( empty( $args['options'] ) || ! is_array( $args['options'] ) ) {
			return;
		}
		$value = reset( array_keys( $args['options'] ) );
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<?php $this->control_label_text( $args['label'] ); ?>
			<label>
				<input
					type="checkbox"
					data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					<?php checked( true, in_array( $value, (array) $args['value'] ) ); ?>
				>
				<?php $this->control_label_text( $args['options'][ $value ], false ); ?>
			</label>
			<?php $this->control_description( $args['description'] ); ?>
		</p>
		<?php
	}

	/**
	 * Add a checklist control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_checklist( $args ) {
		if ( empty( $args['options'] ) ) {
			return;
		}
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<?php if ( $args['label'] ) : ?>
				<label><?php $this->control_label_text( $args['label'] ); ?></label>
			<?php endif; ?>
			<ul class="list">
				<?php foreach ( (array) $args['options'] as $value => $label ) : ?>
					<li>
						<label>
							<input
								type="checkbox"
								data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>[]"
								value="<?php echo esc_attr( $value ); ?>"
								<?php checked( true, in_array( $value, (array) $args['value'] ) ); ?>
							>
							<?php $this->control_label_text( $label, false ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</p>
		<?php
	}

	/**
	 * Add a checklist control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_select( $args ) {
		if ( empty( $args['options'] ) ) {
			return;
		}
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<label>
				<?php $this->control_label_text( $args['label'] ); ?>
				<select data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>" class="widefat">
					<?php foreach ( (array) $args['options'] as $value => $text ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( true, in_array( $value, (array) $args['value'] ) ); ?>>
							<?php echo esc_html( $text ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php $this->control_description( $args['description'] ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Add an image control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_image( $args ) {
		$image = wp_parse_args( (array) $args['value'], array( 'id' => '', 'url' => '' ) );
		$args['class'] .= empty( $image['id'] ) && empty( $image['url'] ) ? ' megamenu-media megamenu-media--empty' : ' megamenu-media';
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?>">
			<?php if ( $args['label'] ) : ?>
				<label><?php $this->control_label_text( $args['label'] ); ?></label>
			<?php endif; ?>

			<span class="megamenu-media__preview field-type-image__preview">
				<?php $this->render_image( $image ); ?>
			</span>

			<button class="megamenu-media__remove field-type-image__button-remove">
				<span class="dashicons dashicons-trash"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Remove', 'konte-addons' ) ?></span>
			</button>

			<input type="hidden" data-name="<?php echo $this->get_field_name( 'id', $args['name'] ); ?>" value="<?php echo esc_attr( $image['id'] ); ?>" data-image_input="id">
			<input type="hidden" data-name="<?php echo $this->get_field_name( 'url', $args['name'] ); ?>" value="<?php echo esc_attr( $image['url'] ); ?>" data-image_input="url">
		</p>
		<?php
	}

	/**
	 * Add a link control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_link( $args ) {
		$link = wp_parse_args( (array) $args['value'], array( 'url' => '', 'target' => '_self' ) );
		?>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?> field-link__url">
			<?php if ( $args['label'] ) : ?>
				<label><?php $this->control_label_text( $args['label'] ); ?></label>
			<?php endif; ?>
			<label>
				<?php esc_html_e( 'URL', 'konte-addons' ); ?><br>
				<input type="text" data-name="<?php echo esc_attr( $this->get_field_name( 'url', $args['name'] ) ); ?>" value="<?php echo esc_attr( $link['url'] ); ?>" class="widefat">
			</label>
		</p>
		<p class="<?php echo esc_attr( $this->control_wrapper_class( $args ) ); ?> field-link__target">
			<label>
				<input type="checkbox" data-name="<?php echo esc_attr( $this->get_field_name( 'target', $args['name'] ) ); ?>" value="_blank" <?php checked( '_blank', $link['target'] ); ?>>
				<?php esc_html_e( 'Open link in a new tab', 'konte-addons' ); ?><br>
			</label>
		</p>
		<?php
	}

	/**
	 * Add a hidden control to the widget
	 *
	 * @param array $args
	 */
	protected function print_control_hidden( $args ) {
		?>
		<input type="hidden" data-name="<?php echo esc_attr( $this->get_field_name( $args['name'] ) ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>">
		<?php
	}

	/**
	 * Display the control label text
	 *
	 * @param string $text
	 * @param string $newline
	 */
	protected function control_label_text( $text, $newline = 'after' ) {
		if ( empty( $text ) ) {
			return;
		}

		if ( 'before' === $newline ) {
			echo '<br>';
		}

		echo esc_html( $text );

		if ( 'after' === $newline ) {
			echo '<br>';
		}
	}

	/**
	 * Display the control description
	 *
	 * @param string $text
	 */
	protected function control_description( $text ) {
		if ( empty( $text ) ) {
			return;
		}
		?>
		<span class="description"><?php echo esc_html( $text ); ?></span>
		<?php
	}

	/**
	 * Get the control wrapper CSS classes
	 *
	 * @param array $args
	 * @return string
	 */
	protected function control_wrapper_class( $args ) {
		$class = 'description field-type-' . $args['type'] . ' field-' . $args['name'];

		if ( ! empty( $args['class'] ) ) {
			$class .= ' ' . $args['class'];
		}

		return $class;
	}

	/**
	 * Render the link open
	 *
	 * @param array $link
	 */
	protected function render_link_open( $link ) {
		if ( empty( $link['url'] ) ) {
			return;
		}

		$class = ! empty( $link['class'] ) ? $link['class'] : '';

		printf( '<a href="%s" target="%s" class="%s">', esc_url( $link['url'] ), esc_attr( $link['target'] ), esc_attr( $class ) );
	}

	/**
	 * Render the link close
	 *
	 * @param array $link
	 */
	protected function render_link_close( $link ) {
		if ( empty( $link['url'] ) ) {
			return;
		}

		echo '</a>';
	}

	/**
	 * Render a sinbgle image
	 *
	 * @param array $link
	 */
	protected function render_image( $image, $size = 'full', $attr = '' ) {
		if ( $image['id'] ) {
			echo wp_get_attachment_image( $image['id'], $size, false, $attr );
		} elseif ( $image['url'] ) {
			$attr = wp_parse_args( $attr, array(
				'src' => $image['url'],
				'alt' => '',
			) );

			if ( empty( $attr['alt'] ) ) {
				$image_info = pathinfo( $image['url'] );
				$attr['alt'] = basename( $image['url'], '.' . $image_info['extension'] );
			}

			$attr = array_map( 'esc_attr', $attr );
			$attr_str = '';

			foreach ( $attr as $name => $value ) {
				$attr_str .= " $name=" . '"' . $value . '"';
			}

			echo '<img ' . $attr_str . '>';
		}
	}
}

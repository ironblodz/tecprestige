<?php
/**
 * WooCommerce additional settings.
 *
 * @package Konte
 */

class Konte_WooCommerce_Settings {
	/**
	 * Initialize.
	 */
	public static function init() {
		// Option of newness.
		add_action( 'woocommerce_product_options_advanced', array( __CLASS__, 'product_advanced_options' ) );
		add_action( 'save_post', array( __CLASS__, 'save_product_data' ) );

		// Option of order tracking page.
		add_filter( 'woocommerce_get_settings_checkout', array( __CLASS__, 'order_tracking_setting' ) );
		add_filter( 'woocommerce_get_settings_advanced', array( __CLASS__, 'order_tracking_setting' ) );

		// Add page header fields to taxonomies.
		$taxonomies = get_object_taxonomies( 'product' );

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_edit_form_fields', array( __CLASS__, 'taxonomy_fields' ), 20 );
		}
		add_action( 'edit_term', array( __CLASS__, 'save_taxonomy_fields' ), 20, 3 );
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_advanced_options() {
		woocommerce_wp_checkbox( array(
			'id'          => '_is_new',
			'label'       => esc_html__( 'New product?', 'konte' ),
			'description' => esc_html__( 'Enable to set this product as a new product. A "New" badge will be added to this product.', 'konte' ),
		) );
	}

	/**
	 * Save product data.
	 *
	 * @param int $post_id The post ID.
	 */
	public static function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_is_new'] ) ) {
			delete_post_meta( $post_id, '_is_new' );
		} else {
			update_post_meta( $post_id, '_is_new', 'yes' );
		}
	}

	/**
	 * Register meta boxes for product.
	 *
	 * @param array $meta_boxes The Meta Box plugin configuration variable for meta boxes.
	 *
	 * @return array
	 */
	public static function product_meta_boxes( $meta_boxes ) {
		if ( in_array( konte_get_option( 'product_layout' ), array( 'v1', 'v3' ) ) ) {
			$meta_boxes[] = array(
				'id'       => 'display-settings',
				'title'    => esc_html__( 'Display Settings', 'konte' ),
				'pages'    => array( 'product' ),
				'context'  => 'normal',
				'priority' => 'low',
				'fields'   => array(
					array(
						'name' => esc_html__( 'Background Color', 'konte' ),
						'desc' => esc_html__( 'Pick a background color for product page. Or leave it empty to automatically detect the background from product main image.', 'konte' ),
						'id'   => 'background_color',
						'type' => 'color',
					),
				),
			);
		}

		return $meta_boxes;
	}

	/*
	 * Add setting for order tracking page.
	 *
	 * @param array $settings Checkout settings.
	 * @return array
	 */
	public static function order_tracking_setting( $settings ) {
		$new_settings = array();

		foreach ( $settings as $index => $setting ) {
			$new_settings[ $index ] = $setting;

			if ( isset( $setting['id'] ) && 'woocommerce_terms_page_id' == $setting['id'] ) {
				$new_settings['order_tracking_page_id'] = array(
					'title'    => esc_html__( 'Order Tracking Page', 'konte' ),
					'desc'     => esc_html__( 'Page content: [woocommerce_order_tracking]', 'konte' ),
					'id'       => 'order_tracking_page_id',
					'type'     => 'single_select_page',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => true,
				);
			}
		}

		return $new_settings;
	}

	/**
	 * Page header fields for product taxonomies.
	 *
	 * @param mixed $term Term being edited
	 */
	public static function taxonomy_fields( $term ) {
		$header_textcolor = get_term_meta( $term->term_id, 'header_textcolor', true );
		$text_color       = get_term_meta( $term->term_id, 'page_header_textcolor', true );
		$image_id         = absint( get_term_meta( $term->term_id, 'page_header_image_id', true ) );
		$image            = $image_id ? wp_get_attachment_thumb_url( $image_id ) : wc_placeholder_img_src();
		?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label><?php esc_html_e( 'Page Header Image', 'konte' ); ?></label>
			</th>
			<td>
				<div id="page-header-image" style="float: left; margin-right: 10px;">
					<img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" />
				</div>
				<div style="line-height: 60px;">
					<input type="hidden" id="page-header-image-id" name="page_header_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
					<button type="button" class="upload-header-image-button button"><?php esc_html_e( 'Upload/Add Image', 'konte' ); ?></button>
					<button type="button" class="remove-header-image-button button"><?php esc_html_e( 'Remove Image', 'konte' ); ?></button>
				</div>
				<div class="clear"></div>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="page_header_text_color"><?php esc_html_e( 'Page Header Text Color', 'konte' ); ?></label>
			</th>
			<td>
				<select name="page_header_textcolor" id="page_header_text_color" class="postform">
					<option value=""><?php esc_html_e( 'Default', 'konte' ) ?></option>
					<option value="dark" <?php selected( 'dark', $text_color ) ?>><?php esc_html_e( 'Dark', 'konte' ) ?></option>
					<option value="light" <?php selected( 'light', $text_color ) ?>><?php esc_html_e( 'Light', 'konte' ) ?></option>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="header_text_color"><?php esc_html_e( 'Header Text Color', 'konte' ); ?></label>
			</th>
			<td>
				<select name="header_textcolor" id="header_text_color" class="postform">
					<option value=""><?php esc_html_e( 'Default', 'konte' ) ?></option>
					<option value="dark" <?php selected( 'dark', $header_textcolor ) ?>><?php esc_html_e( 'Dark', 'konte' ) ?></option>
					<option value="light" <?php selected( 'light', $header_textcolor ) ?>><?php esc_html_e( 'Light', 'konte' ) ?></option>
				</select>
			</td>
		</tr>

		<?php
	}

	/**
	 * Save custom data of term.
	 * Save data of page header image and text color.
	 *
	 * @param mixed  $term_id Term ID being saved
	 * @param mixed  $tt_id
	 * @param string $taxonomy
	 */
	public static function save_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['page_header_image_id'] ) ) {
			update_term_meta( $term_id, 'page_header_image_id', absint( $_POST['page_header_image_id'] ) );
		}

		if ( isset( $_POST['page_header_textcolor'] ) ) {
			update_term_meta( $term_id, 'page_header_textcolor', $_POST['page_header_textcolor'] );
		}

		if ( isset( $_POST['header_textcolor'] ) ) {
			update_term_meta( $term_id, 'header_textcolor', $_POST['header_textcolor'] );
		}
	}
}

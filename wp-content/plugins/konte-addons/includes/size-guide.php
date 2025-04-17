<?php
/**
 * Size guide for WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WooCommerce Size Guide
 */
class Konte_Addons_WooCommerce_Size_Guide {
	const POST_TYPE     = 'konte_size_guide';
	const OPTION_NAME   = 'konte_size_guide';

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'settings_fields' ), 10, 2 );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		if ( 'yes' != $this->get_option() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_post_type' ) );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Add JS templates to footer.
		add_action( 'admin_print_scripts', array( $this, 'templates' ) );

		// Add options to product.
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );
		add_action( 'wp_ajax_konte_addons_load_product_size_guide_attributes', array( $this, 'ajax_load_product_size_guide_attributes' ) );

		// Display size guide.
		add_action( 'woocommerce_before_single_product', array( $this, 'display_size_guide'  ) );
	}

	/**
	 * Add Size Guide settings section to the Products setting tab.
	 *
	 * @param array $sections
	 * @return array
	 */
	public function settings_section( $sections ) {
		$sections['konte_addons_size_guide'] = esc_html__( 'Size Guide', 'konte-addons' );

		return $sections;
	}

	/**
	 * Adds a new setting field to products tab.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function settings_fields( $settings, $section ) {
		if ( 'konte_addons_size_guide' != $section ) {
			return $settings;
		}

		$settings_size_guide = array(
			array(
				'name' => esc_html__( 'Size Guide', 'konte-addons' ),
				'type' => 'title',
				'id'   => self::OPTION_NAME . '_options',
			),
			array(
				'name'    => esc_html__( 'Enable Size Guide', 'konte-addons' ),
				'desc'    => esc_html__( 'Enable product size guides', 'konte-addons' ),
				'id'      => self::OPTION_NAME,
				'default' => 'no',
				'type'    => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'    => esc_html__( 'Enable on variable products only', 'konte-addons' ),
				'id'      => self::OPTION_NAME . '_variable_only',
				'default' => 'no',
				'type'    => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'name'    => esc_html__( 'Guide Display', 'konte-addons' ),
				'id'      => self::OPTION_NAME . '_display',
				'default' => 'tab',
				'class'   => 'wc-enhanced-select',
				'type'    => 'select',
				'options' => array(
					'tab'   => esc_html__( 'In product tabs', 'konte-addons' ),
					'panel' => esc_html__( 'By clicking on a button', 'konte-addons' ),
				),
			),
			array(
				'name'    => esc_html__( 'Button Position', 'konte-addons' ),
				'id'      => self::OPTION_NAME . '_button_position',
				'default' => 'bellow_summary',
				'class'   => 'wc-enhanced-select',
				'type'    => 'select',
				'options' => array(
					'bellow_summary'   => esc_html__( 'Bellow short description', 'konte-addons' ),
					'bellow_price'     => esc_html__( 'Bellow price', 'konte-addons' ),
					'bellow_button'    => esc_html__( 'Bellow Add To Cart button', 'konte-addons' ),
					'beside_attribute' => esc_html__( 'Beside the Size attribute (for variable products only)', 'konte-addons' ),
				),
			),
			array(
				'name'    => esc_html__( 'Attribute Slug', 'konte-addons' ),
				'id'      => self::OPTION_NAME . '_attribute',
				'default' => 'size',
				'type'    => 'text',
				'desc_tip' => esc_html__( 'This is the slug of a product attribute', 'konte-addons' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => self::OPTION_NAME . '_options',
			),
		);

		return $settings_size_guide;
	}

	/**
	 * Register size guide post type
	 */
	public function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'description'         => esc_html__( 'Product size guide', 'konte-addons' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Size Guide', 'konte-addons' ),
				'singular_name'         => esc_html__( 'Size Guide', 'konte-addons' ),
				'menu_name'             => esc_html__( 'Size Guides', 'konte-addons' ),
				'all_items'             => esc_html__( 'Size Guides', 'konte-addons' ),
				'add_new'               => esc_html__( 'Add New', 'konte-addons' ),
				'add_new_item'          => esc_html__( 'Add New Size Guide', 'konte-addons' ),
				'edit_item'             => esc_html__( 'Edit Size Guide', 'konte-addons' ),
				'new_item'              => esc_html__( 'New Size Guide', 'konte-addons' ),
				'view_item'             => esc_html__( 'View Size Guide', 'konte-addons' ),
				'search_items'          => esc_html__( 'Search size guides', 'konte-addons' ),
				'not_found'             => esc_html__( 'No size guide found', 'konte-addons' ),
				'not_found_in_trash'    => esc_html__( 'No size guide found in Trash', 'konte-addons' ),
				'filter_items_list'     => esc_html__( 'Filter size guides list', 'konte-addons' ),
				'items_list_navigation' => esc_html__( 'Size guides list navigation', 'konte-addons' ),
				'items_list'            => esc_html__( 'Size guides list', 'konte-addons' ),
			),
			'supports'            => array( 'title', 'editor' ),
			'rewrite'             => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'show_in_menu'        => 'edit.php?post_type=product',
			'menu_position'       => 20,
			'capability_type'     => 'page',
			'query_var'           => is_admin(),
			'map_meta_cap'        => true,
			'exclude_from_search' => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'taxonomies'          => array( 'product_cat' ),
		) );
	}

	/**
	 * Add custom column to size guides management screen
	 * Add Thumbnail column
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$columns = array_merge( $columns, array(
			'apply_to' => esc_html__( 'Apply to Category', 'konte-addons' )
		) );

		return $columns;
	}

	/**
	 * Handle custom column display
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'apply_to':
				$applied_cat = get_post_meta( $post_id, 'size_guide_category', true );
				$applied_cat = is_array( $applied_cat ) ? 'custom' : $applied_cat;
				$applied_cat = $applied_cat ? $applied_cat : 'none';

				switch ( $applied_cat ) {
					case 'none':
						esc_html_e( 'No Category', 'konte-addons' );
						break;

					case 'all':
						esc_html_e( 'All Categories', 'konte-addons' );
						break;

					case 'custom':
						$cats  = wp_get_post_terms( $post_id, 'product_cat' );

						if ( is_wp_error( $cats ) ) {
							break;
						}

						foreach ( $cats as $index => $cat ) {
							printf( '<a href="%s">%s</a>', esc_url( get_edit_term_link( $cat, 'product_cat', 'product' ) ), $cat->name );

							if ( $index < count( $cats ) - 1 ) {
								echo ', ';
							}
						}
						break;
				}
				break;
		}
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'konte-size-guide-category', esc_html__( 'Apply to Categories', 'konte-addons' ), array( $this, 'category_meta_box' ), self::POST_TYPE, 'side' );
		add_meta_box( 'konte-size-guide-tables', esc_html__( 'Tables', 'konte-addons' ), array( $this, 'tables_meta_box' ), self::POST_TYPE, 'advanced', 'high' );
	}

	/**
	 * Category meta box.
	 *
	 * @param object $post
	 */
	public function category_meta_box( $post ) {
		$cats = get_post_meta( $post->ID, 'size_guide_category', true );
		$selected = is_array( $cats ) ? 'custom' : $cats;
		$selected = $selected ? $selected : 'none';
		?>

		<p>
			<label>
				<input type="radio" name="_size_guide_category" value="none" <?php checked( 'none', $selected ) ?>>
				<?php esc_html_e( 'No category', 'konte-addons' ); ?>
			</label>
		</p>

		<p>
			<label>
				<input type="radio" name="_size_guide_category" value="all" <?php checked( 'all', $selected ) ?>>
				<?php esc_html_e( 'All Categories', 'konte-addons' ); ?>
			</label>
		</p>

		<p>
			<label>
				<input type="radio" name="_size_guide_category" value="custom" <?php checked( 'custom', $selected ) ?>>
				<?php esc_html_e( 'Select Categories', 'konte-addons' ); ?>
			</label>
		</p>

		<div class="taxonomydiv" style="display: none;">
			<div class="tabs-panel">
				<ul class="categorychecklist">
					<?php
					wp_terms_checklist( $post->ID, array(
						'taxonomy' => 'product_cat',
					) );
					?>
				</ul>
			</div>
		</div>

		<?php
	}

	/**
	 * Tables meta box.
	 * Content will be filled by js.
	 *
	 * @param object $post
	 */
	public function tables_meta_box( $post ) {
		$tables = get_post_meta( $post->ID, 'size_guides', true );
		$tables = $tables ? $tables : array(
			'names' => array( '' ),
			'tabs' => array( __( 'Table 1', 'konte-addons' ) ),
			'tables' => array( '[["",""],["",""]]' ),
			'descriptions' => array( '' ),
			'information' => array( '' ),
		);
		wp_localize_script( 'konte-addons-size-guide', 'konteSizeGuideTables', $tables );
		?>

		<div id="konte-size-guide-tabs" class="konte-size-guide-tabs">
			<div class="konte-size-guide-tabs--tabs">
				<div class="konte-size-guide-table-tabs--tab add-new-tab" data-title="<?php esc_attr_e( 'Table', 'konte-addons' ) ?>"><span class="dashicons dashicons-plus"></span></div>
			</div>
		</div>

		<?php
	}

	/**
	 * Save meta box content.
	 *
	 * @param int $post_id
	 * @param object $post
	 */
	public function save_post( $post_id, $post ) {
		// Early return if this is not a size guide.
		if ( self::POST_TYPE != $post->post_type ) {
			return;
		}

		// Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
		}

		// Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
		}

		if ( ! empty( $_POST['_size_guide_category'] ) ) {
			update_post_meta( $post_id, 'size_guide_category', sanitize_text_field( $_POST['_size_guide_category'] ) );

			if ( 'custom' == $_POST['_size_guide_category'] && ! empty( $_POST['tax_input'] ) && ! empty( $_POST['tax_input']['product_cat'] ) ) {
				$cat_ids = array_map( 'intval', $_POST['tax_input']['product_cat'] );

				wp_set_post_terms( $post_id, $cat_ids, 'product_cat' );
			}
		}

		if ( ! empty( $_POST['_size_guides'] ) ) {
			$data = map_deep( $_POST['_size_guides'], [ $this, 'sanitize_guide_meta' ] );
			update_post_meta( $post_id, 'size_guides', $data );
		}
	}

	/**
	 * Sanitize the guide meta content.
	 * Support rich-text content.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function sanitize_guide_meta( $content ) {
		return format_to_edit( $content, true );
	}

	/**
	 * Load scripts and style in admin area
	 */
	public function admin_scripts( $hook ) {
		$screen = get_current_screen();

		if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) && self::POST_TYPE == $screen->post_type ) {
			wp_enqueue_style( 'konte-addons-size-guide', KONTE_ADDONS_URL . 'assets/css/size-guide-admin.css' );

			wp_enqueue_script( 'konte-addons-size-guide', KONTE_ADDONS_URL . 'assets/js/admin/size-guide.js', array( 'jquery', 'wp-util' ), KONTE_ADDONS_VER, true );
		}

		if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) && 'product' == $screen->post_type ) {
			wp_enqueue_style( 'konte-addons-product-size-guide', KONTE_ADDONS_URL . 'assets/css/product-size-guide-admin.css' );

			wp_enqueue_script( 'konte-addons-product-size-guide', KONTE_ADDONS_URL . 'assets/js/admin/product-size-guide.js', array( 'jquery' ), KONTE_ADDONS_VER, true );
		}

		if ( 'woocommerce_page_wc-settings' == $screen->base && ! empty( $_GET['section'] ) && 'konte_addons_size_guide' == $_GET['section'] ) {
			wp_enqueue_script( 'konte-addons-size-guide', KONTE_ADDONS_URL . 'assets/js/admin/size-guide-settings.js', array( 'jquery' ), KONTE_ADDONS_VER, true );
		}
	}

	/**
	 * Tab templates
	 */
	public function templates() {
		?>

		<script type="text/html" id="tmpl-konte-size-guide-tab">
			<div class="konte-size-guide-table-tabs--tab" data-tab="{{data.index}}">
				<span class="konte-size-guide-table-tabs--tab-text">{{data.tab}}</span>
				<input type="text" name="_size_guides[tabs][]" value="{{data.tab}}" class="hidden">
				<span class="dashicons dashicons-edit edit-button"></span>
				<span class="dashicons dashicons-yes confirm-button"></span>
			</div>
		</script>

		<script type="text/html" id="tmpl-konte-size-guide-panel">
			<div class="konte-size-guide-table-editor" data-tab="{{data.index}}">
				<p>
					<label>
						<?php esc_html_e( 'Table Name', 'konte-addons' ); ?><br/>
						<input type="text" name="_size_guides[names][]" class="widefat" value="{{data.name}}">
					</label>
				</p>

				<p>
					<label>
						<?php esc_html_e( 'Description', 'konte-addons' ) ?>
						<textarea name="_size_guides[descriptions][]" class="widefat" rows="6">{{data.description}}</textarea>
					</label>
				</p>

				<p><label><?php esc_html_e( 'Table', 'konte-addons' ) ?></label></p>

				<textarea name="_size_guides[tables][]" class="widefat konte-size-guide-table hidden">{{{data.table}}}</textarea>

				<p>
					<label>
						<?php esc_html_e( 'Additional Information', 'konte-addons' ) ?>
						<textarea name="_size_guides[information][]" class="widefat" rows="6">{{{data.information}}}</textarea>
					</label>
				</p>

				<p class="delete-table-p">
					<a href="#" class="delete-table"><?php esc_html_e( 'Delete Table', 'konte' ) ?></a>
				</p>
			</div>
		</script>

		<?php
	}

	/**
	 * Add new product data tab for size guide
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_data_tab( $tabs ) {
		$tabs['konte_size_guide'] = array(
			'label'    => esc_html__( 'Size Guide', 'konte-addons' ),
			'target'   => 'konte-size-guide',
			'class'    => array( 'konte-size-guide', ),
			'priority' => 62,
		);

		return $tabs;
	}

	/**
	 * Outputs the size guide panel
	 */
	public function product_data_panel() {
		global $post, $thepostid, $product_object;

		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		$default_display = get_option( self::OPTION_NAME . '_display', 'tab' );
		$default_positon = get_option( self::OPTION_NAME . '_button_position', 'bellow_summary' );

		$display_options = array(
			'tab'   => esc_html__( 'In product tabs', 'konte-addons' ),
			'panel' => esc_html__( 'By clicking on a button', 'konte-addons' ),
		);

		$button_options = array(
			'bellow_summary'   => esc_html__( 'Bellow short description', 'konte-addons' ),
			'bellow_price'     => esc_html__( 'Bellow price', 'konte-addons' ),
			'bellow_button'    => esc_html__( 'Bellow Add To Cart button', 'konte-addons' ),
			'beside_attribute' => esc_html__( 'Beside the Size attribute', 'konte-addons' ),
		);

		$product_size_guide = get_post_meta( $thepostid, 'konte_size_guide', true );
		$product_size_guide = wp_parse_args( $product_size_guide, array(
			'guide'           => '',
			'display'         => '',
			'button_position' => '',
			'attribute'       => '',
		) );

		$guides = get_posts( array(
			'post_type'        => self::POST_TYPE,
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'fields'           => 'ids',
			'suppress_filters' => false,
		) );

		$guide_options = array(
			'' => esc_html__( '--Default--', 'konte-addons' ),
			'none' => esc_html__( '--No Size Guide--', 'konte-addons' ),
		);
		foreach ( $guides as $guide ) {
			$guide_options[ $guide ] = get_post_field( 'post_title', $guide );
		}

		$attributes   = $product_object->get_attributes( 'edit' );
		$attribute_options = array();
		foreach ( $attributes as $attribute ) {
			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$option_value = $attribute->get_name();
			$option_name =  $option_value;

			if ( $attribute->get_id() ) {
				$taxonomy = wc_get_attribute( $attribute->get_id() );
				$option_name = $taxonomy ? $taxonomy->name : $option_name;
			}

			$attribute_options[ $option_value ] = $option_name;
		}
		?>

		<div id="konte-size-guide" class="panel woocommerce_options_panel hidden" data-nonce="<?php echo esc_attr( wp_create_nonce( 'konte_size_guide' ) ) ?>">
			<div class="options_group">
				<?php
				woocommerce_wp_select( array(
					'id'      => 'konte_size_guide-guide',
					'name'    => 'konte_size_guide[guide]',
					'value'   => $product_size_guide['guide'],
					'label'   => esc_html__( 'Size Guide', 'konte-addons' ),
					'options' => $guide_options,
				) );
				?>
			</div>

			<div class="options_group">
				<?php
				woocommerce_wp_select( array(
					'id'      => 'konte_size_guide-display',
					'name'    => 'konte_size_guide[display]',
					'value'   => $product_size_guide['display'],
					'label'   => esc_html__( 'Size Guide Display', 'konte-addons' ),
					'options' => array_merge( array( '' => esc_html__( 'Default', 'konte-addons' ) . ' (' . $display_options[ $default_display ] . ')' ), $display_options ),
				) );

				woocommerce_wp_select( array(
					'id'      => 'konte_size_guide-button_position',
					'name'    => 'konte_size_guide[button_position]',
					'value'   => $product_size_guide['button_position'],
					'label'   => esc_html__( 'Button Position', 'konte-addons' ),
					'options' => array_merge( array( '' => esc_html__( 'Default', 'konte-addons' ) . ' (' . $button_options[ $default_positon ] . ')' ), $button_options ),
				) );

				if ( ! empty( $attribute_options ) ) {
					woocommerce_wp_select( array(
						'id'      => 'konte_size_guide-attribute',
						'name'    => 'konte_size_guide[attribute]',
						'value'   => $product_size_guide['attribute'],
						'label'   => esc_html__( 'Attribute', 'konte-addons' ),
						'options' => $attribute_options,
					) );
				}
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Ajax load product variation attributes.
	 */
	public function ajax_load_product_size_guide_attributes() {
		check_ajax_referer( 'konte_size_guide', 'security' );

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		// Set $post global so its available, like within the admin screens.
		global $post;

		$product_id     = absint( $_POST['product_id'] );
		$post           = get_post( $product_id ); // phpcs:ignore
		$product_object = wc_get_product( $product_id );

		$product_size_guide = get_post_meta( $product_id, 'konte_size_guide', true );
		$product_size_guide = wp_parse_args( $product_size_guide, array(
			'guide'           => '',
			'display'         => '',
			'button_position' => '',
			'attribute'       => '',
		) );

		$attributes   = $product_object->get_attributes( 'edit' );
		$attribute_options = array();
		foreach ( $attributes as $attribute ) {
			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$option_value = $attribute->get_name();
			$option_name  = $option_value;

			if ( $attribute->get_id() ) {
				$taxonomy = wc_get_attribute( $attribute->get_id() );
				$option_name = $taxonomy ? $taxonomy->name : $option_name;
			}

			$attribute_options[ $option_value ] = $option_name;
		}

		woocommerce_wp_select( array(
			'id'      => 'konte_size_guide-attribute',
			'name'    => 'konte_size_guide[attribute]',
			'value'   => $product_size_guide['attribute'],
			'label'   => esc_html__( 'Attribute', 'konte-addons' ),
			'options' => $attribute_options,
		) );

		wp_die();
	}

	/**
	 * Save product data of selected size guide
	 *
	 * @param int $post_id
	 */
	public function process_product_meta( $post_id ) {
		if ( isset( $_POST['konte_size_guide'] ) ) {
			update_post_meta( $post_id, 'konte_size_guide', $_POST['konte_size_guide'] );
		}
	}

	/**
	 * Get option of size guide.
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_option( $option = '', $default = false ) {
		if ( ! is_string( $option ) ) {
			return $default;
		}

		if ( empty( $option ) ) {
			return get_option( self::OPTION_NAME, $default );
		}

		return get_option( sprintf( '%s_%s', self::OPTION_NAME, $option ), $default );
	}

	/**
	 * Hooks to display size guide.
	 */
	public function display_size_guide() {
		global $product;

		if ( 'yes' == $this->get_option( 'variable_only' ) && ! $product->is_type( 'variable' ) ) {
			return false;
		}

		$guide_id = $this->get_product_size_guide_id();

		if ( ! $guide_id ) {
			return;
		}

		$guide_settings = get_post_meta( $product->get_id(), 'konte_size_guide', true );
		$display = ( is_array( $guide_settings ) && ! empty( $guide_settings['display'] ) ) ? $guide_settings['display'] : $this->get_option( 'display' );

		if ( 'tab' == $display ) {
			add_filter( 'woocommerce_product_tabs', array( $this, 'size_guide_tab' ) );
		} else {
			$button_position = ( is_array( $guide_settings ) && ! empty( $guide_settings['button_position'] ) ) ? $guide_settings['button_position'] : $this->get_option( 'button_position' );

			switch ( $button_position ) {
				case 'bellow_summary':
					add_action( 'woocommerce_single_product_summary', array( $this, 'size_guide_button' ), 8 );
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'size_guide_panel' ), 12 );
					break;

				case 'bellow_price':
					add_action( 'woocommerce_single_product_summary', array( $this, 'size_guide_button' ), 15 );
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'size_guide_panel' ), 12 );
					break;

				case 'bellow_button':
					add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'size_guide_button' ), 15 );
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'size_guide_panel' ), 12 );
					break;

				case 'beside_attribute':
					if ( ! $product->is_type( 'variable' ) ) {
						break;
					}

					$attribute = $this->get_option( 'attribute' );

					if ( is_array( $guide_settings ) && 'panel' == $guide_settings['display'] && 'beside_attribute' == $guide_settings['button_position'] ) {
						$attribute = $guide_settings['attribute'];
					}

					if ( empty( $attribute ) ) {
						break;
					}

					$variations = $product->get_variation_attributes();
					$attributes = array_keys( $variations );

					if ( empty( $variations ) ) {
						break;
					}

					if ( in_array( $attribute, $attributes ) || in_array( 'pa_' . $attribute, $attributes ) ) {
						add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'size_guide_attribute_button' ), 999, 2 );
						add_action( 'woocommerce_after_single_product_summary', array( $this, 'size_guide_panel' ), 12 );
					}

					break;
			}
		}
	}

	/**
	 * Add size guide tab to product tabs.
	 *
	 * @param array $tabs
	 * @return array
	 */
	public function size_guide_tab( $tabs ) {
		$guide = $this->get_product_size_guide_id();

		if ( $guide ) {
			$tabs['konte_size_guide'] = array(
				'title' 	=> __( 'Size Guide', 'konte-addons' ),
				'priority' 	=> 50,
				'callback' 	=> array( $this, 'size_guide_content' ),
			);
		}

		return $tabs;
	}

	/**
	 * Get HTML of size guide button
	 *
	 * @return string
	 */
	protected function get_size_guide_button() {
		return apply_filters(
			'konte_size_guide_button',
			sprintf(
				'<p class="product-size-guide"><a href="#" data-toggle="off-canvas" data-target="size-guide-panel" class="size-guide-button">%s</a></p>',
				esc_html__( 'Size Guide', 'konte-addons' )
			)
		);
	}

	/**
	 * Display the button to open size guide.
	 */
	public function size_guide_button() {
		echo $this->get_size_guide_button();
	}

	/**
	 * Filter function to add size guide button beside selected attribute.
	 *
	 * @param string $html
	 * @param array $args
	 * @return string
	 */
	public function size_guide_attribute_button( $html, $args ) {
		global $product;

		$attribute = $this->get_option( 'attribute' );
		$guide_settings = get_post_meta( $product->get_id(), 'konte_size_guide', true );

		if ( is_array( $guide_settings ) && 'panel' == $guide_settings['display'] && 'beside_attribute' == $guide_settings['button_position'] ) {
			$attribute = $guide_settings['attribute'];
		}

		if ( $attribute == $args['attribute'] || ( 'pa_' . $attribute ) == $args['attribute'] ) {
			$html .= $this->get_size_guide_button();
		}

		return $html;
	}

	/**
	 * Size guide panel.
	 */
	public function size_guide_panel() {
		?>
		<div id="size-guide-panel" class="size-guide-panel offscreen-panel">
			<div class="backdrop"></div>
			<div class="panel">
				<div class="hamburger-menu button-close active">
					<span class="menu-text"><?php esc_html_e( 'Close', 'konte-addons' ) ?></span>

					<div class="hamburger-box">
						<div class="hamburger-inner"></div>
					</div>
				</div>

				<div class="panel-header">
					<div class="panel__title"><?php esc_html_e( 'Size Guide', 'konte-addons' ) ?></div>
				</div>


				<div class="panel-content">
					<?php $this->size_guide_content(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display product size guide as a tab.
	 */
	public function size_guide_content() {
		$guide_id = $this->get_product_size_guide_id();

		if ( ! $guide_id ) {
			return;
		}

		$guide = get_post( $guide_id );

		echo '<div class="konte-size-guide">';

		if ( ! empty( $guide->post_content ) ) {
			echo '<div class="konte-size-guide--global-content">' . $this->kses_content( $guide->post_content, $guide ) . '</div>';
		}

		$size_guides = get_post_meta( $guide_id, 'size_guides', true );

		if ( ! $size_guides || ! is_array( $size_guides ) || empty( $size_guides['tables'] ) ) {
			echo '</div>';
			return;
		}

		// Display tabs.
		if ( 1 < count( $size_guides['tables'] ) ) {
			$tabs = array();

			foreach ( $size_guides['tabs'] as $index => $tab ) {
				$tabs[] = sprintf( '<li data-target="%s" class="%s">%s</li>', esc_attr( $index + 1 ), ( $index ? '' : 'active' ), esc_html( $tab ) );
			}

			echo '<div class="konte-size-guide-tabs konte-tabs">';
			echo '<ul class="konte-tabs__nav">' . implode( '', $tabs ) . '</ul>';
			echo '<div class="konte-tabs__panels">';
		}

		foreach ( $size_guides['tables'] as $index => $table ) {
			echo '<div class="konte-size-guide__panel konte-tabs__panel ' . ( $index ? '' : 'active' ) . '" data-panel="' . esc_attr( $index + 1 ) . '">';

			if ( ! empty( $size_guides['names'][ $index ] ) ) {
				echo '<h3 class="konte-size-guide__name">' . wp_kses_post( $size_guides['names'][ $index ] ) . '</h3>';
			}

			if ( ! empty( $size_guides['descriptions'][ $index ] ) ) {
				echo '<div class="konte-size-guide__description">' . $this->kses_content( $size_guides['descriptions'][ $index ], $guide ) . '</div>';
			}

			if ( ! empty( $table ) ) {
				$table = json_decode( $table, true );

				echo '<table class="konte-size-guide__table">';

				foreach ( $table as $row => $columns ) {
					$columns = array_map( 'wp_kses_post', $columns );

					if ( 0 === $row ) {
						echo '<thead>';
					} elseif ( 1 === $row ) {
						echo '</thead><tbody>';
					}

					echo '<tr>';

					if ( 0 === $row ) {
						echo '<th>' . implode( '</th><th>', $columns ) . '</th>';
					} else {
						echo '<td>' . implode( '</td><td>', $columns ) . '</td>';
					}

					echo '</tr>';
				}

				echo '</tbody>';
				echo '</table>';
			}

			if ( ! empty( $size_guides['information'][ $index ] ) ) {
				echo '<div class="konte-size-guide__info">' . $this->kses_content( $size_guides['information'][ $index ], $guide ) . '</div>';
			}

			echo '</div>';
		}

		if ( 1 < count( $size_guides['tables'] ) ) {
			echo '</div></div>';
		}

		echo '</div>';
	}

	/**
	 * Add the_content filter to the guide content
	 *
	 * @param  string $content
	 * @param  WP_Post $guide
	 * @return string
	 */
	protected function kses_content( $content, $guide ) {
		$_post_object = $GLOBALS['post'];
		$GLOBALS['post'] = $guide;
		$content = apply_filters( 'the_content', $content );
		$GLOBALS['post'] = $_post_object;

		return $content;
	}

	/**
	 * Get assigned size guide of the product.
	 *
	 * @param int|object $object Product object
	 * @return int
	 */
	public function get_product_size_guide_id( $object = false ) {
		global $product;

		$_product = $object ? wc_get_product( $object ) : $product;

		if ( ! $_product ) {
			return false;
		}

		$size_guide = get_post_meta( $_product->get_id(), 'konte_size_guide', true );

		// Return selected guide.
		if ( is_array( $size_guide ) ) {
			if ( 'none' == $size_guide['guide'] ) {
				return false;
			}

			if ( ! empty( $size_guide['guide'] ) ) {
				return $size_guide['guide'];
			}
		}

		// Get default size guide.
		$categories  = $_product->get_category_ids();
		$accept_cats = $categories;

		foreach ( $categories as $cat ) {
			$ancestors   = get_ancestors( $cat, 'product_cat', 'taxonomy' );
			$accept_cats = array_merge( $ancestors, $accept_cats );
		}

		// Firstly, get size guide that assign for these categories directly.
		$guides = new WP_Query( array(
			'post_type'        => self::POST_TYPE,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => false,
			'meta_query '      => array(
				array(
					'key'     => 'size_guide_category',
					'value'   => array( 'none', 'all' ),
					'compare' => 'NOT IN',
				),
			),
			'tax_query'        => array(
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $accept_cats,
					'operator'         => 'IN',
					'include_children' => false,
				),
			),
		) );

		// Get the global size guide if no direct one found.
		if ( ! $guides->have_posts() ) {
			$guides = new WP_Query( array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => 'publish',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_key'               => 'size_guide_category',
				'meta_value'             => 'all',
				'suppress_filters'       => false,
			) );
		}

		if ( $guides->have_posts() ) {
			$id = current( $guides->posts );

			return konte_addons_get_translated_object_id( $id, self::POST_TYPE );
		}

		return false;
	}
}

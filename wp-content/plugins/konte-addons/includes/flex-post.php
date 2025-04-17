<?php
/**
 * Flex post custom post type.
 */

class Konte_Addons_Flex_Post {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	private $post_type = 'flex_post';

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	private $taxonomy = 'flex_post_tag';

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option = 'flex_posts';

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function init() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Add an option to enable the CPT
		add_action( 'admin_init', array( $this, 'settings_api_init' ) );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		if ( ! get_option( $this->option, true ) ) {
			return;
		}

		$this->register_post_type();

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', $this->post_type ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->post_type ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Ajax search posts.
		add_action( 'wp_ajax_flex_post_search_posts', array( $this, 'ajax_search_posts' ) );
	}

	/**
	 * Add a checkbox field in 'Settings' > 'Writing'
	 * for enabling CPT functionality.
	 */
	public function settings_api_init() {
		add_settings_section(
			'konte_custom_content_section',
			esc_html__( 'Konte Custom Content Types', 'konte-addons' ),
			array( $this, 'writing_section_html' ),
			'writing'
		);

		add_settings_field(
			$this->option,
			'<span class="flex-post-options">' . esc_html__( 'Flex Posts', 'konte-addons' ) . '</span>',
			array( $this, 'enable_field_html' ),
			'writing',
			'konte_custom_content_section'
		);
		register_setting(
			'writing',
			$this->option,
			array(
				'sanitize_callback' => 'intval',
				'default' => 1
			)
		);
	}

	/**
	 * Add writing setting section
	 */
	public function writing_section_html() {
		?>
		<p>
			<?php esc_html_e( 'Use these settings to display different types of content on your website', 'konte-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * HTML code to display a checkbox true/false option
	 * for the Flex Post CPT setting.
	 */
	public function enable_field_html() {
		?>

		<label for="<?php echo esc_attr( $this->option ); ?>">
			<input name="<?php echo esc_attr( $this->option ); ?>"
				   id="<?php echo esc_attr( $this->option ); ?>" <?php checked( get_option( $this->option ), true ); ?>
				   type="checkbox" value="1" />
			<?php esc_html_e( 'Enable Flex Posts for this site.', 'konte-addons' ); ?>
		</label>

		<?php
	}

	/**
	 * Register flex_post post type
	 */
	public function register_post_type() {
		if ( post_type_exists( $this->post_type ) ) {
			return;
		}

		register_post_type( $this->post_type, array(
			'description'         => esc_html__( 'Flexible post content', 'konte-addons' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Flex Post', 'konte-addons' ),
				'singular_name'         => esc_html__( 'Post', 'konte-addons' ),
				'menu_name'             => esc_html__( 'Flex Posts', 'konte-addons' ),
				'all_items'             => esc_html__( 'All Posts', 'konte-addons' ),
				'add_new'               => esc_html__( 'Add New', 'konte-addons' ),
				'add_new_item'          => esc_html__( 'Add New Post', 'konte-addons' ),
				'edit_item'             => esc_html__( 'Edit Post', 'konte-addons' ),
				'new_item'              => esc_html__( 'New Post', 'konte-addons' ),
				'view_item'             => esc_html__( 'View Post', 'konte-addons' ),
				'search_items'          => esc_html__( 'Search Posts', 'konte-addons' ),
				'not_found'             => esc_html__( 'No posts found', 'konte-addons' ),
				'not_found_in_trash'    => esc_html__( 'No posts found in Trash', 'konte-addons' ),
				'filter_items_list'     => esc_html__( 'Filter posts list', 'konte-addons' ),
				'items_list_navigation' => esc_html__( 'Project list navigation', 'konte-addons' ),
				'items_list'            => esc_html__( 'Posts list', 'konte-addons' ),
			),
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
			),
			'public'              => false,
			'show_ui'             => true,
			'menu_position'       => 20, // below Pages
			'menu_icon'           => 'dashicons-pressthis',
			'capability_type'     => 'post',
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'query_var'           => is_admin(),
		) );

		register_taxonomy( $this->taxonomy, $this->post_type, array(
			'hierarchical'      => false,
			'labels'            => array(
				'name'                  => esc_html__( 'Post Tags', 'konte-addons' ),
				'singular_name'         => esc_html__( 'Post Tag', 'konte-addons' ),
				'menu_name'             => esc_html__( 'Tags', 'konte-addons' ),
				'all_items'             => esc_html__( 'All Post Tags', 'konte-addons' ),
				'edit_item'             => esc_html__( 'Edit Post Tag', 'konte-addons' ),
				'view_item'             => esc_html__( 'View Post Tag', 'konte-addons' ),
				'update_item'           => esc_html__( 'Update Post Tag', 'konte-addons' ),
				'add_new_item'          => esc_html__( 'Add New Post Tag', 'konte-addons' ),
				'new_item_name'         => esc_html__( 'New Post Tag Name', 'konte-addons' ),
				'parent_item'           => esc_html__( 'Parent Post Tag', 'konte-addons' ),
				'parent_item_colon'     => esc_html__( 'Parent Post Tag:', 'konte-addons' ),
				'search_items'          => esc_html__( 'Search Post Tags', 'konte-addons' ),
				'items_list_navigation' => esc_html__( 'Post tag list navigation', 'konte-addons' ),
				'items_list'            => esc_html__( 'Post tag list', 'konte-addons' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'query_var'         => is_admin(),
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
		) );
	}

	/**
	 * Add custom column to manage portfolio screen
	 * Add Thumbnail column
	 *
	 * @since  1.0.0
	 *
	 * @param  array $columns Default columns
	 *
	 * @return array
	 */
	public function edit_admin_columns( $columns ) {
		$columns = array_slice( $columns, 0, 2, true ) + array( 'content_type' => esc_html__( 'Content Type', 'konte-addons' ) ) + array_slice( $columns, 2, null, true );

		return $columns;
	}

	/**
	 * Handle custom column display
	 *
	 * @since  1.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_custom_columns( $column, $post_id ) {
		if ( 'content_type' == $column ) {
			$types = $this->content_type_options();
			$type = get_post_meta( $post_id, 'flex_content_type', true );

			echo isset( $types[ $type ] ) ? $types[ $type ] : esc_html__( 'Standard', 'konte-addons' );
		}
	}

	/**
	 * Add meta boxes
	 *
	 * @param object $post
	 */
	public function meta_boxes( $post ) {
		add_meta_box( 'flex-post-type', esc_html__( 'Content Type', 'konte-addons' ), array( $this, 'content_type_meta_box' ), $this->post_type, 'side', 'high' );
		add_meta_box( 'flex-post-data', esc_html__( 'Post Data', 'konte-addons' ), array( $this, 'content_data_meta_box' ), $this->post_type, 'advanced', 'high' );
	}

	/**
	 * Post type meta box.
	 *
	 * @param object $post
	 */
	public function content_type_meta_box( $post ) {
		$types = $this->content_type_options();
		$selected = get_post_meta( $post->ID, 'flex_content_type', true );

		if ( ! $selected ) {
			$selected = '';
		}

		echo '<div id="post-formats-select">';
		echo '<fieldset"><legend class="screen-reader-text">' . esc_html__( 'Flex content type', 'konte-addons' ) . '</legend>';

		foreach ( $types as $type => $title ) {
			printf(
				'<input type="radio" name="flex_content_type" id="flex-content-type-%s" class="post-format flex-content-type" value="%s" %s>
				<label for="flex-content-type-%s">%s</label><br>',
				esc_attr( $type ),
				esc_attr( $type ),
				checked( $selected, $type, false ),
				esc_attr( $type ),
				$title
			);
		}

		echo '</fieldset>';
		echo '</div>';
	}

	/**
	 * Post data meta box.
	 *
	 * @param object $post
	 */
	public function content_data_meta_box( $post ) {
		$type      = get_post_meta( $post->ID, 'flex_content_type', true );
		$custom    = get_post_meta( $post->ID, 'flex_post_custom', true );
		$custom    = wp_parse_args( $custom, array( 'link' => '', 'text' => '' ) );
		$post_id   = get_post_meta( $post->ID, 'flex_post_post', true );
		$instagram = get_post_meta( $post->ID, 'flex_post_instagram', true );
		$instagram = wp_parse_args( $instagram, array( 'link' => '', 'caption' => '', 'user' => '' ) );
		$design    = get_post_meta( $post->ID, 'flex_post_design', true );
		$design    = wp_parse_args( $design, array( 'tag_color' => '', 'background_image' => '', 'css' => '' ) );
		?>

		<div id="flex-post__post-data" class="flex-post-data-group <?php echo 'post' == $type ? '' : 'hidden'; ?>">
			<div class="flex-post-setting-field flex-post-setting__post">
				<div class="flex-post-setting__label"><label for="flex-post-post"><?php esc_html_e( 'Select Post', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<select id="flex-post-post" name="flex_post_post">
						<?php if ( $post_id ) : ?>
							<option value="<?php echo esc_attr( $post_id ); ?>" selected="selected"><?php echo get_post_field( 'post_title', $post_id ) ?></option>
						<?php endif; ?>
					</select>
				</div>
			</div>
		</div>

		<div id="flex-post__instagram-data" class="flex-post-data-group <?php echo 'instagram' == $type ? '' : 'hidden'; ?>">
			<div class="flex-post-setting-field flex-post-setting__instagram-link">
				<div class="flex-post-setting__label"><label for="flex-post-instagram-link"><?php esc_html_e( 'Instagram Post URL', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-instagram-link" class="widefat" name="flex_post_instagram[link]" value="<?php echo esc_attr( $instagram['link'] ); ?>">
					<p class="description"><?php esc_html_e( 'Enter the full URL of the Instagram post', 'konte-addons' ); ?></p>
				</div>
			</div>

			<div class="flex-post-setting-field flex-post-setting__instagram-caption">
				<div class="flex-post-setting__label"><label for="flex-post-instagram-caption"><?php esc_html_e( 'Instagram Post Caption', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<textarea id="flex-post-instagram-caption" class="widefat" name="flex_post_instagram[caption]"><?php echo esc_textarea( $instagram['caption'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Enter the short caption for the Instagram image (featured image) to display on the frontend', 'konte-addons' ); ?></p>
				</div>
			</div>

			<div class="flex-post-setting-field flex-post-setting__instagram-user">
				<div class="flex-post-setting__label"><label for="flex-post-instagram-user"><?php esc_html_e( 'Instagram Username', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-instagram-user" name="flex_post_instagram[user]" value="<?php echo esc_attr( $instagram['user'] ); ?>">
					<p class="description"><?php esc_html_e( 'The Instagram username', 'konte-addons' ); ?></p>
				</div>
			</div>
		</div>

		<div id="flex-post__custom-data" class="flex-post-data-group <?php echo 'custom' == $type ? '' : 'hidden'; ?>">
			<div class="flex-post-setting-field flex-post-setting__custom-link">
				<div class="flex-post-setting__label"><label for="flex-post-custom-link"><?php esc_html_e( 'Custom URL', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-custom-link" class="widefat" name="flex_post_custom[link]" value="<?php echo esc_attr( $custom['link'] ); ?>">
				</div>
			</div>

			<div class="flex-post-setting-field flex-post-setting__custom-text">
				<div class="flex-post-setting__label"><label for="flex-post-custom-text"><?php esc_html_e( 'Read More Text', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-custom-text" name="flex_post_custom[text]" value="<?php echo esc_attr( $custom['text'] ); ?>">
				</div>
			</div>
		</div>

		<div id="flex-post__design-data" class="flex-post-data-group">
			<div class="flex-post-setting-field flex-post-setting__design-tag-color">
				<div class="flex-post-setting__label"><label for="flex-post-tag-color"><?php esc_html_e( 'Tag Color', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-tag-color" name="flex_post_design[tag_color]" value="<?php echo esc_attr( $design['tag_color'] ); ?>">
					<p class="description"><?php esc_html_e( 'The color of the post tag', 'konte-addons' ); ?></p>
				</div>
			</div>

			<div class="flex-post-setting-field flex-post-setting__design-background">
				<div class="flex-post-setting__label"><label for="flex-post-background-image"><?php esc_html_e( 'Background Image', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__preview">
					<img src="<?php echo esc_url( $design['background_image'] ); ?>" class="<?php $design['background_image'] ? '' : 'hidden' ?>">
				</div>
				<div class="flex-post-setting__input">
					<input type="text" id="flex-post-background-image" class="widefat" name="flex_post_design[background_image]" value="<?php echo esc_attr( $design['background_image'] ); ?>">
					<br><br>
					<button type="button" class="upload-background-button button"><?php esc_html_e( 'Upload Image', 'konte-addons' ) ?></button>
				</div>
			</div>

			<div class="flex-post-setting-field flex-post-setting__design-css">
				<div class="flex-post-setting__label"><label for="flex-post-custom-css"><?php esc_html_e( 'Custom CSS', 'konte-addons' ) ?></label></div>
				<div class="flex-post-setting__input">
					<textarea type="text" id="flex-post-custom-css" class="widefat textarea" name="flex_post_design[css]" rows="5"><?php echo wp_unslash( $design['css'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Custom CSS for this post. This CSS will be added directly to this post. Please DO NOT enter CSS selector, just CSS rules only.', 'konte-addons' ); ?></p>
				</div>
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
		// If not the flex post.
		if ( $this->post_type != $post->post_type ) {
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

		if ( ! empty( $_POST['flex_content_type'] ) ) {
			$type = sanitize_text_field( $_POST['flex_content_type'] );
			update_post_meta( $post_id, 'flex_content_type', $type );
		}

		if ( ! empty( $_POST['flex_post_custom'] ) ) {
			$custom = wp_parse_args( $_POST['flex_post_custom'], array( 'link' => '', 'text' => '' ) );
			update_post_meta( $post_id, 'flex_post_custom', $custom );
		}

		if ( ! empty( $_POST['flex_post_post'] ) ) {
			update_post_meta( $post_id, 'flex_post_post', intval( $_POST['flex_post_post'] ) );
		}

		if ( ! empty( $_POST['flex_post_instagram'] ) ) {
			$instagram = wp_parse_args( $_POST['flex_post_instagram'], array( 'link' => '', 'caption' => '', 'user' => '' ) );
			update_post_meta( $post_id, 'flex_post_instagram', $instagram );
		}

		if ( ! empty( $_POST['flex_post_design'] ) ) {
			$design = wp_parse_args( $_POST['flex_post_design'], array( 'tag_color' => '', 'background_image' => '', 'css' => '' ) );
			update_post_meta( $post_id, 'flex_post_design', $design );
		}
	}

	/**
	 * Load scripts and style.
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

		if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) && $this->post_type == $screen->post_type ) {
			wp_enqueue_media();

			if ( function_exists( 'wp_enqueue_code_editor' ) ) {
				wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'select2', KONTE_ADDONS_URL . 'assets/css/select2.css' );
			wp_enqueue_style( 'konte-addons-flex-post', KONTE_ADDONS_URL . 'assets/css/flex-post-admin.css' );

			wp_register_script( 'select2', KONTE_ADDONS_URL . 'assets/js/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3', true );
			wp_enqueue_script( 'konte-addons-flex-post', KONTE_ADDONS_URL . 'assets/js/flex-post-admin.js', array( 'jquery', 'select2', 'wp-color-picker' ), KONTE_ADDONS_VER, true );
		}
	}

	/**
	 * Content type options.
	 *
	 * @return array
	 */
	public function content_type_options() {
		return array(
			''          => esc_attr__( 'Standard', 'konte-addons' ),
			'post'      => esc_attr__( 'Post', 'konte-addons' ),
			'instagram' => esc_attr__( 'Instagram Post', 'konte-addons' ),
			'custom'    => esc_attr__( 'Custom', 'konte-addons' ),
		);
	}

	/**
	 * Ajax search for posts.
	 */
	public function ajax_search_posts() {
		add_filter( 'posts_search', array( $this, 'search_by_title' ), 100, 2 );
		$term = $_GET['term'];

		$query = new WP_Query(
			array(
				's'              => $term,
				'orders'         => 'DESC',
				'posts_per_page' => $term ? -1 : 10,
			)
		);

		remove_filter( 'posts_search', array( $this, 'search_by_title' ), 100, 2 );

		$results = array();

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				$results[] = array( 'id' => get_the_ID(), 'text' => get_the_title() );
			endwhile;

			wp_reset_postdata();
		endif;

		wp_send_json( $results );
		exit;
	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * @param   string      $search
	 * @param   WP_Query    $wp_query
	 */
	public function search_by_title( $search, $wp_query ) {
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';

			$search = array();

			foreach ( ( array ) $q['search_terms'] as $term ) {
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
			}

			if ( ! is_user_logged_in() ) {
				$search[] = "$wpdb->posts.post_password = ''";
			}

			$search = ' AND ' . implode( ' AND ', $search );
		}

		return $search;
	}
}

<?php

/**
 * Register portfolio support
 */
class Konte_Addons_Portfolio {
	const POST_TYPE     = 'portfolio';
	const TAXONOMY_TYPE = 'portfolio_type';
	const TAXONOMY_TAG  = 'portfolio_tag';
	const OPTION_NAME   = 'konte_portfolio';

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
		add_action( 'current_screen', array( $this, 'save_settings' ) );

		// Make sure the post types are loaded for imports
		add_action( 'import_start', array( $this, 'register_post_type' ) );

		if ( ! $this->get_option() ) {
			return;
		}

		$this->register_post_type();
		add_action( sprintf( 'add_option_%s', self::OPTION_NAME ), 'flush_rewrite_rules' );
		add_action( sprintf( 'update_option_%s', self::OPTION_NAME ), 'flush_rewrite_rules' );
		add_action( sprintf( 'publish_%s', self::POST_TYPE), 'flush_rewrite_rules' );

		// Handle post columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'edit_admin_columns' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'manage_custom_columns' ), 10, 2 );

		// Add image size
		add_image_size( 'konte-portfolio-thumbnail', 660, 660, true );
		add_image_size( 'konte-portfolio-masonry', 660, 9999, false );

		// Enqueue style and javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Adjust CPT archive and custom taxonomies to obey CPT reading setting
		add_filter( 'pre_get_posts', array( $this, 'query_reading_setting' ) );

		// Rewrite url
		add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules' ), 30 );
		add_filter( 'post_type_link', array( $this, 'portfolio_post_type_link' ), 10, 2 );
		add_filter( 'attachment_link', array( $this, 'portfolio_attachment_link' ), 10, 2 );

		// Template redirect
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		// Breadcrumb & archive title
		add_filter( 'konte_breadcrumbs_args', array( $this, 'breadcrumb' ) );
		add_filter( 'get_the_archive_title', array( $this, 'archive_title' ) );

		// Support WPML.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
			if ( ! is_admin() ) {
				add_filter( 'pre_get_posts', array( $this, 'translate_archive_query' ), 99 );
				add_filter( 'icl_ls_languages', array( $this, 'translate_archive_url' ) );
			}
		}
	}

	/**
	 * Register portfolio post type
	 */
	public function register_post_type() {
		if ( post_type_exists( self::POST_TYPE ) ) {
			return;
		}

		$permalinks          = $this->get_option( 'permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'konte-addons' ) : $permalinks['portfolio_base'];
		$portfolio_page_id   = $this->get_option( 'page_id' );
		$portfolio_type_base = empty( $permalinks['portfolio_type_base'] ) ? _x( 'portfolio-type', 'slug', 'konte-addons' ) : $permalinks['portfolio_type_base'];
		$portfolio_tag_base  = empty( $permalinks['portfolio_tag_base'] ) ? _x( 'portfolio-tag', 'slug', 'konte-addons' ) : $permalinks['portfolio_tag_base'];

		register_post_type( self::POST_TYPE, array(
			'description'         => esc_html__( 'Portfolio Items', 'konte-addons' ),
			'labels'              => array(
				'name'                  => esc_html__( 'Portfolio', 'konte-addons' ),
				'singular_name'         => esc_html__( 'Project', 'konte-addons' ),
				'menu_name'             => esc_html__( 'Portfolio', 'konte-addons' ),
				'all_items'             => esc_html__( 'All Projects', 'konte-addons' ),
				'add_new'               => esc_html__( 'Add New', 'konte-addons' ),
				'add_new_item'          => esc_html__( 'Add New Project', 'konte-addons' ),
				'edit_item'             => esc_html__( 'Edit Project', 'konte-addons' ),
				'new_item'              => esc_html__( 'New Project', 'konte-addons' ),
				'view_item'             => esc_html__( 'View Project', 'konte-addons' ),
				'search_items'          => esc_html__( 'Search Projects', 'konte-addons' ),
				'not_found'             => esc_html__( 'No Projects found', 'konte-addons' ),
				'not_found_in_trash'    => esc_html__( 'No Projects found in Trash', 'konte-addons' ),
				'filter_items_list'     => esc_html__( 'Filter projects list', 'konte-addons' ),
				'items_list_navigation' => esc_html__( 'Project list navigation', 'konte-addons' ),
				'items_list'            => esc_html__( 'Projects list', 'konte-addons' ),
			),
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'author',
			),
			'rewrite'             => $portfolio_permalink ? array(
				'slug'       => untrailingslashit( $portfolio_permalink ),
				'with_front' => false,
				'feeds'      => true,
				'pages'      => true,
			) : false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_position'       => 20,                    // below Pages
			'menu_icon'           => 'dashicons-portfolio', // 3.8+ dashicon option
			'capability_type'     => 'page',
			'query_var'           => true,
			'map_meta_cap'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => false,
			'has_archive'         => $portfolio_page_id && get_post( $portfolio_page_id ) ? urldecode( get_page_uri( $portfolio_page_id ) ) : self::POST_TYPE,
			'show_in_nav_menus'   => true,
		) );

		register_taxonomy( self::TAXONOMY_TYPE, self::POST_TYPE, array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'                  => _x( 'Portfolio Categories', 'Portfolio categories', 'konte-addons' ),
				'singular_name'         => _x( 'Category', 'Portfolio category', 'konte-addons' ),
				'menu_name'             => _x( 'Categories', 'Portfolio categories', 'konte-addons' ),
				'all_items'             => _x( 'All Categories', 'Portfolio categories', 'konte-addons' ),
				'edit_item'             => _x( 'Edit Category', 'Portfolio categories', 'konte-addons' ),
				'view_item'             => _x( 'View Category', 'Portfolio categories', 'konte-addons' ),
				'update_item'           => _x( 'Update Category', 'Portfolio categories', 'konte-addons' ),
				'add_new_item'          => _x( 'Add New Category', 'Portfolio categories', 'konte-addons' ),
				'new_item_name'         => _x( 'New Category Name', 'Portfolio categories', 'konte-addons' ),
				'parent_item'           => _x( 'Parent Category', 'Portfolio categories', 'konte-addons' ),
				'parent_item_colon'     => _x( 'Parent Category:', 'Portfolio categories', 'konte-addons' ),
				'search_items'          => _x( 'Search Categories', 'Portfolio categories', 'konte-addons' ),
				'items_list_navigation' => _x( 'Category list navigation', 'Portfolio categories', 'konte-addons' ),
				'items_list'            => _x( 'Category list', 'Portfolio categories', 'konte-addons' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => $portfolio_type_base,
				'with_front'   => false,
				'hierarchical' => true,
			),
		) );

		register_taxonomy( self::TAXONOMY_TAG, self::POST_TYPE, array(
			'hierarchical'      => false,
			'labels'            => array(
				'name'                  => _x( 'Portfolio Tags', 'Portfolio tag', 'konte-addons' ),
				'singular_name'         => _x( 'Tag', 'Portfolio tag', 'konte-addons' ),
				'menu_name'             => _x( 'Tags', 'Portfolio tag', 'konte-addons' ),
				'all_items'             => _x( 'All Tags', 'Portfolio tag', 'konte-addons' ),
				'edit_item'             => _x( 'Edit Tag', 'Portfolio tag', 'konte-addons' ),
				'view_item'             => _x( 'View Tag', 'Portfolio tag', 'konte-addons' ),
				'update_item'           => _x( 'Update Tag', 'Portfolio tag', 'konte-addons' ),
				'add_new_item'          => _x( 'Add New Tag', 'Portfolio tag', 'konte-addons' ),
				'new_item_name'         => _x( 'New Tag Name', 'Portfolio tag', 'konte-addons' ),
				'parent_item'           => _x( 'Parent Tag', 'Portfolio tag', 'konte-addons' ),
				'parent_item_colon'     => _x( 'Parent Tag:', 'Portfolio tag', 'konte-addons' ),
				'search_items'          => _x( 'Search Tags', 'Portfolio tag', 'konte-addons' ),
				'items_list_navigation' => _x( 'Tag list navigation', 'Portfolio tag', 'konte-addons' ),
				'items_list'            => _x( 'Tag list', 'Portfolio tag', 'konte-addons' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $portfolio_tag_base ),
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
		// change 'Title' to 'Project'
		$columns['title'] = esc_html__( 'Project', 'konte-addons' );

		if ( current_theme_supports( 'post-thumbnails' ) ) {
			// add featured image before 'Project'
			$columns = array_slice( $columns, 0, 1, true ) + array( 'thumbnail' => '' ) + array_slice( $columns, 1, null, true );
		}

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
		switch ( $column ) {
			case 'thumbnail':
				echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
				break;
		}
	}

	/**
	 * Load scripts and style for meta box
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'edit.php' == $hook && self::POST_TYPE == $screen->post_type && current_theme_supports( 'post-thumbnails' ) ) {
			wp_add_inline_style( 'wp-admin', '.manage-column.column-thumbnail { width: 50px; } @media screen and (max-width: 360px) { .column-thumbnail{ display:none; } }' );
		}
	}

	/**
	 * Add a checkbox field in 'Settings' > 'Writing' for enabling CPT functionality.
	 * Use the Writing section from Flex Posts class.
	 */
	public function settings_api_init() {
		add_settings_field(
			self::OPTION_NAME,
			'<span class="portfolio-options">' . esc_html__( 'Portfolio Projects', 'konte-addons' ) . '</span>',
			array( $this, 'enable_field_html' ),
			'writing',
			'konte_custom_content_section'
		);
		register_setting(
			'writing',
			self::OPTION_NAME,
			'intval'
		);

		// Check if CPT is enabled first so that intval doesn't get set to NULL on re-registering
		if ( $this->get_option() ) {
			// Reading settings
			add_settings_section(
				'konte_portfolio_section',
				'<span id="portfolio-options">' . esc_html__( 'Portfolio', 'konte-addons' ) . '</span>',
				array( $this, 'reading_section_html' ),
				'reading'
			);

			add_settings_field(
				self::OPTION_NAME . '_page_id',
				'<span class="portfolio-options">' . esc_html__( 'Portfolio page', 'konte-addons' ) . '</span>',
				array( $this, 'page_field_html' ),
				'reading',
				'konte_portfolio_section'
			);

			register_setting(
				'reading',
				self::OPTION_NAME . '_page_id',
				'intval'
			);

			add_settings_field(
				self::OPTION_NAME . '_posts_per_page',
				'<label for="portfolio_items_per_page">' . esc_html__( 'Portfolio items show at most', 'konte-addons' ) . '</label>',
				array( $this, 'per_page_field_html' ),
				'reading',
				'konte_portfolio_section'
			);

			register_setting(
				'reading',
				self::OPTION_NAME . '_posts_per_page',
				'intval'
			);

			// Permalink settings
			add_settings_section(
				'konte_portfolio_section',
				'<span id="portfolio-options">' . esc_html__( 'Portfolio Item Permalink', 'konte-addons' ) . '</span>',
				array( $this, 'permalink_section_html' ),
				'permalink'
			);

			add_settings_field(
				'portfolio_type_slug',
				'<label for="portfolio_type_slug">' . esc_html__( 'Portfolio category base', 'konte-addons' ) . '</label>',
				array( $this, 'portfolio_type_slug_field_html' ),
				'permalink',
				'optional'
			);

			register_setting(
				'permalink',
				'portfolio_type_slug',
				'sanitize_text_field'
			);

			add_settings_field(
				'portfolio_tag_slug',
				'<label for="portfolio_tag_slug">' . esc_html__( 'Portfolio tag base', 'konte-addons' ) . '</label>',
				array( $this, 'portfolio_tag_slug_field_html' ),
				'permalink',
				'optional'
			);

			register_setting(
				'permalink',
				'portfolio_tag_slug',
				'sanitize_text_field'
			);
		}
	}

	/**
	 * Add reading setting section
	 */
	public function reading_section_html() {
		?>
		<p>
			<?php esc_html_e( 'Use these settings to control custom post type content', 'konte-addons' ); ?>
		</p>
		<?php
	}

	/**
	 * Add permalink setting section
	 * and add fields
	 */
	public function permalink_section_html() {
		$permalinks          = $this->get_option( 'permalinks' );
		$portfolio_permalink = isset( $permalinks['portfolio_base'] ) ? $permalinks['portfolio_base'] : '';

		$portfolio_page_id = $this->get_option( 'page_id' );
		$base_slug         = urldecode( ( $portfolio_page_id > 0 && get_post( $portfolio_page_id ) ) ? get_page_uri( $portfolio_page_id ) : _x( 'portfolio', 'Default slug', 'konte-addons' ) );
		$portfolio_base    = _x( 'portfolio', 'Default slug', 'konte-addons' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $base_slug ),
			2 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%portfolio_type%' ),
		);
		?>
		<p>
			<?php esc_html_e( 'Use these settings to control the permalink used specifically for portfolio.', 'konte-addons' ); ?>
		</p>

		<table class="form-table konte-portfolio-permalink-structure">
			<tbody>
			<tr>
				<th>
					<label><input name="portfolio_permalink" type="radio"
								  value="<?php echo esc_attr( $structures[0] ); ?>" <?php checked( $structures[0], $portfolio_permalink ); ?>
								  class="konte-portfolio-base" /> <?php esc_html_e( 'Default', 'konte-addons' ); ?>
					</label>
				</th>
				<td>
					<code class="default-example"><?php echo esc_html( home_url() ); ?>/?portfolio=sample-portfolio</code>
					<code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $portfolio_base ); ?>/sample-portfolio/</code>
				</td>
			</tr>
			<?php if ( $base_slug !== $portfolio_base ) : ?>
				<tr>
					<th>
						<label><input name="portfolio_permalink" type="radio"
									  value="<?php echo esc_attr( $structures[1] ); ?>" <?php checked( $structures[1], $portfolio_permalink ); ?>
									  class="konte-portfolio-base" /> <?php esc_html_e( 'Portfolio base', 'konte-addons' ); ?>
						</label>
					</th>
					<td>
						<code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/sample-portfolio/</code>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th>
					<label><input name="portfolio_permalink" type="radio"
								  value="<?php echo esc_attr( $structures[2] ); ?>" <?php checked( $structures[2], $portfolio_permalink ); ?>
								  class="konte-portfolio-base" /> <?php esc_html_e( 'Portfolio base with type', 'konte-addons' ); ?>
					</label>
				</th>
				<td>
					<code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/portfolio-type/sample-portfolio/</code>
				</td>
			</tr>
			<tr>
				<th>
					<label><input name="portfolio_permalink" id="konte_portfolio_custom_selection" type="radio"
								  value="custom" <?php checked( in_array( $portfolio_permalink, $structures ), false ); ?> /> <?php esc_html_e( 'Custom Base', 'konte-addons' ); ?>
					</label>
				</th>
				<td>
					<code><?php echo esc_html( home_url() ); ?></code>
					<input name="portfolio_permalink_structure" id="konte_portfolio_permalink_structure" type="text"
						   value="<?php echo esc_attr( $portfolio_permalink ); ?>" class="regular-text code">
				</td>
			</tr>
			</tbody>
		</table>

		<script type="text/javascript">
			jQuery( function () {
				jQuery( 'input.konte-portfolio-base' ).change( function () {
					jQuery( '#konte_portfolio_permalink_structure' ).val( jQuery( this ).val() );
				} );
				jQuery( '.permalink-structure input' ).change( function () {
					jQuery( '.konte-portfolio-permalink-structure' ).find( 'code.non-default-example, code.default-example' ).hide();
					if ( jQuery( this ).val() ) {
						jQuery( '.konte-portfolio-permalink-structure code.non-default-example' ).show();
						jQuery( '.konte-portfolio-permalink-structure input' ).removeAttr( 'disabled' );
					} else {
						jQuery( '.konte-portfolio-permalink-structure code.default-example' ).show();
						jQuery( '.konte-portfolio-permalink-structure input:eq(0)' ).click();
						jQuery( '.konte-portfolio-permalink-structure input' ).attr( 'disabled', 'disabled' );
					}
				} );
				jQuery( '.permalink-structure input:checked' ).change();
				jQuery( '#konte_portfolio_permalink_structure' ).focus( function () {
					jQuery( '#konte_portfolio_custom_selection' ).click();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * HTML code to display a checkbox true/false option
	 * for the Portfolio CPT setting.
	 */
	public function enable_field_html() {
		?>

		<label for="<?php echo esc_attr( self::OPTION_NAME ); ?>">
			<input name="<?php echo esc_attr( self::OPTION_NAME ); ?>"
				   id="<?php echo esc_attr( self::OPTION_NAME ); ?>" <?php checked( $this->get_option(), true ); ?>
				   type="checkbox" value="1" />
			<?php esc_html_e( 'Enable Portfolio Projects for this site.', 'konte-addons' ); ?>
		</label>

		<?php
	}

	/**
	 * HTML code to display a drop-down of option for portfolio page
	 */
	public function page_field_html() {
		wp_dropdown_pages( array(
			'selected'          => $this->get_option( 'page_id' ),
			'name'              => self::OPTION_NAME . '_page_id',
			'show_option_none'  => esc_html__( '&mdash; Select &mdash;', 'konte-addons' ),
			'option_none_value' => 0,
		) );
	}

	/**
	 * HTML code to display a input of option for portfolio items per page
	 */
	public function per_page_field_html() {
		$name = self::OPTION_NAME . '_posts_per_page';
		?>

		<label for="portfolio_posts_per_page">
			<input name="<?php echo esc_attr( $name ) ?>" id="portfolio_items_per_page" type="number" step="1" min="1"
				   value="<?php echo esc_attr( get_option( $name, '9' ) ) ?>" class="small-text" />
			<?php _ex( 'items', 'Portfolio items per page', 'konte-addons' ) ?>
		</label>

		<?php
	}

	/**
	 * HTML code to display a input of option for portfolio type slug
	 */
	public function portfolio_type_slug_field_html() {
		$permalinks = $this->get_option( 'permalinks' );
		$type_base  = isset( $permalinks['portfolio_type_base'] ) ? $permalinks['portfolio_type_base'] : _x( 'portfolio-type', 'slug', 'konte-addons' );
		?>
		<input name="portfolio_type_slug" id="portfolio_type_slug" type="text"
			   value="<?php echo esc_attr( $type_base ) ?>"
			   placeholder="<?php echo esc_attr( _x( 'portfolio-type', 'Portfolio type base', 'konte-addons' ) ) ?>"
			   class="regular-text code">
		<?php
	}

	/**
	 * HTML code to display a input of option for portfolio type slug
	 */
	public function portfolio_tag_slug_field_html() {
		$permalinks = $this->get_option( 'permalinks' );
		$tag_base  = isset( $permalinks['portfolio_tag_base'] ) ? $permalinks['portfolio_tag_base'] : _x( 'portfolio-tag', 'slug', 'konte-addons' );
		?>
		<input name="portfolio_tag_slug" id="portfolio_tag_slug" type="text"
			   value="<?php echo esc_attr( $tag_base ) ?>"
			   placeholder="<?php echo esc_attr( _x( 'portfolio-tag', 'Portfolio tag base', 'konte-addons' ) ) ?>"
			   class="regular-text code">
		<?php
	}

	/**
	 * Save the settings for permalink
	 * Settings api does not trigger save for the permalink page.
	 */
	public function save_settings() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $screen = get_current_screen() ) {
			return;
		}

		if ( 'options-permalink' != $screen->id ) {
			return;
		}

		$permalinks = $this->get_option( 'permalinks' );

		if ( ! $permalinks ) {
			$permalinks = array();
		}

		if ( isset( $_POST['portfolio_type_slug'] ) ) {
			$permalinks['portfolio_type_base'] = $this->sanitize_permalink( trim( $_POST['portfolio_type_slug'] ) );
		}

		if ( isset( $_POST['portfolio_tag_slug'] ) ) {
			$permalinks['portfolio_tag_base'] = $this->sanitize_permalink( trim( $_POST['portfolio_tag_slug'] ) );
		}

		if ( isset( $_POST['portfolio_permalink'] ) ) {
			$portfolio_permalink = sanitize_text_field( $_POST['portfolio_permalink'] );

			if ( 'custom' === $portfolio_permalink ) {
				if ( isset( $_POST['portfolio_permalink_structure'] ) ) {
					$portfolio_permalink = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( $_POST['portfolio_permalink_structure'] ) ) );
				} else {
					$portfolio_permalink = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '%portfolio_type%' == $portfolio_permalink ) {
					$portfolio_permalink = '/' . _x( 'portfolio', 'slug', 'konte-addons' ) . '/' . $portfolio_permalink;
				}
			} elseif ( empty( $portfolio_permalink ) ) {
				$portfolio_permalink = false;
			}

			$permalinks['portfolio_base'] = $this->sanitize_permalink( $portfolio_permalink );

			// Portfolio base may require verbose page rules if nesting pages.
			$portfolio_page_id   = $this->get_option( 'page_id' );
			$portfolio_permalink = ( $portfolio_page_id > 0 && get_post( $portfolio_page_id ) ) ? get_page_uri( $portfolio_page_id ) : _x( 'portfolio', 'Default slug', 'konte-addons' );

			if ( $portfolio_page_id && trim( $permalinks['portfolio_base'], '/' ) === $portfolio_permalink ) {
				$permalinks['use_verbose_page_rules'] = true;
			}
		}

		update_option( self::OPTION_NAME . '_permalinks', $permalinks );
	}

	/**
	 * Follow CPT reading setting on CPT archive and taxonomy pages
	 */
	function query_reading_setting( $query ) {
		if ( ( ! is_admin() || ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			&& $query->is_main_query()
			&& ( $query->is_post_type_archive( self::POST_TYPE )
				|| $query->is_tax( self::TAXONOMY_TYPE )
				|| $query->is_tax( self::TAXONOMY_TAG ) )
		) {
			$query->set( 'posts_per_page', $this->get_option( 'posts_per_page', 9 ) );
		}

		// Special check for portfolio with the portfolio post type archive on front.
		if ( $query->is_page() && 'page' === get_option( 'show_on_front' ) && $query->get( 'page_id' ) == get_option( 'page_on_front' ) && absint( $query->get( 'page_id' ) ) == $this->get_option( 'page_id' ) ) {
			$query->set( 'post_type', self::POST_TYPE );
			$query->set( 'page_id', '' );

			if ( isset( $query->query['paged'] ) ) {
				$query->set( 'paged', $query->query['paged'] );
			}

			$query->is_singular          = false;
			$query->is_page              = true;
			$query->is_post_type_archive = true;
			$query->is_archive           = true;
		}
	}

	/**
	 * Sanitize permalink
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function sanitize_permalink( $value ) {
		global $wpdb;

		$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

		if ( is_wp_error( $value ) ) {
			$value = '';
		}

		$value = esc_url_raw( $value );
		$value = str_replace( 'http://', '', $value );

		return untrailingslashit( $value );
	}

	/**
	 * Various rewrite rule fixes.
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	public function rewrite_rules( $rules ) {
		global $wp_rewrite;

		$permalinks          = $this->get_option( 'permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'konte-addons' ) : $permalinks['portfolio_base'];

		// Fix the rewrite rules when the portfolio permalink have %portfolio_type% flag.
		if ( preg_match( '`/(.+)(/%portfolio_type%)`', $portfolio_permalink, $matches ) ) {
			foreach ( $rules as $rule => $rewrite ) {
				if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '/\(`', $rule ) && preg_match( '/^(index\.php\?portfolio_type)(?!(.*portfolio))/', $rewrite ) ) {
					unset( $rules[ $rule ] );
				}
			}
		}

		// If the portfolio page is used as the base, we need to handle shop page subpages to avoid 404s.
		if ( ! isset( $permalinks['use_verbose_page_rules'] ) || ! $permalinks['use_verbose_page_rules'] ) {
			return $rules;
		}

		$portfolio_page_id = $this->get_option( 'page_id' );

		if ( $portfolio_page_id ) {
			$page_rewrite_rules = array();
			$subpages           = $this->get_page_children( $portfolio_page_id );

			// Subpage rules
			foreach ( $subpages as $subpage ) {
				$uri = get_page_uri( $subpage );
				$page_rewrite_rules[ $uri . '/?$' ] = 'index.php?pagename=' . $uri;
				$wp_generated_rewrite_rules         = $wp_rewrite->generate_rewrite_rules( $uri, EP_PAGES, true, true, false, false );
				foreach ( $wp_generated_rewrite_rules as $key => $value ) {
					$wp_generated_rewrite_rules[ $key ] = $value . '&pagename=' . $uri;
				}
				$page_rewrite_rules = array_merge( $page_rewrite_rules, $wp_generated_rewrite_rules );
			}

			// Merge with rules
			$rules = array_merge( $page_rewrite_rules, $rules );
		}

		return $rules;
	}

	/**
	 * Prevent portfolio attachment links from breaking when using complex rewrite structures.
	 *
	 * @param  string $link
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function portfolio_attachment_link( $link, $post_id ) {
		global $wp_rewrite;

		$post = get_post( $post_id );
		if ( self::POST_TYPE === get_post_type( $post->post_parent ) ) {
			$permalinks          = $this->get_option( 'permalinks' );
			$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'konte-addons' ) : $permalinks['portfolio_base'];
			if ( preg_match( '/\/(.+)(\/%portfolio_type%)$/', $portfolio_permalink, $matches ) ) {
				$link = home_url( '/?attachment_id=' . $post->ID );
			}
		}

		return $link;
	}

	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 */
	public function template_redirect() {
		if ( ! is_page() ) {
			return;
		}

		// When default permalinks are enabled, redirect portfolio page to post type archive url
		if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && $_GET['page_id'] == $this->get_option( 'page_id' ) ) {
			wp_safe_redirect( get_post_type_archive_link( self::POST_TYPE ) );
			exit;
		}
	}

	/**
	 * Filter to allow portfolio_type in the permalinks for portfolios.
	 *
	 * @param  string  $permalink The existing permalink URL.
	 * @param  WP_Post $post
	 *
	 * @return string
	 */
	public function portfolio_post_type_link( $permalink, $post ) {
		// Abort if post is not a portfolio.
		if ( $post->post_type !== self::POST_TYPE ) {
			return $permalink;
		}

		// Abort early if the placeholder rewrite tag isn't in the generated URL.
		if ( false === strpos( $permalink, '%' ) ) {
			return $permalink;
		}

		// Get the custom taxonomy terms in use by this post.
		$terms = get_the_terms( $post->ID, self::TAXONOMY_TYPE );

		if ( ! empty( $terms ) ) {
			if ( function_exists( 'wp_list_sort' ) ) {
				$terms = wp_list_sort( $terms, 'term_id', 'ASC' );
			} else {
				usort( $terms, '_usort_terms_by_ID' );
			}
			$type_object    = get_term( $terms[0], self::TAXONOMY_TYPE );
			$portfolio_type = $type_object->slug;

			if ( $type_object->parent ) {
				$ancestors = get_ancestors( $type_object->term_id, self::TAXONOMY_TYPE );

				foreach ( $ancestors as $ancestor ) {
					$ancestor_object = get_term( $ancestor, self::TAXONOMY_TYPE );
					$portfolio_type  = $ancestor_object->slug . '/' . $portfolio_type;
				}
			}
		} else {
			// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
			$portfolio_type = _x( 'uncategorized', 'slug', 'konte-addons' );
		}

		$find = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			'%post_id%',
			'%portfolio_type%',
		);

		$replace = array(
			date_i18n( 'Y', strtotime( $post->post_date ) ),
			date_i18n( 'm', strtotime( $post->post_date ) ),
			date_i18n( 'd', strtotime( $post->post_date ) ),
			date_i18n( 'H', strtotime( $post->post_date ) ),
			date_i18n( 'i', strtotime( $post->post_date ) ),
			date_i18n( 's', strtotime( $post->post_date ) ),
			$post->ID,
			$portfolio_type,
		);

		$permalink = str_replace( $find, $replace, $permalink );

		return $permalink;
	}

	/**
	 * Change taxonomy for breadcrumb
	 *
	 * @param array $args
	 *
	 * @return array mixed
	 */
	public function breadcrumb( $args ) {
		if ( is_singular( self::POST_TYPE ) ) {
			$args['taxonomy'] = self::TAXONOMY_TYPE;
		}

		if ( is_post_type_archive( self::POST_TYPE ) ) {
			$portfolio_page_id = $this->get_option( 'page_id' );

			if ( $portfolio_page_id && get_post( $portfolio_page_id ) ) {
				$args['labels']['archive'] = get_the_title( $portfolio_page_id );
			} else {
				$args['labels']['archive'] = _x( 'Portfolio', 'Portfolio post type breadcrumb', 'konte-addons' );
			}
		}

		return $args;
	}

	/**
	 * Change archive title
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function archive_title( $title ) {
		if ( is_post_type_archive( self::POST_TYPE ) ) {
			$portfolio_page_id = $this->get_option( 'page_id' );

			if ( $portfolio_page_id && get_post( $portfolio_page_id ) ) {
				$title = get_the_title( $portfolio_page_id );
			} else {
				$title = _x( 'Portfolio', 'Portfolio post type breadcrumb', 'konte-addons' );
			}
		}

		return $title;
	}

	/**
	 * Recursively get page children.
	 * @param  int $page_id
	 *
	 * @return array
	 */
	public function get_page_children( $page_id ) {
		$page_ids = get_posts( array(
			'post_parent'      => $page_id,
			'post_type'        => 'page',
			'numberposts'      => -1,
			'post_status'      => 'any',
			'fields'           => 'ids',
			'suppress_filters' => false,
		) );

		if ( ! empty( $page_ids ) ) {
			foreach ( $page_ids as $page_id ) {
				$page_ids = array_merge( $page_ids, $this->get_page_children( $page_id ) );
			}
		}

		return $page_ids;
	}

	/**
	 * Get option of portfolio.
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	private function get_option( $option = '', $default = false ) {
		if ( ! is_string( $option ) ) {
			return $default;
		}

		if ( empty( $option ) ) {
			return get_option( self::OPTION_NAME, $default );
		}

		return get_option( sprintf( '%s_%s', self::OPTION_NAME, $option ), $default );
	}

	/**
	 * Modify the main query of the post type archive page in other languages
	 * @see WCML_Store_Pages::shop_page_query
	 */
	public function translate_archive_query( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$portfolio_page_id = $this->get_option( 'page_id' );
		$portfolio_page_id = apply_filters( 'translate_object_id', $portfolio_page_id, 'page', true, null );
		$page_id           = $query->get( 'page_id' ) ? $query->get( 'page_id' ) : $query->queried_object_id;

		if ( $portfolio_page_id && $portfolio_page_id == $page_id && self::POST_TYPE !== $query->get( 'post_type' ) ) {
			global $wp_query;

			// do not alter query_object and query_object_id (part 1 of 2).
			$queried_object_original    = isset( $wp_query->queried_object ) ? $wp_query->queried_object : null;
			$queried_object_id_original = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : null;

			$query->set( 'post_type', self::POST_TYPE );
			$query->set( 'page_id', '' );
			$query->set( 'pagename', '' );
			$query->set( 'posts_per_page', $this->get_option( 'posts_per_page', 9 ) );
			$query->set( 'suppress_filters', false );

			if ( isset( $query->query['paged'] ) ) {
				$query->set( 'paged', $query->query['paged'] );
			}

			// Get the actual WP page to avoid errors
			// This is hacky but works. Awaiting http://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$query->is_page = true;
			$portfolio_page = get_post( $portfolio_page_id );

			$wp_post_types[ self::POST_TYPE ]->ID         = $portfolio_page->ID;
			$wp_post_types[ self::POST_TYPE ]->post_title = $portfolio_page->post_title;
			$wp_post_types[ self::POST_TYPE ]->post_name  = $portfolio_page->post_name;
			$wp_post_types[ self::POST_TYPE ]->post_type  = $portfolio_page->post_type;
			$wp_post_types[ self::POST_TYPE ]->ancestors  = get_ancestors( $portfolio_page->ID, $portfolio_page->post_type );

			// fix condition funcitons.
			$query->is_singular          = false;
			$query->is_post_type_archive = true;
			$query->is_archive           = true;
			$query->is_page              = false;

			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// do not alter query_object and query_object_id (part 2 of 2).
			if ( is_null( $queried_object_original ) ) {
				unset( $wp_query->queried_object );
			} else {
				$wp_query->queried_object = $queried_object_original;
			}
			if ( is_null( $queried_object_id_original ) ) {
				unset( $wp_query->queried_object_id );
			} else {
				$wp_query->queried_object_id = $queried_object_id_original;
			}
		}
	}

	/**
	 * Translate portfolio archive page URL.
	 * @see WCML_Store_Pages::translate_ls_shop_url
	 *
	 * @param array $languages
	 * @return array
	 */
	public function translate_archive_url( $languages ) {
		$portfolio_page_id = $this->get_option( 'page_id' );
		$current_lang      = apply_filters( 'wpml_current_language', null );

		foreach ( $languages as $language ) {
			if ( is_post_type_archive( self::POST_TYPE ) ) {
				do_action( 'wpml_switch_language', $language['language_code'] );
				$url = get_permalink( apply_filters( 'translate_object_id', $portfolio_page_id, 'page', true, $language['language_code'] ) );
				do_action( 'wpml_switch_language', $current_lang );

				$languages[ $language['language_code'] ]['url'] = $url;
			}
		}

		// copy get parameters?
		$gets_passed       = array();
		$parameters_copied = apply_filters(
			'icl_lang_sel_copy_parameters',
			array_map(
				'trim',
				explode(
					',',
					wpml_get_setting_filter(
						'',
						'icl_lang_sel_copy_parameters'
					)
				)
			)
		);
		if ( $parameters_copied ) {
			foreach ( $_GET as $k => $v ) {
				if ( in_array( $k, $parameters_copied ) ) {
					$gets_passed[ $k ] = $v;
				}
			}

			foreach ( $languages as $code => $language ) {
				$languages[ $code ]['url'] = add_query_arg( $gets_passed, $language['url'] );
			}
		}

		return $languages;
	}
}

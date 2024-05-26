<?php
/**
 * Portfolio Compatibility file
 *
 * @package Konte
 */

class Konte_Portfolio {
	/**
	 * The single instance of the class
	 *
	 * @var Konte_Portfolio
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return Konte_Portfolio
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construction function
	 */
	public function __construct() {
		// Add meta box for single portfolio.
		add_filter( 'rwmb_meta_boxes', array( $this, 'meta_box' ) );

		// Add theme options.
		add_filter( 'konte_customize_panels', array( $this, 'customize_panels' ) );
		add_filter( 'konte_customize_sections', array( $this, 'customize_sections' ) );
		add_filter( 'konte_customize_settings', array( $this, 'customize_settings' ) );

		// Custom CSS.
		add_filter( 'konte_inline_style', array( $this, 'inline_style' ) );

		// Add more classes to body and header.
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'konte_header_class', array( $this, 'header_class' ), 20 );

		// Check the header layout of the portfolio page.
		add_filter( 'konte_get_header_layout', array( $this, 'header_layout' ) );

		// Change content container class.
		add_filter( 'konte_content_container_class', array( $this, 'content_container_class' ) );

		// Portfolio page header.
		add_action( 'konte_before_content_wrapper', array( $this, 'page_header' ) );

		// Portfolio filter.
		add_action( 'konte_portfolio_before_loop', array( $this, 'filter' ) );

		// Portfolio pagination.
		add_action( 'konte_portfolio_after_loop', array( $this, 'pagination' ) );

		// Float social and scroll-down arrow.
		add_action( 'konte_after_site', array( $this, 'socials' ) );
		add_action( 'konte_after_site', array( $this, 'scrolldown_arrow' ) );
	}

	/**
	 * Check if current page is portfolio archive page.
	 *
	 * @return boolean
	 */
	public function is_portfolio_archive() {
		return is_post_type_archive( 'portfolio' ) || is_tax( get_object_taxonomies( 'portfolio' ) );
	}

	/**
	 * Filter function to add custom CSS of WPB to the portfolio archive page.
	 *
	 * @param  string $css
	 * @return string
	 */
	public function inline_style( $css ) {
		if ( ! is_post_type_archive( 'portfolio' ) ) {
			return $css;
		}

		if ( $portfolio_page_id = get_option( 'konte_portfolio_page_id' ) ) {
			$page_css      = get_post_meta( $portfolio_page_id, '_wpb_post_custom_css', true );
			$shortcode_css = get_post_meta( $portfolio_page_id, '_wpb_shortcodes_custom_css', true );

			if ( ! empty( $page_css ) ) {
				$css .= $page_css;
			}

			if ( ! empty( $shortcode_css ) ) {
				$css .= $shortcode_css;
			}
		}

		return $css;
	}

	/**
	 * Add body classes.
	 *
	 * @param array $classes
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( is_singular( 'portfolio' ) ) {
			$classes[] = 'project-layout-' . konte_get_option( 'project_layout' );
		} elseif ( $this->is_portfolio_archive() ) {
			$classes[] = 'portfolio-archive';
			$classes[] = 'portfolio-layout-' . konte_get_option( 'portfolio_layout' );
		}

		return $classes;
	}

	/**
	 * Add more header classes
	 *
	 * @param array $classes
	 * @return array
	 */
	public function header_class( $classes ) {
		if ( is_singular( 'portfolio' ) && 'v2' == konte_get_option( 'project_layout' ) ) {
			$classes = array_diff( $classes, array( 'dark', 'light', 'custom', 'text-dark', 'text-light' ) );

			$classes[] = 'transparent';

			if ( $text_color = get_post_meta( get_the_ID(), 'header_textcolor', true ) ) {
				$classes[] = 'text-' . $text_color;
			} else {
				$classes[] = 'text-dark';
			}
		}

		return $classes;
	}

	/**
	 * Change the header layout based on display settings.
	 *
	 * @param string $header_layout
	 * @return string
	 */
	public function header_layout( $header_layout ) {
		if ( $this->is_portfolio_archive() ) {
			$portfolio_page_id = get_option( 'konte_portfolio_page_id' );

			$layout            = $portfolio_page_id ? get_post_meta( $portfolio_page_id, 'header_layout', true ) : '';
			$header_layout     = $layout ? $layout : $header_layout;
		}

		return $header_layout;
	}

	/**
	 * Change the site container class.
	 *
	 * @param string $class
	 * @return string
	 */
	public function content_container_class( $class ) {
		if ( is_singular( 'portfolio' ) || $this->is_portfolio_archive() ) {
			return 'konte-container';
		}

		return $class;
	}

	/**
	 * Page header
	 */
	public function page_header() {
		if ( ! konte_get_option( 'portfolio_page_header' ) ) {
			return;
		}

		if ( ! $this->is_portfolio_archive() ) {
			return;
		}

		get_template_part( 'template-parts/portfolio/page-header' );
	}

	/**
	 * Portfolio filter
	 */
	public function filter() {
		if ( ! konte_get_option( 'portfolio_filter' ) ) {
			return;
		}

		$active   = false;
		$terms    = array();
		$taxonomy = 'portfolio_type';
		$slugs    = trim( konte_get_option( 'portfolio_filter_cats' ) );

		// Get terms.
		if ( is_tax( $taxonomy ) && konte_get_option( 'portfolio_filter_cats_replace' ) ) {
			$queried = get_queried_object();

			$args = array(
				'taxonomy' => $taxonomy,
				'parent'   => $queried->term_id,
			);

			if ( is_numeric( $slugs ) ) {
				$args['orderby'] = 'count';
				$args['order']   = 'DESC';
				$args['number']  = intval( $slugs );
			}

			$terms = get_terms( $args );
		}

		// Keep get default tabs if there is no sub-categorys.
		if ( empty( $terms ) ) {
			if ( is_numeric( $slugs ) ) {
				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'orderby'  => 'count',
					'order'    => 'DESC',
					'number'   => intval( $slugs ),
				) );
			} elseif ( ! empty( $slugs ) ) {
				$slugs = explode( ',', $slugs );
				$slugs = array_filter( $slugs );

				if ( empty( $slugs ) ) {
					return;
				}

				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'orderby'  => 'slug__in',
					'slug'     => $slugs,
				) );
			} else {
				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'orderby'  => 'count',
					'order'    => 'DESC',
					'parent'   => 0,
				) );
			}
		}

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			if ( is_tax( $taxonomy, $term->slug ) ) {
				$active = true;
			}

			$tabs[] = sprintf(
				'<a href="%s" class="tab-%s underline-hover %s">%s</a>',
				esc_url( get_term_link( $term ) ),
				esc_attr( $term->slug ),
				is_tax( $taxonomy, $term->slug ) ? 'active' : '',
				esc_html( $term->name )
			);
		}

		if ( $portfolio_page_id = get_option( 'konte_portfolio_page_id' ) ) {
			array_unshift( $tabs, sprintf(
				'<a href="%s" class="tab-all underline-hover %s">%s</a></li>',
				esc_url( get_page_link( $portfolio_page_id ) ),
				$active ? '' : 'active',
				esc_html__( 'All Projects', 'konte' )
			) );
		}

		echo '<div class="portfolio-filter">';

		echo implode( '', $tabs );

		echo '</div>';
	}

	/**
	 * Portfolio pagination
	 */
	public function pagination() {
		get_template_part( 'template-parts/portfolio/pagination' );
	}

	/**
	 * Display floating socials on side
	 */
	public function socials() {
		if ( ! $this->is_portfolio_archive() ) {
			return;
		}

		if ( ! konte_get_option( 'portfolio_socials' ) || ! has_nav_menu( 'socials' ) ) {
			return;
		}

		wp_nav_menu( array(
			'theme_location'  => 'socials',
			'container_id'    => 'sticky-socials',
			'container_class' => 'socials-menu sticky-socials',
			'menu_id'         => 'menu-socials-sticky',
			'depth'           => 1,
			'link_before'     => '<span>',
			'link_after'      => '</span>',
		) );
	}

	/**
	 * Display the scroll-down arrow
	 */
	public function scrolldown_arrow() {
		if ( ! $this->is_portfolio_archive() ) {
			return;
		}

		if ( ! konte_get_option( 'portfolio_scrolldown' ) ) {
			return;
		}

		?>
		<span class="flex-posts-scroll-down sticky-scrolldown">
			<?php konte_svg_icon( 'icon=arrow-left&class=arrow-left-icon' ) ?>
			<?php esc_html_e( 'Scroll Down', 'konte' ); ?>
		</span>
		<?php
	}

	/**
	 * Add display settings meta box.
	 *
	 * @param array $meta_boxes
	 * @return array
	 */
	public function meta_box( $meta_boxes ) {
		if ( 'v2' != konte_get_option( 'project_layout' ) ) {
			return $meta_boxes;
		}

		// Display Settings.
		$meta_boxes[] = array(
			'id'       => 'display-settings',
			'title'    => esc_html__( 'Display Settings', 'konte' ),
			'pages'    => array( 'portfolio' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Header', 'konte' ),
					'id'   => 'heading_site_header',
					'type' => 'heading',
				),
				array(
					'name'    => esc_html__( 'Header Text Color', 'konte' ),
					'id'      => 'header_textcolor',
					'type'    => 'radio',
					'std'     => 'dark',
					'options' => array(
						'dark'  => esc_html__( 'Text Dark', 'konte' ),
						'light' => esc_html__( 'Text Light', 'konte' ),
					),
				),
				array(
					'name' => esc_html__( 'Project Header', 'konte' ),
					'id'   => 'heading_project_header',
					'type' => 'heading',
				),
				array(
					'name'    => esc_html__( 'Project Header Text Color', 'konte' ),
					'id'      => 'project_header_textcolor',
					'type'    => 'radio',
					'std'     => 'dark',
					'options' => array(
						'dark'  => esc_attr__( 'Text Dark', 'konte' ),
						'light' => esc_attr__( 'Text Light', 'konte' ),
					),
				),
			),
		);

		return $meta_boxes;
	}

	/**
	 * Add portfolio panel to the Customizer.
	 *
	 * @param array $panels
	 * @return array
	 */
	public function customize_panels( $panels ) {
		$panels['portfolio'] = array(
			'priority' => 270,
			'title'    => esc_html__( 'Portfolio', 'konte' ),
		);

		return $panels;
	}

	/**
	 * Add portfolio sections to the Customizer.
	 *
	 * @param array $sections
	 * @return array
	 */
	public function customize_sections( $sections ) {
		$sections['portfolio_archive'] = array(
			'title'    => esc_html__( 'Portfolio Page', 'konte' ),
			'priority' => 10,
			'panel'    => 'portfolio',
		);

		$sections['portfolio_single'] = array(
			'title'    => esc_html__( 'Project Page', 'konte' ),
			'priority' => 20,
			'panel'    => 'portfolio',
		);

		return $sections;
	}

	/**
	 * Add portfolio settings to the Customizer.
	 *
	 * @param array $settings
	 * @return array
	 */
	public function customize_settings( $settings ) {
		$settings['portfolio_archive'] = array(
			'portfolio_layout'                    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Portfolio Layout', 'konte' ),
				'default'     => 'masonry',
				'choices'     => array(
					'grid'    => esc_html__( 'Grid', 'konte' ),
					'masonry' => esc_html__( 'Masonry', 'konte' ),
				),
			),
			'portfolio_columns'                    => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Columns', 'konte' ),
				'default'     => 2,
				'choices'     => array(
					'2' => esc_html__( '2 Columns', 'konte' ),
					'3' => esc_html__( '3 Columns', 'konte' ),
				),
			),
			'portfolio_hr_1'                    => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'portfolio_page_header'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Page Header', 'konte' ),
				'default'     => true,
			),
			'portfolio_page_header_content'                    => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Page Header Content', 'konte' ),
				'tooltip'     => esc_html__( 'Portfolio page is set in Settings > Reading.', 'konte' ),
				'default'     => 'page_content',
				'choices'     => array(
					'page_content' => esc_html__( 'Portfolio Page Content', 'konte' ),
					'page_title'   => esc_html__( 'Portfolio Page Title', 'konte' ),
					'custom'       => esc_html__( 'Custom Content', 'konte' ),
				),
				'active_callback' => array(
					array(
						'setting'       => 'portfolio_page_header',
						'operator'      => '==',
						'value'         => true,
					),
				),
				'partial_refresh'   => array(
					'portfolio_page_header_content' => array(
						'selector'            => '.portfolio-page-header',
						'container_inclusive' => true,
						'render_callback'     => function() {
							get_template_part( 'template-parts/portfolio/page-header' );
						},
					)
				),
			),
			'portfolio_page_header_custom'                    => array(
				'type'        => 'textarea',
				'description' => esc_html__( 'Custom content. Allows HTML and shortcodes.', 'konte' ),
				'active_callback' => array(
					array(
						'setting'       => 'portfolio_page_header',
						'operator'      => '==',
						'value'         => true,
					),
					array(
						'setting'       => 'portfolio_page_header_content',
						'operator'      => '==',
						'value'         => 'custom',
					),
				),
				'partial_refresh'   => array(
					'portfolio_page_header_custom' => array(
						'selector'            => '.portfolio-page-header',
						'container_inclusive' => true,
						'render_callback'     => function() {
							get_template_part( 'template-parts/portfolio/page-header' );
						},
					)
				),
			),
			'portfolio_hr_2'                    => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'portfolio_filter'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Categories Filter', 'konte' ),
				'default'     => true,
			),
			'portfolio_filter_cats'                    => array(
				'type'        => 'text',
				'description' => esc_html__( 'Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get a limit number of top categories.', 'konte' ),
				'active_callback' => array(
					array(
						'setting'       => 'portfolio_filter',
						'operator'      => '==',
						'value'         => true,
					),
				),
			),
			'portfolio_filter_cats_replace' => array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Replace by sub-categories', 'konte' ),
				'default'         => false,
				'active_callback' => array(
					array(
						'setting'       => 'portfolio_filter',
						'operator'      => '==',
						'value'         => true,
					),
				),
			),
			'portfolio_hr_3'                    => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'portfolio_socials'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Floating Social Icons', 'konte' ),
				'description' => esc_html__( 'Display floating social icons on side on large screens', 'konte' ),
				'default'     => true,
			),
			'portfolio_scrolldown'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Scroll Down Arrow', 'konte' ),
				'description' => esc_html__( 'Display floating scroll down arrow on side on large screens', 'konte' ),
				'default'     => true,
			),
			'portfolio_hr_4'                    => array(
				'type'    => 'custom',
				'default' => '<hr>',
			),
			'portfolio_nav_type'                    => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Navigation Type', 'konte' ),
				'default'     => 'loadmore',
				'choices'     => array(
					'numeric'  => esc_html__( 'Numeric', 'konte' ),
					'loadmore' => esc_html__( 'Load More', 'konte' ),
				),
			),
			'portfolio_nav_ajax_url_change'               => array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Change the URL after page loaded', 'konte' ),
				'default' => true,
				'active_callback' => array(
					array(
						'setting'  => 'portfolio_nav_type',
						'operator' => '!=',
						'value'    => 'numeric',
					),
				),
			),
		);

		$settings['portfolio_single'] = array(
			'project_layout'          => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Project Layout', 'konte' ),
				'default' => 'v1',
				'choices' => array(
					'v1' => esc_html__( 'Layout v1', 'konte' ),
					'v2' => esc_html__( 'Layout v2', 'konte' ),
				),
			),
			'project_share'           => array(
				'type'    => 'toggle',
				'label'   => esc_html__( 'Project Sharing', 'konte' ),
				'default' => true,
			),
			'project_sharing_socials' => array(
				'type'            => 'sortable',
				'description'     => esc_html__( 'Select social media for sharing projects', 'konte' ),
				'default'         => array(
					'facebook',
					'twitter',
					'googleplus',
					'pinterest',
					'tumblr',
					'reddit',
					'telegram',
					'email',
				),
				'choices'         => array(
					'facebook'    => esc_html__( 'Facebook', 'konte' ),
					'twitter'     => esc_html__( 'Twitter', 'konte' ),
					'googleplus'  => esc_html__( 'Google Plus', 'konte' ),
					'pinterest'   => esc_html__( 'Pinterest', 'konte' ),
					'tumblr'      => esc_html__( 'Tumblr', 'konte' ),
					'reddit'      => esc_html__( 'Reddit', 'konte' ),
					'linkedin'    => esc_html__( 'Linkedin', 'konte' ),
					'stumbleupon' => esc_html__( 'StumbleUpon', 'konte' ),
					'digg'        => esc_html__( 'Digg', 'konte' ),
					'telegram'    => esc_html__( 'Telegram', 'konte' ),
					'vk'          => esc_html__( 'VK', 'konte' ),
					'email'       => esc_html__( 'Email', 'konte' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'project_share',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'project_navigation'      => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Single Navigation', 'konte' ),
				'description' => esc_html__( 'Enable next/previous project navigation', 'konte' ),
				'default'     => true,
			),
			'project_related'         => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Related Projects', 'konte' ),
				'description' => esc_html__( 'Display projects in the same category at bottom', 'konte' ),
				'default'     => false,
			),
		);

		return $settings;
	}
}

Konte_Portfolio::instance();
<?php
/**
 * Registering meta boxes
 *
 * @package Konte
 */

/**
 * Registering meta boxes
 *
 * @param array $meta_boxes Default meta boxes. By default, there are no meta boxes.
 *
 * @return array All registered meta boxes
 */
function konte_register_meta_boxes( $meta_boxes ) {
	// Subtitle.
	$meta_boxes[] = array(
		'id'       => 'subtitle',
		'title'    => esc_html__( 'Subtitle', 'konte' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'desc'       => esc_html__( 'The subtitle to be displayed bellow the page title.', 'konte' ),
				'id'         => '_subtitle',
				'type'       => 'text',
				'attributes' => array(
					'class' => 'widefat',
				),
			),
		),
	);

	// Flex Posts.
	$meta_boxes[] = array(
		'id'       => 'flexposts-data',
		'title'    => esc_html__( 'Flex Posts Settings', 'konte' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'name' => esc_html__( 'Posts per page', 'konte' ),
				'desc' => esc_html__( 'Number of flex posts per page', 'konte' ),
				'id'   => 'flex_posts_per_page',
				'type' => 'number',
				'std'  => 13,
			),
			array(
				'name' => esc_html__( 'Page title', 'konte' ),
				'desc' => esc_html__( 'Use this page content as the title', 'konte' ),
				'id'   => 'flex_posts_content_as_title',
				'type' => 'checkbox',
				'std'  => true,
			),
		),
	);

	// Display Settings.
	$meta_boxes[] = array(
		'id'       => 'display-settings',
		'title'    => esc_html__( 'Display Settings', 'konte' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'name' => esc_html__( 'Header', 'konte' ),
				'id'   => 'heading_site_header',
				'class' => 'header-heading',
				'type' => 'heading',
			),
			array(
				'name'    => esc_html__( 'Header Layout', 'konte' ),
				'id'      => 'header_layout',
				'type'    => 'select',
				'options' => array(
					''    => esc_html__( 'Default', 'konte' ),
					'v1'  => esc_html__( 'Header V1', 'konte' ),
					'v2'  => esc_html__( 'Header V2', 'konte' ),
					'v3'  => esc_html__( 'Header V3', 'konte' ),
					'v4'  => esc_html__( 'Header V4', 'konte' ),
					'v5'  => esc_html__( 'Header V5', 'konte' ),
					'v6'  => esc_html__( 'Header V6', 'konte' ),
					'v7'  => esc_html__( 'Header V7', 'konte' ),
					'v8'  => esc_html__( 'Header V8', 'konte' ),
					'v9'  => esc_html__( 'Header V9', 'konte' ),
					'v10' => esc_html__( 'Header V10', 'konte' ),
				),
			),
			array(
				'name'    => esc_html__( 'Header Background', 'konte' ),
				'id'      => 'header_background',
				'type'    => 'select',
				'options' => array(
					''            => esc_html__( 'Default', 'konte' ),
					'dark'        => esc_html__( 'Dark', 'konte' ),
					'light'       => esc_html__( 'Light', 'konte' ),
					'transparent' => esc_html__( 'Transparent', 'konte' ),
					'custom'      => esc_html__( 'Custom', 'konte' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'header_background_color',
				'class' => 'header-background-color hidden',
				'type'  => 'color',
			),
			array(
				'name'    => esc_html__( 'Header Text Color', 'konte' ),
				'id'      => 'header_textcolor',
				'class'   => 'header-text-color hidden',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'konte' ),
					'dark'  => esc_html__( 'Dark', 'konte' ),
					'light' => esc_html__( 'Light', 'konte' ),
				),
			),
			array(
				'name' => esc_html__( 'Page Header', 'konte' ),
				'id'   => 'heading_page_header',
				'type' => 'heading',
			),
			array(
				'name' => esc_html__( 'Page Content', 'konte' ),
				'id'   => 'heading_page_content',
				'class' => 'page-content-heading split-content-field hidden',
				'type' => 'heading',
			),
			array(
				'name'    => esc_html__( 'Page Title Display', 'konte' ),
				'id'      => 'page_title_display',
				'type'    => 'select',
				'options' => array(
					''      => esc_attr__( 'Default', 'konte' ),
					'none'  => esc_attr__( 'Hide Page Title', 'konte' ),
					'above' => esc_attr__( 'Above featured image', 'konte' ),
					'front' => esc_attr__( 'In front of featured image', 'konte' ),
				),
			),
			array(
				'name'    => esc_html__( 'Page Title Color', 'konte' ),
				'id'      => 'page_title_color',
				'class'   => 'page-title-color hidden',
				'type'    => 'select',
				'std'     => 'light',
				'options' => array(
					''      => esc_attr__( 'Default', 'konte' ),
					'dark'  => esc_attr__( 'Text Dark', 'konte' ),
					'light' => esc_attr__( 'Text Light', 'konte' ),
				),
			),
			array(
				'name'    => esc_html__( 'Page Header Height', 'konte' ),
				'id'      => 'page_header_height',
				'type'    => 'select',
				'options' => array(
					''       => esc_attr__( 'Default', 'konte' ),
					'full'   => esc_attr__( 'Full height', 'konte' ),
					'manual' => esc_attr__( 'Manual', 'konte' ),
				),
			),
			array(
				'name'       => '&nbsp;',
				'id'         => 'page_header_manual_height',
				'class'      => 'page-header-manual-height hidden',
				'type'       => 'slider',
				'suffix'     => 'px',
				'std'        => 800,
				'js_options' => array(
					'min'  => 100,
					'max'  => 2400,
					'step' => 1,
				),
			),
			array(
				'name'    => esc_html__( 'Content Position', 'konte' ),
				'id'      => 'split_content_position',
				'class'   => 'split-content-field split-content-position hidden',
				'type'    => 'radio',
				'std'     => 'right',
				'options' => array(
					'left'  => esc_attr__( 'Left', 'konte' ),
					'right' => esc_attr__( 'Right', 'konte' ),
				),
			),
			array(
				'name'    => esc_html__( 'Page Header Content', 'konte' ),
				'id'      => 'page_featured_content',
				'class'   => 'page-featured-content split-content-field',
				'type'    => 'select',
				'options' => array(
					''       => esc_attr__( 'Featured image', 'konte' ),
					'video'  => esc_attr__( 'Video', 'konte' ),
					'map'    => esc_attr__( 'Google Map', 'konte' ),
					'hidden' => esc_attr__( 'Do not show', 'konte' ),
				),
			),
			array(
				'name'       => '&nbsp;',
				'desc'       => esc_html__( 'Youtube video URL', 'konte' ),
				'id'         => 'page_featured_video',
				'type'       => 'text',
				'class'      => 'featured-video-field split-content-field hidden',
				'attributes' => array( 'class' => 'widefat' ),
			),
			array(
				'name'  => '&nbsp;',
				'desc'  => esc_html__( 'Mute the video', 'konte' ),
				'id'    => 'page_featured_video_mute',
				'type'  => 'checkbox',
				'class' => 'featured-video-field split-content-field hidden',
				'std'   => 1,
			),
			array(
				'name'       => '&nbsp;',
				'desc'       => esc_html__( 'Address', 'konte' ),
				'id'         => 'page_featured_map_address',
				'type'       => 'text',
				'class'      => 'featured-map-field split-content-field hidden',
				'attributes' => array( 'class' => 'widefat' ),
			),
			array(
				'name'        => '&nbsp;',
				'desc'        => esc_html__( 'Coordinates', 'konte' ),
				'placeholder' => esc_attr__( 'Latitude, Longitude', 'konte' ),
				'id'          => 'page_featured_map_coordinates',
				'type'        => 'text',
				'class'       => 'featured-map-field split-content-field hidden',
			),
			array(
				'name'       => '&nbsp;',
				'desc'       => esc_html__( 'Zoom', 'konte' ),
				'id'         => 'page_featured_map_zoom',
				'type'       => 'slider',
				'class'      => 'featured-map-field split-content-field hidden',
				'js_options' => array(
					'min' => 1,
					'max' => 20,
				),
				'std' => 12,
			),
			array(
				'name'        => '&nbsp;',
				'desc'        => esc_html__( 'Show the map marker', 'konte' ),
				'id'          => 'page_featured_map_marker',
				'type'        => 'checkbox',
				'class'       => 'featured-map-field split-content-field hidden',
			),
			array(
				'name'  => esc_html__( 'Footer', 'konte' ),
				'id'    => 'heading_site_header',
				'class' => 'footer-heading',
				'type'  => 'heading',
			),
			array(
				'name'    => esc_html__( 'Footer Background', 'konte' ),
				'id'      => 'footer_background',
				'type'    => 'select',
				'options' => array(
					''            => esc_html__( 'Default', 'konte' ),
					'dark'        => esc_html__( 'Dark', 'konte' ),
					'light'       => esc_html__( 'Light', 'konte' ),
					'transparent' => esc_html__( 'Transparent', 'konte' ),
					'custom'      => esc_html__( 'Custom', 'konte' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'footer_background_color',
				'class' => 'footer-background-color hidden',
				'type'  => 'color',
			),
			array(
				'name'    => esc_html__( 'Footer Text Color', 'konte' ),
				'id'      => 'footer_textcolor',
				'class'   => 'footer-text-color hidden',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'konte' ),
					'dark'  => esc_html__( 'Dark', 'konte' ),
					'light' => esc_html__( 'Light', 'konte' ),
				),
			),
			array(
				'name' => esc_html__( 'Layout', 'konte' ),
				'id'   => 'heading_layout',
				'type' => 'heading',
			),
			// array(
			// 	'name' => esc_html__( 'Custom Layout', 'konte' ),
			// 	'id'   => 'custom_layout',
			// 	'type' => 'checkbox',
			// 	'std'  => false,
			// ),
			// array(
			// 	'name'    => esc_html__( 'Layout', 'konte' ),
			// 	'id'      => 'layout',
			// 	'type'    => 'image_select',
			// 	'class'   => 'custom-layout',
			// 	'options' => array(
			// 		'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
			// 		'sidebar-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
			// 		'sidebar-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
			// 	),
			// ),
			array(
				'name'    => esc_html__( 'Content Top Spacing', 'konte' ),
				'id'      => 'top_spacing',
				'class'   => 'top-spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'konte' ),
					'none'   => esc_html__( 'No spacing', 'konte' ),
					'custom' => esc_html__( 'Custom', 'konte' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'top_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '60px',
			),
			array(
				'name'    => esc_html__( 'Content Bottom Spacing', 'konte' ),
				'id'      => 'bottom_spacing',
				'class'   => 'bottom-spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'konte' ),
					'none'   => esc_html__( 'No spacing', 'konte' ),
					'custom' => esc_html__( 'Custom', 'konte' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'bottom_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '60px',
			),
			array(
				'name'    => esc_html__( 'Content Area Container', 'konte' ),
				'id'      => 'content_container_width',
				'class'   => 'content-area-container',
				'type'    => 'select',
				'options' => array(
					''         => esc_html__( 'Default', 'konte' ),
					'standard' => esc_html__( 'Standard', 'konte' ),
					'large'    => esc_html__( 'Large', 'konte' ),
					'wide'     => esc_html__( 'Wide', 'konte' ),
					'wider'    => esc_html__( 'Wider', 'konte' ),
					'full'     => esc_html__( 'Full Width', 'konte' ),
				),
			),
			array(
				'name'  => esc_html__( 'Custom Footer Layout', 'konte' ),
				'desc'  => esc_html__( 'Change the content of site footer', 'konte' ),
				'id'    => 'split_content_custom_footer',
				'class' => 'split-content-field split-content-footer hidden',
				'type'  => 'checkbox',
				'std'   => false,
			),
			array(
				'name'    => '&nbsp;',
				'desc'    => esc_html__( 'Footer Left', 'konte' ),
				'id'      => 'split_content_footer_left',
				'class'   => 'split-content-field split-content-footer split-content-footer-left hidden',
				'type'    => 'select',
				'std'     => 'copyright',
				'options' => konte_footer_items_option(),
			),
			array(
				'name'    => '&nbsp;',
				'desc'    => esc_html__( 'Footer Right', 'konte' ),
				'id'      => 'split_content_footer_right',
				'class'   => 'split-content-field split-content-footer split-content-footer-right hidden',
				'type'    => 'select',
				'std'     => 'menu',
				'options' => konte_footer_items_option(),
			),
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'konte_register_meta_boxes' );

/**
 * Enqueue scripts for meta boxes.
 *
 * @param string $hook Admin page slugs.
 */
function konte_meta_boxes_scripts( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		wp_enqueue_script( 'konte-meta-boxes', get_template_directory_uri() . '/js/admin/meta-boxes.js', array( 'jquery' ), '', true );
	}
}

add_action( 'admin_enqueue_scripts', 'konte_meta_boxes_scripts' );

/**
 * Print inline CSS on admin head for meta boxes.
 *
 * @since 2.1.6
 */
function konte_meta_boxes_inline_css() {
	echo '<style>.rwmb-field.hidden { display: none; }</style>';
}

add_action( 'admin_head', 'konte_meta_boxes_inline_css' );

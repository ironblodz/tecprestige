<?php
/**
 * Add more style to the edtior.
 *
 * @package Konte
 */

/**
 * Add dynamic style to editor.
 *
 * @param  string $mce
 * @return string
 */
function konte_editor_dynamic_styles( $mce ) {
    $styles = konte_block_editor_typography_css();
    $styles = preg_replace( '/\s+/', ' ', $styles );
    $styles = str_replace( '"', '', $styles );

    if ( isset( $mce['content_style'] ) ) {
        $mce['content_style'] .= ' ' . $styles . ' ';
    } else {
        $mce['content_style'] = $styles . ' ';
    }

    return $mce;
}

add_filter( 'tiny_mce_before_init', 'konte_editor_dynamic_styles' );

/**
 * Enqueue editor styles for Gutenberg
 */
function konte_block_editor_styles() {
	wp_enqueue_style( 'konte-block-editor-style', get_theme_file_uri( '/css/style-block-editor.css' ), false, '1.0', 'all' );

	wp_add_inline_style( 'konte-block-editor-style', konte_block_editor_typography_css() );
}

add_action( 'enqueue_block_editor_assets', 'konte_block_editor_styles' );

/**
 * Get typography CSS based on theme options.
 *
 * @return string
 */
function konte_block_editor_typography_css() {
	$settings = array(
		'typo_body' => '.wp-block, .block-editor .editor-styles-wrapper, .mce-content-body',
		'typo_h1'   => '.wp-block h1, .wp-block .h1, .mce-content-body h1, .mce-content-body .h1',
		'typo_h2'   => '.wp-block h2, .wp-block .h2, .mce-content-body h2, .mce-content-body .h2',
		'typo_h3'   => '.wp-block h3, .wp-block .h3, .mce-content-body h3, .mce-content-body .h3',
		'typo_h4'   => '.wp-block h4, .wp-block .h4, .mce-content-body h4, .mce-content-body .h4',
		'typo_h5'   => '.wp-block h5, .wp-block .h5, .mce-content-body h5, .mce-content-body .h5',
		'typo_h6'   => '.wp-block h6, .wp-block .h6, .mce-content-body h6, .mce-content-body .h6',
	);

	return konte_get_typography_css( $settings, true );
}
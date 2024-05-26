jQuery( function( $ ) {
	'use strict';

	var $box = $( '#display-settings' );

	// Toggle header background field
	$( '#header_background' ).on( 'change', function( event ) {
		if ( event.target.value === 'transparent' || event.target.value === 'custom' ) {
			$( '.header-text-color', $box ).removeClass( 'hidden' );
		} else {
			$( '.header-text-color', $box ).addClass( 'hidden' );
		}

		if ( event.target.value === 'custom' ) {
			$( '.header-background-color', $box ).removeClass( 'hidden' );
		} else {
            $( '.header-background-color', $box ).addClass( 'hidden' );
        }

        if ( event.target.value === 'transparent' ) {
            $( '.top-spacing', $box ).addClass( 'hidden' ).next( '.custom-spacing' ).addClass( 'hidden' );
        } else {
            $( '.top-spacing', $box ).removeClass( 'hidden' ).next( '.custom-spacing' ).removeClass( 'hidden' );
            $( '#top_spacing' ).trigger( 'change' );
        }
	} );

	// Toggle footer background field
	$( '#footer_background' ).on( 'change', function( event ) {
		if ( event.target.value === 'custom' ) {
			$( '.footer-background-color', $box ).removeClass( 'hidden' );
			$( '.footer-text-color', $box ).removeClass( 'hidden' );
		} else {
			$( '.footer-background-color', $box ).addClass( 'hidden' );
			$( '.footer-text-color', $box ).addClass( 'hidden' );
		}

		if ( event.target.value === 'transparent' ) {
			$( '.bottom-spacing', $box ).addClass( 'hidden' ).next( '.custom-spacing' ).addClass( 'hidden' );
			$( '.footer-text-color', $box ).removeClass( 'hidden' );
        } else {
			$( '.footer-text-color', $box ).addClass( 'hidden' );
            $( '.bottom-spacing', $box ).removeClass( 'hidden' ).next( '.custom-spacing' ).removeClass( 'hidden' );
            $( '#bottom_spacing' ).trigger( 'change' );
        }
	} );

	// Toggle custom layout field
	$( '#custom_layout' ).on( 'change', function( event ) {
		if ( event.target.checked ) {
			$( '.custom-layout', $box ).removeClass( 'hidden' );
		} else {
			$( '.custom-layout', $box ).addClass( 'hidden' );
		}
	} );

	// Toggle spacing fields
	$( '#top_spacing, #bottom_spacing' ).on( 'change', function( event ) {
		if ( 'custom' === event.target.value ) {
			$( this ).closest( '.rwmb-field' ).next( '.custom-spacing' ).removeClass( 'hidden' );
		} else {
			$( this ).closest( '.rwmb-field' ).next( '.custom-spacing' ).addClass( 'hidden' );
		}
	} );

	// Toggle page title color field.
	$( '#page_title_display' ).on( 'change', function() {
		if ( 'front' == $( this ).val() ) {
			$( this ).closest( '.rwmb-field' ).next( '.page-title-color' ).removeClass( 'hidden' );
		} else {
			$( this ).closest( '.rwmb-field' ).next( '.page-title-color' ).addClass( 'hidden' );
		}
	} );

	// Toggle page header height field.
	$( '#page_header_height' ).on( 'change', function() {
		if ( 'manual' == $( this ).val() ) {
			$( this ).closest( '.rwmb-field' ).next( '.page-header-manual-height' ).removeClass( 'hidden' );
		} else {
			$( this ).closest( '.rwmb-field' ).next( '.page-header-manual-height' ).addClass( 'hidden' );
		}
	} );

	// Toggle featured content fields.
	$( '#page_featured_content' ).on( 'change', function() {
		switch ( $( this ).val() ) {
			case 'video':
				$( this ).closest( '.rwmb-field' ).nextAll( '.split-content-field' ).addClass( 'hidden' ).filter( '.featured-video-field, .split-content-position, .split-content-footer' ).removeClass( 'hidden' );
				break;

			case 'map':
				$( this ).closest( '.rwmb-field' ).nextAll( '.split-content-field' ).addClass( 'hidden' ).filter( '.featured-map-field, .split-content-position, .split-content-footer' ).removeClass( 'hidden' );
				break;

			default:
				$( this ).closest( '.rwmb-field' ).nextAll( '.split-content-field' ).addClass( 'hidden' ).filter( '.split-content-position, .split-content-footer' ).removeClass( 'hidden' );
				break;
		}
	} );

	// Toggle custom footer field.
	$( '#split_content_custom_footer' ).on( 'change', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.rwmb-field' ).nextAll( '.split-content-footer' ).removeClass( 'hidden' );
		} else {
			$( this ).closest( '.rwmb-field' ).nextAll( '.split-content-footer' ).addClass( 'hidden' );
		}
	} );

	// Toggle split content template fields
	$( '#page_template' ).on( 'change', function() {
		var template = $( this ).val();

		handlePageTemplateChanges( template );
	} );

	handlePageTemplateChanges( $( '#page_template' ).val() );

	// If this is blog page/shop page. This works with Gutenberg too.
	if ( ! $( '#page_template' ).length ) {
		$( '#header_background, #footer_background, #page_title_display, #page_header_height, #custom_layout, #top_spacing, #bottom_spacing, #page_featured_content' ).trigger( 'change' );

		$box.find( '.split-content-field' ).addClass( 'hidden' ).filter( '.page-featured-content' ).removeClass( 'hidden' );
	}

	/**
	 * Handle page template changes.
	 *
	 * @param {string} template
	 */
	function handlePageTemplateChanges( template ) {
		if ( 'templates/split.php' === template ) {
			$box.find( '.rwmb-field' ).addClass( 'hidden' );
			$box.find( '.split-content-field, .header-heading, .header-text-color, .footer-heading, .footer-text-color' ).removeClass( 'hidden' );

			$( '#page_featured_content' ).trigger( 'change' );
			$( '#split_content_custom_footer' ).trigger( 'change' );
		} else {
			$box.find( '.rwmb-field' ).removeClass( 'hidden' );
			$box.find( '.split-content-field' ).addClass( 'hidden' ).filter( '.page-featured-content' ).removeClass( 'hidden' );

			$( '#header_background, #footer_background, #page_title_display, #page_header_height, #custom_layout, #top_spacing, #bottom_spacing, #page_featured_content' ).trigger( 'change' );

			$box.find( '.split-content-position, .split-content-footer' ).addClass( 'hidden' );
		}

		if ( 'templates/flex-posts.php' === template ) {
			$( '#flexposts-data' ).show();
		} else {
			$( '#flexposts-data' ).hide();
		}

		if ( ! template || 'default' === template ) {
			$box.find( '.content-area-container' ).removeClass( 'hidden' );
		} else {
			$box.find( '.content-area-container' ).addClass( 'hidden' );
		}
	}

	/**
	 * This section for Gutenberg
	 */
	if ( typeof window.wp.data !== 'undefined' && wp.data.select( 'core/editor' ) ) {
		var currentTemplate = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'template' );

		wp.data.subscribe( function() {
			var template = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'template' );

			if ( currentTemplate !== template ) {
				handlePageTemplateChanges( template );
				currentTemplate = template;
			}
		} );

		wp.domReady( function() {
			handlePageTemplateChanges( currentTemplate );
		} );
	}
} );

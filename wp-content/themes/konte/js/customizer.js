( function( $, api ) {
	'use strict';

	// Open preloader when open the section popup
	api.section( 'preloader', function( section ) {
		section.expanded.bind( function( isExpanded ) {
			api.previewer.send( 'open_preloader_previewer', {expanded: isExpanded} );
		} );
	} );

	// Open popup when open the section popup
	api.section( 'popup', function( section ) {
		section.expanded.bind( function( isExpanded ) {
			api.previewer.send( 'open_popup_previewer', {expanded: isExpanded} );
		} );
	} );

	// Active mobile mode when open Mobile panel.
	api.panel( 'mobile', function( panel ) {
		panel.expanded.bind( function( isExpanded ) {
			if ( isExpanded ) {
				api.previewedDevice.set( 'mobile' );
			} else {
				api.previewedDevice.set( 'desktop' );
			}
		} );
	} );

	// Update header search style when changing header version
	api( 'header_version', function( value ) {
		value.bind( function( to ) {
			if ( 'v1' === to || 'v8' === to || 'v9' === to ) {
				api( 'header_search_style' ).set( 'form' );
			} else {
				api( 'header_search_style' ).set( 'icon' );
			}

			if ( 'v8' === to || 'v9' === to ) {
				api( 'header_main_height' ).set( 90 );
			} else {
                api( 'header_main_height' ).set( 120 );
            }
		} );
	} );
} )( jQuery, wp.customize );
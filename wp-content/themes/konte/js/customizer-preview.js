/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $, api ) {
	'use strict';

	var $popup = $( '#popup-modal' );
	var popupOpened = $popup.hasClass( 'open' );

	// Some handlers need to attach after previewer ready.
	api.bind( 'preview-ready', function() {
		// Toggle the preloader when entering/leaving the section.
		api.preview.bind( 'open_preloader_previewer', function( data ) {
			if ( ! api( 'preloader_enable' ).get() ) {
				return;
			}

			if ( data.expanded ) {
				$( '#preloader' ).fadeIn();
			} else {
				$( '#preloader' ).fadeOut();
			}
		} );

		api.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
			if ( 'preloader_content' === placement.partial.id ) {
				$( '#preloader' ).show();
			}
		} );

		// Toggle the poup when entering/leaving the section.
		api.preview.bind( 'open_popup_previewer', function( data ) {
			if ( ! api( 'popup_enable' ).get() ) {
				return;
			}

			if ( data.expanded ) {
				openPopup();
			} else if ( ! popupOpened ) {
				closePopup();
			}
		} );

	} );

	/** PRELOADER **/

	// Toggle the preloader when enable the option.
	api( 'preloader_enable', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '#preloader' ).fadeIn();
			} else {
				$( '#preloader' ).fadeOut();
			}
		} );
	} );

	api( 'preloader_image', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '#preloader .preloader-icon' ).removeClass( 'spinner' ).html( '<img src="' + to + '">' );
			} else {
				$( '#preloader .preloader-icon' ).addClass( 'spinner' ).empty();
			}
		} );
	} );

	api( 'preloader_url', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '#preloader .preloader-icon' ).removeClass( 'spinner' ).html( '<img src="' + to + '">' );
			} else {
				$( '#preloader .preloader-icon' ).addClass( 'spinner' ).empty();
			}
		} );
	} );

	/** POPUP **/

	/**
	 * Open popup
	 */
	function openPopup() {
		if ( $popup.hasClass( 'open' ) ) {
			return;
		}

		$popup.fadeIn();
		$popup.addClass( 'open' );

		$( document.body ).addClass( 'modal-opened popup-opened' );
	}

	/**
	 * Close popup
	 */
	function closePopup() {
		$popup.removeClass( 'open' ).fadeOut();
		$( document.body ).removeClass( 'modal-opened popup-opened' );
	}

	/**
	 * Change popup image
	 *
	 * @param string image
	 */
	function changePopupImage( image ) {
		if ( image ) {
			if ( '1-column' === api( 'popup_layout' ).get() ) {
				$( '.popup-image', $popup ).html( '<div class="popup-image-holder" style="background-image: url(' + image + ')"></div>' );
			} else {
				$( '.popup-image', $popup ).html( '<img src="' + image + '">' );
			}
		} else {
			$( '.popup-image', $popup ).empty();
		}
	}

	// Toggle the popup when enable the option.
	api( 'popup_enable', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				openPopup();
			} else {
				closePopup();
			}
		} );
	} );

	api( 'popup_layout', function( value ) {
		value.bind( function( to ) {
			$popup.removeClass( 'popup-layout-1-column popup-layout-2-columns' ).addClass( 'popup-layout-' + to );

			changePopupImage( api( 'popup_image' ).get() );
		} );
	} );

	api( 'popup_image', function( value ) {
		value.bind( function( to ) {
			changePopupImage( to );
		} );
	} );

	/** COLOR SCHEME */

	api( 'color_scheme', function( value ) {
		value.bind( function( to ) {
			changeColorSchemeCSS( to );
		} );
	} );

	api( 'color_scheme_color', function( value ) {
		value.bind( function( to ) {
			changeColorSchemeCSS( to );
		} );
	} );

	api( 'color_scheme_custom', function ( value ) {
		value.bind( function( to ) {
			var color = '';

			if ( to ) {
				color = api( 'color_scheme_color' ).get();
			} else {
				color = api( 'color_scheme' ).get();
			}

			changeColorSchemeCSS( color );
		} );
	} );

	/**
	 * Change theme colors
	 *
	 * @param string newColor
	 */
	function changeColorSchemeCSS( newColor ) {
		var style = $( '#custom-theme-colors' ),
			color = style.data( 'color' ),
			css = style.html();

		// Equivalent to css.replaceAll
		css = css.split( color ).join( newColor );
		style.html( css ).data( 'color', newColor );
	}

	/** HEADER */
	// Header version change.
	api( 'header_version', function( value ) {
		value.bind( function( to ) {
			var $header = $( '#masthead' );

			if ( 'v10' === to ) {
				$( document.body ).addClass( 'header-vertical' );
			} else {
				$( document.body ).removeClass( 'header-vertical' );
			}

			for ( var i = 1; i <= 10; i++ ) {
				$header.removeClass( 'header-v' + i );
			}

			$header.addClass( 'header-' + to );
		} );
	} );

	api( 'header_present', function ( value ) {
		value.bind( function( to ) {
			if ( 'prebuild' === to ) {
				$( document.body ).removeClass( 'header-vertical' );
			} else if ( 'v10' === api( 'header_version' ).get() ) {
				$( document.body ).addClass( 'header-vertical' );
			}
		});
	} );

	// Header background
	api( 'header_background', function( value ) {
		value.bind( function( to ) {
			var $body = $( document.body ),
				textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'header_text_color' ).get();
					break;
			}


			if ( $body.hasClass( 'blog-hfeed' ) && api( 'header_background_blog_custom' ).get() ) {

			} else if ( $body.hasClass( 'woocommerce' ) && api( 'header_background_shop_custom' ).get() ) {

			} else {
				$( '#masthead' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
			}
		} );
	} );

	// Header background of blog
	api( 'header_background_blog', function( value ) {
		value.bind( function( to ) {
			var textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'header_blog_textcolor' ).get();
					break;
			}

			$( '.blog-hfeed #masthead' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Header background of shop
	api( 'header_background_shop', function( value ) {
		value.bind( function( to ) {
			var textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'header_shop_textcolor' ).get();
					break;
			}

			$( '.woocommerce #masthead' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Header text color.
	api( 'header_text_color', function( value ) {
		value.bind( function( to ) {
			$( '#masthead' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	// Header text color for blog
	api( 'header_blog_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '.blog-hfeed #masthead' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	// Header text color for shop
	api( 'header_shop_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce #masthead' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	api( 'campaign_container', function( value ) {
		value.bind( function( to ) {
			$( '#campaign-bar > div' ).removeClass().addClass( to );
		} );
	} );

	/** FOOTER */
	// Footer background
	api( 'footer_background', function( value ) {
		value.bind( function( to ) {
			var $body = $( document.body ),
				textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'footer_textcolor' ).get();
					break;
			}


			if ( $body.hasClass( 'blog-hfeed' ) && api( 'footer_background_blog_custom' ).get() ) {

			} else if ( $body.hasClass( 'woocommerce' ) && api( 'footer_background_shop_custom' ).get() ) {

			} else {
				$( '#colophon' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
			}
		} );
	} );

	// Footer background of blog
	api( 'footer_background_blog', function( value ) {
		value.bind( function( to ) {
			var textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'footer_blog_textcolor' ).get();
					break;
			}

			$( '.blog-hfeed #colophon' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Footer background of shop
	api( 'footer_background_shop', function( value ) {
		value.bind( function( to ) {
			var textColorClass = '';

			switch ( to ) {
				case 'dark':
					textColorClass = 'text-light';
					break;

				case 'light':
					textColorClass = 'text-dark';
					break;

				default:
					textColorClass = 'text-' + api( 'footer_shop_textcolor' ).get();
					break;
			}

			$( '.woocommerce #colophon' ).removeClass( 'dark light transparent custom text-dark text-light' ).addClass( to ).addClass( textColorClass );
		} );
	} );

	// Footer text color.
	api( 'footer_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '#colophon' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	// Footer text color for blog
	api( 'footer_blog_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '.blog-hfeed #colophon' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	// Footer text color for shop
	api( 'footer_shop_textcolor', function( value ) {
		value.bind( function( to ) {
			$( '.woocommerce #colophon' ).removeClass( 'text-dark text-light' ).addClass( 'text-' + to );
		} );
	} );

	// Mobile cart panel width
	api( 'mobile_shop_cart_panel_width', function( value ) {
		value.bind( function( to ) {
			$( '#cart-panel' ).fadeIn().addClass( 'open' );
		} );
	} );

} )( jQuery, wp.customize );

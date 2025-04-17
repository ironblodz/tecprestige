function KonteCustomCssModule() {
	var self = this;

	self.init = function() {
		elementor.hooks.addFilter( 'editor/style/styleText', self.addCustomCss );

		elementor.settings.page.model.on( 'change', self.addPageCustomCss );

		elementor.on( 'preview:loaded', self.addPageCustomCss );
	};

	self.addPageCustomCss = function() {
		var customCSS = elementor.settings.page.model.get( 'custom_css' );

		if ( customCSS ) {
			customCSS = customCSS.replace( /selector/g, elementor.config.settings.page.cssWrapperSelector );

			elementor.settings.page.getControlsCSS().elements.$stylesheetElement.append( customCSS );
		}
	};

	self.addCustomCss = function( css, view ) {
		if ( ! view || typeof view.getEditModel === 'undefined' ) {
			return css;
		}

		var model = view.getEditModel(),
		    customCSS = model.get( 'settings' ).get( 'custom_css' );

		if ( customCSS ) {
			css += customCSS.replace( /selector/g, '.elementor-element.elementor-element-' + view.model.id );
		}

		return css;
	};

	self.init();
}

function KonteDisplaySettingsModule() {
	var self = this;

	self.init = function() {
		var settings = [
			'split_content_position',
			// 'subtitle',
			'header_layout',
			'header_background',
			'header_background_color',
			'header_textcolor',
			'page_title_display',
			'page_title_textcolor',
			'page_header_height',
			'page_header_height_custom',
			'page_header_content',
			// 'page_header_content_video',
			'page_header_content_video_mute',
			// 'page_header_content_map_address',
			'page_header_content_map_zoom',
			'footer_background',
			'footer_background_color',
			'footer_textcolor',
			'footer_custom_content',
			'footer_custom_content_left',
			'footer_custom_content_right',
			'content_container_width',
			'content_top_spacing',
			'content_top_spacing_custom',
			'content_bottom_spacing',
			'content_bottom_spacing_custom',
		];

		_.each( settings, function( settingName ) {
			var fn = 'on' + settingName.split( '_' ).map( function( word ) {
				return word.charAt(0).toUpperCase() + word.slice( 1 );
			} ).join( '' ) + 'Change';

			if ( self[ fn ] && _.isFunction( self[ fn ] ) ) {
				elementor.settings.page.addChangeCallback( settingName, self[ fn ] );
			}
		} );
	}

	self.reloadOnChange = function() {
		elementor.reloadPreview();
	}

	self.onHeaderBackgroundChange = function( value ) {
		var backgroundClass = value,
			textColorClass = '';

		if ( 'default' === value ) {
			backgroundClass = ElementorConfig.KonteThemeSettings.headerBackground;
		}

		if ( 'dark' === backgroundClass ) {
			textColorClass = 'text-light';
		} else if ( 'light' === backgroundClass ) {
			textColorClass = 'text-dark';
		}

		elementor.$previewContents.find( '#masthead' ).removeClass( 'dark light transparent custom' ).addClass( backgroundClass );

		if ( textColorClass ) {
			elementor.$previewContents.find( '#masthead' ).removeClass( 'text-dark text-light' ).addClass( textColorClass );
		}

		if ( 'transparent' === backgroundClass ) {
			elementor.$previewContents.find( 'body' ).addClass( 'no-top-spacing' );
		}
	}

	self.onHeaderTextcolorChange = function( value ) {
		textColorClass = 'text-' + ElementorConfig.KonteThemeSettings.headerTextColor;
		textColorClass = ( value && 'default' !== value ) ? 'text-' + value : textColorClass;
		elementor.$previewContents.find( '#masthead' ).removeClass( 'text-dark text-light' ).addClass( textColorClass );
	}

	self.onPageHeaderHeightChange = function( value ) {
		if ( 'full' === value ) {
			elementor.$previewContents.find( '.page-header' ).addClass( 'full-height' );
		} else {
			elementor.$previewContents.find( '.page-header' ).removeClass( 'full-height' );
		}
	}

	self.onPageTitleTextcolorChange = function( value ) {
		textColorClass = '';
		textColorClass = ( value && 'default' !== value ) ? 'text-' + value : textColorClass;
		elementor.$previewContents.find( '.page-header.title-front' ).removeClass( 'text-dark text-light' ).addClass( textColorClass );
	}

	self.onFooterBackgroundChange = function( value ) {
		var backgroundClass = value,
			textColorClass = '';

		if ( 'default' === value ) {
			backgroundClass = ElementorConfig.KonteThemeSettings.footerBackground;
		}

		if ( 'dark' === backgroundClass ) {
			textColorClass = 'text-light';
		} else if ( 'light' === backgroundClass ) {
			textColorClass = 'text-dark';
		}

		elementor.$previewContents.find( '#colophon' ).removeClass( 'dark light transparent custom' ).addClass( backgroundClass );

		if ( textColorClass ) {
			elementor.$previewContents.find( '#colophon' ).removeClass( 'text-dark text-light' ).addClass( textColorClass );
		}
	}

	self.onFooterTextcolorChange = function( value ) {
		textColorClass = 'text-' + ElementorConfig.KonteThemeSettings.footerTextColor;
		textColorClass = ( value && 'default' !== value ) ? 'text-' + value : textColorClass;
		elementor.$previewContents.find( '#colophon' ).removeClass( 'text-dark text-light' ).addClass( textColorClass );
	}

	self.onContentContainerWidthChange = function( value ) {
		var containerClasses = {
			standard: 'container',
			large   : 'konte-container',
			wide    : 'konte-container-fluid',
			wider   : 'container-fluid',
			full    : 'konte-container-full',
		};

		var containerClassName = _.findKey( containerClasses, value ) >= 0 ? containerClasses[ value ] : ElementorConfig.KonteThemeSettings.contentContainerClass;

		elementor.$previewContents.find( '.site-content-container' ).removeClass( 'container container-fluid konte-container konte-container-fluid konte-container-full' ).addClass( containerClassName );
	}

	self.onContentTopSpacingChange = function( value ) {
		if ( 'none' === value ) {
			elementor.$previewContents.find( 'body' ).addClass( 'no-top-spacing' );
		} else {
			elementor.$previewContents.find( 'body' ).removeClass( 'no-top-spacing' );
		}
	}

	self.onContentBottomSpacingChange = function( value ) {
		if ( 'none' === value ) {
			elementor.$previewContents.find( 'body' ).addClass( 'no-bottom-spacing' );
		} else {
			elementor.$previewContents.find( 'body' ).removeClass( 'no-bottom-spacing' );
		}
	}

	self.init();
}

jQuery( window ).on( 'elementor:init', function() {
	new KonteDisplaySettingsModule();

	if ( typeof ElementorProConfig === 'undefined' ) {
		new KonteCustomCssModule();
	}
} );
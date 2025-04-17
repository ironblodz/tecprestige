( function( $, _ ) {
	window.konteBannerGrid2 = vc.shortcode_view.extend( {
		elementTemplate: false,
		$wrapper: false,

		events: {
			'click > .wpb_element_wrapper [data-vc-control="add"]': "addElement"
		},

		changeShortcodeParams: function( model ) {
			var params;

			window.konteBannerGrid2.__super__.changeShortcodeParams.call( this, model );

			params = _.extend( {}, model.get( 'params' ) );

			if ( ! this.elementTemplate ) {
				this.elementTemplate = this.$el.find( '.konte_banner-grid-container' ).html();
			}

			if ( ! this.$wrapper ) {
				this.$wrapper = this.$el.find( '.wpb_element_wrapper' );
			}

			if ( _.isObject( params ) ) {
				var template = vc.template( this.elementTemplate, vc.templateOptions.custom );
				this.$wrapper.find( '.konte_banner-grid-container' ).html( template( { params: params } ) );
			}
		}
	} );
} )( window.jQuery, window._ );
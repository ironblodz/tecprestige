jQuery( document ).ready( function( $ ) {
	// Init post field.
	$( '#flex-post__post-data' ).removeClass( 'hidden' );
	$( '#flex-post-post' ).select2( {
		ajax: {
			url: ajaxurl,
			dataType: 'json',
			data: function( params ) {
				return {
					term: params.term,
					action: 'flex_post_search_posts'
				};
			},
			processResults: function (data, params) {
				return {
					results: data
				};
			},
			cache: true,
			delay: 250
			// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
		}
	} );

	// Color picker for tag color.
	$( '#flex-post-tag-color' ).wpColorPicker();

	// Image upload.
	var file_frame;

	$( document.body ).on( 'click', '.upload-background-button', function ( event ) {
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media( {
			multiple: false
		} );

		// When an image is selected, run a callback.
		file_frame.on( 'select', function () {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();

			$( '#flex-post-background-image' ).val( attachment.url ).trigger( 'change' );
		} );

		// Finally, open the modal.
		file_frame.open();
	} );

	$( '#flex-post-background-image' ).on( 'change', function() {
		var url = $( this ).val();

		if ( ! url ) {
			$( '.flex-post-setting__design-background .flex-post-setting__preview' ).find( 'img' ).addClass( 'hidden' );
		} else {
			$( '.flex-post-setting__design-background .flex-post-setting__preview' ).find( 'img' ).attr( 'src', url ).removeClass( 'hidden' );
		}
	} );

	// CSS editor.
	if ( typeof wp !== undefined && typeof _ !== undefined ) {
		var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};

		editorSettings.codemirror = _.extend(
			{},
			editorSettings.codemirror,
			{
				indentUnit: 4,
				tabSize: 4,
				mode: 'css',
				lint: false
			}
		);

		var editor = wp.codeEditor.initialize( $( '#flex-post-custom-css' ), editorSettings );
		editor.codemirror.setSize( null, 120 );
		console.log(editor);
	}

	// Toggle post data group and the editor.
	toggleEditor();
	$( 'input[name=flex_content_type]' ).on( 'change', toggleEditor );

	/**
	 * Handle content type change.
	 */
	function toggleEditor() {
		var type = $( 'input[name=flex_content_type]:checked' ).val();

		$( '.flex-post-data-group' ).addClass( 'hidden' );
		$( '#flex-post__' + type + '-data' ).removeClass( 'hidden' );
		$( '#flex-post__design-data' ).removeClass( 'hidden' );

		if ( ! type ) {
			$( '#post-body-content' ).removeClass( 'editor-disabled' );
		} else {
			$( '#post-body-content' ).addClass( 'editor-disabled' );
		}

		$( window ).trigger( 'resize' );
	}
} );
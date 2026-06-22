( function( $ ) {
	
	/**
	 * Handles logic for the user templates admin add new interface.
	 *
	 * @class FLBuilderUserTemplatesAdminAdd
	 * @since 1.10
	 */
	FLBuilderUserTemplatesAdminAdd = {
		
		/**
		 * Initializes the user templates admin add new interface.
		 *
		 * @since 1.10
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._bind();
		},

		/**
		 * Binds events for the Add New form.
		 *
		 * @since 1.10
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( 'select.fl-template-type' ).on( 'change', $.proxy( this._templateTypeChange, this ) );
			$( '#fl-template-content-type' ).on( 'change', $.proxy( this._templateContentTypeChange, this ) );
			$( 'form.fl-new-template-form .dashicons-editor-help' ).tipTip();
			$( 'form.fl-new-template-form' ).validate();
			
			this._templateTypeChange();

		},

		/**
		 * Callback for when the template type select changes.
		 *
		 * @since 1.10
		 * @access private
		 * @method _templateTypeChange
		 */
		_templateTypeChange: function(e)
		{
			const templateType   = $( 'select.fl-template-type' ).val();
			const moduleRow      = $( '#fl-template-module-row' );
			const contentTypeRow = $( '#fl-template-content-type-row' );
			const add            = $( '.fl-template-add' );
			const globalRow      = $( '#fl-template-global-row' );
			
			if ( '' == templateType ) {
				add.val( FLBuilderConfig.strings.addButton.add );
			}
			else {
				add.val( FLBuilderConfig.strings.addButton[ templateType ] );
			}

			if ( templateType === 'row' || templateType === 'module' ) {
			
				contentTypeRow.show();
				this._templateContentTypeChange();
				
				if ( templateType === 'module' ) {
					moduleRow.show();
				} else {
					moduleRow.hide();
				}

			} else {
				contentTypeRow.hide();
			}

		},

		/**
		 * Handler for when the template content type select changes.
		 *
		 * @since 2.10
		 * @access private
		 * @method _templateContentTypeChange
		 */
		_templateContentTypeChange: function(e)
		{
			const templateContentType = $( '#fl-template-content-type' ).val();
			const globalRow           = $( '.fl-template-global-row' );

			if ( templateContentType === 'template' ) {
				globalRow.show();
			} else {
				globalRow.hide();
			}

		},

	};
	
	// Initialize
	$( function() { FLBuilderUserTemplatesAdminAdd._init(); } );

} )( jQuery );
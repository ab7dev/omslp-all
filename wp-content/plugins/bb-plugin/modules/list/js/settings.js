(function($){
    
    /**
     * Determine whether or not to override the icon in the List Item.
     * Only Generic List type are allowed to override the icon. 
     * Ordered and unordered lists can only use the predefined icons available in the module settings.
     */
    FLBuilder.registerModuleHelper('list_item_form', {
        
        init: function () {
            var moduleSettingsForm = $('form.fl-builder-list-settings'),
                listType = moduleSettingsForm.find('select[name=list_type]').val();

            if ('ol' == listType || 'ul' == listType) {
                $('#fl-builder-settings-section-list_item_icon_section').hide();
            } else {
                $('#fl-builder-settings-section-list_item_icon_section').show();
            }
        }
    });

    FLBuilder.registerModuleHelper('list', {

        init: function () {
            var form = $( '.fl-builder-settings:visible' ),
                layoutToggle = form.find( 'select[name=list_layout]' );

            layoutToggle.on( 'change', this._layoutToggle );
            this._layoutToggle();
        },

        _layoutToggle: function() {

            var form = $( '.fl-builder-settings:visible' ),
                listLayout = form.find('select[name=list_layout]'),
                iconPlacement = form.find('select[name=list_icon_placement]'),
                firstOption = iconPlacement.find('option:eq(0)'),
                thirdOption = iconPlacement.find('option:eq(2)'),
                layout = listLayout.val();

            if ( layout === 'basic' ) {
                firstOption.hide();
                thirdOption.hide();
            } else {
                firstOption.show();
                thirdOption.show();
            }
        },
    });

})(jQuery);
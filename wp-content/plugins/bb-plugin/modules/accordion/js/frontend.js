/* eslint-disable no-undef */
(function($) {

	FLBuilderAccordion = function( settings )
	{
		this.settings = settings;
		this._init();
	};

	FLBuilderAccordion.prototype = {

		settings: {},

		_init: function()
		{
			const node = '.fl-node-' + this.settings.id;
			const buttons = $( node + ' .fl-accordion-button' );
			const contents = $( node + ' .fl-accordion-content' );
			// Prevent event handlers from being attached multiple times when the accordion is nested
			if ( $( node ).closest( '.fl-accordion' ).length ) return;
			buttons.on( 'click keydown', $.proxy( this._buttonClick, this ) );
			contents.on( 'keydown', $.proxy( this._contentKeys, this ) );
			buttons.on( 'focus', $.proxy( this._buttonFocus, this ) );
			if ( 'undefined' !== typeof FLBuilderLayout ) FLBuilderLayout.preloadAudio( node + ' .fl-accordion-content' );
			if ( contents.first().closest( '.fl-accordion-item' ).hasClass( 'fl-accordion-item-active' ) ) contents.first().show();
		},

		_contentKeys: function( event )
		{
			const item   = $( event.target ).closest( '.fl-accordion-item' );
			const active = item.hasClass( 'fl-accordion-item-active' );
			const typing = $( event.target ).is( 'input, textarea, select' ) || event.target.isContentEditable;
			if ( event.key === 'Escape' && active ) {
				// Only toggle the accordion if the escape key was pressed and the item is active
				this._toggleAccordion( item.find( '.fl-accordion-button' ) );
			} else if ( event.key === ' ' && ! typing ) {
				// Prevent the space key from scrolling the page when focus is on the content and not on a form field
				event.preventDefault();
			}
		},

		_buttonFocus: function( event ) {
			if ( ! event.relatedTarget || ! this.settings.expandOnTab ) return;
			// Only toggle the accordion if the focus was triggered via keyboard navigation
			if ( ! event.target.matches( ':focus-visible' ) ) return;
			const button = $( event.target ).closest( '.fl-accordion-button' );
			this._toggleAccordion( button );
		},

		_buttonClick: function( event )
		{
			// Check keyboard keys and ignore the rest
			if ( event.type === 'keydown' && ! [ ' ', 'Enter', 'Escape' ].includes( event.key ) ) return;
			// Only allow left click for mouse input or simulated clicks
			if ( event.type === 'click' && event.button !== 0 && event.button !== undefined ) return;
			// Prevent the space & enter keys from retoggling the button by not triggering a click event
			if ( [ ' ', 'Enter' ].includes( event.key ) && $( event.target ).hasClass( 'fl-accordion-button' ) ) event.preventDefault();
			const item   = $( event.target ).closest( '.fl-accordion-item' );
			const active = item.hasClass( 'fl-accordion-item-active' );
			const button = item.find( '.fl-accordion-button' ).first();
			const parent = item.parent().closest( '.fl-accordion-item' ).find( '.fl-accordion-button' ).first();
			// Do not toggle the accordion if the escape key is pressed and the item is not active or nested
			if ( event.key === 'Escape' && ! active && ! parent.length ) return;
			const outer = event.key === 'Escape' && parent.length && ! active;
			this._toggleAccordion( outer ? parent : button );
		},

		_toggleAccordion: function( button ) {
			const accordion = button.closest( '.fl-accordion' );
			if ( accordion.hasClass( 'fl-accordion-collapse' ) ) {
				// collapse all accordion items if the accordion is set to collapse when a new item is opened
				this._collapseAccordion( accordion );
			}
			const item = button.closest( '.fl-accordion-item' );
			const buttons = item.find( '.fl-accordion-button' );
			const contents = item.find( '.fl-accordion-content' );
			const icon = item.find( 'i.fl-accordion-button-icon' ).first();
			const hidden = contents.first().is( ':hidden' );
			if ( hidden ) {
				// opens only the current accordion item and does not affect nested accordions
				button.attr( 'aria-expanded', 'true' );
				contents.first().attr( 'aria-hidden', 'false' ).slideDown( 'normal', this._slideDownComplete );
			}
			else {
				// collapse all nested accordions within the current item
				buttons.attr( 'aria-expanded', 'false' );
				contents.attr( 'aria-hidden', 'true' ).slideUp( 'normal', this._slideUpComplete );
			}
			item.toggleClass( 'fl-accordion-item-active', hidden );
			this._toggleIcon( icon, hidden );
			if ( ! button.is( ':focus' ) ) {
				button.trigger( 'focus' );
			}
		},

		_collapseAccordion: function( accordion ) {
			const contents = accordion.find( '.fl-accordion-content' );
			const buttons = accordion.find( '.fl-accordion-button' );
			const icons = accordion.find( '.fl-accordion-button i.fl-accordion-button-icon' );
			accordion.find( '.fl-accordion-item-active' ).removeClass( 'fl-accordion-item-active' );
			contents.attr( 'aria-hidden', 'true' ).slideUp( 'normal' );
			buttons.attr( 'aria-expanded', 'false' );
			this._toggleIcon( icons, false );
		},

		_toggleIcon: function( icon, opened ) {
			const self = this;
			const text = opened ? this.settings.collapseTxt : this.settings.expandTxt;
			icon.each( function() {
				const $icon = $( this );
				const svg = $icon.find( 'svg' );
				if ( svg.length ) {
					const name = opened ? 'minus' : 'plus';
					svg.attr( 'data-icon', name );
				} else {
					const labelIcon  = $icon.data( 'label-icon' )  || self.settings.labelIcon;
					const activeIcon = $icon.data( 'active-icon' ) || self.settings.activeIcon;
					const classes = `${labelIcon} ${activeIcon}`;
					const status  = opened ? activeIcon : labelIcon;
					$icon.removeClass( classes ).addClass( status );
				}
				$icon.find( 'span' ).text( text );
			} );
		},

		_slideUpComplete: function()
		{
			$( this ).closest( '.fl-accordion' ).trigger( 'fl-builder.fl-accordion-toggle-complete' );
		},

		_slideDownComplete: function()
		{
			const content = $( this );
			const	item = content.parent();
			if ( 'undefined' !== typeof FLBuilderLayout ) {
				FLBuilderLayout.refreshGalleries( content );
				// Grid layout support (uses Masonry)
				FLBuilderLayout.refreshGridLayout( content );
				// Post Carousel support (uses BxSlider)
				FLBuilderLayout.reloadSlider( content );
				// WP audio shortcode support
				FLBuilderLayout.resizeAudio( content );
				// Reload Google Map embed.
				FLBuilderLayout.reloadGoogleMap( content );
				// Slideshow module support.
				FLBuilderLayout.resizeSlideshow();
			}
			if ( item.offset().top < $( window ).scrollTop() + 100 ) {
				$( 'html, body' ).animate( { scrollTop: item.offset().top - 100 }, 500, 'swing' );
			}
			content.closest( '.fl-accordion' ).trigger( 'fl-builder.fl-accordion-toggle-complete' );
		}

	};

})(jQuery);

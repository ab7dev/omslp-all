/* eslint-disable no-undef */
(function($) {

	FLBuilderAccordion = function( settings )
	{
		this.settings 	 = settings;
		this.nodeClass   = '.fl-node-' + settings.id;
		this.expandOnTab = settings.expandOnTab;
		this._init();
	};

	FLBuilderAccordion.prototype = {

		settings	  : {},
		nodeClass   : '',
		expandOnTab : false,

		_init: function()
		{
			$( this.nodeClass + ' .fl-accordion-button' ).on('click keydown', $.proxy( this._buttonClick, this ) );
			$( this.nodeClass + ' .fl-accordion-content' ).on('keydown', $.proxy( this._contentKeys, this ) );
			$( this.nodeClass + ' .fl-accordion-button' ).on('focusin', $.proxy( this._focusIn, this ) );

			if ( 'undefined' !== typeof FLBuilderLayout ) {
				FLBuilderLayout.preloadAudio( this.nodeClass + ' .fl-accordion-content' );
			}

			this._openActiveAccordion();
		},

		_openActiveAccordion: function () {
			var activeAccordion = $( this.nodeClass + ' .fl-accordion-item.fl-accordion-item-active' );

			if ( activeAccordion.length > 0 ) {
				activeAccordion.find('.fl-accordion-content:first').show();
			}
		},

		_contentKeys: function( e )
		{
			const item   = $( e.target ).closest( '.fl-accordion-item' );
			const active = item.hasClass( 'fl-accordion-item-active' );
			const typing = $( e.target ).is( 'input, textarea, select' ) || e.target.isContentEditable;
			if ( e.key === 'Escape' && active ) {
				// Only toggle the accordion if the escape key was pressed and the item is active
				this._toggleAccordion( item.find( '.fl-accordion-button' ) );
			} else if ( e.key === ' ' && ! typing ) {
				// Prevent the space key from scrolling the page when focus is on the content and not on a form field
				e.preventDefault();
			}
		},

		_focusIn: function( e ) {
			if ( ! e.relatedTarget || ! this.expandOnTab ) return;
			// Only toggle the accordion if the focus was triggered via keyboard navigation
			if ( ! e.target.matches( ':focus-visible' ) ) return;
			const button = $( e.target ).closest( '.fl-accordion-button' );
			this._toggleAccordion( button );
		},

		_buttonClick: function( e )
		{
			const item   = $( e.currentTarget ).closest( '.fl-accordion-item' );
			const active = item.hasClass( 'fl-accordion-item-active' );
			const button = item.find( '.fl-accordion-button' );
			const target = 'fl-node-' + item.closest( '.fl-module-accordion' ).data( 'node' );
			const	node   = this.nodeClass.replace( '.', '' );
			// Check keyboard keys and ignore the rest
			if( e.type === 'keydown' && ! [ ' ', 'Enter', 'Escape' ].includes( e.key ) ) return;
			// Only allow left click for mouse input or simulated clicks
			if ( e.type === 'click' && e.button !== 0 && e.button !== undefined ) return;
			// Prevent event handler being called twice when Accordion is nested
			if ( node !== target ) return;
			// Do not toggle the accordion if the escape key is pressed and the item is not active
			if ( e.key === 'Escape' && ! active ) return;
			// Prevent the space & enter keys from retoggling the button by not triggering a click event
			if ( [ ' ', 'Enter' ].includes( e.key ) && $( e.target ).hasClass( 'fl-accordion-button-icon' ) ) e.preventDefault();
			this._toggleAccordion( button );
		},

		_toggleAccordion: function( button ) {
			var accordion = button.closest('.fl-accordion'),
				item	      = button.closest('.fl-accordion-item'),
				allContent  = accordion.find('.fl-accordion-content'),
				allIcons    = accordion.find('.fl-accordion-button i.fl-accordion-button-icon'),
				content     = button.siblings('.fl-accordion-content'),
				icon        = button.find('i.fl-accordion-button-icon');

			if(accordion.hasClass('fl-accordion-collapse')) {
				accordion.find( '.fl-accordion-item-active' ).removeClass( 'fl-accordion-item-active' );
				accordion.find( '.fl-accordion-button-icon:not(i)' ).attr('aria-expanded', 'false');
				accordion.find( '.fl-accordion-content' ).attr('aria-hidden', 'true');
				allContent.slideUp('normal');

				if( allIcons.find('svg').length > 0 ) {
					allIcons.find('svg').attr("data-icon",'plus');
				} else {
					allIcons.removeClass( this.settings.activeIcon );
					allIcons.addClass( this.settings.labelIcon );
				}
			}

			if ( ! item.find( '.fl-accordion-button-icon:not(i)' ).is( ':focus' ) ) {
				item.find( '.fl-accordion-button-icon:not(i)' ).trigger( 'focus' );
			}

			if(content.is(':hidden')) {
				item.find( '.fl-accordion-button-icon:not(i)' ).attr('aria-expanded', 'true');
				item.find( '.fl-accordion-content' ).attr('aria-hidden', 'false');
				item.addClass( 'fl-accordion-item-active' );
				content.slideDown('normal', this._slideDownComplete);

				if( icon.find('svg').length > 0 ) {
					icon.find('svg').attr("data-icon",'minus');
				} else {
					icon.removeClass( this.settings.labelIcon );
					icon.addClass( this.settings.activeIcon );
				}
				icon.parent().find('span').text( this.settings.collapseTxt );
				icon.find('span').text( this.settings.collapseTxt );
			}
			else {
				item.find( '.fl-accordion-button-icon:not(i)' ).attr('aria-expanded', 'false');
				item.find( '.fl-accordion-content' ).attr('aria-hidden', 'true');
				item.removeClass( 'fl-accordion-item-active' );
				content.slideUp('normal', this._slideUpComplete);

				if( icon.find('svg').length > 0 ) {
					icon.find('svg').attr("data-icon",'plus');
				} else {
					icon.removeClass( this.settings.activeIcon );
					icon.addClass( this.settings.labelIcon );
				}
				icon.parent().find('span').text( this.settings.expandTxt );
				icon.find('span').text( this.settings.expandTxt );
			}
		},

		_slideUpComplete: function()
		{
			var content = $( this ),
				accordion = content.closest( '.fl-accordion' );

			accordion.trigger( 'fl-builder.fl-accordion-toggle-complete' );
		},

		_slideDownComplete: function()
		{
			var content = $( this ),
				accordion = content.closest( '.fl-accordion' ),
				item 		  = content.parent(),
				win  		  = $( window );

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

			if ( item.offset().top < win.scrollTop() + 100 ) {
				$( 'html, body' ).animate({
					scrollTop: item.offset().top - 100
				}, 500, 'swing');
			}

			accordion.trigger( 'fl-builder.fl-accordion-toggle-complete' );
		}

	};

})(jQuery);

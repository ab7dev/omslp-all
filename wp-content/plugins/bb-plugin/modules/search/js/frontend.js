/* eslint-disable no-undef */
(function($) {

	FLBuilderSearchForm = function(settings) {
		this.settings   = settings;
		this.nodeClass  = '.fl-node-' + settings.id;
		this.searchForm = $(this.nodeClass + ' .fl-search-form');
		this.form       = this.searchForm.find('form');
		this.input      = this.form.find('input[type=search]');
		this.button     = this.searchForm.find('.fl-button:is(a, button:not([type="submit"]), .fl-button:is(a, button:not([type="submit"]) *');
		this.resultsEl  = $(this.nodeClass + ' .fl-search-results-content');

		this._init();
	};

	FLBuilderSearchForm.prototype = {

		settings: {},
		nodeClass: '',
		searchForm: '',
		form: null,
		input: null,
		button: null,
		resultsEl: '',
		searching: false,
		prevSearchData: {},
		request: null,

		_init: function() {
			this._bindEvents();
			this._popupSearch();
		},

		_bindEvents: function(){
			var $this        = this,
				keyCode      = null,
				keyType      = null,
			    enterPressed = false,
				t, et;

			this.button.on('click', $.proxy(this._buttonClick, this));
			this.input.on('input', (event) => {
				const input = event.target;
				if ( input.checkValidity() ) {
					input.removeAttribute('aria-invalid');
				} else {
					input.setAttribute('aria-invalid', true);
				}
			});

			if ( this.searchForm.hasClass('fl-search-button-expand') ) {
				this.input.attr({'id':  'input-' + this.settings.id, 'tabindex': -1});
				this.button.closest('.fl-button').attr({'aria-controls': 'input-' + this.settings.id, 'aria-expanded': false});
			}
			else if ( this.searchForm.hasClass('fl-search-button-reveal') ) {
				const form = this.searchForm.find('.fl-search-form-input-wrap');
				form.attr('id',  'form-' + this.settings.id);
				this.button.closest('.fl-button').attr({'aria-controls': 'form-' + this.settings.id, 'aria-expanded': false, 'aria-haspopup': true});
				form.on('focusout', (event) => {
					if (form.is(':visible') && form.has($(event.relatedTarget)).length === 0) {
						this._buttonClick(event);
					}
				});
				this.searchForm.on('keydown', (event) => {
					if (event.key === 'Escape' && form.is(':visible')) {
						this.button.focus();
					}
				});
			}

			if ( 'ajax' == this.settings.result ) {
				$(document).on('click touchend', function(e){
					if( $(e.target).is('input') ) return;

					$this._hideResults();
				} );

				$this.resultsEl.bind("click touchend", function (e) {
	                e.stopImmediatePropagation();
	            });

				// Disable form submit.
				$this.form.on( 'submit', function (e) {
                    e.preventDefault();
	            });

				// Prevent auto-clearing errors in the FLBuilderLayout helper class.
				this.input.off('focus');

				this.input.on('keyup', function(e) {
                    if (window.event) {
						keyCode = window.event.keyCode;
                        keyType = window.event.type;
                    } else if (e) {
						keyCode = e.which;
                        keyType = e.type;
                    }

					// Prevent rapid enter
	                if ( 13 == keyCode ) {
	                    clearTimeout(et);
	                    et = setTimeout(function(){
	                        enterPressed = false;
	                    }, 300);
	                    if ( enterPressed ) {
					        return false;
	                    } else {
	                        enterPressed = true;
	                    }
	                }

					if ( $this.input.val().length >= 3 && 'keyup' == keyType && 13 == keyCode ) {
						$this._search(e);
						return false;
					}
                });

				this.input.on('click input', function(e) {
					if (window.event) {
						keyCode = window.event.keyCode;
                        keyType = window.event.type;
                    } else if (e) {
                        keyCode = e.which;
                        keyType = e.type;
                    }

					// F1 to F12
					if ( (keyCode >= 37 && keyCode <= 40) || (keyCode >= 112 && keyCode <= 123) ) {
						return;
					}

					if ($this.input.val().length < 3) {
	                    $this._hideLoader();
	                    $this._hideResults();
	                    $this.input.attr('aria-invalid', true);
	                    if ($this.post != null) $this.post.abort();
	                    clearTimeout(t);
	                    return;
	                }

	                if ( 'click' == keyType || keyCode == 32 ) {
						if ( $this.resultsEl.html().length != 0 ) {
							clearTimeout(t);
							if( $this.resultsEl.hasClass('fl-search-open') ) return;
							$this._showResults();
						}
						else {
							$this._hideResults();
						}
						return;
	                }

	                if ($this.request != null) $this.request.abort();
	                $this._hideLoader();

					clearTimeout(t);
					t = setTimeout(function() {
						$this._search(e);
					}, 100);
				});
			}
		},

		_search: function(e) {
			if ($.trim(this.input.val()).length < 1 || ('ajax' == this.settings.result && this.input.val().length < 3)) {
				this.input.attr('aria-invalid', true);
				return;
			}
			e.preventDefault();

			if ( 'ajax' == this.settings.result ) {
				this._doAjaxSearch();
			}
			else {
				this.form.submit();
				// TODO: _doRedirectResults()
			}

			return false;
		},

		_doAjaxSearch: function() {
			var searchText     = this.input.val(),
				postId         = this.searchForm.closest( '.fl-builder-content' ).data( 'post-id' ),
				templateId     = this.searchForm.data( 'template-id' ),
				templateNodeId = this.searchForm.data( 'template-node-id' ),
				ajaxData       = {},
				self           = this;

			if ( searchText.length < 1 ) return;

			this.searching = true;

			// Show loader
			this._showLoader();

			ajaxData = {
				action           : 'fl_search_query',
				keyword          : searchText,
				post_id          : postId,
				template_id      : templateId,
				template_node_id : templateNodeId,
				node_id          : this.settings.id,
				nonce            : this.settings.nonce,
			}

			// Check to see if searching the same keywords.
			if ( JSON.stringify(ajaxData) === JSON.stringify(this.prevSearchData) ) {
				if ( ! this.resultsEl.hasClass('fl-search-open') ) {
					this._showResults();
				}
                this._hideLoader();
                return false;
            }

			// Send server request.
			this.request = $.post( FLBuilderLayoutConfig.paths.wpAjaxUrl, ajaxData, function(response){
				self._hideLoader();

				self.resultsEl.html("");
				self.resultsEl.html(response);
				self._showResults();

				self.prevSearchData = ajaxData;
			});
		},

		_popupSearch: function() {
			var inputWrap = this.searchForm.find('.fl-search-form-input-wrap'),
				$this     = this;

			if ('button' !== this.settings.layout || ( 'fullscreen' !== this.settings.btnAction && 'reveal' !== this.settings.btnAction )) {
				return;
			}

			this.button.off('click');
			this.button.magnificPopup({
				type: 'inline',
				mainClass: 'fl-node-' + this.settings.id,
				items: {
					src: inputWrap[0],
				},
				alignTop: true,
				showCloseBtn: $this.settings.showCloseBtn,
				closeBtnInside: false,
				enableEscapeKey: true,
				closeOnBgClick: false,
				focus: 'input[type=search]',
				tLoading: '<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>',
				callbacks: {
					open: function(){
						$this.input.trigger('click');
					}
				}
			});
			this.resultsEl.appendTo( inputWrap );
		},

		_buttonClick: function(e) {
			e.stopImmediatePropagation();
			if (this.searchForm.hasClass('fl-search-button-expand')) {
				this.searchForm.find('.fl-search-form-wrap').toggleClass('fl-search-expanded');

				if (this.searchForm.find('.fl-search-form-wrap').hasClass('fl-search-expanded')) {
					this.button.closest('.fl-button').attr('aria-expanded', true);
					this.input.attr('tabindex', 0);
					this.input.focus();
				}
				else {
					this._hideResults();
					this.input.attr('tabindex', -1);
					this.button.closest('.fl-button').attr('aria-expanded', false);
				}

				return false;
			} else if (this.searchForm.hasClass('fl-search-button-reveal') && $(e.target).closest('.fl-button:not([type="submit"])')) {
				const form = this.searchForm.find('.fl-search-form-input-wrap');
				if (e.detail && e.type === 'click' && form.is(':visible')) return false;
				form.fadeToggle('fast', () => {
					this.button.attr('aria-expanded', form.is(':visible'));
					if (form.is(':visible')) {
						this.input.focus();
					}
				});

				return false;
			} else {
				this._search(e);
			}
		},

		_showResults: function(){
			// Close any search results in a page.
			this._hideResults();
			this.resultsEl.addClass('fl-search-open');

			if ('button' == this.settings.layout && 'expand' == this.settings.btnAction) {
				this.searchForm.find('.fl-search-form-input-wrap').css('overflow', 'visible');
			}
		},

		_hideResults: function(){
			$('.fl-search-results-content').removeClass('fl-search-open');

			if ('button' == this.settings.layout && 'expand' == this.settings.btnAction) {
				this.searchForm.find('.fl-search-form-input-wrap').removeAttr('style');
			}
		},

		_doRedirectResults: function(){
			// TODO
		},

		_showLoader: function(){
			$(this.nodeClass + ' .fl-search-loader-wrap').show();
		},

		_hideLoader: function(){
			this.searching = false;
			$(this.nodeClass + ' .fl-search-loader-wrap').hide();
		},

		_cleanInput: function(s) {
	        return encodeURIComponent(s).replace(/%20/g, '+');
	    }

	}

})(jQuery);

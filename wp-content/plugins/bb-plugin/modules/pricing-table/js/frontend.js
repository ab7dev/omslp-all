var FLBuilderPricingTable;

(function($) {

	/**
	 * Class for Pricing Table Module
	 *
	 * @since 2.5
	 */
	FLBuilderPricingTable = function( settings ){

		// set params
        this.settings = settings;
		this.nodeClass = '.fl-node-' + settings.id;
        this.wrapperClass = this.nodeClass + ' .fl-pricing-table';

		// initialize
		this._initPricingTable();

	};

	FLBuilderPricingTable.prototype = {
		nodeClass               : '',
		wrapperClass            : '',

		_initPricingTable: function(){

			var self = this;

            /* Tooltips */
			// Unbind any previous handlers
			$(this.nodeClass + ' .fl-builder-tooltip').off('.flTooltip');

			$(this.nodeClass + ' .fl-builder-tooltip').each(function(){
				var $tooltip = $( this );
				var trigger  = $tooltip.data('tooltip-trigger') || 'click';
				var $text    = $tooltip.find('.fl-builder-tooltip-text');
				var $icon    = $tooltip.find('.fl-builder-tooltip-icon');

				console.log("tooltip trigger:", trigger);

				$text.hide();

				if ( 'hover' === trigger ) {
					$tooltip.on('mouseenter.flTooltip', function( e ) {
						$text.stop(true, true).show();
					}).on('mouseleave.flTooltip', function( e ) {
						$text.stop(true, true).hide();
					});
					// Prevent clicks from toggling hover-tooltips
					$icon.on('click.flTooltip', function( e ) {
						e.stopPropagation();
					});
				} else {
					// click behavior
					$icon.on('click.flTooltip', function( e ) {
						// hide other open tooltips inside pricing modules
						$('.fl-module-pricing-table .fl-builder-tooltip-text').not( $text ).hide();
						$text.stop(true, true).hide();
						e.stopPropagation();

						if ( $text.is(':visible') ) {
							$text.stop(true,true).hide();
						} else {
							$text.stop(true,true).show();
						}
					});
				}
			});

			// Clicking outside hides any open click-tooltips
			$('body').off('click.flTooltipHide').on('click.flTooltipHide', function(){
				self._hideHelpTooltip();
			});

			// Switch between the two Billing Options.
			$(this.nodeClass + ' .switch-button').on('click', function(e) {
				self._switchPrice(e);
				self._switchButtonUrl(e);
			});
		},

		/**
		 * Shows a help tooltip.
		 *
		 * @since 2.5
		 * @access private
		 * @method _showHelpTooltip
		 */
		_showHelpTooltip: function( e )
		{
			this._hideHelpTooltip();
			$(e.target).closest('.fl-builder-tooltip').find('.fl-builder-tooltip-text').fadeIn();
			e.stopPropagation();
		},

		/**
		 * Hides a help tooltip.
		 *
		 * @since 2.5
		 * @access private
		 * @method _hideHelpTooltip
		 */
		_hideHelpTooltip: function( evt )
		{
			$('.fl-module-pricing-table .fl-builder-tooltip-text').fadeOut();
		},

		/**
		 * Toggle between the two Billing Options.
		 * 
		 * @since 2.5
		 * @access private
		 * @method _switchPrice
		 */
		_switchPrice: function (event) {
			var nodeClass = this.nodeClass,
			    issecond_option = $(nodeClass + ' .switch-button').prop('checked');

			if (issecond_option) {
				$(nodeClass + ' .first_option-price').hide();
				$(nodeClass + ' .second_option-price').show();
				$(nodeClass + ' .slider').removeClass('first_option');
				$(nodeClass + ' .slider').addClass( 'second_option');
			} else {
				$(nodeClass + ' .first_option-price').show();
				$(nodeClass + ' .second_option-price').hide();
				$(nodeClass + ' .slider').removeClass('second_option');
				$(nodeClass + ' .slider').addClass( 'first_option');
			}

			event.stopPropagation();
		},

		/**
		 * Toggle between the two Button urls.
		 * 
		 * @access private
		 * @method _switchButtonUrl
		 */
		_switchButtonUrl: function (event) {
			var nodeClass       = this.nodeClass,
			issecond_option = $(nodeClass + ' .switch-button').prop('checked'),
			wrapperSelector = nodeClass + ' .fl-pricing-table-button-wrap';

			$(wrapperSelector).each(function() {
				var $wrap   = $(this),
					$btn    = $wrap.find('.fl-button:is(a, button), .fl-button a'),
					monthly = $wrap.data('monthly-url'),
					yearly  = $wrap.data('yearly-url'),
					monthlyTarget = $wrap.data('monthly-target'),
					yearlyTarget  = $wrap.data('yearly-target');

				if (issecond_option && yearly) {
					$btn.attr('href', yearly);
					$btn.attr('target', yearlyTarget || '_self');
				} else if (monthly) {
					$btn.attr('href', monthly);
					$btn.attr('target', monthlyTarget || '_self');
				}
			});

			event.stopPropagation();
		}
	};

})(jQuery);

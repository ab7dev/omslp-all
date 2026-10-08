(function($){

	FLBuilder.registerModuleHelper('pricing-table', {

		init: function () {
			var form = $('.fl-builder-settings'),
				billingOptions = form.find('select[name=dual_billing]')
				billingOption1 = form.find('input[name=billing_option_1]'),
				billingOption2 = form.find('input[name=billing_option_2]');

			billingOptions.on('change', this._updateLabels);
			billingOption1.on('keyup', this._updateLabels);
			billingOption2.on('keyup', this._updateLabels);

			this._updateLabels();
			this._limitAdvancedSpacing( form );
		},

		_updateLabels: function ( event ) {
			var form = $('.fl-builder-settings'),
				billingOptions = form.find('select[name=dual_billing]').val(),
				firstOptionText = form.find('input[name=billing_option_1]').val().trim(),
				secondOptionText = form.find('input[name=billing_option_2]').val().trim(),
				firstOptionPriceButtonColor = form.find('#fl-field-billing_option_1_btn_color label'),
				secondOptionPriceButtonColor = form.find('#fl-field-billing_option_2_btn_color label');

			if ( 'yes' === billingOptions ) {
				$(firstOptionPriceButtonColor).text( '' === firstOptionText ? 'Monthly' : firstOptionText );
				$(secondOptionPriceButtonColor).text( '' === secondOptionText ? 'Yearly' : secondOptionText );
			}

		},

		_limitAdvancedSpacing: function ( form ) {
			var spacingTop              = form.find('input[name=advanced_spacing_top]'),
				spacingTopMedium        = form.find('input[name=advanced_spacing_top_medium]'),
				spacingTopResponsive    = form.find('input[name=advanced_spacing_top_responsive]'),
				spacingBottom           = form.find('input[name=advanced_spacing_bottom]'),
				spacingBottomMedium     = form.find('input[name=advanced_spacing_bottom_medium]'),
				spacingBottomResponsive = form.find('input[name=advanced_spacing_bottom_responsive]');

			spacingTop.closest('.fl-dimension-field-unit ').hide();
			spacingTopMedium.closest('.fl-dimension-field-unit ').hide();
			spacingTopResponsive.closest('.fl-dimension-field-unit ').hide();
			spacingBottom.closest('.fl-dimension-field-unit ').hide();
			spacingBottomMedium.closest('.fl-dimension-field-unit ').hide();
			spacingBottomResponsive.closest('.fl-dimension-field-unit ').hide();
		},
	});

	FLBuilder.registerModuleHelper('pricing_column_form', {

		init: function () {
			var form 				= $('.fl-builder-settings[data-type=pricing_column_form]'),
				moduleSettingsForm 	= $('form.fl-builder-pricing-table-settings'),
				featuresSection		= $( '#fl-builder-settings-section-features' ),
				featureToggleButton	= '.fl-builder-price-feature-toggle-button',
				boxTopMarginField   = $( '#fl-field-pbox_top_margin' ),
				ptSettingsForm      = $( '.fl-builder-settings.fl-builder-module-settings' ),
				ptSettings          = FLBuilder._getSettings( ptSettingsForm );

			if ( 'legacy' === ptSettings.border_type ) {
				boxTopMarginField.show();
			} else {
				boxTopMarginField.hide();
			}

			featuresSection.on('click', featureToggleButton, this._togglePricesFeaturesClicked);
			this._toggleFields( form, moduleSettingsForm );

			form.find('input[name=button_url_yearly_used]').on('change', function() {
				if ( 'yes' === $( this ).val() ) {
					$('#fl-field-button_url_yearly').show();
				} else {
					$('#fl-field-button_url_yearly').hide();
				}
			});
		},

		_toggleFields: function (form, moduleSettingsForm ) {
			var billingLabel1 = form.find('#fl-field-price label'),
				billingLabel2 = form.find('#fl-field-price_option_2 label'),
				billingOptions = moduleSettingsForm.find('select[name=dual_billing]').val(),
				firstOptionText = moduleSettingsForm.find('input[name=billing_option_1]').val().trim(),
				secondOptionText = moduleSettingsForm.find('input[name=billing_option_2]').val().trim(),
				borderType = moduleSettingsForm.find('select[name=border_type]').val();

			if ( 'no' === billingOptions ) {
				$('#fl-field-price').show();
				$('#fl-field-duration').show();
				$('#fl-field-price_option_1').hide();
				$('#fl-field-price_option_2').hide();
				$('#fl-field-button_url_yearly_used').hide();
				$('#fl-field-button_url_yearly').hide();
				$('#fl-field-price_term_color').hide();
				$('#fl-field-price_term_typography').hide();
				$('#fl-field-price_term_1').hide();
				$('#fl-field-price_term_2').hide();
			} else if ('yes' === billingOptions ) {
				var buttonUrlYearlyUsed = form.find('input[name=button_url_yearly_used]').val();
				$('#fl-field-duration').hide();
				$('#fl-field-price_option_1').show();
				$('#fl-field-price_option_2').show();
				if ( 'yes' === buttonUrlYearlyUsed ) {
					$('#fl-field-button_url_yearly').show();
				} else {
					$('#fl-field-button_url_yearly').hide();
				}
				$('#fl-field-price_term_color').show();
				$('#fl-field-price_term_typography').show();
				$('#fl-field-price_term_1').show();
				$('#fl-field-price_term_2').show();

				firstOptionText = '' === firstOptionText ? 'Monthly' : firstOptionText;
				secondOptionText = '' === secondOptionText ? 'Yearly' : secondOptionText;

				$(billingLabel1).text( firstOptionText );
				$(billingLabel2).text( secondOptionText );

				// Update button_url_yearly field label to use the user-entered billing option 2 text.
				var $yearlyUrlLabel = $( '#fl-field-button_url_yearly' ).find( 'label' ).first();
				var $yearlyUrlHelpTip = $yearlyUrlLabel.find( '.fl-help-tip' ).detach();
				$yearlyUrlLabel.text( 'Button URL (' + secondOptionText + ')' );
				if ( $yearlyUrlHelpTip.length ) {
					$yearlyUrlHelpTip.attr( 'title', 'Used for the ' + secondOptionText + ' billing option.' );
					$yearlyUrlLabel.append( $yearlyUrlHelpTip );
				}

				// Update Separate Billing Option URLs help tooltip to use the user-entered billing option 2 text.
				$( '#fl-field-button_url_yearly_used .fl-help-tip' ).attr(
					'title',
					'If set to "Yes", the ' + secondOptionText + ' billing option will use a different URL.'
				);
			}

			// If using Standard Border, hide the Box Border field ( ID = 'fl-field-background' ).
			if ( 'standard' === borderType ) {
				$('#fl-field-background').hide();
			} else {
				$('#fl-field-background').show();
			}
		},

		_togglePricesFeaturesClicked: function () {
			var form = $('.fl-builder-settings[data-type=pricing_column_form]'),
			priceFeatureButtons = form.find('.fl-builder-price-feature-toggle-button');
				button = $(this);

			form.find('.fl-price-feature-icon-row').hide();
			form.find('.fl-price-feature-tooltip-row').hide();

			if (button.hasClass('down')) {
				button.closest('.fl-price-feature-field').find('.fl-price-feature-icon-row').show();
				button.closest('.fl-price-feature-field').find('.fl-price-feature-tooltip-row').show();

				form.find('.fl-builder-price-feature-toggle-button').removeClass('up');
				form.find('.fl-builder-price-feature-toggle-button').addClass('down');

				button.removeClass('down');
				button.addClass('up');

			} else {

				button.removeClass('up');
				button.addClass('down');
			}

		},

	});

})(jQuery);

<?php
$input_selector  = ".fl-node-$id .fl-subscribe-form input:not([type=checkbox])";
$button_selector = ".fl-node-$id .fl-button:is(a, button)";

if ( 'show' === $settings->labels || 'show' === $settings->terms_checkbox ) :
	$label_selector = ".fl-node-$id .fl-subscribe-form-label";

	// Label padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'label_padding',
		'enabled'      => 'placeholder' !== $settings->labels,
		'selector'     => ".fl-node-$id .fl-form-field:not(.fl-terms-checkbox) .fl-subscribe-form-label",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'label_padding_top',
			'padding-right'  => 'label_padding_right',
			'padding-bottom' => 'label_padding_bottom',
			'padding-left'   => 'label_padding_left',
		),
	) );

	// Label color
	FLBuilderCSS::rule( array(
		'selector' => $label_selector,
		'props'    => array(
			'color' => $settings->label_color,
		),
	) );

	// Label typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'label_typography',
		'selector'     => $label_selector,
	) );

endif;

// Input gap
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-form-field",
	'enabled'  => ! empty( $settings->input_gap ),
	'props'    => array(
		'margin-bottom' => "{$settings->input_gap}px",
	),
) );

// Input padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_padding',
	'selector'     => $input_selector,
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'input_padding_top',
		'padding-right'  => 'input_padding_right',
		'padding-bottom' => 'input_padding_bottom',
		'padding-left'   => 'input_padding_left',
	),
) );

// Input color
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'color' => $settings->input_text_color,
	),
) );

// Input hover color
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover, ' . $input_selector . ':focus',
	'props'    => array(
		'color' => $settings->input_text_hover_color,
	),
) );

// Input typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_typography',
	'selector'     => $input_selector,
) );

// Input background
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'background-color' => $settings->input_bg_color,
	),
) );

// Input background hover
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover, ' . $input_selector . ':focus',
	'props'    => array(
		'background-color' => $settings->input_bg_hover_color,
	),
) );

// Input border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border',
	'selector'     => $input_selector,
) );

// Input border hover
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover, ' . $input_selector . ':focus',
	'props'    => array(
		'border-color' => $settings->input_border_hover_color,
	),
) );

// Button color
FLBuilderCSS::rule( array(
	'selector' => $button_selector . ', .fl-node-$id a.fl-button:visited, ' . $button_selector . ' *, .fl-node-$id a.fl-button:visited *',
	'enabled'  => ! empty( $settings->btn_text_color ),
	'props'    => array(
		'color' => $settings->btn_text_color,
	),
) );

// Button hover color
FLBuilderCSS::rule( array(
	'selector' => $button_selector . ':hover, ' . $button_selector . ':hover *',
	'enabled'  => ! empty( $settings->btn_text_hover_color ),
	'props'    => array(
		'color' => $settings->btn_text_hover_color,
	),
) );

// Button CSS settings
FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );

// Hide message
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-form-success-message",
	'props'    => array(
		'display' => 'none',
	),
) );

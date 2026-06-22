<?php
$input_selector = ".fl-node-$id .fl-contact-form :is(input:not([type=checkbox]), textarea)";

if ( 'placeholder' !== $settings->placeholder_labels || 'show' === $settings->terms_checkbox ) :
	$label_selector = ".fl-node-$id .fl-contact-form-label";

	// Label padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'label_padding',
		'enabled'      => 'placeholder' !== $settings->placeholder_labels,
		'selector'     => $label_selector . ':not(:has(input[type=checkbox]))',
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
	'selector' => $input_selector . ',' . $input_selector . '::placeholder',
	'props'    => array(
		'color' => $settings->input_color,
	),
) );

// Input typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_typography',
	'selector'     => $input_selector,
) );

// Input background color
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'background-color' => $settings->input_bg_color,
	),
) );

// Input background hover
FLBuilderCSS::rule( array(
	'selector' => $input_selector . ':hover',
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
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border_hover',
	'selector'     => $input_selector . ':hover, ' . $input_selector . ':focus',
) );

FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() ); ?>

<?php if ( 'right' == $settings->btn_align ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-msg {
	float: right;
}
<?php endif; ?>

<?php if ( 'center' == $settings->btn_align ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-contact-form .fl-success-msg {
	display: block;
	text-align: center;
}
<?php endif; ?>

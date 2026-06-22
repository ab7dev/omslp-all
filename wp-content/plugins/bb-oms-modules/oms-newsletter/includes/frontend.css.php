
.fl-node-<?php echo $id; ?> input.submit-button {
  background-color: #<?php echo $settings->btn_color; ?>;
  border: 1px solid #<?php echo $settings->btn_color; ?>;
  border-radius: 5px;
}

.fl-node-<?php echo $id; ?> input.submit-button:hover {
  background-color: #<?php echo $settings->btn_color; ?>;
  border: 1px solid #<?php echo $settings->btn_color; ?>;
  border-radius: 5px;
}

<?php

// Custom Width
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id input.submit-button",
	'enabled'  => ! empty( $settings->width ) && 'custom' === $settings->width,
	'props'    => array(
		'width' => ( '' === trim( $settings->custom_width ) ? '200' : abs( $settings->custom_width ) ) . $settings->custom_width_unit,
	),
) );

// Alignment
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => ".fl-node-$id input.submit-button",
	'prop'         => 'text-align',
) );

// Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'padding',
	'selector'     => ".fl-node-$id input.submit-button",
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'padding_top',
		'padding-right'  => 'padding_right',
		'padding-bottom' => 'padding_bottom',
		'padding-left'   => 'padding_left',
	),
) );

// Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-node-$id input.submit-button",
) );
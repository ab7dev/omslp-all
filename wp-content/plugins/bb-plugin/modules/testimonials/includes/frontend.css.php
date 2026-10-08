<?php

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'text_color',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-testimonials :is(.fl-testimonial, .fl-testimonial *)",
	'prop'         => 'color',
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'text_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-testimonials :is(.fl-testimonial, .fl-testimonial *)",
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'heading_color',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-testimonials-wrap.compact h3",
	'prop'         => 'color',
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'heading_typography',
	'selector'     => ".fl-builder-content .fl-node-$id .fl-testimonials-wrap.compact h3",
) );

?>
<?php if ( 1 === $module->version ) : ?>
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a:focus,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a.active {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->dot_color ); ?>;
	opacity: 1;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager a {
	opacity: 0.2;
}
<?php else : ?>
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager button,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager button:focus,
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager button.active {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->dot_color ); ?>;
	opacity: 1;
}
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap .bx-pager.bx-default-pager button {
	opacity: 0.2;
}
<?php endif; ?>
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'arrow_color',
	'selector'     => array(
		".fl-node-$id .fl-testimonials-wrap .fas:hover",
		".fl-node-$id .fl-testimonials-wrap .fas",
	),
	'prop'         => 'color',
) );
?>
.fl-node-<?php echo $id; ?> .fl-testimonials-wrap.fl-testimonials-no-heading {
	padding-top: 25px;
}

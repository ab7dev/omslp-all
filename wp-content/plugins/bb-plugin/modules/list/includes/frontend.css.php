<?php
	// Heading Style - Color
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'heading_color',
		'selector'     => '.fl-node-' . $id . ' .fl-list-item-heading-text,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-heading-text',
		'prop'         => 'color',
	) );

	// Heading Style - Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'heading_typography',
		'selector'     => ".fl-node-$id.fl-module-list .fl-list-item-heading",
	) );

	// Content Style - Color
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'content_color',
		'selector'     => '	.fl-node-' . $id . ' .fl-list-item-content .fl-list-item-content-text,
			.fl-node-' . $id . ' .fl-list-item-content .fl-list-item-content-text * ,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-content .fl-list-item-content-text,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-content .fl-list-item-content-text *',
		'prop'         => 'color',
		'enabled'      => 'not_empty',
	) );

	// List Style - Background Color
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'list_bg_color',
		'selector'     => '.fl-node-' . $id . ' .fl-list, .fl-row .fl-col .fl-node-' . $id . ' .fl-list',
		'prop'         => 'background-color',
		'enabled'      => 'not_empty',
	) );

	// List Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'list_padding',
		'selector'     => ".fl-node-$id .fl-list",
		'props'        => array(
			'padding-top'    => 'list_padding_top',
			'padding-right'  => 'list_padding_right',
			'padding-bottom' => 'list_padding_bottom',
			'padding-left'   => 'list_padding_left',
		),
	) );

	// List Item Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'common_list_item_padding',
		'selector'     => ".fl-node-$id .fl-list .fl-list-item",
		'props'        => array(
			'padding-top'    => 'common_list_item_padding_top',
			'padding-right'  => 'common_list_item_padding_right',
			'padding-bottom' => 'common_list_item_padding_bottom',
			'padding-left'   => 'common_list_item_padding_left',
		),
	) );

	// List Border
	FLBuilderCSS::border_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'list_border',
		'selector'     => '.fl-node-' . $id . ' .fl-list',
	) );

	// Icon Style - Color
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'list_icon_color',
		'selector'     => '.fl-node-' . $id . ' .fl-list-item-heading-icon .fl-list-item-icon,
			.fl-node-' . $id . ' .fl-list-item-content-icon .fl-list-item-icon,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-heading-icon .fl-list-item-icon,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-content-icon .fl-list-item-icon',
		'prop'         => 'color',
		'enabled'      => 'not_empty',
	) );

	// Icon Style - Size
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'icon_size',
		'selector'     => '.fl-node-' . $id . ' .fl-list-item-heading-icon .fl-list-item-icon,
			.fl-node-' . $id . ' .fl-list-item-content-icon .fl-list-item-icon,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-heading-icon .fl-list-item-icon,
			.fl-row .fl-col .fl-node-' . $id . ' .fl-list-item-content-icon .fl-list-item-icon',
		'prop'         => 'font-size',
		'unit'         => 'px',
		'enabled'      => 'not_empty',
	) );

	// Icon Style - Width
	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'icon_width',
		'selector'     => '.fl-node-' . $id . ' .fl-list-item-heading-icon .fl-list-item-icon,
			.fl-node-' . $id . ' .fl-list-item-content-icon .fl-list-item-icon',
		'prop'         => 'width',
		'unit'         => 'px',
		'enabled'      => 'not_empty',
	) );

	// Icon Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings,
		'setting_name' => 'icon_padding',
		'selector'     => ".fl-node-$id .fl-list .fl-list-item-icon",
		'props'        => array(
			'padding-top'    => 'icon_padding_top',
			'padding-right'  => 'icon_padding_right',
			'padding-bottom' => 'icon_padding_bottom',
			'padding-left'   => 'icon_padding_left',
		),
	) );

	if ( ! empty( $settings->icon_width ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-list-item-heading-icon .fl-list-item-icon,
	.fl-node-<?php echo $id; ?> .fl-list-item-content-icon .fl-list-item-icon {
		text-align: center;
	}
	<?php endif; ?>
.fl-node-<?php echo $id; ?> ul.fl-list,
.fl-node-<?php echo $id; ?> ol.fl-list {
	list-style-type: none;
}

<?php if ( empty( $settings->separator_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-list .fl-list-item ~ .fl-list-item {
	border-top-color: transparent;
}
	<?php
endif;

// Item Content Style - Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_typography',
	'selector'     => ".fl-node-$id.fl-module-list .fl-list-item-content .fl-list-item-content-text",
) );

// List Item Line Separator
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'separator_size',
	'selector'     => ".fl-node-$id .fl-list .fl-list-item ~ .fl-list-item",
	'prop'         => 'border-top-width',
	'unit'         => 'px',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'separator_style',
	'selector'     => ".fl-node-$id .fl-list .fl-list-item ~ .fl-list-item",
	'prop'         => 'border-top-style',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'separator_color',
	'selector'     => ".fl-node-$id .fl-list .fl-list-item ~ .fl-list-item",
	'prop'         => 'border-top-color',
	'enabled'      => 'not_empty',
) );

$section      = 'section-' . $id;
$item_counter = 0;

if ( 'basic' === $settings->list_layout ) {
	$list_items = explode( "\n", $settings->item_data );
} else {
	$list_items = $settings->list_items;
}

foreach ( $list_items as $k => $item ) :

	if ( ! is_object( $item ) && 'advanced' === $settings->list_layout ) {
		$item_counter++;
		continue;
	}

	if ( 'advanced' === $settings->list_layout ) :

		// Item Heading Text Color
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $item,
			'setting_name' => 'heading_text_color',
			'selector'     => ".fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-heading-text, .fl-row .fl-col .fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-heading-text",
			'prop'         => 'color',
			'enabled'      => 'not_empty',
		) );

		// Item Content Text Color
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $item,
			'setting_name' => 'content_text_color',
			'selector'     => ".fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-content-text *, .fl-row .fl-col .fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-content-text *",
			'prop'         => 'color',
			'enabled'      => 'not_empty',
		) );

		// Item Background Color
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $item,
			'setting_name' => 'bg_color',
			'selector'     => ".fl-node-$id .fl-list .fl-list-item-$item_counter",
			'prop'         => 'background-color',
			'enabled'      => 'not_empty',
		) );

		// Item Icon Style - Color
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $item,
			'setting_name' => 'icon_color',
			'selector'     => ".fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-heading-icon .fl-list-item-icon,
							   .fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-content-icon .fl-list-item-icon,
							   .fl-row .fl-col .fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-heading-icon .fl-list-item-icon,
							   .fl-row .fl-col .fl-node-$id .fl-list .fl-list-item-$item_counter .fl-list-item-content-icon .fl-list-item-icon",
			'prop'         => 'color',
			'enabled'      => 'not_empty',
		) );

		// List Item Padding
		FLBuilderCSS::dimension_field_rule( array(
			'settings'     => $settings->list_items[ $k ],
			'setting_name' => 'list_item_padding',
			'selector'     => ".fl-node-$id .fl-list .fl-list-item-$item_counter",
			'props'        => array(
				'padding-top'    => 'list_item_padding_top',
				'padding-right'  => 'list_item_padding_right',
				'padding-bottom' => 'list_item_padding_bottom',
				'padding-left'   => 'list_item_padding_left',
			),
		) );
	endif;

	// Item Icons
	// For the Ordered List, Icons will be a numeric sequence unless overridden
	// in the individual list item.
	if ( 'ol' === $settings->list_type ) :
		?>
		.fl-node-<?php echo $id; ?> {
			counter-reset: <?php echo $section; ?>;
		}

		.fl-node-<?php echo $id; ?> .fl-list .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-icon::before {
			counter-increment: <?php echo $section; ?>;
			content: counter( <?php echo $section; ?>,  <?php echo $settings->ol_icon; ?>);
		}
		<?php
		// For the Unordered List, icons are determined from the predefined icons ( square, circle, disc )  in the Settings Form.
	elseif ( 'ul' === $settings->list_type ) :
		if ( 'square' === $settings->ul_icon ) {
			$item_icon = '\25A0';
		} elseif ( 'circle' === $settings->ul_icon ) {
			$item_icon = '\25CB';
		} elseif ( 'disc' === $settings->ul_icon ) {
			$item_icon = '\25cf';
		} else {
			$item_icon = '\25cf';
		}
		?>
		.fl-node-<?php echo $id; ?> .fl-list .fl-list-item-<?php echo $item_counter; ?> .fl-list-item-icon::before {
			content: '<?php echo $item_icon; ?>';
		}
		<?php
	endif;

	$item_counter++;

endforeach;

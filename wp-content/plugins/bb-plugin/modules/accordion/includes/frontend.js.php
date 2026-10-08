(function($) {

	$(function() {

		new FLBuilderAccordion({
			id: '<?php echo $id; ?>',
			labelIcon: '<?php echo esc_js( FLBuilderModuleUtils::get_icon_classes( $settings, 'label_' ) ); ?>',
			activeIcon: '<?php echo esc_js( FLBuilderModuleUtils::get_icon_classes( $settings, 'label_active_' ) ); ?>',
			expandOnTab: <?php echo wp_validate_boolean( $settings->expand_on_tab ) ? 'true' : 'false'; ?>,
			expandTxt: '<?php echo esc_attr__( 'Expand', 'fl-builder' ); ?>',
			collapseTxt: '<?php echo esc_attr__( 'Collapse', 'fl-builder' ); ?>'
		});
	});

})(jQuery);

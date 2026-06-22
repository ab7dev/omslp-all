<div class="fl-global-template-message">
	<?php

	/* translators: %s: row or module */
	$message        = sprintf( _x( 'This is a global %s. Changes will appear everywhere it has been placed.', '%s stands for either row or module.', 'fl-builder' ), $type );
	$badge          = __( 'Global', 'fl-builder' );
	$badge_template = 'fl-global-template-message-label';

	if ( FLBuilderModel::is_post_dynamic_editing_node_template( $post->ID ) ) {
		/* translators: %s: row or module */
		$message        = sprintf( _x( 'This %s is a component. It provides global defaults and per page-settings.', 's stands for either row or module.', 'fl-builder' ), $type );
		$badge          = __( 'Component', 'fl-builder' );
		$badge_template = 'fl-dynamic-template-message-label';
	}

	?>
	<div class="<?php echo $badge_template; ?>"><?php echo $badge; ?></div>
	<div class="fl-global-template-message-content"><?php echo $message; ?></div>
</div>

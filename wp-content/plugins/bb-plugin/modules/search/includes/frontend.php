<<?php echo $module->get_wrapper_attributes(); ?>>
	<div class="fl-search-form-wrap">
		<div class="fl-search-form-fields">
			<?php
			if ( 'reveal' === $settings->btn_action ) :
				$module->render_button();
			endif;
			?>
			<div class="fl-search-form-input-wrap">
				<?php require $module->dir . 'includes/wp-search.php'; ?>
			</div>
			<?php
			if ( 3 > $module->version || ( 'button' === $settings->layout && 'fullscreen' === $settings->btn_action ) ) :
				$module->render_button();
			endif;
			?>
		</div>
	</div>
</<?php echo esc_attr( $module->get_wrapper_attributes() ); ?>>

<?php

$enabled_templates = FLBuilderModel::get_enabled_templates();

?>
<div id="fl-templates-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Template Settings', 'fl-builder' ); ?></h3>
	<p><?php _e( 'Control which templates are available in the builder interface and configure template overrides.', 'fl-builder' ); ?></p>

	<?php wp_nonce_field( 'templates', 'fl-templates-nonce' ); ?>

	<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
	<div class="fl-templates-override-ms">
		<label class="fl-toggle-switch">
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php echo ( get_option( '_fl_builder_enabled_templates' ) ) ? 'checked="checked"' : ''; ?> />
			<span><?php _e( 'Override network settings?', 'fl-builder' ); ?></span>
		</label>
	</div>
	<?php endif; ?>

	<div class="fl-settings-form-content">

		<div class="fl-templates-row">
			<div class="fl-templates-card">
				<div class="fl-templates-card-header">
					<h3><?php _e( 'Enable Templates', 'fl-builder' ); ?></h3>
					<p><?php _e( 'Choose which templates are available in the builder interface.', 'fl-builder' ); ?></p>
				</div>
				<div class="fl-templates-card-body">
					<div class="fl-templates-field">
						<label for="fl-template-settings"><?php _e( 'Template Visibility', 'fl-builder' ); ?></label>
						<select id="fl-template-settings" name="fl-template-settings" class="fl-template-visibility-select">
							<option value="enabled" <?php selected( $enabled_templates, 'enabled' ); ?>><?php _e( 'Enable All Templates', 'fl-builder' ); ?></option>
							<option value="core" <?php selected( $enabled_templates, 'core' ); ?>><?php _e( 'Enable Core Templates Only', 'fl-builder' ); ?></option>
							<option value="user" <?php selected( $enabled_templates, 'user' ); ?>><?php _e( 'Enable User Templates Only', 'fl-builder' ); ?></option>
							<option value="disabled" <?php selected( $enabled_templates, 'disabled' ); ?>><?php _e( 'Disable All Templates', 'fl-builder' ); ?></option>
						</select>
					</div>
				</div>
			</div>

			<?php do_action( 'fl_builder_admin_settings_templates_form' ); ?>
		</div>

	</div>
</div>

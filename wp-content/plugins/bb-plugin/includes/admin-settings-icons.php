<div id="fl-icons-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Icons', 'fl-builder' ); ?></h3>
	<p><?php _e( 'Manage icon sets and Font Awesome integration for the builder.', 'fl-builder' ); ?></p>

	<?php
	$fa5_pro_enabled = get_option( '_fl_builder_enable_fa_pro', false );
	$legacy          = apply_filters( 'fl_enable_fa5_pro', false );
	$kit_checked     = ( $fa5_pro_enabled ) ? 'checked="checked"' : '';

	if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) {

		global $blog_id;

		if ( BLOG_ID_CURRENT_SITE == $blog_id ) {
			?>
			<p><?php _e( 'Icons for the main site must be managed in the network admin.', 'fl-builder' ); ?></p>
			</div>
			<?php
			return;
		}
	}

	?>

	<form id="icons-form" action="<?php FLBuilderAdminSettings::render_form_action( 'icons' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php echo ( get_option( '_fl_builder_enabled_icons' ) ) ? 'checked="checked"' : ''; ?> />
			<?php _e( 'Override network settings?', 'fl-builder' ); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">

			<div class="fl-icons-row">

				<!-- Card 1: Icon Sets -->
				<div class="fl-icons-card">
					<div class="fl-icons-card-header">
						<h3><?php _e( 'Icon Sets', 'fl-builder' ); ?></h3>
						<?php if ( 'Beaver Builder' === FLBuilderModel::get_branding() ) : ?>
							<?php /* translators: %s: docs link */ ?>
						<p><?php printf( __( 'Choose which icon sets appear in the icon picker, or upload your own. %s to learn how to create a custom set.', 'fl-builder' ), sprintf( '<a href="https://docs.wpbeaverbuilder.com/beaver-builder/styles/icons/create-and-import-a-custom-icon-set/" target="_blank">%s</a>', _x( 'See the docs', 'Link text', 'fl-builder' ) ) ); ?></p>
						<?php else : ?>
						<p><?php _e( 'Choose which icon sets appear in the icon picker, or upload your own.', 'fl-builder' ); ?></p>
						<?php endif; ?>
					</div>
					<div class="fl-icons-card-body">
						<details class="fl-icons-note">
							<summary><span class="dashicons dashicons-info-outline"></span><?php _e( 'How does this work?', 'fl-builder' ); ?></summary>
							<p><?php _e( 'If an icon is being used in a supported module, its CSS will be enqueued. Deselecting sets here only removes the set from the icon picker.', 'fl-builder' ); ?></p>
						</details>

						<?php
						$enabled_icons = FLBuilderModel::get_enabled_icons();
						$icon_sets     = FLBuilderIcons::get_sets_for_current_site();

						foreach ( $icon_sets as $key => $set ) {
							$checked = in_array( $key, $enabled_icons ) ? ' checked' : '';
							?>
							<div class="fl-icons-set-item">
								<label class="fl-toggle-switch">
									<input type="checkbox" class="fl-icon-set-toggle" name="fl-enabled-icons[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
									<span><?php echo $set['name']; ?>
									<?php
									if ( 'core' != $set['type'] ) :
										?>
										<a href="javascript:void(0);" class="fl-delete-icon-set fl-icons-delete-link" data-set="<?php echo $key; ?>"><?php _ex( 'Delete', 'Plugin setup page: Delete icon set.', 'fl-builder' ); ?></a><?php endif; ?></span>
								</label>
							</div>
							<?php
						}
						?>
					</div>
				</div>

				<!-- Card 2: Font Awesome -->
				<div class="fl-icons-card">
					<div class="fl-icons-card-header">
						<h3><?php _e( 'Font Awesome', 'fl-builder' ); ?></h3>
						<p><?php _e( 'Configure Font Awesome Pro icons and integration settings.', 'fl-builder' ); ?></p>
					</div>
					<div class="fl-icons-card-body">
						<?php if ( ! FLBuilderFontAwesome::is_installed() ) : ?>

							<?php if ( $legacy ) : ?>
							<p class="fl-icons-legacy-notice"><?php _e( 'Font Awesome PRO already enabled via fl_enable_fa5_pro filter.', 'fl-builder' ); ?></p>
							<?php else : ?>
							<div class="fl-icons-set-item">
								<label class="fl-toggle-switch">
									<input type="checkbox" class="fl-fa-pro-toggle" name="fl-enable-fa-pro" <?php echo $kit_checked; ?>>
									<span><?php _e( 'Enable Font Awesome PRO icons', 'fl-builder' ); ?></span>
								</label>
								<span class="fl-icons-deprecated-badge"><?php _e( 'Deprecated', 'fl-builder' ); ?></span>
							</div>
							<?php endif; ?>

							<?php if ( $fa5_pro_enabled || $legacy ) : ?>
							<div class="fl-icons-field">
								<label for="fl-fa-pro-kit"><?php _e( 'Kit URL', 'fl-builder' ); ?></label>
								<input type="text" id="fl-fa-pro-kit" name="fl-fa-pro-kit" placeholder="https://kit.fontawesome.com/nnnnnn.js" value="<?php echo esc_attr( get_option( '_fl_builder_kit_fa_pro' ) ); ?>" />
							</div>
							<p class="fl-icons-deprecated-notice"><span class="dashicons dashicons-warning"></span><?php _e( 'This method is deprecated and no longer supported.', 'fl-builder' ); ?></p>
							<?php endif; ?>

							<div class="fl-icons-fa-links fl-icons-notice">
								<p><span class="dashicons dashicons-info-outline"></span><?php _e( 'Install the official Font Awesome plugin to use Pro icons or Font Awesome version 6 and later.', 'fl-builder' ); ?></p>
								<ul>
									<li><?php printf( '<a target="_blank" href="https://wordpress.org/plugins/font-awesome/">%s <i class="dashicons dashicons-external"></i></a>', __( 'Official Font Awesome Plugin', 'fl-builder' ) ); ?></li>
									<li><?php printf( '<a target="_blank" href="https://fontawesome.com/v6/docs/web/use-with/wordpress/">%s <i class="dashicons dashicons-external"></i></a>', __( 'Font Awesome Plugin Documentation', 'fl-builder' ) ); ?></li>
								</ul>
							</div>

						<?php else : ?>
							<?php $data = FLBuilderFontAwesome::get_fa_data(); ?>
							<div class="fl-icons-fa-integration">
								<?php
								foreach ( $data as $k => $item ) {
									?>
									<div class="fl-icons-fa-item">
										<span class="fl-icons-fa-item-label"><?php echo $item['name']; ?></span>
										<span class="fl-icons-fa-item-value"><?php echo $item['value']; ?></span>
									</div>
									<?php
								}
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</div><!-- .fl-icons-row -->

		</div>
		<p class="submit">
			<input type="button" name="fl-upload-icon" class="button" value="<?php esc_attr_e( 'Upload Icon Set', 'fl-builder' ); ?>" />
			<input type="hidden" name="fl-new-icon-set" value="" />
			<input type="hidden" name="fl-delete-icon-set" value="" />
			<?php wp_nonce_field( 'icons', 'fl-icons-nonce' ); ?>
		</p>
	</form>
</div>

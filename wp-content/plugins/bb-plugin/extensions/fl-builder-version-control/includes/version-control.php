<?php
$branding       = FLBuilderModel::get_branding();
$is_branded     = ( 'Beaver Builder' !== $branding );
$theme_branding = class_exists( 'FLBuilderWhiteLabel' ) ? FLBuilderWhiteLabel::get_theme_branding() : array();
$theme_name     = ! empty( $theme_branding['name'] ) ? $theme_branding['name'] : __( 'Beaver Builder Theme', 'fl-builder' );
$themer_name    = $is_branded ? $branding . ' Themer' : __( 'Beaver Themer', 'fl-builder' );
?>
<div id="fl-versions-form" class="fl-settings-form">
	<h3 class="fl-settings-form-header"><?php _e( 'Version Control', 'fl-builder' ); ?></h3>
	<?php // translators: %s: Branding name ?>
	<p><?php printf( __( 'Install a specific version of any %s product. Up to the last 10 versions are shown for each product.', 'fl-builder' ), esc_html( $branding ) ); ?></p>

	<div class="fl-settings-warning">
		<span class="dashicons dashicons-warning"></span>
		<div>
			<strong><?php _e( 'We highly recommend that you make a backup before switching versions.', 'fl-builder' ); ?></strong>
			<?php if ( ! $is_branded ) : ?>
				<?php // translators: %s, Link to changelogs page ?>
				<p><?php printf( __( 'For full version details please take a look at our %s.', 'fl-builder' ), $changeloglink ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="fl-version-cards">
		<div class="fl-version-card">
			<div class="fl-version-card-header">
				<h4><?php echo esc_html( $branding ); ?></h4>
			</div>
			<div class="fl-version-card-body">
				<select class="bb-plugin">
					<?php
					foreach ( $this->format_versions( $bb_data['versions'] ) as $version ) {
						printf( '<option name="%s">%s</option>', $version, $version );
					}
					?>
				</select>
				<input type="hidden" class="flavour" value="<?php echo $this->_get_version_name(); ?>" />
				<button type="button" class="button button-primary bb-plugin-install"><?php _e( 'Install', 'fl-builder' ); ?></button>
			</div>
		</div>

		<div class="fl-version-card">
			<div class="fl-version-card-header">
				<h4><?php echo esc_html( $themer_name ); ?></h4>
			</div>
			<div class="fl-version-card-body">
				<select class="bb-theme-builder">
					<?php
					foreach ( $this->format_versions( $themer['versions'] ) as $version ) {
						printf( '<option name="%s">%s</option>', $version, $version );
					}
					?>
				</select>
				<button type="button" class="button button-primary bb-themer-install"><?php _e( 'Install', 'fl-builder' ); ?></button>
			</div>
		</div>

		<div class="fl-version-card">
			<div class="fl-version-card-header">
				<h4><?php echo esc_html( $theme_name ); ?></h4>
			</div>
			<div class="fl-version-card-body">
				<select class="bb-theme">
					<?php
					foreach ( $this->format_versions( $theme['versions'] ) as $version ) {
						printf( '<option name="%s">%s</option>', $version, $version );
					}
					?>
				</select>
				<button type="button" class="button button-primary bb-theme-install"><?php _e( 'Install', 'fl-builder' ); ?></button>
			</div>
		</div>
	</div>

	<div class="status"></div>
</div>

<div id="fl-versions-form" class="fl-settings-form">
	<h3 class="fl-settings-form-header"><?php _e( 'Version Control', 'fl-builder' ); ?></h3>
	<p><strong><?php _e( 'We highly recommend that you make a backup before switching versions.', 'fl-builder' ); ?></strong></p>
	<?php // translators: %s, Link to changelogs page ?>
	<p><?php printf( __( 'For full version details please take a look at our %s. ', 'fl-builder' ), $changeloglink ); ?></p>

	<p>
		<span class="name"><?php _e( 'Beaver Builder', 'fl-builder' ); ?></span>
		<select class="bb-plugin">
			<?php
			foreach ( $this->format_versions( $bb_data['versions'] ) as $version ) {
				printf( '<option name="%s">%s</option>', $version, $version );
			}
			?>
		</select>
		<input type="hidden" class="flavour" value="<?php echo $this->_get_version_name(); ?>" />
		<input type="submit" value="Install" class="bb-plugin-install button button-primary"/>
	</p>
	<p>
		<span class="name"><?php _e( 'Beaver Themer', 'fl-builder' ); ?></span>
		<select class="bb-theme-builder">
			<?php
			foreach ( $this->format_versions( $themer['versions'] ) as $version ) {
				printf( '<option name="%s">%s</option>', $version, $version );
			}
			?>
		</select>
		<input type="submit" value="Install" class="bb-themer-install button button-primary"/>
	</p>
	<p>
		<span class="name"><?php _e( 'Beaver Theme', 'fl-builder' ); ?></span>
		<select class="bb-theme">
			<?php
			foreach ( $this->format_versions( $theme['versions'] ) as $version ) {
				printf( '<option name="%s">%s</option>', $version, $version );
			}
			?>
		</select>
		<input type="submit" value="Install" class="bb-theme-install button button-primary"/>
	</p>
	<div class="status"></div>
</div>

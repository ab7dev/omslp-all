<p class="howto"><?php esc_html_e( 'Define specific access permissions and capabilities for each user role. Capabilities control what actions users can perform, like managing campaigns, subscribers, forms, and other Mailster features.', 'mailster' ); ?></p>

<p class="howto"><?php esc_html_e( 'The Administrator role always has full access to all features and cannot be restricted. Other roles can be customized with granular permissions to match your organization\'s needs.', 'mailster' ); ?></p>

<p class="howto"><?php esc_html_e( 'To create custom user roles with specific capabilities, we recommend using WordPress plugins like "User Role Editor" or "Members". These plugins provide an easy way to add and manage new roles.', 'mailster' ); ?></p>
<div id="current-cap"></div>
<table class="form-table">
<?php

// no admin roles as they have all rights
unset( $roles['administrator'] );

?>
<tr valign="top" class="settings-row settings-row-capabilities">
	<td>
		<table id="capabilities-table">
			<thead>
				<tr>
				<th>&nbsp;</th>
				<?php foreach ( $roles as $role => $name ) : ?>
					<th><input type="hidden" name="mailster_options[roles][<?php echo esc_attr( $role ); ?>][]" value=""><?php echo esc_html( $name ); ?> <input type="checkbox" class="selectall" value="<?php echo esc_attr( $role ); ?>" title="<?php echo esc_html__( 'toggle all', 'mailster' ); ?>"></th>
				<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>

		<?php require MAILSTER_DIR . 'includes/capability.php'; ?>

		<?php foreach ( $mailster_capabilities as $capability => $data ) : ?>
			<?php
			switch ( $capability ) {
				case 'read_newsletter':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Campaign Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'read_mailster-workflow':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Autoresponder Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'read_mailster-form':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Form Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_manage_lists':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'List Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_manage_subscribers':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Bulk Subscriber Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_add_tags':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Tag Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'read_mailster_templates':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Template Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_edit_subscribers':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Subscriber Management', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_manage_addons':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Other', 'mailster' ); ?></th></tr>
					<?php
					break;
				case 'mailster_manage_templates':
					?>
					<tr class="section-heading"><th colspan="<?php echo count( $roles ) + 1; ?>"><?php esc_html_e( 'Template Management', 'mailster' ); ?></th></tr>
					<?php
					break;

			}
			?>
			<tr><th><span title="<?php echo esc_attr( $capability ); ?>"><?php echo esc_html( $data['title'] ); ?></span></th>
			<?php foreach ( $roles as $role => $name ) : ?>
				<?php $r = get_role( $role ); ?>
				<td><label title="<?php printf( esc_html__( '%1$s can %2$s', 'mailster' ), $name, $data['title'] ); ?>"><input name="mailster_options[roles][<?php echo esc_attr( $role ); ?>][]" type="checkbox" class="cap-check-<?php echo esc_attr( $role ); ?>" value="<?php echo esc_attr( $capability ); ?>" <?php echo checked( ! empty( $r->capabilities[ $capability ] ), 1, false ); ?>></label></td>
			<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	</td>
</tr>
</table>
<p><?php esc_html_e( 'You can reset the capablities for your roles here to the default values.', 'mailster' ); ?></p>
<p>
<a class="button" onclick='return confirm("<?php esc_html_e( 'Do you really like to reset all capabilities? This cannot be undone!', 'mailster' ); ?>");' href="edit.php?post_type=newsletter&page=mailster_settings&reset-capabilities=1&_wpnonce=<?php echo wp_create_nonce( 'mailster-reset-capabilities' ); ?>"><?php esc_html_e( 'Reset all capabilities', 'mailster' ); ?></a></p>
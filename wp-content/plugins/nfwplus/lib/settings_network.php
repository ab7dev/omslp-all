<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

if (! current_user_can( 'manage_network' ) ) {
	die( '<br /><br /><br /><div class="error notice is-dismissible"><p>' .
			sprintf( __('You are not allowed to perform this task (%s).', 'nfwplus'), __LINE__) .
			'</p></div>' );
}

$nfw_options = nfw_get_option( 'nfw_options' );

echo '
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="'. plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) .'">&nbsp;' . __('Network', 'nfwplus') . '</h1>';
if (! is_multisite() ) {
	echo '<div class="updated notice is-dismissible"><p>' . __('You do not have a multisite network.', 'nfwplus') . '</p></div></div>';
	return;
}

// Saved?
if (! empty( $_POST['nf-network'] ) ) {

	if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'network_save') ) {
		wp_nonce_ays('network_save');
	}
	if ( empty( $_POST['nfw_options']['nt_show_status'] ) ) {
		$nfw_options['nt_show_status'] = 2;
	} else {
		$nfw_options['nt_show_status'] = 1;
	}
	// Update options
	nfw_update_option( 'nfw_options', $nfw_options );
	echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'nfwplus') . '</p></div>';
	$nfw_options = nfw_get_option( 'nfw_options' );
}

if ( empty( $nfw_options['nt_show_status'] ) || $nfw_options['nt_show_status'] == 2 ) {
	$nt_show_status = 0;
} else {
	$nt_show_status = 1;
}
nfw_contextual_help();
?>
	<form method="post" name="nfwnetwork">
	<?php wp_nonce_field('network_save', 'nfwnonce', 0); ?>
	<h3><?php _e('NinjaFirewall Status', 'nfwplus') ?></h3>
		<table class="form-table nfw-table">
			<tr>
				<th scope="row" class="row-med"><?php _e('Display NinjaFirewall status icon in the WordPress ToolBar of all sites in the network', 'nfwplus') ?></th>
				<td>
					<?php nfw_toggle_switch( 'info', 'nfw_options[nt_show_status]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $nt_show_status ) ?>
				</td>
			</tr>
		</table>

		<br />
		<br />
		<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Network options', 'nfwplus') ?>" />
		<input type="hidden" name="nf-network" value="1" />
	</form>
</div>
<?php
// =====================================================================
// EOF

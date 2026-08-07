<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Block immediately if user is not allowed
nf_not_allowed( 'block', __LINE__ );

$nfw_options = nfw_get_option( 'nfw_options' );

// Saved options
if (! empty( $_POST['save_webfilter'] ) ) {
	if ( empty( $_POST['nfwnonce'] ) || ! wp_verify_nonce($_POST['nfwnonce'], 'webfilter_save') ) {
		wp_nonce_ays('webfilter_save');
	}
	nf_sub_webfilter_save();
	$nfw_options = nfw_get_option( 'nfw_options' );
	echo '<div class="updated notice is-dismissible"><p>'. __('Your changes have been saved.', 'nfwplus'). '</p></div>';
}

if ( empty( $nfw_options['wf_enable'] ) ) {
	$nfw_options['wf_enable'] = 0;
} else {
	$nfw_options['wf_enable'] = 1;
}
if ( empty( $nfw_options['wf_case'] ) ) {
	$nfw_options['wf_case'] = 0;
} else {
	$nfw_options['wf_case'] = 1;
}
if ( empty( $nfw_options['wf_alert'] ) || ! preg_match('/^(1?5|30|60|180|360|720|1440)$/', $nfw_options['wf_alert'] ) ) {
	$nfw_options['wf_alert'] = 30;
}
if ( empty( $nfw_options['wf_attach'] ) ) {
	$nfw_options['wf_attach'] = 0;
} else {
	$nfw_options['wf_attach'] = 1;
}

if ( defined('NFW_WPWAF') ) {
	?>
	<div class="nfw-notice nfw-notice-orange">
		<p><?php printf( __('You are running NinjaFirewall in <i>WordPress WAF</i> mode. The %s feature will be limited to WordPress files only (e.g., index.php, wp-login.php, xmlrpc.php, admin-ajax.php, wp-load.php etc). If you want it to apply to any PHP script, please <a href="%s">go to the Dashboard page</a> and enable NinjaFirewall\'s Full WAF mode.', 'nfwplus'), 'Web Filter', '?page=NinjaFirewall') ?></p>
	</div>
	<?php
}
?>
<form method="post" onSubmit="return mfwjs_check_webfilter();">
	<?php wp_nonce_field('webfilter_save', 'nfwnonce', 0); ?>
	<table class="form-table nfw-table">
		<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
			<th scope="row" class="row-med"><?php _e('Enable Web Filter', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'green', 'nfw_options[wf_enable]', __('Enabled', 'nfwplus'), __('Disabled', 'nfwplus'), 'large', $nfw_options['wf_enable'], false, 'onclick="nfwjs_up_down(\'table-div\');"', 'wf-enable' ) ?>
			</td>
		</tr>
	</table>

	<br />

	<div id="table-div"<?php echo $nfw_options['wf_enable'] == 1 ? '' : ' style="display:none"' ?>>

	<?php
	$list = '';
	if (! empty( $nfw_options['wf_pattern'] ) ) {
		$list = str_replace( '|', "\n", $nfw_options['wf_pattern'] );
	}
	?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php _e('Search HTML page for the following keywords', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span id="view-wf-span"><a href="javascript:" onClick="nfwjs_view_format('view-wf');"><?php _e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-wf" style="display:none">
						<ul class="view">
							<li><?php _e('A full or partial string.', 'nfwplus') ?></li>
							<li><?php _e('From 4 to maximum 150 characters.', 'nfwplus') ?></li>
							<li><?php _e('Any character, except the vertical bar <code>|</code>', 'nfwplus') ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php echo __('Keywords to search:', 'nfwplus') ?><br />
				<textarea wrap="off" id="wf-pattern" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[wf_pattern]" class="large-text code" rows="15" placeholder="<?php _e('Enter one item per line.', 'nfwplus') ?>"><?php echo htmlspecialchars( $list ) ."\n"; ?></textarea>
			</td>
		</tr>
		<tr>
			<th scope="row" class="row-med"><?php _e('Case-sensitive search', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[wf_case]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $nfw_options['wf_case'] ) ?>
			</td>
		</tr>

		<tr>
			<th scope="row" class="row-med"><?php _e('Email Alerts', 'nfwplus') ?></th>
			<td>
				<?php
				printf( __('Do not send me more than one email alert in a %s interval', 'nfwplus'),
				'<select name="nfw_options[wf_alert]">
					<option value="5"'. selected( $nfw_options['wf_alert'], 5, 0 ) .'>'. __('5-minute', 'nfwplus') .'</option>
					<option value="15"'. selected( $nfw_options['wf_alert'], 15, 0 ) .'>'. __('15-minute', 'nfwplus') .'</option>
					<option value="30"'. selected( $nfw_options['wf_alert'], 30, 0 ) .'>'. __('30-minute', 'nfwplus') .'</option>
					<option value="60"'. selected( $nfw_options['wf_alert'], 60, 0 ) .'>'. __('1-hour', 'nfwplus') .'</option>
					<option value="180"'. selected( $nfw_options['wf_alert'],180, 0 ) .'>'. __('3-hour', 'nfwplus') .'</option>
					<option value="360"'. selected( $nfw_options['wf_alert'], 360, 0 ) .'>'. __('6-hour', 'nfwplus') .'</option>
					<option value="720"'. selected( $nfw_options['wf_alert'], 720, 0 ) .'>'. __('12-hour', 'nfwplus') .'</option>
					<option value="1440"'. selected( $nfw_options['wf_alert'], 1440, 0 ) .'>'. __('24-hour', 'nfwplus') .'</option>
				</select>' );
				?>
				<br />
				<p class="description"><?php _e('Clicking the "Save Web Filter options" button below will reset the current timer.', 'nfwplus') ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="row-med"><?php _e('Attach the HTML page output to email alerts', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[wf_attach]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $nfw_options['wf_attach'] ) ?>
			</td>
		</tr>
	</table>

	</div>

	<br />
	<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Web Filter options', 'nfwplus') ?>" />
	<input class="hidden" name="save_webfilter" value="1" />
	<input class="hidden" name="tab" value="webfilter" />
</form>
<?php

// =====================================================================

function nf_sub_webfilter_save() {

	global $nfw_options;

	// Disable or enable the web filter
	if ( empty( $_POST['nfw_options']['wf_enable'] ) ) {
		$nfw_options['wf_enable'] = 0;
	} else {
		$nfw_options['wf_enable'] = 1;
	}

	// Strings to search for
	$pattern = '';
	if (! empty( $_POST['nfw_options']['wf_pattern'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['wf_pattern'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			$item = str_replace( '|', '', stripslashes( $item ) );
			// Min 4, max 150 characters
			if ( strlen( $item ) < 4 || strlen( $item ) > 150 ) {
				continue;
			}
			$pattern .= trim( $item ) .'|';
		}
		$pattern = rtrim( $pattern, '|' );
	}
	if ( empty( $pattern ) ) {
		$nfw_options['wf_pattern'] = 0;
	} else {
		$nfw_options['wf_pattern'] = $pattern;
	}
	// Case sensitivity
	if ( empty( $_POST['nfw_options']['wf_case'] ) ) {
		$nfw_options['wf_case'] = 0;
	} else {
		$nfw_options['wf_case'] = 1;
	}

	// Alert throttling
	if ( empty( $_POST['nfw_options']['wf_alert'] ) || ! preg_match('/^(1?5|30|60|180|360|720|1440)$/', $_POST['nfw_options']['wf_alert'] ) ) {
		$nfw_options['wf_alert'] = 30;
	} else {
		$nfw_options['wf_alert'] = $_POST['nfw_options']['wf_alert'];
	}

	// Attachment
	if ( isset( $_POST['nfw_options']['wf_attach'] ) ) {
		$nfw_options['wf_attach'] = 1;
	} else {
		$nfw_options['wf_attach'] = 0;
	}

	// Clear alert timer
	if ( is_file( NFW_LOG_DIR . '/nfwlog/cache/wf_timer.php') ) {
		unlink( NFW_LOG_DIR . '/nfwlog/cache/wf_timer.php');
	}

	// Save the path to wp-content dir for the firewall nfw_webfilter() function
	$nfw_options['wp_content'] = WP_CONTENT_DIR;

	// Update
	nfw_update_option( 'nfw_options', $nfw_options );

}
// =====================================================================
// EOF

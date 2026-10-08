<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

// Block immediately if user is not allowed.
nf_not_allowed('block', __LINE__ );


/**
 * Convert the old configuration file to the new format.
 * v4.9.1+
 */
if ( file_exists( NFW_LOG_DIR .'/nfwlog/cache/bf_conf.php') ) {
	NinjaFirewall_bruteforce::convert( NFW_LOG_DIR .'/nfwlog/cache');
}

echo '
<div class="wrap">
	<h1><img style="vertical-align:top" src="'. plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) .'">&nbsp;' . __('Login Protection', 'nfwplus') . '</h1>';

// Saved ?
if ( isset( $_POST['nfw_options'] ) ) {
	if ( empty($_POST['nfwnonce'] ) || ! wp_verify_nonce( $_POST['nfwnonce'], 'bfd_save' ) ) {
		wp_nonce_ays('bfd_save');
	}
	$res = nf_sub_loginprot_save();
	if (! $res ) {
		echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'nfwplus') . '</p></div>';
	} else {
		echo '<div class="error notice is-dismissible"><p>' . $res . '</p></div>';
	}
}

/**
 * Load the current configuration.
 */
if ( file_exists( NFW_LOG_DIR .'/nfwlog/cache/login_protection.php') ) {

	$config = NinjaFirewall_bruteforce::read_config( NFW_LOG_DIR .'/nfwlog/cache/login_protection.php');

	if ( empty( $config['bf_enable'] ) || ! preg_match('/^[12]$/', $config['bf_enable'] ) ) {
		$config['bf_enable'] = 0;
	}
	if ( empty( $config['bf_request'] ) || ! preg_match('/^(GET|POST|GETPOST)$/', $config['bf_request'] ) ) {
		$config['bf_request'] = 'POST';
	}
	if ( $config['bf_request'] == 'GETPOST' ) {
		$get_post = 'GET/POST';
	} else {
		$get_post = $config['bf_request'];
	}
	if ( empty( $config['bf_bantime'] ) || ! preg_match('/^[1-9][0-9]?$/', $config['bf_bantime'] ) ) {
		$config['bf_bantime'] = 5;
	}
	if ( empty( $config['bf_attempt'] ) || ! preg_match('/^[1-9][0-9]?$/', $config['bf_attempt'] ) ) {
		$config['bf_attempt'] = 8;
	}
	if ( empty( $config['bf_maxtime'] ) || ! preg_match('/^[1-9][0-9]?$/', $config['bf_maxtime'] ) ) {
		$config['bf_maxtime'] = 15;
	}
	if ( empty( $config['auth_pass'] ) ) {
		$config['auth_pass'] = '';
	}
	if ( empty( $config['auth_name'] ) || strlen( $config['auth_pass'] ) != 40 ) {
		$config['auth_name']= '';
	}
	if ( empty( $config['auth_msgtxt'] ) ) {
		// NinjaFirewall <= 3.4.2
		if (! empty( $config['auth_msg'] ) ) {
			$config['auth_msgtxt'] = $config['auth_msg'];
		} else {
			$config['auth_msgtxt'] = __('Access restricted', 'nfwplus');
		}
	} else {
		$config['auth_msgtxt'] = base64_decode( $config['auth_msgtxt'] );
	}
	if ( strlen( $config['auth_msgtxt'] ) > 1024 ) {
		$config['auth_msgtxt'] = mb_substr( $config['auth_msgtxt'], 0, 1024, 'utf-8' );
	}
	if ( empty( $config['captcha_text'] ) ) {
		$config['captcha_text'] = __( 'Type the characters you see in the picture below:', 'nfwplus' );
	} else {
		$config['captcha_text'] = html_entity_decode( base64_decode( $config['captcha_text'] ) );
		if ( strlen( $config['captcha_text'] ) > 255 ) {
			$config['captcha_text'] = mb_substr( $config['captcha_text'], 0, 255, 'utf-8' );
		}
	}

	if ( empty( $config['bf_xmlrpc'] ) ) {
		$config['bf_xmlrpc'] = 0;
	} else {
		$config['bf_xmlrpc'] = 1;
	}
	if ( empty( $config['bf_authlog'] ) ) {
		$config['bf_authlog'] = 0;
	} else {
		$config['bf_authlog'] = 1;
	}
	if ( empty( $config['bf_type'] ) ) {
		// Password
		$config['bf_type'] = 0;
	} else {
		// Captcha
		$config['bf_type'] = 1;
	}
	if ( empty( $config['bf_allow_bot'] ) ) {
		$config['bf_allow_bot'] = 0;
	} else {
		$config['bf_allow_bot'] = 1;
	}
	if ( empty( $config['bf_nosig'] ) ) {
		$config['bf_nosig'] = 0;
	} else {
		$config['bf_nosig'] = 1;
	}

} else {
	// Default values
	$config['bf_type']      = 1;
	$config['bf_enable']    = 0;
	$config['bf_request']   = 'POST';
	$config['bf_bantime']   = 5;
	$config['bf_attempt']   = 8;
	$config['bf_maxtime']   = 15;
	$config['auth_name']    = '';
	$config['auth_msgtxt']  = __('Access restricted', 'nfwplus');
	$config['bf_xmlrpc']    = 0;
	$config['bf_authlog']   = 0;
	$config['bf_allow_bot'] = 0;
	$config['captcha_text'] = __( 'Type the characters you see in the picture below:', 'nfwplus');
	$config['bf_nosig']     = 0;
	$get_post               = 'POST';
}
?>
<script type="text/javascript">
	var bf_type = <?php echo $config['bf_type'] ?>;
	var bf_enable = <?php echo $config['bf_enable'] ?>;
</script>
<br />
<?php
// Protection is disabled:
if ( empty( $config['bf_enable'] ) ) {
	$ui_enabled = 0;
	$show_bf_table = 0;
	$show_bf_table_password = 0;
	$show_bf_table_extra = 0;
	$show_bf_table_captcha = 0;

// Protection set to "When under attack":
} elseif ( $config['bf_enable'] == 1 ) {
	$ui_enabled = 1;
	$show_bf_table = 1;
	$show_bf_table_extra = 1;
	// Password?
	if ( empty( $config['bf_type'] ) ) {
		$show_bf_table_password = 1;
		$show_bf_table_captcha = 0;
	// Captcha?
	} else {
		$show_bf_table_password = 0;
		$show_bf_table_captcha = 1;
	}

// Protection set to "Always ON" (2):
} else {
	$ui_enabled = 1;
	$show_bf_table = 0;
	$show_bf_table_extra = 1;
			// Password?
	if ( empty( $config['bf_type'] ) ) {
		$show_bf_table_password = 1;
		$show_bf_table_captcha = 0;
	// Captcha?
	} else {
		$show_bf_table_password = 0;
		$show_bf_table_captcha = 1;
	}
}

// Make sure we can display the captcha with the GD extension:
if ( function_exists( 'gd_info' ) ) {
	$missing_gd = '';
	$gd_disabled = '';
} else {
	$missing_gd = '<p class="description">' .
		__( 'GD Support is not available on your server, the CAPTCHA option is disabled.', 'nfwplus' ) . '</p>';
	$gd_disabled = ' disabled="disabled"';
}
if ( $gd_disabled && $config['bf_type'] == 1 ) {
	echo '<div class="error notice is-dismissible"><p>' .
		__('Error: GD Support is not available on your server, the captcha protection will not work!', 'nfwplus') .'</p></div>';
}
nfw_contextual_help();
?>
<form method="post" name="bp_form" onSubmit="return check_login_fields();">

	<?php wp_nonce_field('bfd_save', 'nfwnonce', 0); ?>

	<table class="form-table nfw-table">
		<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
			<th scope="row" class="row-med"><?php _e('Enable brute force attack protection', 'nfwplus') ?></th>
			<td>
				<?php  nfw_toggle_switch( 'green', 'ui_enabled', __('Enabled', 'nfwplus'), __('Disabled', 'nfwplus'), 'large', $ui_enabled, false, 'onclick="nfwjs_up_down(\'submenu_table\');nfwjs_up_down(\'bf_table_extra\');"', 'ui-enabled' ) ?>
			</td>
		</tr>
	</table>

	<br />

	<div class="nfw-table" id="submenu_table"<?php echo $ui_enabled == 1 ? '' : ' style="display:none"' ?>>

		<table class="form-table">
			<tr>
				<th scope="row" class="row-med"><?php _e('Type of protection', 'nfwplus') ?></th>
				<td>
					<p><label><input type="radio" name="nfw_options[bf_type]" value="0"<?php checked($config['bf_type'], 0) ?> onclick="nfwjs_toggle_table(bf_enable, 0);">&nbsp;<?php _e('Username + Password', 'nfwplus') ?></label></p>
					<p><label><input type="radio" name="nfw_options[bf_type]" value="1"<?php checked($config['bf_type'], 1) ?> onclick="nfwjs_toggle_table(bf_enable, 1);"<?php echo $gd_disabled ?> />&nbsp;<?php _e('Captcha image', 'nfwplus') ?></label></p>
					<?php echo $missing_gd ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="row-med"><?php _e('When to enable the protection', 'nfwplus') ?></th>
				<td>
					<p><label><input type="radio" name="nfw_options[bf_enable]" value="2"<?php checked($config['bf_enable'], 2) ?> onclick="nfwjs_toggle_submenu(2);">&nbsp;<?php _e('Always enabled', 'nfwplus') ?></label></p>
					<p><label><input type="radio" name="nfw_options[bf_enable]" value="1"<?php checked($config['bf_enable'], 1) ?> onclick="nfwjs_toggle_submenu(1);">&nbsp;<?php _e('When under attack', 'nfwplus') ?></label></p>
				</td>
				<td>
			</tr>

		</table>

		<div id="bf_table"<?php echo $show_bf_table == 1 ? '' : ' style="display:none"' ?>>
			<table class="form-table">
				<tr>
					<th scope="row" class="row-med"><?php _e('Protect the login page against', 'nfwplus') ?></th>
					<td>
					<p><label><input onclick="nfwjs_getpost(this.value);" type="radio" name="nfw_options[bf_request]" value="GET"<?php checked($config['bf_request'], 'GET') ?>>&nbsp;<?php _e('<code>GET</code> request attacks', 'nfwplus') ?></label></p>
					<p><label><input onclick="nfwjs_getpost(this.value);" type="radio" name="nfw_options[bf_request]" value="POST"<?php checked($config['bf_request'], 'POST') ?>>&nbsp;<?php _e('<code>POST</code> request attacks (default)', 'nfwplus') ?></label></p>
					<p><label><input onclick="nfwjs_getpost(this.value);" type="radio" name="nfw_options[bf_request]" value="GETPOST"<?php checked($config['bf_request'], 'GETPOST') ?>>&nbsp;<?php _e('<code>GET</code> and <code>POST</code> requests attacks', 'nfwplus') ?></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row" class="row-med"><?php _e('Enable protection', 'nfwplus') ?></th>
					<td>
					<?php
						printf( __('For %1$s minutes, if more than %2$s %3$s requests within %4$s seconds.', 'nfwplus'),
							'<input maxlength="2" size="2" min="1" value="'. $config['bf_bantime'] .'" name="nfw_options[bf_bantime]" id="ban1" class="small-text" type="number" />',
							'<input maxlength="2" size="2" min="1" value="'. $config['bf_attempt'] .'" name="nfw_options[bf_attempt]" id="ban2" class="small-text" type="number" />', '<code id="get_post">'. $get_post .'</code>',
							'<input maxlength="2" size="2" min="1" value="'. $config['bf_maxtime'] .'" name="nfw_options[bf_maxtime]" id="ban3" class="small-text" type="number" />'
						);
					?>
					</td>
				</tr>
			</table>
		</div>

		<div id="bf_table_password"<?php echo $show_bf_table_password ? '' : ' style="display:none"' ?>>
			<table class="form-table">
				<tr>
					<th scope="row" class="row-med"><?php _e('HTTP authentication', 'nfwplus') ?></th>
					<td>
						<?php _e('User:', 'nfwplus') ?>&nbsp;<input maxlength="255" type="text" autocomplete="off" value="<?php echo htmlspecialchars( $config['auth_name'] ) ?>" name="nfw_options[auth_name]" onkeyup="nfwjs_auth_user_valid();" />
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php _e('Password:', 'nfwplus') ?>&nbsp;<input maxlength="255" type="password" autocomplete="off" value="" name="nfw_options[auth_pass]" />
						<br /><p class="description">&nbsp;<?php _e('The password length must be from 8 to 255 characters.', 'nfwplus') ?></p>
						<br /><br /><?php _e('Message (max. 1024 characters, HTML tags allowed)', 'nfwplus') ?>:<br />
						<textarea id="realm" name="nfw_options[auth_msgtxt]" class="large-text code" rows="5" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" oninput="nfwjs_realm_valid();"><?php echo htmlspecialchars( $config['auth_msgtxt'] ) ?></textarea>
					</td>
				</tr>
			</table>
		</div>


		<div id="bf_table_captcha"<?php echo $show_bf_table_captcha ? '' : ' style="display:none"' ?>>
			<table class="form-table">
				<tr>
					<th scope="row" class="row-med"><?php _e('Message', 'nfwplus') ?></th>
					<td>
						<input maxlength="255" class="large-text" type="text" autocomplete="off" value="<?php echo htmlspecialchars( $config['captcha_text'] ) ?>" name="nfw_options[captcha_text]" />
						<p class="description"><?php _e('This message will be displayed above the captcha. Max. 255 characters.', 'nfwplus') ?></p>
					</td>
				</tr>
			</table>
		</div>

	</div>

	<br />

	<div class="nfw-table" id="bf_table_extra"<?php echo $show_bf_table_extra ? '' : ' style="display:none"' ?>>
		<h3>&nbsp;&nbsp;<?php _e('Various options', 'nfwplus') ?></h3>
		<table class="form-table">
			<?php
			// Warn the user if Jetpack is installed
			if ( is_dir( WP_PLUGIN_DIR . '/jetpack' ) ) {
				$is_JetPack = '<p class="description">'. __('If you are using the Jetpack plugin, blocking access to the XML-RPC API may prevent it from working correctly.', 'nfwplus') .'</p>';
			} else {
				$is_JetPack = '';
			}
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Apply the protection to the <code>xmlrpc.php</code> script as well', 'nfwplus') ?></th>
				<td>
					<?php nfw_toggle_switch( 'info', 'nfw_options[bf_xmlrpc]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $config['bf_xmlrpc'] ) ?>
					<?php echo $is_JetPack; ?>
				</td>
			</tr>

			<tr>
				<th scope="row" class="row-med"><?php _e('Enable bot protection', 'nfwplus') ?></th>
				<td>
					<?php
					if ( $config['bf_allow_bot'] ) {
						$bot = 0;
					} else {
						$bot = 1;
					}

					nfw_toggle_switch( 'info', 'nfw_options[bf_allow_bot]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $bot ) ?>
				</td>
			</tr>

			<tr>
				<th scope="row" class="row-med"><?php _e('Write the incident to the server Authentication log', 'nfwplus') ?></th>
				<td>
					<?php
					// Ensure that openlog() and syslog() are not disabled:
					if (! function_exists('syslog') || ! function_exists('openlog') ) {
						$config['bf_authlog'] = 0;
						$bf_msg = __('Your server configuration is not compatible with that option.', 'nfwplus');
						$disabled = 1;
					} else {
						$bf_msg = __('The login protection must be set to "When under attack" in order to use this option.', 'nfwplus');
						if ( $config['bf_enable'] != 1 ) {
							$disabled = 1;
						} else {
							$disabled = 0;
						}
					}
					nfw_toggle_switch( 'info', 'nfw_options[bf_authlog]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $config['bf_authlog'], $disabled, false, 'nfw-authlog' ) ?>
					<p class="description"><?php echo $bf_msg ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="row-med"><?php _e('Disable NinjaFirewall\'s signature on the login protection page', 'nfwplus') ?></th>
				<td>
					<?php nfw_toggle_switch( 'info', 'nfw_options[bf_nosig]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $config['bf_nosig'] ) ?>
				</td>
			</tr>
		</table>
		<br />
	</div>

	<br />

	<div style="float:left;width:50%">
		<input id="save_login" class="button-primary" type="submit" name="Save" value="<?php _e('Save Login Protection', 'nfwplus') ?>" />
	</div>
	<div style="float:right;width:50%;text-align:right">
		<?php _e('See our benchmark and stress-test:', 'nfwplus') ?>
		<br />
		<a href="https://blog.nintechnet.com/wordpress-brute-force-attack-detection-plugins-comparison-2015/">Brute-force attack detection plugins comparison</a>
	</div>

	</form>
</div>

<?php

/* ================================================================== */

function nf_sub_loginprot_save() {

	/**
	 * Block immediately if user is not allowed.
	 */
	nf_not_allowed( 'block', __LINE__ );

	/**
	 * Ensure the directory is writable.
	 */
	if (! is_writable( NFW_LOG_DIR .'/nfwlog/cache') ) {
		return( sprintf(
			__('Error: %s directory is not writable. Please chmod it to 0777.', 'nfwplus'),
			'<code>'. esc_html( NFW_LOG_DIR ) .'/nfwlog/cache</code>'
		) );
	}

	$nfw_options = nfw_get_option('nfw_options');

	/**
	 * Get current configuration (mostly needed to retrieve 'auth_pass').
	 */
	$config = [];
	if ( file_exists( NFW_LOG_DIR .'/nfwlog/cache/login_protection.php') ) {
		$config = NinjaFirewall_bruteforce::read_config( NFW_LOG_DIR .'/nfwlog/cache/login_protection.php');
	}

	if ( empty( $_POST['ui_enabled'] ) ) {
		$_POST['nfw_options']['bf_enable'] = 0;
	}

	/**
	 * Enabled/disabled.
	 */
	if (! empty( $_POST['nfw_options']['bf_enable'] ) &&
		preg_match('/^[12]$/', $_POST['nfw_options']['bf_enable'] ) ) {

		$config['bf_enable'] = (int) $_POST['nfw_options']['bf_enable'];
	} else {
		$config['bf_enable'] = 0;
		// Clear session
		NinjaFirewall_session::delete('nfw_bfd');
	}

	/**
	 * Password or captcha.
	 */
	if (! empty( $_POST['nfw_options']['bf_type'] ) &&
		preg_match( '/^[01]$/', $_POST['nfw_options']['bf_type'] ) ) {

		$config['bf_type'] = (int) $_POST['nfw_options']['bf_type'];
	} else {
		$config['bf_type'] = 0;
	}

	/**
	 * HTTP method.
	 */
	if (! empty( $_POST['nfw_options']['bf_request'] ) &&
		preg_match('/^(GET|POST|GETPOST)$/', $_POST['nfw_options']['bf_request'] ) ) {

		$config['bf_request'] = $_POST['nfw_options']['bf_request'];
	} else {
		$config['bf_request'] = 'POST';
	}

	/**
	 * Ban period.
	 */
	if (! empty( $_POST['nfw_options']['bf_bantime'] ) &&
		preg_match('/^[1-9][0-9]?$/', $_POST['nfw_options']['bf_bantime'] ) ) {

		$config['bf_bantime'] = (int) $_POST['nfw_options']['bf_bantime'];
	} else {
		$config['bf_bantime'] = 5;
	}

	/**
	 * Threshold.
	 */
	if (! empty( $_POST['nfw_options']['bf_attempt'] ) &&
		preg_match('/^[1-9][0-9]?$/', $_POST['nfw_options']['bf_attempt'] ) ) {

		$config['bf_attempt'] = (int) $_POST['nfw_options']['bf_attempt'];
	} else {
		$config['bf_attempt'] = 8;
	}
	if (! empty( $_POST['nfw_options']['bf_maxtime'] ) &&
		preg_match('/^[1-9][0-9]?$/', $_POST['nfw_options']['bf_maxtime'] ) ) {

		$config['bf_maxtime'] = (int) $_POST['nfw_options']['bf_maxtime'];
	} else {
		$config['bf_maxtime'] = 15;
	}

	/**
	 * Apply to XMLRPC API as well.
	 */
	if ( empty( $_POST['nfw_options']['bf_xmlrpc'] ) ) {
		$config['bf_xmlrpc'] = 0;
	} else {
		$config['bf_xmlrpc'] = 1;
	}

	/**
	 * Syslog.
	 */
	if ( empty( $_POST['nfw_options']['bf_authlog'] ) ) {
		$config['bf_authlog'] = 0;
	} else {
		$config['bf_authlog'] = 1;
	}

	/**
	 * Bot detection.
	 */
	if ( empty( $_POST['nfw_options']['bf_allow_bot'] ) ) {
		$config['bf_allow_bot'] = 1;
	} else {
		$config['bf_allow_bot'] = 0;
	}

	/**
	 * NinjaFirewall's signature.
	 */
	if ( empty( $_POST['nfw_options']['bf_nosig'] ) ) {
		$config['bf_nosig'] = 0;
	} else {
		$config['bf_nosig'] = 1;
	}

	/**
	 * Authentication name.
	 */
	if ( empty( $_POST['nfw_options']['auth_name'] ) &&
		! empty( $config['bf_enable'] ) && empty( $config['bf_type'] ) ) {

		return( __('Error: please enter a user name for HTTP authentication.', 'nfwplus') );

	} elseif (! preg_match('`^[-/\\_.a-zA-Z0-9]{6,255}$`', $_POST['nfw_options']['auth_name'] ) &&
		! empty( $config['bf_enable'] ) && empty( $config['bf_type'] ) ) {

		return( __('Error: HTTP authentication user name is not valid.', 'nfwplus') );
	}
	$config['auth_name'] = $_POST['nfw_options']['auth_name'];

	/**
	 * Authentication password.
	 */
	if ( ! empty( $config['bf_enable'] ) && empty( $config['bf_type'] ) ) {

		if ( empty( $_POST['nfw_options']['auth_pass'] ) ) {

			return __('Error: please enter a password for HTTP authentication.', 'ninjafirewall');
		}
		if ( strlen( $_POST['nfw_options']['auth_pass'] ) < 8 ||
			strlen( $_POST['nfw_options']['auth_pass'] ) > 255 ) {

			return( __('Error: password must be from 8 to 255 characters.', 'ninjafirewall') );
		}

		// Use stripslashes() to prevent WordPress from escaping the password
		$config['auth_pass'] = sha1( stripslashes( $_POST['nfw_options']['auth_pass'] ) );
	}

	/**
	 * Authentication message.
	 */
	if ( empty( $_POST['nfw_options']['auth_msgtxt'] ) ) {
		$config['auth_msgtxt'] =  base64_encode( __('Access restricted', 'nfwplus') );
	} else {
		$config['auth_msgtxt'] = stripslashes( $_POST['nfw_options']['auth_msgtxt'] );
		if ( strlen( $config['auth_msgtxt'] ) > 1024 ) {
			$config['auth_msgtxt'] = mb_substr( $config['auth_msgtxt'], 0, 1024, 'utf-8');
		}
		$config['auth_msgtxt'] = base64_encode( $config['auth_msgtxt'] );
	}

	/**
	 * Captcha message.
	 */
	if ( empty( $_POST['nfw_options']['captcha_text'] ) ) {
		$config['captcha_text'] =  base64_encode(
			__('Type the characters you see in the picture below:', 'nfwplus')
		);
	} else {
		$config['captcha_text'] = stripslashes( $_POST['nfw_options']['captcha_text'] );
		if ( strlen( $config['captcha_text'] ) > 255 ) {
			$config['captcha_text'] = mb_substr( $config['captcha_text'], 0, 255, 'utf-8');
		}
		$config['captcha_text'] = base64_encode( htmlentities( $config['captcha_text'] ) );
	}

	// Generate a new random value.
	$config['bf_rand'] = mt_rand( 100000, 999999 );

	file_put_contents(
		NFW_LOG_DIR .'/nfwlog/cache/login_protection.php', "<?php exit; ?>\n". serialize( $config )
	);

	/**
	 * Whitelist the admin.
	 */
	if ( $config['bf_enable'] ) {
		NinjaFirewall_session::write( ['nfw_bfd' => $config['bf_rand'] ] );
	}

	/**
	 * Delete cached files.
	 */
	$dir	= NFW_LOG_DIR .'/nfwlog/cache';
	$list	= NinjaFirewall_helpers::nfw_glob( $dir, '^bf_', false );
	foreach( $list as $file ) {
		unlink( "$dir/$file" );
	}

}

/* ================================================================== */
// EOF

<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Block immediately if user is not allowed :
nf_not_allowed( 'block', __LINE__ );

$nfw_options = nfw_get_option( 'nfw_options' );

$tz = get_option('timezone_string');
if (! empty( $tz ) ) {
	date_default_timezone_set( $tz );
}

// Fetch the current month logname
$log_dir = NFW_LOG_DIR . '/nfwlog/';
$monthly_log = 'firewall_' . date( 'Y-m' ) . '.php';

// Create it, if it does not exist:
if ( ! is_file( $log_dir . $monthly_log ) ) {
	nf_sub_log_create( $log_dir . $monthly_log );
}

// Make sure the current monthly log and dir are writable
// or display a warning:
if (! is_writable( $log_dir . $monthly_log ) ) {
	$write_err = sprintf( __('the current month log (%s) is not writable. Please chmod it and its parent directory to 0777', 'nfwplus'), htmlspecialchars( $log_dir . $monthly_log ) );
} elseif (! is_writable( $log_dir ) ) {
	$write_err = sprintf( __('the log directory (%s) is not writable. Please chmod it to 0777', 'nfwplus'), htmlspecialchars($log_dir ) );
}

// Get the list of local logs, and remote sites
// if centralized logging is enabled :
global $available_logs, $available_urls;
$available_logs = nf_sub_log_find_local( $log_dir );
$available_urls = nf_sub_log_find_remote( $nfw_options );

// Options:
if (! empty( $_POST['nfw_act']) ) {

	// Save options:
	if ( $_POST['nfw_act'] == 'save_options') {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'log_save') ) {
			wp_nonce_ays('log_save');
		}
		nf_sub_log_save_options( $nfw_options );
		$ok_msg = __('Your changes have been saved.', 'nfwplus');

	// Save/delete public key:
	} elseif ( $_POST['nfw_act'] == 'pubkey') {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'clogs_pubkey') ) {
			wp_nonce_ays('clogs_pubkey');
		}
		// Clear the key ?
		if (isset( $_POST['delete_pubkey'] ) ) {
			$_POST['nfw_options']['clogs_pubkey'] = '';
			$ok_msg = __('Your public key has been deleted', 'nfwplus');
		} else {
			$ok_msg = __('Your public key has been saved', 'nfwplus');
		}
		nf_sub_log_save_pubkey( $nfw_options );
	}
	$nfw_options = nfw_get_option( 'nfw_options' );
}

// We will only display the last $max_lines lines,
// and will warn about it if the log is bigger :
if ( empty($nfw_options['log_line']) || ! preg_match('/^\d+$/', $nfw_options['log_line']) ) {
	$max_lines = $nfw_options['log_line'] = 1500;
} else {
	$max_lines = $nfw_options['log_line'];
}
/**
 * Sorting order.
 */
if ( empty( $nfw_options['log_sorting'] ) ) {
	$nfw_options['log_sorting'] = 0;
} else {
	$nfw_options['log_sorting'] = 1;
}

// View, delete, download etc actions:
if ( isset( $_GET['nfw_logname'] ) ) {
	if ( empty( $_GET['nfwnonce'] ) || ! wp_verify_nonce($_GET['nfwnonce'], 'log_select') ) {
		wp_nonce_ays('log_select');
	}

	// Delete selected log :
	if ( isset($_GET['nfw_delete']) ) {
		nf_sub_log_delete( $_GET['nfw_logname'], $log_dir, $monthly_log );
		$ok_msg = __('The selected log was deleted', 'nfwplus');

		// Delete its name from the list:
		unset( $available_logs[$_GET['nfw_logname']] );
		// Fall back to the current month log:
		$_GET['nfw_logname'] = $monthly_log;
		$available_logs[$_GET['nfw_logname']] = 1;
		krsort($available_logs);
	}

	// Remote log?
	if ( preg_match('/^\d+$/',  $_GET['nfw_logname'] ) &&
		! empty( $available_urls[$_GET['nfw_logname']] ) &&
		! empty( $nfw_options['clogs_enable'] ) ) {

		/**
		 * Fetch remote log.
		 */
		$data = nf_sub_log_read_remote(
			$available_urls[ $_GET['nfw_logname'] ],
			$nfw_options,
			$max_lines-1,
			$nfw_options['log_sorting']
		);

	// Local log?
	} else {
		$data = nf_sub_log_read_local(
			$_GET['nfw_logname'],
			$log_dir,
			$max_lines-1,
			$nfw_options['log_sorting']
		);
	}
}

if ( isset( $_GET['nfw_logname'] ) &&
	( ! empty( $available_logs[$_GET['nfw_logname']] ) || ! empty( $available_urls[$_GET['nfw_logname']] ) ) ) {

	$selected_log = $_GET['nfw_logname'];
} else {
	// Something wrong here, show the current month log instead:
	$selected_log = $monthly_log;
	$data = nf_sub_log_read_local(
		$monthly_log,
		$log_dir,
		$max_lines-1,
		$nfw_options['log_sorting']
	);
}

// Display all error and notice messages:
if ( ! empty( $write_err ) ) {
	echo '<div class="error notice is-dismissible"><p>' . __('Error', 'nfwplus') . ': ' . $write_err . '</p></div>';
}

if ( ! empty( $ok_msg ) ) {
	echo '<div class="updated notice is-dismissible"><p>' . $ok_msg . '</p></div>';
}
if ( isset( $data['lines'] ) && $data['lines'] > $max_lines ) {
	echo '<div class="notice-info notice is-dismissible"><p>' . __('Note', 'nfwplus') . ': ' . sprintf( __('your log has %s lines. I will display the last %s lines only.', 'nfwplus'), $data['lines'], $max_lines ) . '</p></div>';
}


// Add select box:
echo '<center>' . __('Viewing:', 'nfwplus') . ' <select onChange=\'window.location="?page=nfsublog&nfwnonce='. wp_create_nonce('log_select') .'&nfw_logname=" + this.value;\'>';
foreach ($available_logs as $log_name => $tmp) {
	echo '<option value="' . $log_name . '"';
	if ( $selected_log == $log_name ) {
		echo ' selected';
	}
	$log_stat = stat($log_dir . $log_name);
	echo '>' . str_replace('.php', '', $log_name) . ' (' . number_format_i18n($log_stat['size']) .' '. __('bytes', 'nfwplus') . ')</option>';
}

// Add centralized logging:
if (! empty( $nfw_options['clogs_urls'] )  && ! empty( $available_urls ) ) {
	echo '<optgroup label="=== '. __('Centralized Logging', 'nfwplus' ) .' ==="></optgroup>';
	foreach ($available_urls as $count => $url) {
		echo '<option value="' . $count . '"';
		if ( preg_match( '/^'. $selected_log .'$/', $count ) ) {
			echo ' selected';
		}
		echo '>' . htmlspecialchars( $url ) . '</option>';
	}
}

echo '</select>';
// Enable export/delete buttons only if we are viewing a local log
// and if it is not empty:
if ( isset( $data['type']) && $data['type'] == 'local' && ! empty( $data['lines'] ) ) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button-secondary" value="' .  __('Export', 'nfwplus') . '" onclick=\'window.location="?page=nfsublog&nfw_export=1&nfw_logname='. $selected_log .'&nfwnonce='. wp_create_nonce('log_select') .'"\'>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" value="' .  __('Delete', 'nfwplus') . '" onclick=\'if (confirm("'. __('Delete log?', 'nfwplus') .'")){window.location="?page=nfsublog&nfw_delete=1&nfw_logname='. $selected_log .'&nfwnonce='. wp_create_nonce('log_select') .'";}\'>';
} else {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button-secondary" disabled="disabled" value="' .  __('Export', 'nfwplus') . '" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" disabled="disabled" value="' .  __('Delete', 'nfwplus') . '"  />';
}
echo '</center>';

$levels = array( '', 'MEDIUM', 'HIGH', 'CRITICAL', 'ERROR', 'UPLOAD', 'INFO', 'DEBUG_ON' );
?>

<script>
// We remove the '&nfw_delete=1' query string because if the user reloaded the page,
// that would delete the log again:
var url = window.location.href;
window.history.replaceState({}, document.title, url.replace( /&nfw_delete=1/, '' ) );

var myToday = '<?php echo date( 'd/M/y') ?>';
var myArray = new Array();
<?php

$i = 0;
$logline = '';
$severity = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);

if ( isset( $data['log'] ) && is_array( $data['log'] ) ) {
	foreach ( $data['log'] as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-Fx, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:|b64:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = @pack('H*', $match[11]); }
			if ( $match[10] == 'b64:' ) { $match[11] = base64_decode( $match[11]); }
			$res = date( 'd/M/y H:i:s', $match[1] ) . '  ' . $match[3] . '  ' .
			str_pad( $levels[$match[5]], 8 , ' ', STR_PAD_RIGHT) .'  ' .
			str_pad( $match[4], 4 , ' ', STR_PAD_LEFT) . '  ' . str_pad( $match[6], 15, ' ', STR_PAD_RIGHT) . '  ' .
			$match[7] . ' ' . $match[8] . ' - ' .	$match[9] . ' - [' . $match[11] . '] - ' . $match[2];
			echo 'myArray[' . $i . '] = "' . rawurlencode($res) . '";' . "\n";
			$logline .= htmlentities( $res ."\n" );
			++$i;
			// Keep track of severity levels :
			$severity[$match[5]] = 1;
		}
	}
}
?>
</script>
<?php
if ( defined('NFW_TEXTAREA_HEIGHT') ) {
	$th = (int) NFW_TEXTAREA_HEIGHT;
} else {
	$th = '450';
}
?>
<form name="frmlog">
	<table class="form-table">
		<tr>
			<td width="100%">
				<textarea name="txtlog" class="large-text code" style="height:<?php echo $th; ?>px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
				if ( ! empty( $logline ) ) {
					echo '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST' . "\n";
					echo $logline;
				} else {
					if (! empty( $data['err_msg'] ) ) {
						echo "\n\n > {$data['err_msg']}";
					} else {
						echo "\n\n > " . __('The selected log is empty.', 'nfwplus');
					}
				}
				?></textarea>
				<br />
				<center>
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_today" onClick="nfwjs_filter_log();">
						<span style="display: inline-block"><?php _e('Today', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_crit" onClick="nfwjs_filter_log();"<?php checked($severity[3], 1) ?>>
						<span style="display: inline-block"><?php _e('Critical', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_high" onClick="nfwjs_filter_log();"<?php checked($severity[2], 1) ?>>
						<span style="display: inline-block"><?php _e('High', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_med" onClick="nfwjs_filter_log();"<?php checked($severity[1], 1) ?>>
						<span style="display: inline-block"><?php _e('Medium', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_upl" onClick="nfwjs_filter_log();"<?php checked($severity[5], 1) ?>>
						<span style="display: inline-block"><?php _e('Upload', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_nfo" onClick="nfwjs_filter_log();"<?php checked($severity[6], 1) ?>>
						<span style="display: inline-block"><?php _e('Info', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
					<label>
						<input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_dbg" onClick="nfwjs_filter_log();"<?php checked($severity[7], 1) ?>>
						<span style="display: inline-block"><?php _e('Debug', 'nfwplus') ?></span>
					</label>&nbsp;&nbsp;
				</center>
				<br />
				<center>
					<?php _e('IP address:', 'nfwplus' )?> <input type="text" name="nfw-ajax-ip" id="nfw-ajax-ip-id" placeholder="<?php _e('e.g., 1.2.3.4', 'nfwplus') ?>" value="" />&nbsp; &nbsp;<select name="nfw-ajax-ip-list"><option value="blacklist"><?php _e('Add IP to blacklist', 'nfwplus') ?></option><option value="whitelist"><?php _e('Add IP to whitelist', 'nfwplus') ?></option></select>&nbsp; &nbsp;<input id="ajax-ip-button" type="button" name="add" value="&nbsp;&nbsp;&nbsp;<?php _e('Add', 'nfwplus') ?>&nbsp;&nbsp;&nbsp;" class="button-secondary" onClick="nfw_ajax_ip('<?php echo wp_create_nonce('nfw_ajax_ip') ?>')" />
				</center>
				<center><label id="nfw-ajax-label" style="color:green;visibility:hidden"><?php _e('The address was added to your IP Access Control list.', 'nfwplus') ?></label></center>
			</td>
		</tr>
	</table>
</form>
<?php

// Log options:
nf_sub_log_options($max_lines);

// =====================================================================
function nf_sub_log_options($max_lines) {

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( empty( $nfw_options['logging'] ) ) {
		$nfw_options['logging'] = 0;
	} else {
		$nfw_options['logging'] = 1;
	}
	if ( empty($nfw_options['log_rotate']) ) {
		$nfw_options['log_rotate'] = 0;
		$nfw_options['log_maxsize'] = 2;
	} else {
		// Default : rotate at the end of the month OR if bigger than 5MB
		$nfw_options['log_rotate'] = 1;
		if ( empty($nfw_options['log_maxsize']) || ! preg_match('/^\d+$/', $nfw_options['log_maxsize']) ) {
			$nfw_options['log_maxsize'] = 2;
		} else {
			$nfw_options['log_maxsize'] = intval( $nfw_options['log_maxsize'] / 1048576);
			if (empty( $nfw_options['log_maxsize']) ) {
				$nfw_options['log_maxsize'] = 2;
			}
		}
	}
	if ( empty( $nfw_options['syslog'] ) ) {
		$nfw_options['syslog'] = 0;
	} else {
		$nfw_options['syslog'] = 1;
	}
	if ( empty( $nfw_options['auto_del_log'] ) ) {
		$nfw_options['auto_del_log'] = 0;
	}
	if ( empty( $nfw_options['log_sorting'] ) ) {
		$nfw_options['log_sorting'] = 0;
	} else {
		$nfw_options['log_sorting'] = 1;
	}
?>
<h3><?php _e('Log Options', 'nfwplus') ?></h3>
<form method="post" action="?page=nfsublog"><?php wp_nonce_field('log_save', 'nfwnonce', 0); ?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php _e('Enable firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'danger', 'nfw_options[logging]', __('Enabled', 'nfwplus'), __('Disabled', 'nfwplus'), 'large', $nfw_options['logging'] ) ?>
			</td>
		</tr>
		<tr>
			<th scope="row" class="row-med"><?php _e('Auto-rotate log', 'nfwplus') ?></th>
			<td>
			<p><label><input type="radio" name="nfw_options[log_rotate]" value="1"<?php checked($nfw_options['log_rotate'], 1) ?>>&nbsp;<?php printf (__('1st day of the month, or if bigger than %s MB', 'nfwplus'), '</label><input id="sizeid" name="nfw_options[log_maxsize]" size="2" min="1" maxlength="2" value="'. $nfw_options['log_maxsize'] .'" class="small-text" type="number" />' ) ?> (<?php _e('default', 'nfwplus') ?>)</p>
			<p><label><input type="radio" name="nfw_options[log_rotate]" value="0"<?php checked($nfw_options['log_rotate'], 0) ?>>&nbsp;<?php _e('1st day of the month, regardless of its size', 'nfwplus') ?></label></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="row-med"><?php _e('Auto-delete log', 'nfwplus') ?></th>
			<td>
			<?php
				$input = '<input type="number" name="nfw_options[auto_del_log]" min="0" value="'. (int) $nfw_options['auto_del_log'] .'" class="small-text" />';
				printf( __('Automatically delete logs older than %s days', 'nfwplus' ), $input );
			?>
			<p class="description"><?php _e('Set this option to 0 to disable it.', 'nfwplus' ) ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Sorting', 'nfwplus') ?></th>
			<td>
			<p><label><input type="radio" name="nfw_options[log_sorting]" value="0"<?php
				checked($nfw_options['log_sorting'], 0 )
			?>>&nbsp;<?php esc_html_e('Ascending (oldest entries first)', 'nfwplus') ?></label></p>
			<p><label><input type="radio" name="nfw_options[log_sorting]" value="1"<?php
				checked($nfw_options['log_sorting'], 1 )
				?>>&nbsp;<?php esc_html_e('Descending (newest entries first)', 'nfwplus') ?></label></p>
			</td>
		</tr>

		<tr>
			<th scope="row" class="row-med"><?php _e('Show the most recent', 'nfwplus') ?></th>
			<td>
				<p><input name="nfw_options[log_line]" step="50" min="50" value="<?php echo $max_lines ?>" class="small-text" type="number"> <?php _e('lines', 'nfwplus') ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="row-med"><?php _e('Write events to the Syslog server too', 'nfwplus') ?></th>
			<td>
				<?php
				// Ensure that openlog() and syslog() are not disabled:
				if (! function_exists('syslog') || ! function_exists('openlog') ) {
					$nfw_options['syslog'] = 0;
					$syslog_msg = __('Your server configuration is not compatible with this option.', 'nfwplus');
					$disabled = 1;
				} else {
					$syslog_msg = __('See contextual help before enabling this option.', 'nfwplus');
					$disabled = 0;
				}
				nfw_toggle_switch( 'info', 'nfw_options[syslog]', __('Yes', 'nfwplus'), __('No', 'nfwplus'), 'small', $nfw_options['syslog'], $disabled );
				?>
				<p class="description"><?php echo $syslog_msg ?></p>
			</td>
		</tr>
	</table>
	<br />
	<input type="hidden" name="nfw_act" value="save_options" />
	<input type="submit" class="button-primary" value="<?php _e('Save Log Options', 'nfwplus') ?>" name="savelog" />
	<input type="hidden" name="tab" value="firewalllog" />
</form>
<?php

	// If this website is used as the main site for centralized logging,
	// we don't display the form that is used by remote websites:
	if (! empty( $nfw_options['clogs_enable'] ) ) {
		return;
	}

?>
<a name="clogs"></a>
<form name="frmlog2" method="post" action="?page=nfsublog" onsubmit="return nfwjs_check_key();">
	<?php

	wp_nonce_field('clogs_pubkey', 'nfwnonce', 0);
	if ( empty( $nfw_options['clogs_pubkey'] ) || ! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	}

	?>
	<br />
	<br />

	<a name="clogs"></a>
	<h3><?php _e('Centralized Logging', 'nfwplus') ?></h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php _e('Enter your public key (optional)', 'nfwplus') ?></th>
			<td>
				<input id="clogs-pubkey" class="large-text" type="text" maxlength="80" name="nfw_options[clogs_pubkey]" value="<?php echo htmlspecialchars( $nfw_options['clogs_pubkey'] ) ?>" autocomplete="off" />
				<p class="description"><?php printf( __('<a href="%s">Consult our blog</a> if you want to enable centralized logging.', 'nfwplus'), 'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/' ) ?></p>
			</td>
		</tr>
	</table>

	<br />
	<input type="hidden" name="nfw_act" value="pubkey" />
	<input class="button-primary" name="save_pubkey" onclick="what=0" value="<?php _e('Save Public Key', 'nfwplus') ?>" type="submit" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button-secondary" name="delete_pubkey" onclick="what=1" value="<?php _e('Delete Public Key', 'nfwplus') ?>" type="submit"<?php disabled($nfw_options['clogs_pubkey'], '' ) ?> />
	<input type="hidden" name="tab" value="firewalllog" />

</form>
<?php
}

// =====================================================================

function nf_sub_log_create( $log ) {

	// Create an empty log :
	file_put_contents( $log, "<?php exit; ?>\n" );

}

// =====================================================================

function nf_sub_log_delete( $log, $log_dir, $monthly_log ) {

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', trim( $log ) ) ) {
		wp_nonce_ays('log_select');
	}
	if (! is_file( $log_dir . $log ) ) {
		wp_nonce_ays('log_select');
	}
	// Delete the requested log:
	@unlink($log_dir . $log);

	/**
	 * Write the event to the current log.
	 */
	$current_user = wp_get_current_user();
	$nfw_options = nfw_get_option('nfw_options');

	NinjaFirewall_log::write(
		'Firewall log deleted by admin',
		"user: ". htmlspecialchars( $current_user->user_login ) . ", log: $log",
		NFWLOG_INFO, 0, $nfw_options, NFW_LOG_DIR .'/nfwlog'
	);

}

// =====================================================================

function nf_sub_log_find_local( $log_dir ) {

	// Find all available logs :
	$available_logs = array();
	if ( is_dir( $log_dir ) ) {
		if ( $dh = opendir( $log_dir ) ) {
			while ( ($file = readdir($dh) ) !== false ) {
				if (preg_match( '/^(firewall_(\d{4})-(\d\d)(?:\.\d+)?\.php)$/', $file, $match ) ) {
					$available_logs[$match[1]] = 1;
				}
			}
			closedir($dh);
		}
	}
	krsort($available_logs);

	return $available_logs;
}

// =====================================================================

function nf_sub_log_find_remote( $nfw_options ) {

	// Centralized logging; build URLs list:

	// Make sure it is enabled:
	if ( empty( $nfw_options['clogs_enable'] ) ) {
		return;
	}

	if (! empty( $nfw_options['clogs_urls'] ) ) {
		return unserialize( $nfw_options['clogs_urls'] );
	}

}

// =====================================================================

function nf_sub_log_save_options( $nfw_options ) {

	if (! empty( $_POST['savelog'] ) ) {
		if ( empty( $_POST['nfw_options']['logging'] ) ) {
			$nfw_options['logging'] = 0;
		} else {
			$nfw_options['logging'] = 1;
		}
		if ( empty($_POST['nfw_options']['log_rotate']) ) {
			$nfw_options['log_rotate'] = 0;
			$nfw_options['log_maxsize'] = 2 * 1048576;
		} else {
			$nfw_options['log_rotate'] = 1;
			if ( empty($_POST['nfw_options']['log_maxsize']) || ! preg_match('/^([1-9]?[0-9])$/', $_POST['nfw_options']['log_maxsize']) ) {
				$nfw_options['log_maxsize'] = 2 * 1048576;
			} else {
				$nfw_options['log_maxsize'] = $_POST['nfw_options']['log_maxsize'] * 1048576;
			}
		}

		if ( empty( $_POST['nfw_options']['log_sorting'] ) ) {
			$nfw_options['log_sorting'] = 0;
		} else {
			$nfw_options['log_sorting'] = 1;
		}

		if ( empty( $_POST['nfw_options']['auto_del_log'] ) || ! preg_match('/^\d+$/',  $_POST['nfw_options']['auto_del_log'] ) ) {
			$nfw_options['auto_del_log'] = 0;
		} else {
			$nfw_options['auto_del_log'] = (int) $_POST['nfw_options']['auto_del_log'];
		}
		// We need to keep the log for more than 24 hours otherwise
		// the daily report will be empty
		if ( $nfw_options['auto_del_log'] == 1 ) {
			$nfw_options['auto_del_log'] = 2;
		}

		if ( empty($_POST['nfw_options']['log_line']) || ! preg_match('/^\d+$/', $_POST['nfw_options']['log_line']) || $_POST['nfw_options']['log_line'] < 50  ) {
			$nfw_options['log_line'] = 1500;
		} else {
			$nfw_options['log_line'] = $_POST['nfw_options']['log_line'];
		}
		if (empty( $_POST['nfw_options']['syslog']) ) {
			$nfw_options['syslog'] = 0;
			// Remove the flag:
			if ( is_file( NFW_LOG_DIR . '/nfwlog/cache/syslog_enabled.php' ) ) {
				unlink( NFW_LOG_DIR . '/nfwlog/cache/syslog_enabled.php' );
			}
		} else {
			$nfw_options['syslog'] = 1;

			// Create a flag that will be used by the login protection against bots:
			file_put_contents( NFW_LOG_DIR . '/nfwlog/cache/syslog_enabled.php', 'Do not delete!' );
		}

		nfw_update_option( 'nfw_options', $nfw_options);
	}
}

// =====================================================================

function nf_sub_log_save_pubkey( $nfw_options ) {

	if ( empty( $_POST['nfw_options']['clogs_pubkey'] ) ||
		! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $_POST['nfw_options']['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	} else {
		$nfw_options['clogs_pubkey'] = $_POST['nfw_options']['clogs_pubkey'];
	}

	nfw_update_option( 'nfw_options', $nfw_options);

}

// =====================================================================

function nf_sub_log_read_local( $log, $log_dir, $max_lines, $sorting ) {

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', trim( $log ) ) ) {
		wp_nonce_ays('log_select');
	}

	$data = array();
	$data['type'] = 'local';

	if (! is_file( $log_dir . $log ) ) {
		$data['err_msg'] = __('The requested log does not exist.', 'nfwplus');
		return $data;
	}

	$data['log'] = file( $log_dir . $log, FILE_SKIP_EMPTY_LINES );

	if ( $data['log'] === false ) {
		$data['err_msg'] = __('Unable to open the log for read operation.', 'nfwplus');
		return $data;
	}
	if ( strpos( $data['log'][0], '<?php' ) !== FALSE ) {
		unset( $data['log'][0] );
	}
	// Keep only the last $max_lines:
	$data['lines'] = count( $data['log'] );
	if ( $max_lines < $data['lines'] ) {
		for ($i = 0; $i < ( $data['lines'] - $max_lines); ++$i ) {
			unset( $data['log'][$i] ) ;
		}
	}

	if ( $data['lines'] == 0 ) {
		$data['err_msg'] = __('The selected log is empty.', 'nfwplus');
	}

	if ( $sorting ) {
		/**
		 * Sort log in descending order.
		 */
		arsort( $data['log'] );
	}

	return $data;

}

// =====================================================================

function nf_sub_log_read_remote( $url, $nfw_options, $max_lines, $sorting ) {

	// Connect to the remote website:
	$response = wp_safe_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 30,
			'redirection' => 3,
			'body' => array( 'clogs_req' => base64_decode( $nfw_options['clogs_seckey'] ) )
		)
	);

	$data = array();
	$data['type'] = 'remote';

	if ( is_wp_error( $response ) ) {
		$data['err_msg'] = $response->get_error_message();
		return $data;
	}

	$data = explode ( ':~*~:', $response['body'] , 2 );

	// HTTP errors:
	if ( $response['response']['code'] == 406 ) {
		$data['err_msg'] = __('The remote server rejected your request. Make sure that you uploaded the correct public key.', 'nfwplus');
	} elseif ( preg_match( '/^[45]\d\d$/', $response['response']['code'] ) ) {
		$data['err_msg'] = sprintf( __('The remote server returned the following HTTP error: %s', 'nfwplus'), htmlspecialchars( $response['response']['code'] . ' ' . $response['response']['message'] ) );

	// Check the received data:
	} elseif ( $data[0] == 1 ) {
		$data['err_msg'] = __('The requested log does not exist on the remote website.', 'nfwplus');

	} elseif ( $data[0] == 2 ) {
		$data['err_msg'] = __('Unable to open the log for read operation.', 'nfwplus');

	} elseif ( ! isset( $data[1] ) ) {
		$data['err_msg'] = __('The remote website did not return the expected response.', 'nfwplus');

	} else {
		// Decode and clean it up:
		$data['log'] = json_decode( base64_decode( $data[1] ) );
		if ( strpos( $data['log'][0], '<?php' ) !== FALSE ) {
			unset( $data['log'][0] );
		}
		// Keep only the last $max_lines:
		$data['lines'] = count( $data['log'] );
		if ( $max_lines < $data['lines'] ) {
			for ($i = 0; $i < ( $data['lines'] - $max_lines); ++$i ) {
				unset( $data['log'][$i] ) ;
			}
		}
	}

	if ( $sorting ) {
		/**
		 * Sort log in descending order.
		 */
		arsort( $data['log'] );
	}

	return $data;

}

// =====================================================================
// EOF

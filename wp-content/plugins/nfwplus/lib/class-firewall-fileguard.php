<?php
/*
 +=====================================================================+
 |    _   _ _        _       _____ _                        _ _        |
 |   | \ | (_)_ __  (_) __ _|  ___(_)_ __ _____      ____ _| | |       |
 |   |  \| | | '_ \ | |/ _` | |_  | | '__/ _ \ \ /\ / / _` | | |       |
 |   | |\  | | | | || | (_| |  _| | | | |  __/\ V  V / (_| | | |       |
 |   |_| \_|_|_| |_|/ |\__,_|_|   |_|_|  \___| \_/\_/ \__,_|_|_|       |
 |                |__/                                                 |
 |  (c) NinTechNet Limited ~ https://nintechnet.com/                   |
 +=====================================================================+
*/

if ( class_exists('NinjaFirewall_fileguard') ) {
	return;
}


class NinjaFirewall_fileguard {

	static public function run( $nfw_ ) {
		/**
		 * Look for exclusion.
		 */
		if ( empty( $nfw_['nfw_options']['fg_exclude'] ) ||
			! @preg_match( "`{$nfw_['nfw_options']['fg_exclude']}`", $_SERVER['SCRIPT_FILENAME'] ) ) {
			/**
			 * Stat() the requested script.
			 */
			if ( $nfw_['nfw_options']['fg_stat'] = stat( $_SERVER['SCRIPT_FILENAME'] ) ) {
				/**
				 * Was it created/modified lately ?
				 */
				if ( time() - $nfw_['nfw_options']['fg_mtime'] * 3660 < $nfw_['nfw_options']['fg_stat']['ctime'] ) {
					/**
					 * Did we check it already ?
					 */
					if (! is_file("{$nfw_['log_dir']}/cache/fg_{$nfw_['nfw_options']['fg_stat']['ino']}.php") ) {
						/**
						 * Log it.
						 */
						NinjaFirewall_log::write(
							"Access to a script modified/created less than {$nfw_['nfw_options']['fg_mtime']} hour(s) ago",
							$_SERVER['SCRIPT_FILENAME'],
							NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
						);
						/**
						 * Send the notification.
						 */
						$server_name = NinjaFirewall_data::sanitise_string_fn( $_SERVER['SERVER_NAME'] );
						$headers = 'From: "NinjaFirewall" <postmaster@'. "$server_name>\r\n";
						$subject = [];
						$content = [ $nfw_['nfw_options']['fg_mtime'], $server_name,
										NFW_REMOTE_ADDR, $_SERVER['SCRIPT_FILENAME'], $_SERVER['REQUEST_URI'],
										date('F j, Y @ H:i:s T', $nfw_['nfw_options']['fg_stat']['ctime'] ) ];

						require_once __DIR__ .'/class_mail.php';
						NinjaFirewall_mail::PHPsend(
							$nfw_['nfw_options']['alert_email'], 'fileguard',
							$subject, $content, $nfw_['log_dir'], $headers
						);
						/**
						 * Remember it so that we don't spam the admin each time the script is requested.
						 */
						touch( "{$nfw_['log_dir']}/cache/fg_{$nfw_['nfw_options']['fg_stat']['ino']}.php" );
					}
					/**
					 * Undocumented: if 'NFW_FG_BLOCK' is defined in the .htninja, we block the request.
					 */
					if ( defined('NFW_FG_BLOCK') ) {

						$nfw_['incidentID'] = NinjaFirewall_log::write(
							'File Guard: blocked request',
							$_SERVER['SCRIPT_FILENAME'],
							NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
						);
						nfw_block();
					}
				}
			}
		}
	}

}
// =====================================================================
// EOF

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

if ( class_exists('NinjaFirewall_centralisedlogging') ) {
	return;
}

class NinjaFirewall_centralisedlogging {


	/**
	 * Return the requested local log.
	 */
	public static function run( $nfw ) {

		$pubkey = explode(':', $nfw['nfw_options']['clogs_pubkey'], 2 );

		if ( isset( $pubkey[1]) &&  $pubkey[1] != '*') {

			if ( NFW_REMOTE_ADDR != $pubkey[1] ) {

				NinjaFirewall_log::write(
				'Centralized logging: IP not allowed',
					NFW_REMOTE_ADDR,
					NFWLOG_INFO, 0, $nfw['nfw_options'], $nfw['log_dir']
				);
				self::die();
			}
		}

		/**
		 * Backward compatibility: older versions of NinjaFirewall are using SHA1.
		 */
		if ( strlen( $pubkey[0] ) == 40 ) {
			$res = hash_equals( $pubkey[0], sha1( $_POST['clogs_req'] ) );
		} else {
			$res = hash_equals( $pubkey[0], hash('sha256', $_POST['clogs_req'] ) );
		}
		if ( empty( $pubkey[0] ) || $res === false ) {

			NinjaFirewall_log::write(
				'Centralized logging: public key rejected',
				NFW_REMOTE_ADDR,
				NFWLOG_INFO, 0, $nfw['nfw_options'], $nfw['log_dir']
			);
			self::die();
		}

		$cur_month = date('Y-m');
		$log_file = "{$nfw['log_dir']}/firewall_{$cur_month}.php";

		if (! is_file( $log_file ) ) {
			exit('1:');
		}

		$data = file( $log_file, FILE_SKIP_EMPTY_LINES );
		if ( $data === false ) {
			exit('2:');
		}

		echo '0:~*~:' . base64_encode( json_encode( $data ) );
		exit;
	}


	/**
	 * Exit.
	 */
	private static function die() {

		header('HTTP/1.1 406 Not Acceptable');
		header('Status: 406 Not Acceptable');
		exit;
	}

}
// =====================================================================
// EOF

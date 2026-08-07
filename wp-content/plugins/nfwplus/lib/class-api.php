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

if ( class_exists('NinjaFirewall_api') ) {
	return;
}


class NinjaFirewall_api {

	private static $security_update_uri = 'https://api.nintechnet.com/ninjafirewall/security-update';


	/**
	 * Download security updates from the remote API (WordPress, plugins and themes).
	 */
	public static function download_security_updates() {

		global $wp_version;
		$res = wp_remote_get(
			self::$security_update_uri,
			[
				'timeout' => 20,
				'httpversion' => '1.1' ,
				'user-agent' => 'Mozilla/5.0 (compatible; NinjaFirewall/'.
										NFW_ENGINE_VERSION ."; WordPress/$wp_version)",
				'sslverify' => true
			]
		);

		if ( is_wp_error( $res ) ) {
			nfw_log_error(
				__('Cannot download security rules: connection error. Will try again later',
				'nfwplus')
			);
			return false;
		}

		if ( $res['response']['code'] != 200 ) {
			nfw_log_error(
				sprintf(
					__('Cannot download security rules: HTTP response error %s. Will try again later',
					'nfwplus'),
					$res['response']['code']
				)
			);
			return false;
		}

		/**
		 * Decode and return the content.
		 */
		$list = json_decode( $res['body'], true );
		if ( $list === null ) {
			return false;
		}

		return $list;
	}

}
// =====================================================================
// EOF

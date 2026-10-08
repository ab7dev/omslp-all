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

if ( class_exists('NinjaFirewall_helpers') ) {
	return;
}

class NinjaFirewall_helpers {


	/**
	 * Retrieve and return matching files from a directory.
	 * Replacement for the PHP glob() function to make file search compatible with remote files.
	 */
	public static function nfw_glob( $directory, $regex, $pathname = false, $sortname = true ) {

		$list = [];

		if (! is_dir( $directory ) ) {
			return $list;
		}

		foreach ( new DirectoryIterator( $directory ) as $finfo ) {
			if (! $finfo->isDot() && preg_match("`$regex`", $finfo->getFilename() ) ) {
				if ( $pathname ) {
					$list[] = $finfo->getPathname();
				} else {
					$list[] = $finfo->getFilename();
				}
			}
		}
		if ( $sortname === true ) {
			asort( $list );
		}

		return $list;
	}


	/**
	 * Retrieve and return matching files from a directory, recursively.
	 * Replacement for the PHP glob() function to make file search compatible with remote files.
	 */
	public static function nfw_glob_recursive( $directory, $regex, $pathname = false ) {

		$list = [];

		if (! is_dir( $directory ) ) {
			return $list;
		}

		$dir_iterator = new RecursiveDirectoryIterator( $directory );
		$iterator = new RecursiveIteratorIterator( $dir_iterator );

		foreach ( $iterator as $finfo ) {
			if ( preg_match("`$regex`", $finfo->getFilename() ) ) {
				if ( $pathname ) {
					$list[] = $finfo->getPathname();
				} else {
					$list[] = $finfo->getFilename();
				}
			}
		}
		return $list;
	}


	/**
	 * Verify the digital signature of a payload.
	 */
	public static function verify_signature( $data, $base64_signature, $log = '') {

		if (! function_exists('openssl_verify') || ! defined('OPENSSL_ALGO_SHA256') ) {
			if ( $log ) {
				nf_sub_updates_log(
					$log,
					__('Error: OpenSSL is required for the digital signature verification',
					'nfwplus')
				);
			}
			return 0;
		}

		$public_key = rtrim( file_get_contents( __DIR__ .'/sign.pub') );
		$pubkeyid   = openssl_pkey_get_public( $public_key );
		$verify     = openssl_verify(
			$data, base64_decode( $base64_signature ), $pubkeyid, OPENSSL_ALGO_SHA256
		);

		if ( $verify != 1 ) {
			if ( $log ) {
				nf_sub_updates_log(
					$log,
					__('Error: The digital signature is not correct. Data may be corrupt',
					'nfwplus'),
				);
			}
			return 0;
		}
		return 1;
	}

}

// =====================================================================
// EOF

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

if ( class_exists('NinjaFirewall_IP') ) {
	return;
}

class NinjaFirewall_IP {

	/**
	 * Return an IP address and its type (IPv4/IPv6, public/private).
	 */
	public static function check_ip( $nfw_options ) {
		/**
		 * It could have been defined by the firewall, if already loaded.
		 */
		if ( defined('NFW_REMOTE_ADDR') ) {
			return;
		}

		/**
		 * Some command line cron jobs may return an 'Undefined array key REMOTE_ADDR' warning.
		 */
		if (! isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}

		/**
		 * The user selected REMOTE_ADDR.
		 */
		if ( empty( $nfw_options['ac_ip'] ) || $nfw_options['ac_ip'] == 1 ) {
			if ( strpos( $_SERVER['REMOTE_ADDR'], ',') !== false ) {
				$matches = array_map('trim', @ explode(',', $_SERVER['REMOTE_ADDR'] ) );
				foreach( $matches as $match ) {
					if ( filter_var( $match, FILTER_VALIDATE_IP ) )  {
						define('NFW_REMOTE_ADDR', $match );
						/**
						 * Adjust it, so that WP and other plugins can use it too
						 */
						$_SERVER['REMOTE_ADDR'] = $match;
						break;
					}
				}
			}
		/**
		 * The user selected HTTP_X_FORWARDED_FOR.
		 */
		} elseif ( $nfw_options['ac_ip'] == 2 && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$matches = array_map('trim', @ explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			foreach( $matches as $match ) {
				if ( filter_var( $match, FILTER_VALIDATE_IP ) )  {
					define('NFW_REMOTE_ADDR', $match );
					break;
				}
			}
		/**
		 * The user selected a custom environment variable.
		 */
		} elseif ( $nfw_options['ac_ip'] == 3 && ! empty( $nfw_options['ac_ip_2'] ) &&
			! empty( $_SERVER[ $nfw_options['ac_ip_2'] ] ) ) {

			$matches = array_map('trim', @ explode(',', $_SERVER[ $nfw_options['ac_ip_2'] ] ) );
			foreach( $matches as $match ) {
				if ( filter_var( $match, FILTER_VALIDATE_IP ) )  {
					define('NFW_REMOTE_ADDR', $match );
					break;
				}
			}
		}

		if (! defined('NFW_REMOTE_ADDR') ) {
			/**
			 * Last hope.
			 */
			define('NFW_REMOTE_ADDR', htmlspecialchars( $_SERVER['REMOTE_ADDR'] ) );
		}

		/**
		 * Check if it's an IPv6 or IPv4.
		 */
		if ( filter_var( NFW_REMOTE_ADDR, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			define('NFW_REMOTE_ADDR_IPV6', true );
		} else {
			define('NFW_REMOTE_ADDR_IPV6', false );
		}

		/**
		 * Check if it's a private address.
		 */
		if (filter_var( NFW_REMOTE_ADDR, FILTER_VALIDATE_IP,
			FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {

			define('NFW_REMOTE_ADDR_PRIVATE', false );
		} else {
			define('NFW_REMOTE_ADDR_PRIVATE', true );
		}
	}

	/**
	 * Anonymize an IP address by hidding it last 3 characters, unless it's a prive IP.
	 */
	public static function anonymize_ip( $ip, $nfw_options ) {

		if (! empty( $nfw_options['anon_ip'] ) && NFW_REMOTE_ADDR_PRIVATE === false ) {

			return substr( $ip, 0, -3 ) .'xxx';
		}

		return $ip;
	}


	/**
	 * IP Access Control methods (whitelist & blacklist).
	 */
	public static function compare_ip( $NFW_REMOTE_ADDR, $nfw_ip ) {

		$ip_cidr = explode('/', $nfw_ip );
		/**
		 * Do we have a CIDR ?
		 */
		if ( isset( $ip_cidr[1] ) ) {
			/**
			 * Quick IPv6 check
			 */
			if ( strpos( $ip_cidr[0], ':') !== false ) {
				/**
				 * Compare IPv6
				 */
				return self::ipv6_range( $NFW_REMOTE_ADDR, $ip_cidr );
			} else {
				/**
				 * Compare IPv4
				 */
				return self::ipv4_range( $NFW_REMOTE_ADDR, $ip_cidr );
			}
		/**
		 * We're just searching for a (sub)string
		 */
		} else {
			return strpos( $NFW_REMOTE_ADDR, $nfw_ip );
		}

	}


	public static function ipv4_range( $remote_addr, $ip_cidr = [] ) {

		$ip         = ip2long( $remote_addr );
		$ip_cidr[0] = ip2long( $ip_cidr[0] );
		$mask       = -1 << ( 32 - $ip_cidr[1] );
		$ip_cidr[0] &= $mask;
		return ( $ip & $mask ) == $ip_cidr[0];
	}


	public static function ipv6_range( $remote_addr, $ip_cidr = [] ) {

		$remote_addr = inet_pton( $remote_addr );
		$ip_cidr[0]  = inet_pton( $ip_cidr[0] );
		$addr        = str_repeat('f', intval( $ip_cidr[1] / 4 ) );
		switch ( $ip_cidr[1] % 4 ) {
			case 0:
			break;
			case 1:
			$addr .= '8';
			break;
			case 2:
			$addr .= 'c';
			break;
			case 3:
			$addr .= 'e';
			break;
		}
		$addr = str_pad( $addr, 32, '0');
		return ( $remote_addr & pack("H*" , $addr ) ) == $ip_cidr[0];
	}

}

// =====================================================================
// EOF

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
if ( strpos($_SERVER['SCRIPT_NAME'], '/nfwlog/') !== FALSE ||
	strpos($_SERVER['SCRIPT_NAME'], '/nfwplus/') !== FALSE ) {
	die('Forbidden');
}
if ( defined('NFW_STATUS') ) { return; }
if ( defined('WP_CLI') && WP_CLI && PHP_SAPI === 'cli' ) {
	if (! defined('NFW_UWL') ) {
		define('NFW_UWL', true);
	}
	return;
}

$nfw_ = [];
// Used for benchmarks purpose:
$nfw_['fw_starttime'] = nfw_fc_metrics('start');

/**
 * Required classes and constants (some classes may have been already
 * instantiated by the firewall when running in Full WAF mode).
 */
if ( ! defined('NFWLOG_DEBUG') ) {
	define('NFWLOG_MEDIUM', 1);
	define('NFWLOG_HIGH', 2);
	define('NFWLOG_CRITICAL', 3);
	define('NFWLOG_POSTDETECT', 4);
	define('NFWLOG_UPLOAD', 5);
	define('NFWLOG_INFO', 6);
	define('NFWLOG_DEBUG', 7);
}
require_once __DIR__ .'/class-ip.php';
require_once __DIR__ .'/class-firewall-log.php';
require_once __DIR__ .'/class-firewall-data.php';
require_once __DIR__ .'/class-firewall-config.php';
require_once __DIR__ .'/class-firewall-bruteforce.php';
/**
 * Optional NinjaFirewall configuration file.
 * See https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/
 */
if ( @is_file( $nfw_['file'] = $_SERVER['DOCUMENT_ROOT'] .'/.htninja') ||
	@is_file( $nfw_['file'] = dirname( $_SERVER['DOCUMENT_ROOT'] ) .'/.htninja') ) {

	$nfw_['res'] = @include_once $nfw_['file'];
	/**
	 * Allow and stop filtering.
	 */
	if ( $nfw_['res'] == 'ALLOW') {
		if (! defined('NFW_UWL') ) {
			define('NFW_UWL', true );
		}
		nfw_quit( 21 );
		return;
	}
	/**
	 * Reject immediately.
	 */
	if ( $nfw_['res'] == 'BLOCK') {
		header('HTTP/1.1 403 Forbidden');
		header('Status: 403 Forbidden');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Expires: 0');
		die('403 Forbidden');
	}
}
// Clear warning if there's an open_basedir restriction
if ( function_exists('error_clear_last') ) { // PHP 7.0+
	error_clear_last();
}

// Check if we have a user-defined log directory
// (see "Path to NinjaFirewall's log and cache directory"
// at https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/ ) :
if ( defined('NFW_LOG_DIR') ) {
	$nfw_['log_dir'] = NFW_LOG_DIR . '/nfwlog';
} else {
	$nfw_['log_dir'] = dirname(dirname(dirname( __DIR__ ))) . '/nfwlog';
}
// We ensure the log dir exists otherwise we try to create it.
// We quit and warn immediately if we fail :
if (! is_dir($nfw_['log_dir']) ) {
	if (! mkdir( $nfw_['log_dir'] . '/cache', 0755, true) ) {
		define( 'NFW_STATUS', 13 );
		return;
	}
}

/**
 * Start a session.
 */
if (! defined('NFWSESSION_DIR') ) {
	/**
	 * NFWSESSION_DIR can be defined in the .htninja.
	 */
	define('NFWSESSION_DIR', "{$nfw_['log_dir']}/session" );
}
require_once __DIR__ .'/class-session.php';

// Get/set PID
if ( is_file( "{$nfw_['log_dir']}/cache/.pid" ) ) {
	define( 'NFW_PID', file_get_contents( "{$nfw_['log_dir']}/cache/.pid" ) );
}

// Check if we are connecting over HTTPS
nfw_is_https();

/**
 * Brute-force attack protection on the login page and/or XMLRPC  API.
 */
if ( strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php') !== FALSE ) {
	// wp-login.php
	NinjaFirewall_bruteforce::run( 1 );

} elseif ( strpos( $_SERVER['SCRIPT_NAME'], 'xmlrpc.php') !== FALSE ) {
	// XML-RPC API
	NinjaFirewall_bruteforce::run( 2 );
}

/**
 * `$wp_config` is kept for backward compatibility; removing it would break too many sites.
 */
if (! empty( $wp_config ) ) {
	define('NFW_WPCONFIG', $wp_config );
	unset( $wp_config );
}

// Connection
$ret = nfw_connect();
if ( $ret !== true ) {
	nfw_quit( $ret );
	return;
}

/**
 * Fetch options.
 */
$ret = NinjaFirewall_rules::get('nfw_options');
if ( $ret !== true || empty( $nfw_['nfw_options'] ) ) {
	nfw_quit( $ret );
	return;
}

/**
 * Verify and retrieve the user IP address.
 */
NinjaFirewall_IP::check_ip( $nfw_['nfw_options'] );

/**
 * Centralized logging.
 */
if (! empty($nfw_['nfw_options']['clogs_pubkey']) && isset($_POST['clogs_req']) ) {
	include_once __DIR__ .'/class-centralised-logging.php';
	NinjaFirewall_centralisedlogging::run( $nfw_ );
	exit;
}

// Are we supposed to do anything ?
if ( empty($nfw_['nfw_options']['enabled']) ) {
	nfw_quit( 21 );
	return;
}

// HTTP response headers
if ( (! empty( $nfw_['nfw_options']['response_headers'] ) || ! empty($nfw_['nfw_options']['custom_headers']) )
	&& function_exists('header_register_callback') ) {

	if (! empty( $nfw_['nfw_options']['response_headers'] ) ) {
		define('NFW_RESHEADERS', $nfw_['nfw_options']['response_headers']);
		if (! empty( $nfw_['nfw_options']['response_headers'][6] ) && ! empty( $nfw_['nfw_options']['csp_frontend_data'] ) ) {
			define( 'CSP_FRONTEND_DATA', $nfw_['nfw_options']['csp_frontend_data']);
		}
		if (! empty( $nfw_['nfw_options']['response_headers'][7] ) && ! empty( $nfw_['nfw_options']['csp_backend_data'] )  ) {
			define( 'CSP_BACKEND_DATA', $nfw_['nfw_options']['csp_backend_data'] );
		}
	}
	if (! empty( $nfw_['nfw_options']['custom_headers'] ) ) {
		define('NFW_CUSTHEADERS', $nfw_['nfw_options']['custom_headers']);
	}
	header_register_callback('nfw_response_headers');
}

// Force SSL for admin and logins ?
if (! empty($nfw_['nfw_options']['force_ssl']) ) {
	define('FORCE_SSL_ADMIN', true);
}
// Disable the plugin and theme editor ?
if (! empty($nfw_['nfw_options']['disallow_edit']) ) {
	define('DISALLOW_FILE_EDIT', true);
}
// Disable plugin and theme update/installation ?
if (! empty($nfw_['nfw_options']['disallow_mods']) ) {
	define('DISALLOW_FILE_MODS', true);
}
if (! empty($nfw_['nfw_options']['disable_error_handler']) ) {
	define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
}

// Superglobals override
if (! empty($nfw_['nfw_options']['php_superglobals']) ) {
	$sgs = [
		'_GET', '_POST', '_SESSION', '_COOKIE',
		'_SERVER', '_FILES', '_ENV',  '_REQUEST', 'GLOBALS'
	];
	foreach( $sgs as $sg ) {
		if ( isset( $_GET[$sg] ) ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'Superglobals override attempt',
				"\$_GET[$sg]: ". serialize( $_GET[$sg] ),
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			unset( $_GET[$sg] );
		}
		if ( isset( $_POST[$sg] ) ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'Superglobals override attempt',
				"\$_POST[$sg]: ". serialize( $_POST[$sg] ),
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			unset( $_POST[$sg] );
		}
		if ( isset( $_COOKIE[$sg] ) ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'Superglobals override attempt',
				"\$_COOKIE[$sg]: ". serialize( $_COOKIE[$sg] ),
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			unset( $_COOKIE[$sg] );
		}
	}
}

// We only start a session if users already have a session
// cookie because we don't need write access yet
$session_name = NinjaFirewall_session::name();
if ( isset( $_COOKIE[ $session_name ] ) ) {
	NinjaFirewall_session::start();
}

// Is it a whitelisted (and logged in) user ?
if (! empty( NinjaFirewall_session::read('nfw_goodguy') ) ) {
	// Look for Live Log AJAX request
	if (! empty( NinjaFirewall_session::read('nfw_livelog') ) &&
		isset( $_POST['livecls'] ) && isset( $_POST['lines'] ) ) {

		include_once 'fw_livelog.php';
		fw_livelog_show();
	}

	/**
	 * Fetch admin rules.
	 */
	$ret = NinjaFirewall_rules::get('nfw_rules');
	if ( $ret !== true ) {
		nfw_quit( $ret );
		return;
	}
	nfw_check_admin_request();

	// Check whether we need to append NINJA_COUNTRY_CODE to PHP headers :
	if (! empty($nfw_['nfw_options']['ac_geoip']) && ! empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
		nfw_check_geoip(NFW_REMOTE_ADDR, 0);
	}
	nfw_quit( 21, 0);
	return;
}
define('NFW_SWL', 1);

// Live Log : record the request if needed
if ( is_file($nfw_['log_dir'] .'/cache/livelogrun.php')) {
	include_once 'fw_livelog.php';
	fw_livelog_record();
}

// Hide PHP notice/error messages ?
if (! empty($nfw_['nfw_options']['php_errors']) ) {
	@ error_reporting(0);
	@ ini_set('display_errors', 0);
}

// Ignore localhost & private IP address spaces (REMOTE_ADDR) ?
if ( empty($nfw_['nfw_options']['allow_local_ip']) && NFW_REMOTE_ADDR_PRIVATE == true ) {
	nfw_quit(21);
	return;
}

// Allow WP pseudo cron to run if the request is from a local IP:
if ( NFW_REMOTE_ADDR_PRIVATE == true && strpos( $_SERVER['SCRIPT_NAME'], '/wp-cron.php' ) !== FALSE ) {
	nfw_quit(20);
	return;
}

// Scan HTTP traffic only... ?
if ( @$nfw_['nfw_options']['scan_protocol'] == 1 && NFW_IS_HTTPS == true ) {
	nfw_quit(21);
	return;
}
// ...or HTTPS only ?
if ( @$nfw_['nfw_options']['scan_protocol'] == 2 && NFW_IS_HTTPS == false ) {
	nfw_quit(21);
	return;
}

// Access Control directives (except "User Input Access Control"):

/**
 * HTTP methods: GET, POST, HEAD, PUT, DELETE or PROPFIND.
 */
if (! empty($nfw_['nfw_options']['ac_method']) && ! empty($_SERVER['REQUEST_METHOD']) &&
	strpos($nfw_['nfw_options']['ac_method'], $_SERVER['REQUEST_METHOD']) !== false ) {

	// IP Access Control : allowed IPs
	if (! empty($nfw_['nfw_options']['ac_allow_ip']) || ! empty($nfw_['nfw_options']['nf_gateways']) ) {
		$nfw_['nfw_options']['ips'] = nfw_build_iplist( $nfw_ );
		foreach ($nfw_['nfw_options']['ips'] as $nfw_['nfw_options']['ip']) {
			if ( NinjaFirewall_IP::compare_ip( NFW_REMOTE_ADDR, $nfw_['nfw_options']['ip'] ) !== false ) {
				// Check whether we need to append NINJA_COUNTRY_CODE to PHP headers
				if (! empty($nfw_['nfw_options']['ac_geoip']) && ! empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
					nfw_check_geoip(NFW_REMOTE_ADDR, 0);
				}
				// Log event ?
				if (! empty($nfw_['nfw_options']['ac_allow_ip_log']) ) {

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'IP is in the IP Access Control whitelist',
						NFW_REMOTE_ADDR,
						NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
				}
				// Whitelisted user (mostly used for the nfw_is_user_whitelisted() function):
				define( 'NFW_UWL', true );
				nfw_quit(21, 0);
				return;
			}
		}
	}
	// IP Access Control : allowed ASN
	if (! empty( $nfw_['nfw_options']['ac_allow_asn'] ) ) {
		if ( @nfw_is_asn( $nfw_['nfw_options']['ac_allow_asn'] ) === true ) {
			// Check whether we need to append NINJA_COUNTRY_CODE to PHP headers
			if (! empty($nfw_['nfw_options']['ac_geoip']) && ! empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
				nfw_check_geoip(NFW_REMOTE_ADDR, 0);
			}
			// Log event
			if (! empty( $nfw_['nfw_options']['ac_allow_ip_log'] ) ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'ASN is in the IP Access Control whitelist',
					@$nfw_['asn'],
					NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}
			// Whitelisted user (mostly used for the nfw_is_user_whitelisted() function)
			define( 'NFW_UWL', true );
			nfw_quit(21, 0);
			return;
		}
	}


	// IP Access Control : blocked IPs
	if (! empty($nfw_['nfw_options']['ac_block_ip']) ) {
		$nfw_['nfw_options']['ips'] = @ unserialize(
			$nfw_['nfw_options']['ac_block_ip'],
			['allowed_classes' => false ]
		);
		foreach ($nfw_['nfw_options']['ips'] as $nfw_['nfw_options']['ip']) {
			if ( NinjaFirewall_IP::compare_ip( NFW_REMOTE_ADDR, $nfw_['nfw_options']['ip'] ) !== false ) {
				// Log event ?
				if (! empty($nfw_['nfw_options']['ac_block_ip_log']) ) {

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'IP is in the IP Access Control blacklist',
						NFW_REMOTE_ADDR,
						NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
				}
				nfw_block();
			}
		}
	}
	// IP Access Control : blocked ASN
	if (! empty( $nfw_['nfw_options']['ac_block_asn'] ) ) {
		if ( @nfw_is_asn( $nfw_['nfw_options']['ac_block_asn'] ) === true ) {
			// Log event
			if (! empty( $nfw_['nfw_options']['ac_block_ip_log'] ) ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'ASN is in the IP Access Control blacklist',
					@$nfw_['asn'],
					NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}
			nfw_block();
		}
	}


	// URL Access Control : allowed URLs
	if (! empty($nfw_['nfw_options']['ac_wl_url']) ) {
		if (  preg_match("`{$nfw_['nfw_options']['ac_wl_url']}`", $_SERVER['SCRIPT_NAME'], $match )
			|| preg_match("`{$nfw_['nfw_options']['ac_wl_url']}`", $_SERVER['REQUEST_URI'], $match ) ) {

			// Log event ?
			if (! empty($nfw_['nfw_options']['ac_wl_url_log']) ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'URL is in the URL Access Control whitelist',
					$match[0],
					NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}

			// Check whether we need to append NINJA_COUNTRY_CODE to PHP headers :
			if (! empty($nfw_['nfw_options']['ac_geoip']) && ! empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
				nfw_check_geoip(NFW_REMOTE_ADDR, 0);
			}
			nfw_quit(21);
			return;
		}
	}
	// URL Access Control : blocked URLs
	if (! empty($nfw_['nfw_options']['ac_bl_url']) ) {
		if (  preg_match( "`{$nfw_['nfw_options']['ac_bl_url']}`", $_SERVER['SCRIPT_NAME'], $match )
			|| preg_match( "`{$nfw_['nfw_options']['ac_bl_url']}`", $_SERVER['REQUEST_URI'], $match ) ) {

			// Log event ?
			if (! empty($nfw_['nfw_options']['ac_bl_url_log']) ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'URL is in the URL Access Control blacklist',
					$match[0],
					NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}
			nfw_block();
		}
	}
	// Bot Access Control :
	if (! empty($nfw_['nfw_options']['ac_bl_bot']) && ! empty($_SERVER['HTTP_USER_AGENT']) ) {
		// Case-insensitive
		if (preg_match("`{$nfw_['nfw_options']['ac_bl_bot']}`i", $_SERVER['HTTP_USER_AGENT']) ) {
			// Log event ?
			if (! empty($nfw_['nfw_options']['ac_bl_bot_log']) ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'User-Agent is in the Bot Access Control blacklist',
					$_SERVER['HTTP_USER_AGENT'],
					NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}
			nfw_block();
		}
	}

	// Geolocation-based Access Control :
	if (! empty($nfw_['nfw_options']['ac_geoip']) ) {
		nfw_check_geoip(NFW_REMOTE_ADDR, 1);
	}

	// Rate Limiting :
	if (! empty($nfw_['nfw_options']['ac_rl_on']) ) {
		$nfw_['ac_rl_file'] = $nfw_['log_dir'] . '/cache/rl.'. $_SERVER['SERVER_NAME']  .'.' . NFW_REMOTE_ADDR . '.php';

		// Do we know that IP ?
		if ( is_file( $nfw_['ac_rl_file'] ) ) {
			$nfw_['ac_rl_tmp']['c'] = $nfw_['ac_rl_tmp']['t'] = 0;
			$nfw_['ac_rl_tmp'] = unserialize(
				file_get_contents( $nfw_['ac_rl_file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
				['allowed_classes' => false ]
			);

			// IP is already banned ?
			if (! empty($nfw_['ac_rl_tmp']['s']) ) {
				// Check whether the banning period is over :
				if ( time() - $nfw_['ac_rl_tmp']['t'] <= $nfw_['nfw_options']['ac_rl_time']) {
					// Not over yet, keep it blocked and return "Too many requests" code:
					$nfw_['nfw_options']['ret_code'] = '429';
					nfw_block();
				} else {
					// It is over. Reset and let it in :
					@file_put_contents( $nfw_['ac_rl_file'], 'a:3:{s:1:"s";i:0;s:1:"c";i:1;s:1:"t";i:'. time() .';}', LOCK_EX);
				}
			// Not banned, let's check it :
			} else {
				@++$nfw_['ac_rl_tmp']['c'];
				// Did it exceed the threshold ?
				if ($nfw_['ac_rl_tmp']['c'] >  $nfw_['nfw_options']['ac_rl_conn'] ) {
					// Block it :
					@file_put_contents($nfw_['ac_rl_file'], 'a:3:{s:1:"s";i:1;s:1:"c";i:'. $nfw_['ac_rl_tmp']['c'] . ';s:1:"t";i:'. $nfw_['ac_rl_tmp']['t'] .';}', LOCK_EX);
					if (! empty($nfw_['nfw_options']['ac_rl_log']) ) {

						$nfw_['incidentID'] = NinjaFirewall_log::write(
							'IP temporarily blocked (too many connections)',
							NFW_REMOTE_ADDR,
							NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
						);
					}
					// Return "Too many requests" code:
					$nfw_['nfw_options']['ret_code'] = '429';
					nfw_block();
				} else {
					// Check if we need to reset the timer and counter :
					if ( time() - @$nfw_['ac_rl_tmp']['t'] > $nfw_['nfw_options']['ac_rl_intv']) {
						$nfw_['ac_rl_tmp']['t'] = time();
						$nfw_['ac_rl_tmp']['c'] = 1;
					}
					// Update log and let it in :
					@file_put_contents($nfw_['ac_rl_file'], 'a:3:{s:1:"s";i:0;s:1:"c";i:'. $nfw_['ac_rl_tmp']['c'] . ';s:1:"t";i:'. $nfw_['ac_rl_tmp']['t'] .';}', LOCK_EX);
				}
			}
		} else {
			// Log it:
			@file_put_contents($nfw_['ac_rl_file'], 'a:3:{s:1:"s";i:0;s:1:"c";i:1;s:1:"t";i:'. time() .';}', LOCK_EX);
		}
	}
} // End of Access Control directives.

define( 'NFW_UWL', false );

/**
 * File Guard.
 */
if (! empty( $nfw_['nfw_options']['fg_enable'] )  ) {
	include_once __DIR__ .'/class-firewall-fileguard.php';
	NinjaFirewall_fileguard::run( $nfw_ );
}

// HTTP_HOST is an IP ?
if (! empty($nfw_['nfw_options']['no_host_ip']) && @filter_var(parse_url('https://'.$_SERVER['HTTP_HOST'], PHP_URL_HOST), FILTER_VALIDATE_IP) ) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'HTTP_HOST is an IP',
		$_SERVER['HTTP_HOST'],
		NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
   nfw_block();
}

// Block POST without Referer header ?
if (! empty($nfw_['nfw_options']['referer_post']) && $_SERVER['REQUEST_METHOD'] == 'POST' && ! isset($_SERVER['HTTP_REFERER']) ) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'POST method without Referer header',
		$_SERVER['REQUEST_METHOD'],
		NFWLOG_MEDIUM, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
   nfw_block();
}

if (! empty($nfw_['nfw_options']['admin_ajax']) && strpos( $_SERVER['SCRIPT_NAME'], 'wp-admin/admin-ajax.php' ) !== FALSE ) {
	nfw_is_bot( 'admin-ajax.php' );
}

// Access to WordPress XML-RPC API (Firewall Policies) ?
if ( strpos($_SERVER['SCRIPT_NAME'], '/xmlrpc.php' ) !== FALSE ) {
	// Always block ?
	if (! empty($nfw_['nfw_options']['no_xmlrpc']) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Access to WordPress XML-RPC API',
			$_SERVER['SCRIPT_NAME'],
			NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if (! isset( $HTTP_RAW_POST_DATA ) ) {
			@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}
		// Block if the 'system.multicall' method is used (POST only) ?
		if (! empty($nfw_['nfw_options']['no_xmlrpc_multi']) ) {
			// Check the raw POST data:
			if ( @strpos( $HTTP_RAW_POST_DATA, '<methodName>system.multicall</methodName>') !== FALSE ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'Access to WordPress XML-RPC API (system.multicall method)',
					$_SERVER['SCRIPT_NAME'],
					NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
				nfw_block();
			}
		}
		// Block if the 'pingback.ping' method is used (POST only) ?
		if (! empty($nfw_['nfw_options']['no_xmlrpc_pingback']) ) {
			// Check the raw POST data:
			if ( @strpos( $HTTP_RAW_POST_DATA, '<methodName>pingback.ping</methodName>') !== FALSE ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'Access to WordPress XML-RPC API (pingback.ping)',
					$_SERVER['SCRIPT_NAME'],
					NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
				nfw_block();
			}
		}
	}
}
// Block if pingback verification:
if (! empty($nfw_['nfw_options']['no_xmlrpc_pingback']) && strpos($_SERVER['HTTP_USER_AGENT'], '; verifying pingback from ') !== FALSE) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'Blocked pingback verification',
		$_SERVER['HTTP_USER_AGENT'],
		NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
   nfw_block();
}

// WordPress Aplication Passwords
if (! empty($nfw_['nfw_options']['no_appswd']) && strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/authorize-application.php' ) !== FALSE ) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'Access to WordPress Application Passwords',
		$_SERVER['SCRIPT_NAME'],
		NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
	nfw_block();
}

// POST request in the themes folder ?
if (! empty($nfw_['nfw_options']['no_post_themes']) && $_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['SCRIPT_NAME'], $nfw_['nfw_options']['no_post_themes']) !== FALSE ) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'POST request in the themes folder',
		$_SERVER['SCRIPT_NAME'],
		NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
   nfw_block();
}

// Block direct access to any PHP file located in wp_dir :
if (! empty($nfw_['nfw_options']['wp_dir']) && preg_match( '`' . $nfw_['nfw_options']['wp_dir'] . '`', $_SERVER['SCRIPT_NAME']) ) {

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'Forbidden direct access to PHP script',
		$_SERVER['SCRIPT_NAME'],
		NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);
   nfw_block();
}

// Look for upload:
nfw_check_upload();

/**
 * Fetch rules.
 */
$ret = NinjaFirewall_rules::get('nfw_rules');
if ( $ret !== true ) {
	nfw_quit( $ret );
	return;
}

// User Input Access Control:
$ac_wl_input = []; $ac_bl_input = [];
if (! empty( $nfw_['nfw_options']['ac_wl_input'] ) ) {
	$ac_wl_input = unserialize( $nfw_['nfw_options']['ac_wl_input'], ['allowed_classes' => false ] );
}
if (! empty( $nfw_['nfw_options']['ac_bl_input'] ) ) {
	$ac_bl_input = unserialize( $nfw_['nfw_options']['ac_bl_input'], ['allowed_classes' => false ] );
}

nfw_check_request( $nfw_['nfw_rules'], $nfw_['nfw_options'], $ac_wl_input, $ac_bl_input );

/**
 * Sanitise requests & variables.
 */
if (! empty( $nfw_['nfw_options']['get_sanitise'] ) && ! empty( $_GET ) ) {
	$_GET = NinjaFirewall_data::sanitise( $_GET, 1, 'GET', $nfw_, $ac_wl_input );
}
if (! empty( $nfw_['nfw_options']['cookies_sanitise'] ) && ! empty( $_COOKIE ) ) {
	$_COOKIE = NinjaFirewall_data::sanitise( $_COOKIE, 3, 'COOKIE', $nfw_, $ac_wl_input );
}
if (! empty( $nfw_['nfw_options']['ua_sanitise'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
	$_SERVER['HTTP_USER_AGENT'] = NinjaFirewall_data::sanitise(
		$_SERVER['HTTP_USER_AGENT'], 1, 'HTTP_USER_AGENT', $nfw_
	);
}
if (! empty( $nfw_['nfw_options']['referer_sanitise'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
	$_SERVER['HTTP_REFERER'] = NinjaFirewall_data::sanitise(
		$_SERVER['HTTP_REFERER'], 1, 'HTTP_REFERER', $nfw_
	);
}
if (! empty( $nfw_['nfw_options']['php_path_i'] ) && ! empty( $_SERVER['PATH_INFO'] ) ) {
	$_SERVER['PATH_INFO'] = NinjaFirewall_data::sanitise(
		$_SERVER['PATH_INFO'], 2, 'PATH_INFO', $nfw_
	);
}
if (! empty( $nfw_['nfw_options']['php_path_t'] ) && ! empty( $_SERVER['PATH_TRANSLATED'] ) ) {
	$_SERVER['PATH_TRANSLATED'] = NinjaFirewall_data::sanitise(
		$_SERVER['PATH_TRANSLATED'], 2, 'PATH_TRANSLATED', $nfw_
	);
}
if (! empty( $nfw_['nfw_options']['php_self'] ) && ! empty( $_SERVER['PHP_SELF'] ) ) {
	$_SERVER['PHP_SELF'] = NinjaFirewall_data::sanitise(
		$_SERVER['PHP_SELF'], 2, 'PHP_SELF', $nfw_
	);
}

// Web Filter :
if (! empty($nfw_['nfw_options']['wf_enable']) ) {
	ob_start("nfw_webfilter");
}

// That's all!
nfw_quit(21);
return;

// ===================================================================== 2023-05-16
// Return the time using hrtime (PHP >= 7.3) or microtime.

function nfw_fc_metrics( $action = 'start') {

	if ( function_exists('hrtime') ) {
		$metrics = 'hrtime';
	} else {
		$metrics = 'microtime';
	}

	// Start the chrono
	if ( $action == 'start') {
		return $metrics(true);
	}

	global $nfw_;

	if ( empty( $nfw_['fw_starttime'] ) ) {
		return 0;
	}

	// Stop the chrono and return the formatted elapsed time
	if ( $metrics == 'hrtime') {
		return number_format( ( $metrics(true) - $nfw_['fw_starttime'] ) / 1000000000, 5 );
	} else {
		return number_format( $metrics(true) - $nfw_['fw_starttime'], 5 );
	}
}

// ===================================================================== 2023-05-16
// Build the list of whitelisted IP addresses.

function nfw_build_iplist( $nfw_ ) {

	$ac_allow_ip = [];
	if (! empty( $nfw_['nfw_options']['ac_allow_ip'] ) ) {
		$ac_allow_ip = unserialize( $nfw_['nfw_options']['ac_allow_ip'], ['allowed_classes' => false ] );
	}
	if (! empty( $nfw_['nfw_options']['nf_gateways'] ) ) {
		$nf_gateways = unserialize( $nfw_['nfw_options']['nf_gateways'], ['allowed_classes' => false ] );
		include __DIR__ .'/fw_gateways.php';
		foreach ( $nf_gateways as $gateway => $null ) {
			foreach( $gateways_ip[$gateway] as $ip ) {
				$ac_allow_ip[] = $ip;
			}
		}
	}
	return $ac_allow_ip;
}

// =====================================================================
// Check if IP belongs to ASN.

function nfw_is_asn( $list ) {

	// Don't check private IP address
	if ( NFW_REMOTE_ADDR_PRIVATE == true ) {
		return false;
	}

	global $nfw_;

	if ( NFW_REMOTE_ADDR_IPV6 == true ) {
		$nfw_['nfw_options']['geoip_dat'] = __DIR__ . '/share/ASNv6.db';
		$nfw_['nfw_options']['geoip_func'] = 'nfw_geoip_name_by_addr_v6';

	} else {
		$nfw_['nfw_options']['geoip_dat'] = __DIR__ . '/share/ASNv4.db';
		$nfw_['nfw_options']['geoip_func'] = 'nfw_geoip_name_by_addr';
	}
	@include_once __DIR__ .'/share/geoip.inc';

	// Already checked?
	if ( empty( $nfw_['asn'] ) ) {
		$gi = nfw_geoip_open( $nfw_['nfw_options']['geoip_dat'], NFW_GEOIP_STANDARD );
		$nfw_['asn'] = $nfw_['nfw_options']['geoip_func']( $gi, NFW_REMOTE_ADDR );
		// Unknown IP/ASN or error:
		if (! $nfw_['asn'] ) {
			return false;
		}
	}

	$asn_list = unserialize( $list, ['allowed_classes' => false ] );
	foreach( $asn_list as $asn ) {
		if ( strpos( $nfw_['asn'], "$asn " ) === 0 ) {
			return true;
		}
	}
	return false;
}

// =====================================================================
// Close any SQL link, set the firewall status, clear the $nfw_ array
// and close the session before leaving.

function nfw_quit( $status, $sql_link = 1 ) {

	global $nfw_;
	define( 'NFW_STATUS', $status );

	if ( $sql_link == 1 && empty( $nfw_['shm_id'] ) && isset( $nfw_['mysqli'] ) ) {
		$nfw_['mysqli']->close();
	}
	$nfw_ = [];
}

// =====================================================================
// Connect to the DB.

function nfw_connect() {

	global $nfw_, $wp_config;

	// WPWAF mode?
	if ( defined('NFW_WPWAF') && NFW_WPWAF == 2 ) {
		$nfw_['wp_waf'] = 2;
		return true;
	}

	// Shared memory?
	if ( function_exists('shmop_open') ) {
		$nfw_['shm_key'] = @ftok( dirname( dirname( __DIR__ ) ), 'N' );
		if ( $nfw_['shm_id'] = @shmop_open( $nfw_['shm_key'], 'a', 0, 0) ) {
			$nfw_['shmop_data'] = @shmop_read($nfw_['shm_id'], 0, shmop_size($nfw_['shm_id']) );
			list($nfw_['shmop_options'], $nfw_['shmop_rules']) = @explode($nfw_['shm_key'], $nfw_['shmop_data']);
			return true;
		} else {
			if ( function_exists('error_clear_last') ) { // PHP 7.0+
				error_clear_last();
			}
		}
	}

	$nfw_['shm_id'] = 0;

	// Check if we have a SQL link that was defined in the .htninja.
	// See "Giving NinjaFirewall a MySQLi link identifier"
	// at https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/
	if (! empty( $GLOBALS['nfw_mysqli'] ) && ! empty( $GLOBALS['nfw_table_prefix'] ) ) {
		$nfw_['mysqli'] = $GLOBALS['nfw_mysqli'];
		$nfw_['table_prefix'] = $GLOBALS['nfw_table_prefix'];
		return true;
	}


	// DB
	if (! defined('NFW_WPCONFIG') ) {
		// This part is also used when coming back from the Web Filter function:
		if (! @is_file( $wp_config = dirname(dirname(dirname(dirname( __DIR__ )))) . '/wp-config.php') ) {
			// Maybe the user moved it inside the parent directory?
			if (! @is_file( $wp_config = dirname(dirname(dirname(dirname(dirname( __DIR__ ))))) . '/wp-config.php') ) {
				return 1;
			}
		}
		define('NFW_WPCONFIG', $wp_config);
	}
	if (! $nfw_['fh'] = @fopen(NFW_WPCONFIG, 'r') ) {
		return 2;
	}

	// Potential SQL flags
	$nfw_['MYSQL_CLIENT_FLAGS'] = 0;

	// Fetch WP configuration
	while (! feof($nfw_['fh'])) {
		$nfw_['line'] = fgets($nfw_['fh']);
		if ( preg_match('/^\s*define\s*\(\s*[\'"]DB_NAME[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_NAME'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_USER[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_USER'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_PASSWORD[\'"]\s*,\s*([\'"])(.+?)\1\s*\);/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_PASSWORD'] = str_replace( '\\'.$nfw_['match'][1], $nfw_['match'][1], $nfw_['match'][2] );
			if ( $nfw_['match'][1] == '"' ) {
				$nfw_['DB_PASSWORD'] = str_replace( '\$', '$', $nfw_['DB_PASSWORD'] );
			}
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_HOST[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_HOST'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*\$table_prefix\s*=\s*[\'"](.*?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['table_prefix'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]MYSQL_CLIENT_FLAGS[\'"]\s*,\s*(.+?)\s*\)/', $nfw_['line'], $nfw_['match']) ) {
			if ( empty( $nfw_['MYSQL_CLIENT_FLAGS'] ) ) {
				$available_flags = [
					'MYSQLI_CLIENT_COMPRESS' => MYSQLI_CLIENT_COMPRESS,
					'MYSQLI_CLIENT_FOUND_ROWS' => MYSQLI_CLIENT_FOUND_ROWS,
					'MYSQLI_CLIENT_IGNORE_SPACE' => MYSQLI_CLIENT_IGNORE_SPACE,
					'MYSQLI_CLIENT_INTERACTIVE' => MYSQLI_CLIENT_INTERACTIVE,
					'MYSQLI_CLIENT_SSL' => MYSQLI_CLIENT_SSL,
					'MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT' => MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
				];
				// There could be one or more flags, e.g., 'MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT'
				$tmp_flags = explode( '|', $nfw_['match'][1] );
				foreach( $tmp_flags as $tmp_flag ) {
					$tmp_flag = trim( $tmp_flag );
					if ( isset( $available_flags[$tmp_flag] ) ) {
						$nfw_['MYSQL_CLIENT_FLAGS'] += $available_flags[$tmp_flag];
					}
				}
			}
		}
	}
	fclose($nfw_['fh']);

	if (! isset($nfw_['DB_NAME']) || ! isset($nfw_['DB_USER']) || ! isset($nfw_['DB_PASSWORD']) || ! isset($nfw_['DB_HOST']) || ! isset($nfw_['table_prefix']) ) {
		return 3;
	}

	/**
	 * Parse hostname/port and socket.
	 */
	require_once __DIR__ .'/class-nfw-database.php';
	$host_data = NinjaFirewall_fwdatabase::parse_db_host( $nfw_['DB_HOST'] );
	if ( $host_data ) {
		list( $nfw_['DB_HOST'], $nfw_['port'], $nfw_['socket'] ) = $host_data;
	}

	// Make sure mysqli extension is loaded
	if (! function_exists( 'mysqli_real_connect' ) ) {
		return 14;
	}
	// Connect to the DB
	@$nfw_['mysqli'] = mysqli_init();
	@mysqli_real_connect( $nfw_['mysqli'], $nfw_['DB_HOST'], $nfw_['DB_USER'], $nfw_['DB_PASSWORD'], $nfw_['DB_NAME'], $nfw_['port'], $nfw_['socket'], $nfw_['MYSQL_CLIENT_FLAGS'] );
	if ($nfw_['mysqli']->connect_error) {
		return 4;
	}

	return true;
}

// =====================================================================
// Check for HTTPS.

function nfw_is_https() {

	// Can be defined in the .htninja:
	if ( defined('NFW_IS_HTTPS') ) { return; }

	if ( ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 ) ||
		( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
		( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) ) {
		define('NFW_IS_HTTPS', true);
	} else {
		define('NFW_IS_HTTPS', false);
	}
}

// =====================================================================

function nfw_check_geoip($user_ip, $block) {

	// Don't check private IP address
	if ( NFW_REMOTE_ADDR_PRIVATE == true ) {
		return false;
	}

	global $nfw_;

	// Check if the option applies to the whole site or some specific
	// URLs only (e.g., /wp-login.php, xmlrpc.php etc):
	if (! empty( $nfw_['nfw_options']['ac_geo_url'] ) ) {
		// Check if it matches:
		if (! preg_match('`'. $nfw_['nfw_options']['ac_geo_url'] . '`', $_SERVER['SCRIPT_NAME'])
			&& ! preg_match('`'. $nfw_['nfw_options']['ac_geo_url'] . '`', $_SERVER['REQUEST_URI']) ) {
			// Let it go, unless we need to populate the NINJA_COUNTRY_CODE variable:
			if ( empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
				return;
			}
			// Go ahead and fetch NINJA_COUNTRY_CODE, but don't block the request:
			$block = 0;
		}
	}

	// Use PHP variable
	if ( $nfw_['nfw_options']['ac_geoip_db'] == 2) {
		$cn = @$_SERVER[ $nfw_['nfw_options']['ac_geoip_db2'] ];
	// Use built-in GeoIP DB
	} else {

		if ( NFW_REMOTE_ADDR_IPV6 == true ) {
			$nfw_['nfw_options']['geoip_dat'] = __DIR__ . '/share/IPv6.db';
			$nfw_['nfw_options']['geoip_func'] = 'nfw_geoip_country_code_by_addr_v6';
		} else {
			$nfw_['nfw_options']['geoip_dat'] = __DIR__ . '/share/IPv4.db';
			$nfw_['nfw_options']['geoip_func'] = 'nfw_geoip_country_code_by_addr';
		}

		@include_once __DIR__ .'/share/geoip.inc';
		$gi = nfw_geoip_open($nfw_['nfw_options']['geoip_dat'], NFW_GEOIP_STANDARD);
		$cn = $nfw_['nfw_options']['geoip_func']($gi, $user_ip);
	}
	// Ensure we have a result
	if (! empty( $cn ) ) {
		if ( $nfw_['nfw_options']['ac_geoip_cn'] && ! empty($block) ) {
			if ( strpos($nfw_['nfw_options']['ac_geoip_cn'], $cn ) !== FALSE ) {
				// Log event
				if (! empty($nfw_['nfw_options']['ac_geoip_log']) ) {

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'Country is in the Access Control blacklist',
						$cn,
						NFWLOG_MEDIUM, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
				}
				nfw_block();
			}
		}
	}
	// Append NINJA_COUNTRY_CODE if needed
	if (! empty( $nfw_['nfw_options']['ac_geoip_ninja'] ) ) {
		if ( $cn ) {
			$_SERVER['NINJA_COUNTRY_CODE'] = $cn;
		} else {
			// Don't know where it comes from :/
			$_SERVER['NINJA_COUNTRY_CODE'] = '--';
		}
	}
}

// =====================================================================

function nfw_check_upload() {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	// Fetch uploaded files, if any :
	$f_uploaded = [];
	$f_uploaded = nfw_fetch_uploads();
	$tmp = '';
	// Uploads are not allowed :
	if ( empty($nfw_['nfw_options']['uploads']) ) {
		$tmp = '';
		foreach ($f_uploaded as $key => $value) {
			// Empty field ?
			if (! $f_uploaded[$key]['name']) { continue; }
			if ( empty( $f_uploaded[$key]['size'] ) ) { $f_uploaded[$key]['size'] = 0; }
			$tmp .= $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes) ';
      }
      if ( $tmp ) {

			// Log and block :
			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'Blocked file upload attempt',
				rtrim( $tmp, ' '),
				NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			nfw_block();
		}
	/**
	 * Uploads are allowed.
	 */
	} else {
		foreach ( $f_uploaded as $key => $value ) {
			if (! $f_uploaded[$key]['name'] ) {
				continue;
			}
			if ( empty( $f_uploaded[$key]['size'] ) ) {
				$f_uploaded[$key]['size'] = 0;
			}
			/**
			 * Check its size.
			 */
			if (! empty( $nfw_['nfw_options']['upload_maxsize'] ) &&
				$f_uploaded[$key]['size'] > $nfw_['nfw_options']['upload_maxsize'] ) {

				$nfw_['incidentID'] = NinjaFirewall_log::write(
					'Attempt to upload a file > '. ( $nfw_['nfw_options']['upload_maxsize'] / 1024 ) .' KB',
					$f_uploaded[$key]['name'] .' ('. number_format( $f_uploaded[$key]['size'] ) .' bytes)',
					NFWLOG_MEDIUM, 0, $nfw_['nfw_options'], $nfw_['log_dir']
				);
				nfw_block();
			}

			// Reject potentially dangerous files
			if ( $nfw_['nfw_options']['uploads'] == 2 ) {
				// ZIP archive
				if ( preg_match( '`\.zip$`', $f_uploaded[$key]['name'] ) && ! empty( $nfw_['nfw_options']['scan_zip'] ) ) {
					$zip_file = $f_uploaded[$key]['tmp_name'];
					$zip = new ZipArchive();
					$zip_files_list = [];
					if ( $zip->open( $zip_file ) === true ) {
						for ( $i = 0; $i < $zip->numFiles; ++$i ) {
							$stat = $zip->statIndex( $i );
							// Ignore folders:
							if ( substr( $stat['name'], -1 ) == '/' ) {
								continue;
							}
							$zip_files_list[ $stat['name'] ] = $stat['size'];
						}
						$zip->close();
					}
					if (! empty( $zip_files_list ) ) {
						foreach( $zip_files_list as $file => $size ) {
							nfw_checkfile_content( $file, $size, "zip://{$zip_file}#{$file}", "zip://{$f_uploaded[$key]['name']}#" );
						}
					}
				// Non-ZIP file
				} else {
					nfw_checkfile_content( $f_uploaded[$key]['name'], $f_uploaded[$key]['size'], $f_uploaded[$key]['tmp_name'] );
				}
			}

			// Look for EICAR AV test file :
			// -The file must start with the 68-bytes EICAR signature.
			// -It can be appended by any combination of whitespace characters
			//  with the total file length not exceeding 128 characters. The only
			//  whitespace characters allowed are the space character, tab, LF, CR, CTRL-Z.
			//	(See: https://blog.nintechnet.com/anatomy-of-the-eicar-antivirus-test-file/)
			if ( $f_uploaded[$key]['size'] > 67 && $f_uploaded[$key]['size'] < 129 ) {
				// Read it:
				if ( empty($data) ) {
					$data = file_get_contents( $f_uploaded[$key]['tmp_name'] );
				}
				if ( preg_match('`^X5O!P%@AP' . '\[4\\\PZX54\(P\^\)7CC\)7}\$EIC' .
				                'AR-STANDARD-ANTIVI' . 'RUS-TEST-FILE!\$H' . '\+H\*' .
				                '[\x09\x10\x13\x20\x1A]*`', $data) ) {

					$nfw_['incidentID'] = NinjaFirewall_log::write(
						'EICAR Standard Anti-Virus Test File blocked',
						$f_uploaded[$key]['name'] .' ('. number_format($f_uploaded[$key]['size']) .' bytes)',
						NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
					);
					// Always block it, even if we allow uploads:
					nfw_block();
				}
			}

			// Sanitise filename ?
			if (! empty($nfw_['nfw_options']['sanitise_fn']) ) {
				if ( empty( $nfw_['nfw_options']['substitute'] ) ) {
					$nfw_['nfw_options']['substitute'] = 'X';
				}
				$tmp = '';
				$f_uploaded_name = $f_uploaded[$key]['name'];
				$f_uploaded[$key]['name'] = preg_replace('/[^\w\.\-]/i', $nfw_['nfw_options']['substitute'], $f_uploaded[$key]['name'], -1, $count);

				// Sanitize double (or more) extensions (e.g., foo.php.gif => foo.php_.gif)
				$ret = [];
				$ret = NinjaFirewall_data::sanitise_extensions( $f_uploaded[$key]['name'], $nfw_['nfw_options']['substitute'] );
				if (! empty( $ret['count'] ) ) {
					$count += $ret['count'];
					$f_uploaded[$key]['name'] = $ret['name'];
				}

				if ($count) {
					$tmp = ' (sanitising '. $count . ' char. from filename)';
					$_FILES = NinjaFirewall_data::sanitise_filename( $_FILES, $f_uploaded_name, $f_uploaded[$key]['name'] );
				}

			}
			// Log and let it go
			if (! isset( $f_uploaded[$key]['size'] ) ) {
				$size = 'n/a';
			} else {
				$size = number_format( $f_uploaded[$key]['size'] );
			}

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'File upload detected, no action taken' . $tmp ,
				"{$f_uploaded[$key]['name']} ($size bytes)",
				NFWLOG_UPLOAD, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
		}
	}
}

// =====================================================================
// Scan files, including ZIP archives, for dangerous files

function nfw_checkfile_content( $fname, $fsize, $ftmp_name, $zip = '' ) {

	global $nfw_;

	// System files
	if (preg_match('/\.ht(?:access|passwd)|(?:php\d?|\.user)\.ini|\.ph(?:p([34x7]|5\d?)?|t(ml)?|ar)(?:\.|$)/', $fname) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Attempt to upload a script or system file',
			$zip.$fname .' ('. number_format($fsize) .' bytes)',
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}

	$data = file_get_contents( $ftmp_name, true, null, 0, 4000000 );

	// ELF
	if (preg_match('`^\x7F\x45\x4C\x46`', $data) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Attempt to upload an executable file (ELF)',
			$zip.$fname .' ('. number_format($fsize) .' bytes)',
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
	// MZ header
	if (preg_match('`^\x4D\x5A`', $data) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Attempt to upload an executable file (Microsoft MZ header)',
			$zip.$fname .' ('. number_format($fsize) .' bytes)',
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
	// Scripts
	if (preg_match('`(<\?(?i:php\s|=\s[\s\x21-\x7e]{10})|#!/(?:usr|bin)/.+?\s|\s#include\s+<[\w/.]+?>|\W\$\{\s*([\'"])\w+\2|\b(?i:__HALT_COMPILER)\s*\(\s*\).+?\?>|<script.*?>.+?<\\\?/script\s*>)`', $data, $match) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Attempt to upload a script',
			$zip.$fname .' ('. number_format($fsize) .' bytes), pattern: '. $match[1],
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
	// Suspicious SVG file
	if ( preg_match( '`(?i)<svg.*>.*?(<[a-z].+?\bon[a-z]{3,29}\b\s*=.{5}|<script.*?>.+?</script\s*>|data:image/.+?;base64|javascript:|ev:event=).*?</svg\s*>`s', $data, $match ) ) {

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'Attempt to upload an SVG file containing Javascript/XML events',
			$zip.$fname .' ('. number_format($fsize) .' bytes), pattern: '. $match[1],
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
}

// =====================================================================

function nfw_fetch_uploads() {

	global $file_buffer, $upload_array, $prop_key;
	$upload_array = [];

	foreach( $_FILES as $f_key => $f_value ) {

		foreach( $f_value as $prop_key => $prop_value ) {

			// Fetch all but 'error':
			if (! in_array( $prop_key, ['name', 'type', 'tmp_name', 'size'] ) ) { continue; }

			$file_buffer = $f_key;

			if ( is_array( $_FILES[$f_key][$prop_key] ) ) {
				nfw_recursive_upload( $_FILES[$f_key][$prop_key] );
			} else {
				if (! empty( $_FILES[$f_key][$prop_key] ) ) {
					$upload_array[$f_key][$prop_key] = $_FILES[$f_key][$prop_key];
				}
			}
		}
	}
	return $upload_array;
}

// =====================================================================

function nfw_recursive_upload( $data ) {

	global $file_buffer, $upload_array, $prop_key;

	foreach( $data as $data_key => $data_value ) {
		if ( is_array( $data_value ) ) {
			$file_buffer .= "_{$data_key}";
			nfw_recursive_upload( $data_value );
		} else {
			if ( empty( $data_value ) ) { continue; }
			$upload_array["{$file_buffer}_{$data_key}"][$prop_key] = $data_value;
		}
	}
}

// =====================================================================

function nfw_check_admin_request( $ac_wl_input = [], $ac_bl_input = [] ) {

	global $nfw_;

	if ( isset( $nfw_['nfw_rules']['999'] ) ) {
		$nfw_['adm_rules'] = [];
		foreach ( $nfw_['nfw_rules']['999'] as $key => $value ) {
			if ( empty( $nfw_['nfw_rules'][$key]['ena'] ) ) { continue; }
			$nfw_['adm_rules'][$key] = $nfw_['nfw_rules'][$key];
		}
		if (! empty( $nfw_['adm_rules'] ) ) {
			nfw_check_request( $nfw_['adm_rules'], $nfw_['nfw_options'], $ac_wl_input, $ac_bl_input );
		}
	}
}

// =====================================================================

function nfw_check_request( $nfw_rules, $nfw_options, $ac_wl_input, $ac_bl_input ) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_, $HTTP_RAW_POST_DATA;

	// Loop through each rule:
	foreach ( $nfw_rules as $id => $rules ) {

		// Ignored disabled or admin-only rules:
		if ( empty( $rules['ena']) ) { continue; }

		// Check the first subrule (chained rules):
		$wherelist = explode('|', $rules['cha'][1]['whe']);

		foreach ($wherelist as $where) {

			// Check it this type of scan is disabled (POST, GET, COOKIE,
			// as well as HTTP_USER_AGENT, HTTP_REFERER):
			if ( nfw_disabled_scan( $where, $nfw_options ) ) { continue; }

			// =================================================================
			// RAW data:
			if ( $where == 'RAW' ) {
				if (! isset( $HTTP_RAW_POST_DATA ) ) {
					@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
				}

				if ( nfw_matching( 'RAW', $_SERVER['REQUEST_METHOD'], $nfw_rules, $rules, 1, $id, $nfw_options, $HTTP_RAW_POST_DATA ) ) {
					// Rule matches, check next subrule:
					nfw_check_subrule( 'RAW', $_SERVER['REQUEST_METHOD'], $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================
			// GET, POST, COOKIE, SERVER...:
			if ( $where == 'POST' || $where == 'GET' || $where == 'COOKIE' ||
				$where == 'SERVER' || $where == 'REQUEST' || $where == 'FILES' ||
				$where == 'SESSION'
			) {

				if (! isset( $GLOBALS['_'. $where ] ) ) { continue; }

				// Loop through the array:
				foreach ($GLOBALS['_' . $where] as $key => $val) {

					// Check user input access control
					if ( nfw_check_input_ac( $where, $key, $nfw_options,$ac_wl_input, $ac_bl_input ) == true ) {
						// Do not filter if input is whitelisted:
						continue;
					}

					if ( nfw_matching( $where, $key, $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
						// Rule matches, check next subrule:
						nfw_check_subrule( $where, $key, $nfw_rules, $nfw_options, $rules, $id );
					}

				}

				continue;
			}// GET, POST, COOKIE, SERVER...

			// =================================================================
			// HTTP_USER_AGENT, HTTP_REFERER, PHP_SELF, REQUEST_URI etc

			if ( isset( $_SERVER[$where] ) ) {

				if ( nfw_matching( 'SERVER', $where, $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
					// Rule matches, check next subrule:
					nfw_check_subrule( 'SERVER', $where, $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================
			// POST:xx, GET:xx, COOKIE:xxx, SERVER:xxx...:

			$w = explode(':', $where);

			// Look for temp hash
			if ( isset( $rules['cha'][1]['tmp'] ) && isset( $w[1] ) ) {
				$w[1] = @nfw_check_temp_hash( $w[0], $w[1] );
			}

			if ( empty($w[1]) || ! isset( $GLOBALS['_'.$w[0]][$w[1]] ) || nfw_disabled_scan( $w[0], $nfw_options ) ) {
				continue;
			}

			// Check user input access control
			if ( nfw_check_input_ac( $w[0], $w[1], $nfw_options,$ac_wl_input, $ac_bl_input ) == true ) {
				// Do not filter if input is whitelisted:
				continue;
			}

			if ( nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
				// Rule matches, check next subrule:
				nfw_check_subrule( $w[0], $w[1], $nfw_rules, $nfw_options, $rules, $id );
			}

			// =================================================================

		} // foreach ($wherelist as $where) {

	} // 	foreach ($nfw_rules as $rules_id => $rules_values) {
}

// =====================================================================
// Check hash found in a temporary rule (used for hotfix, 0-day etc).

function nfw_check_temp_hash( $where, $what ) {

	global $nfw_;

	if (is_array( $GLOBALS["_{$where}"] ) && ! empty( $GLOBALS["_{$where}"] ) ) {
		// Loop
		foreach( $GLOBALS["_{$where}"] as $key => $value ) {
			if ( is_string( $key ) ) {
				// Search in the cache
				if ( isset( $nfw_['hash'][$key] ) ) {
					if ( $nfw_['hash'][$key] == $what ) {
						return $key;
					}
				} else {
					// Save it to the cache
					$nfw_['hash'][$key] = md5( substr_replace( $key, 'nfw', 2, 0 ) );
					if ( $nfw_['hash'][$key] == $what ) {
						return $key;
					}
				}
			}
		}
	}
	return $what;
}

// =====================================================================
// Check user input access control.

function nfw_check_input_ac( $global, $key, $nfw_options,$ac_wl_input, $ac_bl_input ) {

	global $nfw_;

	// Check whitelisted input first:
	if ( isset( $ac_wl_input[$global][$key] ) ) {
		if (! empty($nfw_options['ac_wl_input_log']) ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'User input is in the Input Access Control whitelist',
				"$global:$key",
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
		}
		return true;
	}

	// Check blacklisted input:
	if ( isset( $ac_bl_input[$global][$key] ) ) {
		if (! empty($nfw_options['ac_bl_input_log']) ) {

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				'User input is in the Input Access Control blacklist',
				"$global:$key",
				NFWLOG_HIGH, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
		}
		nfw_block();
	}

	return false;
}

// =====================================================================

function nfw_check_subrule( $w0, $w1, $nfw_rules, $nfw_options, $rules, $id ) {

	// Capture ?
	if ( isset( $rules['cha'][1]['cap'] ) ) {
		nfw_matching( $w0, $w1, $nfw_rules, $rules, 2, $id, $nfw_options );

	} else {
		$w = explode(':', $rules['cha'][2]['whe']);

		if (! isset( $w[1] ) ) {
			// RAW data: we handle it separately:
			if ( $w[0] == 'RAW' ) {
				if ( nfw_disabled_scan( 'POST', $nfw_options) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					return;
				}
				global $HTTP_RAW_POST_DATA;
				if (! isset( $HTTP_RAW_POST_DATA ) ) {
					@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
				}
				nfw_matching( $_SERVER['REQUEST_METHOD'], 'RAW', $nfw_rules, $rules, 2, $id, $nfw_options, $HTTP_RAW_POST_DATA );
				return;
			}
			// HTTP_USER_AGENT, HTTP_REFERER, REQUEST_URI & al.:
			$w[2] = $w[1] = $w[0];
			$w[0] = 'SERVER';
		} else {
			$w[2] = null;

			// Look for temp hash
			if ( isset( $rules['cha'][2]['tmp'] ) ) {
				$w[1] = @nfw_check_temp_hash( $w[0], $w[1] );
			}
		}

		if (! isset( $GLOBALS['_'.$w[0]][$w[1]] ) ) {
			return;
		}

		if ( nfw_disabled_scan( $w[0], $nfw_options, $w[2] ) ) {
			return;
		} else {
			nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 2, $id, $nfw_options);
		}
	}

}

// =====================================================================

function nfw_disabled_scan( $where, $nfw_options, $extra = null ) {

	if ( $extra ) { $where = $extra; }   // Extra: HTTP_USER_AGENT/HTTP_REFERER

	if ( $where == 'POST' && empty($nfw_options['post_scan']) ||
		$where == 'GET' && empty($nfw_options['get_scan']) ||
		$where == 'COOKIE' && empty($nfw_options['cookies_scan']) ||
		$where == 'HTTP_USER_AGENT' && empty($nfw_options['ua_scan']) ||
		$where == 'HTTP_REFERER' && empty($nfw_options['referer_scan'])
	) {
		return 1;
	}
	return 0;
}

// =====================================================================

function nfw_matching( $where, $key, $nfw_rules, $rules, $subid, $id, $nfw_options, $RAW_POST = null ) {

	global $nfw_;

	if ( isset( $RAW_POST ) ) {
		$val = $RAW_POST;
	} else {
		$val = $GLOBALS['_'.$where][$key];
	}

	/**
	 * Check if the user has the required capability, if any.
	 */
	if ( isset( $rules['cpb'] ) ) {
		$allcaps = NinjaFirewall_session::read('allcaps');
		if (! empty( $allcaps ) ) {
			$caps = explode('|', $rules['cpb'] );
			foreach( $caps as $cap ) {
				if ( isset( $allcaps[$cap] ) ) {
					return 0;
				}
			}
		}
	}

	// Is this an array?
	if ( is_array($val) ) {
		if ( isset( $nfw_['flattened'][$where][$key] ) ) {
			$val = $nfw_['flattened'][$where][$key];
		} else {
			$val = nfw_flatten( ' ', $val );
			$nfw_['flattened'][$where][$key] = $val;
		}
	}

	// Look for base64 encoded injection:
	if ( $where == 'POST' && ! empty($nfw_options['post_b64']) && ! isset($nfw_['b64'][$where][$key]) && $val ) {
		nfw_check_b64($key, $val);
		$nfw_['b64'][$where][$key] = 1;
	}

	$transform = 1;
	// Check if we need to execute a function (NF < 4.1.1):
	if ( isset( $rules['cha'][$subid]['exe'] ) ) {
		$transform = 0;
		if ( function_exists( $rules['cha'][$subid]['exe'] ) ) {
			$val = @$rules['cha'][$subid]['exe']( $val );
		}
	}
	// Similar but chaining multiple functions (NF >= 4.1.1):
	if ( isset( $rules['cha'][$subid]['exm'] ) ) {
		$transform = 0;
		$exe = explode( '|', $rules['cha'][$subid]['exm'] );
		foreach ( $exe as $f ) {
			if (! function_exists( $f ) ) { break; }
			$val = @$f( $val );
		}
	}

	$t = '';

	// Check if we need to normalized the data:
	if ( isset( $rules['cha'][$subid]['nor'] ) ) {
		$t .= 'N';
		// Check if normalized already (only if it wasn't modified by executing a function call):
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = NinjaFirewall_data::normalize( $val, $nfw_rules );
			// Don't cache it, if rule required executing a function:
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	// Check if we need to transform/clean up the string from unwanted characters:
	if ( isset( $rules['cha'][$subid]['tra'] ) ) {
		$t .= 'T' . $rules['cha'][$subid]['tra'];
		//	Check if transformed already (only if it wasn't modified by executing a function call):
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			$val = $nfw_[$t][$where][$key];
		} else {

			$val = NinjaFirewall_data::transform( $val, $rules['cha'][$subid]['tra'] );

			// Don't cache it, if rule required executing a function:
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}
	if ( empty( $rules['cha'][$subid]['noc']) ) {
		$t .= 'C';
		// Compress blocks of white space characters:
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			// Use cached copy only if rule wasn't modified by executing a function call:
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = NinjaFirewall_data::compress( $val );
			// Don't cache it, if rule required executing a function:
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	// Check if it matches:
	if ( nfw_operator( $val, $rules['cha'][$subid]['wha'], $rules['cha'][$subid]['ope']	) ) {
		// Check if there is one or more subrules left to check:
		if ( isset( $rules['cha'][$subid+1]) ) {
			return 1;
		} else {
			// Write to the firewall log:
			if ( isset( $nfw_['flattened'][$where][$key] ) ) {

				// If it is an array, we write the flattened cached copy to the log:
				$nfw_['incidentID'] = NinjaFirewall_log::write(
					$rules['why'],
					"$where:$key = {$nfw_['flattened'][$where][$key]}",
					$rules['lev'], $id, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			} elseif ( isset( $RAW_POST ) ) {

				// RAW POST ?
				$nfw_['incidentID'] = NinjaFirewall_log::write(
					$rules['why'],
					"$where:$key = $RAW_POST",
					$rules['lev'], $id, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			} else {

				// Anything else:
				$nfw_['incidentID'] = NinjaFirewall_log::write(
					$rules['why'],
					"$where:$key = {$GLOBALS['_'.$where][$key]}",
					$rules['lev'], $id, $nfw_['nfw_options'], $nfw_['log_dir']
				);
			}
			nfw_block();
		}
	}
	return 0;
}

// =====================================================================

function nfw_operator( $val, $what, $op ) {

	if (! $val ) { return false; }

	// Check operator:
	if ( $op == 2 ) { // '!='
		if ( $val != $what ) {
			return true;
		}
	} elseif ( $op == 3 ) { // 'strpos'
		if ( strpos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 4 ) { // 'stripos'
		if ( stripos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 5 ) { // 'rx'
		if ( preg_match("`$what`", $val ) ) {
			return true;
		}
	} elseif ( $op == 6 ) { // '!rx'
		if (! preg_match("`$what`", $val) ) {
			return true;
		}
	} elseif ( $op == 7 ) { // '*'
		// Always return true:
		return true;

	} elseif ( $op == 8 ) { // '!strpos'
		if ( strpos($val, $what) === FALSE ) {
			return true;
		}
	} elseif ( $op == 9 ) { // '!stripos'
		if ( stripos($val, $what) === FALSE ) {
			return true;
		}
	} else { // '=='
		if ( $val == $what ) {
			return true;
		}
	}
}

// ===================================================================== 2023-05-16
// Flatten an array.

function nfw_flatten( $glue, $pieces ) {

	if ( defined('NFW_STATUS') ) {
		return;
	}

	$ret = [];

   foreach ( $pieces as $r_pieces ) {
      if ( is_array( $r_pieces ) ) {
         $ret[] = nfw_flatten( $glue, $r_pieces );
      } else {
			// Ignore empty keys, otherwise they would be
			// replaced with a white space character
			if (! empty( $r_pieces ) ) {
				$ret[] = $r_pieces;
			}
      }
   }
   return implode( $glue, $ret );
}

// =====================================================================

function nfw_check_b64( $key, $string ) {

	if ( defined('NFW_STATUS') || strlen( $string ) < 4 ) {
		return;
	}

	global $nfw_;

	$whitelist = [
		'fpd_print_order',		// Fancy Product Designer
		'g-recaptcha-response'	// reCAPTCHA
	];
	if ( in_array( $key, $whitelist ) ) {
		return;
	}

	$decoded = base64_decode( $string );
	if ( strlen($decoded) < 4 ) {
		return;
	}

	if ( preg_match( '`\b(?:\$?_(COOKIE|ENV|FILES|(?:GE|POS|REQUES)T|SE(RVER|SSION))|HTTP_(?:(?:POST|GET)_VARS|RAW_POST_DATA)|GLOBALS)\s*[=\[)]|\b(?i:array_map|assert|base64_(?:de|en)code|chmod|curl_exec|(?:ex|im)plode|error_reporting|eval|file(?:_get_contents)?|f(?:open|write|close)|fsockopen|function_exists|gzinflate|md5|move_uploaded_file|ob_start|passthru|[ep]reg_replace|phpinfo|stripslashes|strrev|(?:shell_)?exec|substr|system|unlink)\s*\(|[\s;]echo\s*[\'"]|<(?i:applet|embed|i?frame(?:set)?|marquee|object|script)\b|\W\$\{\s*[\'"]\w+[\'"]|<\?(?i:php|=)\s|(?i:(?:\b|\d)select\b.+?from\b.+?(?:\b|\d)where|(?:\b|\d)insert\b.+?into\b|(?:\b|\d)union\b.+?(?:\b|\d)select\b|(?:\b|\d)update\b.+?(?:\b|\d)set\b)|^.{0,25}[;{}]?\b[OC]:\d+:"[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*":\d+:{.*?}`', $decoded ) ) {
		// JetPack
		if ( $key === 'args' && ! defined('NFW_WPWAF') &&
			preg_match( '/^{"query":"SELECT/', $decoded ) &&
			strpos($_SERVER['SCRIPT_NAME'], '/jetpack-temp/jp-helper-') !== FALSE ) {
			return;
		}

		$nfw_['incidentID'] = NinjaFirewall_log::write(
			'BASE64-encoded injection',
			"POST:$key = $string",
			NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		nfw_block();
	}
}

// =====================================================================

function nfw_webfilter( $buffer ) {

	/**
	 * Global is required for nfw_connect() and NinjaFirewall_rules::get().
	 */
	global $nfw_;
	$nfw_ = [];

	/**
	 * Initialize timer.
	 */
	$nfw_['fw_starttime'] = nfw_fc_metrics('start');
	/**
	 * We are back :)
	 * But we need to get our options once again, as we can't inherit them.
	 */
	if ( nfw_connect() !== true ) {
		return $buffer;
	}

	if ( NinjaFirewall_rules::get('nfw_options') !== true ) {
		return $buffer;
	}

	if ( empty( $nfw_['nfw_options']['wf_pattern'] ) ) {
		return $buffer;
	}
	/**
	 * Log/cache dir ($nfw_['log_dir'] is needed for writing to the log).
	 */
	if ( defined('NFW_LOG_DIR') ) {
		$nfw_['log_dir'] = NFW_LOG_DIR .'/nfwlog';

	} elseif ( defined('WP_CONTENT_DIR') ) {
		$nfw_['log_dir'] = WP_CONTENT_DIR .'/nfwlog';

	} elseif (! empty( $nfw_['nfw_options']['wp_content'] ) ) {
		$nfw_['log_dir'] = $nfw_['nfw_options']['wp_content'] .'/nfwlog';

	} else {
		$nfw_['log_dir'] = dirname( dirname( dirname( __DIR__ ) ) ) . '/nfwlog';
	}
	$wf_timer_file = $nfw_['log_dir'] . '/cache/wf_timer.php';

	if ( is_file( $wf_timer_file ) ) {
		$wf_timer = file_get_contents( $wf_timer_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	}

	$now = time();
	/**
	 * Checks alert throttling.
	 */
	if (! empty( $wf_timer ) && $now - $wf_timer < $nfw_['nfw_options']['wf_alert'] * 60 ) {
		/**
		 * Don't spam the admin.
		 */
		return $buffer;
	}

	$wf_pattern = @explode('|', $nfw_['nfw_options']['wf_pattern'] );

	if ( empty( $nfw_['nfw_options']['wf_case'] ) ) {
		$str_funct = 'stripos';
	} else {
		$str_funct = 'strpos';
	}

	$nfw_found	= '';
	$wf_log		= '';
	foreach ( $wf_pattern as $wf_string ) {
		if ( empty( $wf_string ) ) {
			continue;
		}
		$pos = $str_funct( $buffer, $wf_string );
		if ( $pos !== false ) {
			$nfw_found .= "-[$wf_string] at offset $pos\n";
			$wf_log .= "$wf_string|";
		}
	}

	/**
	 * Leave here if we didn't find anything.
	 */
	if (! $wf_log || ! $nfw_found ) {
		return $buffer;
	}

	$nfw_['incidentID'] = NinjaFirewall_log::write(
		'Keyword(s) detected by Web Filter',
		rtrim( $wf_log, '|'),
		NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
	);

	/**
	 * Attach the HTML source if required.
	 */
	if (! empty( $nfw_['nfw_options']['wf_attach'] ) ) {
		$attachment = $buffer;
		$attachment_name = "ninjafirewall_{$now}.txt";
	/**
	 * No attachment.
	 */
	} else {
		$attachment			= '';
		$attachment_name	= '';
	}
	/**
	 * Alert the admin.
	 */
	$headers = 'From: "NinjaFirewall" <postmaster@'. $_SERVER['SERVER_NAME'] . ">\r\n";

	$subject = [];
	$content = [ $_SERVER['SERVER_NAME'], $_SERVER['SCRIPT_FILENAME'], $_SERVER['REQUEST_URI'],
					NFW_REMOTE_ADDR, date('F j, Y @ H:i:s T'), $nfw_found ];

	/* Should be loaded already */
	require_once __DIR__ .'/class_mail.php';

	NinjaFirewall_mail::PHPsend(
		$nfw_['nfw_options']['alert_email'], 'webfilter', $subject, $content,
		$nfw_['log_dir'], $headers, $attachment, $attachment_name
	);
	/**
	 * Alert throttling.
	 */
	@file_put_contents( $wf_timer_file, $now, LOCK_EX);
	/**
	 * Send the body response.
	 */
	return $buffer;
}

// ===================================================================== 2023-05-16
// Block the user and display a message.

function nfw_block() {

	if ( defined('NFW_STATUS') ) {
		return;
	}

	global $nfw_;

	// We don't block anyone if we are running in debugging mode
	if (! empty( $nfw_['nfw_options']['debug'] ) ) {
		return;
	}

	$http_codes = [
      400 => '400 Bad Request',
      403 => '403 Forbidden',
      404 => '404 Not Found',
      406 => '406 Not Acceptable',
      429 => '429 Too Many Requests',
      418 => "418 I'm a teapot",
      500 => '500 Internal Server Error',
      503 => '503 Service Unavailable'
   ];
   if (! isset( $http_codes[$nfw_['nfw_options']['ret_code']] ) ) {
		$nfw_['nfw_options']['ret_code'] = 403;
	}

	// Prepare the page to display to the blocked user
	if ( empty( $nfw_['incidentID'] ) ) {
		$nfw_['incidentID'] = '000000';
	}

	$tmp = str_replace(
		'%%NUM_INCIDENT%%',
		$nfw_['incidentID'],
		base64_decode( $nfw_['nfw_options']['blocked_msg'] )
	);

	if ( isset( $nfw_['nfw_options']['logo'] ) ) {
		$tmp = str_replace(
			'%%NINJA_LOGO%%',
			"<img alt='NinjaFirewall' src='{$nfw_['nfw_options']['logo']}' />",
			$tmp
		);
	}

	// Add the right IP to the message
	$tmp = str_replace('%%REM_ADDRESS%%', NFW_REMOTE_ADDR, $tmp );

	NinjaFirewall_session::delete();

	if (! headers_sent() ) {
		header("HTTP/1.1 {$http_codes[$nfw_['nfw_options']['ret_code']]}" );
		header("Status: {$http_codes[$nfw_['nfw_options']['ret_code']]}" );
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Expires: 0');
	}

	echo "<!DOCTYPE HTML PUBLIC '-//IETF//DTD HTML 2.0//EN'><html><head>".
		"<title>NinjaFirewall WP+ {$http_codes[$nfw_['nfw_options']['ret_code']]}</title>".
		"<style>body{font-family:sans-serif;font-size:13px;color:#000;}</style>".
		"<meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head>".
		"<body bgcolor='white'>$tmp</body></html>";
	exit;
}

// ===================================================================== 2023-05-16
// Block the request if a bot is detected.

function nfw_is_bot( $block = '') {

	global $nfw_;

	if ( empty( $_SERVER['HTTP_ACCEPT'] ) ||
		empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ||
		empty( $_SERVER['HTTP_USER_AGENT'] ) ||
		stripos( $_SERVER['HTTP_USER_AGENT'], 'Mozilla') === FALSE ) {

		if (! empty( $block ) ) {
			// Whitelist server IP and private addresses calling admin-ajax.php
			if ( $block == 'admin-ajax.php') {
				if ( NFW_REMOTE_ADDR == $_SERVER['SERVER_ADDR'] ||
					NFW_REMOTE_ADDR_PRIVATE == true ) {

					return true;
				}
				$block = 'Blocked access to admin-ajax.php';

			// No whitelist needed for the login page:
			} else {
				$block = 'Blocked access to the login page';
			}

			header('HTTP/1.0 404 Not Found');
			header('Pragma: no-cache');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Expires: 0');
			$nfw_['nfw_options']['ret_code'] = '404';

			$nfw_['incidentID'] = NinjaFirewall_log::write(
				$block,
				'bot detection is enabled',
				NFWLOG_MEDIUM, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			NinjaFirewall_session::delete();
			exit('404 Not Found');
		}

		return true;
	}
	return false;
}

// ===================================================================== 2023-05-16
// Handle HTTP response headers.

function nfw_response_headers() {

	if ( defined('NFW_CUSTHEADERS') ) {
		nfw_custom_headers();
	}

	if (! defined('NFW_RESHEADERS') ) {
		return;
	}

	$NFW_RESHEADERS = NFW_RESHEADERS;
	// NFW_RESHEADERS:
	// 0000000000
	// ||||||||||_ SameSite[0-2]
	// |||||||||__ Referrer-Policy [0-8]
	// ||||||||___ Content-Security-Policy (backend) [0-1]
	// |||||||____ Content-Security-Policy (frontend) [0-1]
	// ||||||_____ Strict-Transport-Security (includeSubDomains) [0-1]
	// |||||______ Strict-Transport-Security [0-4]
	// ||||_______ X-XSS-Protection [0-3]
	// |||________ X-Frame-Options [0-2]
	// ||_________ X-Content-Type-Options [0-1]
	// |__________ HttpOnly cookies [0-1]

	// Force HttpOnly and/or SameSite cookie
	if (! empty( $NFW_RESHEADERS[0] ) || ! empty( $NFW_RESHEADERS[9] ) ) {
		$rewrite = [];
		// Parse all response headers
		foreach (headers_list() as $header) {
			// Ignore it if it is not a cookie
			if ( strpos( $header, 'Set-Cookie:' ) === false ) { continue; }
			$extra = '';
			// HttpOnly
			if (! empty( $NFW_RESHEADERS[0] ) ) {
				// Does it have the HttpOnly flag on
				if ( stripos( $header, '; HttpOnly') === false) {
					$extra .= '; HttpOnly';
				}
			}
			// SameSite
			if (! empty( $NFW_RESHEADERS[9] ) ) {
				// Lax
				if ( $NFW_RESHEADERS[9] == 1 &&
					stripos( $header, '; SameSite=Lax' ) === false ) {

					$extra .= '; SameSite=Lax';
				// Strict
				} elseif ( $NFW_RESHEADERS[9] == 2 &&
					stripos( $header, '; SameSite=Strict' ) === false ) {

					$extra .= '; SameSite=Strict';
				}
			}
			// Save cookie
			$rewrite[] = "{$header}{$extra}";
		}

		// Shall we rewrite cookies
		if (! empty( $rewrite ) ) {
			// Remove all original cookies
			header_remove('Set-Cookie');
			foreach( $rewrite as $cookie ) {
				// Inject ours instead
				header( $cookie, false );
			}
		}
	}

	if (! empty( $NFW_RESHEADERS[1] ) ) {
		header('X-Content-Type-Options: nosniff');
	}

	if (! empty( $NFW_RESHEADERS[2] ) ) {
		if ($NFW_RESHEADERS[2] == 1) {
			header('X-Frame-Options: SAMEORIGIN');
		} else {
			header('X-Frame-Options: DENY');
		}
	}

	if ( empty( $NFW_RESHEADERS[3] ) ) {
		header('X-XSS-Protection: 0');
	} elseif ( $NFW_RESHEADERS[3] == 1 ) {
		header('X-XSS-Protection: 1; mode=block');
	} elseif ( $NFW_RESHEADERS[3] == 2 ) {
		header('X-XSS-Protection: 1');
	}

	if (! empty( $NFW_RESHEADERS[6] ) &&
		strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/') === FALSE ) {

		header('Content-Security-Policy: ' . CSP_FRONTEND_DATA);
	}
	if (! empty( $NFW_RESHEADERS[7] ) &&
		strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/') !== FALSE ) {

		header('Content-Security-Policy: ' . CSP_BACKEND_DATA);
	}

	if (! empty( $NFW_RESHEADERS[8] ) ) {
		if ( $NFW_RESHEADERS[8] == 1 ) {
			$rf = 'no-referrer';
		} elseif ( $NFW_RESHEADERS[8] == 2 ) {
			$rf = 'no-referrer-when-downgrade';
		} elseif ( $NFW_RESHEADERS[8] == 3 ) {
			$rf = 'origin';
		} elseif ( $NFW_RESHEADERS[8] == 4 ) {
			$rf = 'origin-when-cross-origin';
		} elseif ( $NFW_RESHEADERS[8] == 5 ) {
			$rf = 'strict-origin';
		} elseif ( $NFW_RESHEADERS[8] == 6 ) {
			$rf = 'strict-origin-when-cross-origin';
		} elseif ( $NFW_RESHEADERS[8] == 7 ) {
			$rf = 'same-origin';
		} else {
			$rf = 'unsafe-url';
		}
		header("Referrer-Policy: $rf");
	}

	// Stop here if no more headers
	if ( empty($NFW_RESHEADERS[4] ) ) {
		return;
	}

	// We don't send HSTS headers over HTTP
	if (! defined('NFW_IS_HTTPS') ) {
		nfw_is_https();
	}
	if ( NFW_IS_HTTPS == false ) {
		return;
	}

	if ($NFW_RESHEADERS[4] == 1) {
		// 1 month
		$max_age = 'max-age=2628000';
	} elseif ($NFW_RESHEADERS[4] == 2) {
		// 6 months
		$max_age = 'max-age=15768000';
	} elseif ($NFW_RESHEADERS[4] == 3) {
		// 12 months
		$max_age = 'max-age=31536000';
	} elseif ($NFW_RESHEADERS[4] == 4) {
		// Send an empty max-age to signal the UA to
		// cease regarding the host as a known HSTS Host
		$max_age = 'max-age=0';
	} else {
		// 24 months
		$max_age = 'max-age=63072000';
	}
	if (! empty( $NFW_RESHEADERS[5] ) ) {
		if ( $NFW_RESHEADERS[5] == 1 ) {
			$max_age .= '; includeSubDomains';
		} elseif ( $NFW_RESHEADERS[5] == 2 ) {
				$max_age .= '; preload';
		} else {
			$max_age .= '; includeSubDomains; preload';
		}
	}
	header('Strict-Transport-Security: '. $max_age);
}

// ===================================================================== 2023-05-16

function nfw_custom_headers() {

	$headers = json_decode( NFW_CUSTHEADERS, true );
	if (! empty( $headers ) ) {
		foreach( $headers as $key => $value ) {
			header( "$key: $value" );
		}
	}
}

// =====================================================================
// EOF

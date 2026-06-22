<?php
/*
Plugin Name: NinjaFirewall (WP+)
Plugin URI: https://nintechnet.com/
Description: A true Web Application Firewall to protect and secure WordPress.
Version: 4.8.5
Author: The Ninja Technologies Network
Author URI: https://nintechnet.com/
Network: true
Text Domain: nfwplus
Domain Path: /languages
Update URI: https://nintechnet.com/nfwplus/
*/
define('NFW_ENGINE_VERSION', '4.8.5');
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

if (! defined('ABSPATH') ) {
	die('Forbidden');
}

/* ================================================================== */
// Some constants & variables first
define('NFW_NULL_BYTE', 2);
define('NFW_ASCII_CTRL', 500);
define('NFW_DOC_ROOT', 510);
define('NFW_WRAPPERS', 520);
define('NFW_OBJECTS', 525);
define('NFW_LOOPBACK', 540);
define('NFW_BOT_LIST', 'acunetix|backdoor|bandit|' .
	'blackwidow|BOT for JCE|core-project|dts agent|emailmagnet|' .
	'exploit|extract|flood|grabber|harvest|httrack|havij|hunter|indy library|' .
	'LoadTimeBot|mfibot|Microsoft URL Control|Miami Style|morfeus|' .
	'nessus|NetLyzer|pmafind|scanner|Scrapy|siphon|spbot|sqlmap|' .
	'survey|teleport|updown_tester|xovibot|zgrap|zmap'
);
define('NFW_DEFAULT_MSG', '<br /><br /><br /><br /><center>' .
		sprintf('Sorry %s, your request cannot be processed.', '<b>%%REM_ADDRESS%%</b>') .
		'<br />' . 'For security reasons, it was blocked and logged.' .
		'<br /><br />%%NINJA_LOGO%%<br /><br />' .
		'If you believe this was an error please contact the<br />webmaster and enclose the '.
		'following incident ID:' .	'<br /><br />[ <b>#%%NUM_INCIDENT%%</b> ]</center>'
);

/**
 * Since WP 6.7, translation loading must not be triggered too early.
 */
require_once __DIR__ . '/lib/i18n.php';

if (! defined('NFW_LOG_DIR') ) {
	define('NFW_LOG_DIR', WP_CONTENT_DIR );
}
if (! empty( $_SERVER['DOCUMENT_ROOT'] ) && $_SERVER['DOCUMENT_ROOT'] != '/') {
	$_SERVER['DOCUMENT_ROOT'] = rtrim( $_SERVER['DOCUMENT_ROOT'] , '/');
}

/* ================================================================== */

/**
 * Select whether we want to use PHP or NF (default since v4.8.1) sessions.
 */
if ( is_file( NFW_LOG_DIR .'/nfwlog/phpsession') ) {
	require_once __DIR__ .'/lib/class-php-session.php';
} else {
	if (! defined('NFWSESSION_DIR') ) {
		/**
		 * NFWSESSION_DIR can be defined in the .htninja.
		 */
		define('NFWSESSION_DIR', NFW_LOG_DIR .'/nfwlog/session');
	}
	require_once __DIR__ .'/lib/class-nfw-session.php';
}

if (! defined('NFW_REMOTE_ADDR') ) {
	/**
	 * Error: the firewall isn't loaded.
	 */
	require_once __DIR__ .'/lib/class-ip.php';
	NinjaFirewall_IP::check_ip( ['ac_ip' => 1 ] );
}

/**
 * Those classes and constants could be already loaded/defined by the firewall (if enabled).
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
require_once __DIR__ .'/lib/class-firewall-log.php';
require_once __DIR__ . '/lib/class-helpers.php';
require_once __DIR__ .'/lib/class_mail.php';

require __DIR__ . '/lib/scheduled_tasks.php';
require __DIR__ . '/lib/utils.php';
require __DIR__ . '/lib/events.php';

add_action('nfwgccron', 'nfw_garbage_collector');

/* ================================================================== */

function nfw_activate() {

	// Install/activate NinjaFirewall

	if ( defined('WP_CLI') && WP_CLI && PHP_SAPI === 'cli' ) {
		$php_cli = true;
	}

	if (! isset( $php_cli ) ) {
		// Warn if the user does not have the 'unfiltered_html' capability:
		if (! current_user_can('unfiltered_html') ) {
			exit( esc_html__('You do not have "unfiltered_html" capability. Please enable it in order to run NinjaFirewall (or make sure you do not have "DISALLOW_UNFILTERED_HTML" in your wp-config.php script).', 'nfwplus'));
		}
		// Block immediately if user is not allowed
		nf_not_allowed( 'block', __LINE__ );
	}

	// WordPress minimum version
	global $wp_version;
	if ( version_compare( $wp_version, '4.7.0', '<' ) ) {
		exit( sprintf( esc_html__('NinjaFirewall requires WordPress %s or greater but your current version is %s.', 'nfwplus'), '4.7.0', $wp_version) );
	}

	// PHP  minimum version
	if ( version_compare( PHP_VERSION, '7.1.0', '<' ) ) {
		exit( sprintf( esc_html__('NinjaFirewall requires PHP 7.1 or greater but your current version is %s.', 'nfwplus'), PHP_VERSION) );
	}

	// We need the mysqli extension loaded
	if (! function_exists('mysqli_connect') ) {
		exit( sprintf( esc_html__('NinjaFirewall requires the PHP %s extension.', 'nfwplus'), '<code>mysqli</code>') );
	}

	// Yes, there are still some people who have SAFE_MODE enabled with
	// PHP 5.3 ! We must check that right away otherwise the user may lock
	// himself/herself out of the site as soon as NinjaFirewall will be
	// activated
	if ( ini_get( 'safe_mode' ) ) {
		exit( esc_html__('You have SAFE_MODE enabled. Please disable it, it is deprecated as of PHP 5.3.0 (see http://php.net/safe-mode).', 'nfwplus'));
	}

	// We don't do Windows
	if ( PATH_SEPARATOR == ';' ) {
		exit( esc_html__('NinjaFirewall is not compatible with Microsoft Windows.', 'nfwplus') );
	}

	if (! $nfw_options = nfw_get_option( 'nfw_options' ) ) {
		// First time we're running: download the security rules
		// and populate the options:
		require_once __DIR__ .'/lib/install_default.php';
		nfw_load_default_conf();
		// Reload them
		$nfw_options = nfw_get_option( 'nfw_options' );
	} else {
		// (Re)create the loader
		require_once __DIR__ .'/lib/install_default.php';
		nfw_create_loader();
	}

	$nfw_options['enabled'] = 1;
	nfw_update_option( 'nfw_options', $nfw_options);

	$res = nfw_enable_wpwaf();
	if (! empty( $res ) ){
		exit( $res );
	}

	// Create scheduled tasks.
	nfw_create_scheduled_tasks();

	// Re-enable brute-force protection
	if ( file_exists( NFW_LOG_DIR . '/nfwlog/cache/bf_conf_off.php' ) ) {
		rename(NFW_LOG_DIR . '/nfwlog/cache/bf_conf_off.php', NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php');
	}
}

register_activation_hook( __FILE__, 'nfw_activate' );

/* ================================================================== */

function nfw_deactivate() {

	if ( defined('WP_CLI') && WP_CLI && PHP_SAPI === 'cli') {
		$php_cli = true;
	}

	if (! isset( $php_cli ) ) {
		/**
		 * Warn if the user does not have the 'unfiltered_html' capability unless it's CLI.
		 */
		if (! current_user_can('unfiltered_html') ) {
			exit( esc_html__('You do not have "unfiltered_html" capability. Please enable it in order to run NinjaFirewall (or make sure you do not have "DISALLOW_UNFILTERED_HTML" in your wp-config.php script).', 'nfwplus') );
		}
		nf_not_allowed('block', __LINE__ );

		global $current_user;
		$current_user	= wp_get_current_user();
		$user_login		= $current_user->user_login;
		$user_roles		= $current_user->roles[0];
	} else {
		$user_login		= 'WP CLI';
		$user_roles		= '-';
	}

	$nfw_options = nfw_get_option('nfw_options');

	/**
	 * Re-used code from Firewall Options.
	 */
	if ( empty( $_REQUEST['action'] ) || strpos( $_REQUEST['action'], 'deactivate') === false ) {

		if ( is_multisite() ) {
			$url = network_home_url('/');
		} else {
			$url = home_url('/');
		}

		$subject = [ ];
		$content = [ "$user_login ($user_roles)", NFW_REMOTE_ADDR,
						ucfirst( date_i18n('F j, Y @ H:i:s O') ), $url ];

		NinjaFirewall_mail::send('disabled', $subject, $content, '', [], 1 );
	}

	$nfw_options['enabled'] = 0;
	nfw_disable_wpwaf();

	/**
	 * If shmop is enabled, we close and delete it.
	 */
	if (! empty( $nfw_options['shmop'] ) ) {
		nfw_shm_delete(0);
	}

	/**
	 * Disable brute-force protection.
	 */
	if ( file_exists( NFW_LOG_DIR . '/nfwlog/cache/bf_conf.php') ) {
		rename( NFW_LOG_DIR .'/nfwlog/cache/bf_conf.php', NFW_LOG_DIR .'/nfwlog/cache/bf_conf_off.php');
	}

	nfw_update_option('nfw_options', $nfw_options );

	/**
	 * Remove any existing cron.
	 */
	nfw_delete_scheduled_tasks();

}

register_deactivation_hook( __FILE__, 'nfw_deactivate');

// =====================================================================
// Load script/style files

function nfw_load_ext( $hook ) {

	// Load the external JS script and CSS:
	// -Single site: to the admin only.
	// -Multi-site: to the superadmin and from the main network admin screen only.
	// -All: only if this is a NinjaFirewall menu page
	if (! current_user_can('activate_plugins') || ! is_main_site() ) { return; }
	if ( stripos( $hook, "ninjafirewall" ) === false ) { return; }

	if ( strpos ( $hook, 'nfsublog' ) !== false ) {
		// Load jquery-effects-core for log page (WP+ only)
		$extra_js = ['jquery', 'jquery-effects-core'];
	} else {
		$extra_js = ['jquery'];
	}

	wp_enqueue_script(
		'nfw_javascript',
		plugin_dir_url( __FILE__ ) . 'static/ninjafirewall.js',
		$extra_js,
		NFW_ENGINE_VERSION
	);

	// Load Chart.js if we are viewing the statistics page:
	if ( strpos( $hook, 'NinjaFirewall' ) !== false ) {
		wp_enqueue_script(
			'nfw_charts',
			plugin_dir_url( __FILE__ ) . 'static/chart.min.js',
			['jquery'],
			NFW_ENGINE_VERSION,
			// We load it in the footer, because some plugins loads it too
			// on every pages and that could mess with our pages
			true
		);
	}

	wp_enqueue_style(
		'nfw_style',
		plugin_dir_url( __FILE__ ) .'static/ninjafirewall.css',
		null,
		NFW_ENGINE_VERSION,
		false
	);

	// Javascript i18n:
	$nfw_js_array = [

		// Generic
		'restore_default' =>
			__('All fields will be restored to their default values and any changes you made will be lost. Continue?', 'nfwplus'),

		// Full WAF/WordPress WAF
		'missing_nonce' =>
			__('Missing security nonce, try to reload the page.', 'nfwplus'),
		'missing_httpserver' =>
			__('Please select the HTTP server in the list.', 'nfwplus'),
		// Dashboard
		'del_errorlog' =>
			__('Delete the firewall\'s error log ?', 'nfwplus'),

		// Firewall Options
		'restore_warning' =>
			__('This action will restore the selected configuration file and will override all your current firewall options, policies and rules. Continue?', 'nfwplus'),

		// Firewall Policies
		'warn_sanitise' =>
			__('Any character that is not a letter [a-zA-Z], a digit [0-9], a dot [.], a hyphen [-] or an underscore [_] will be removed from the filename and replaced with the substitution character. Continue?', 'nfwplus'),
		'ssl_warning' =>
			__('Ensure that you can access your admin console over HTTPS before enabling this option, otherwise you will lock yourself out of your site. Continue?', 'nfwplus'),
		'woo_warning' =>
			__("WooCommerce is running: if you block accounts creation, your customers won't be able to sign up. Continue?", 'nfwplus'),
		'reguser_warning' =>
			__("Your blog has user registration enabled: if you block accounts creation, your customers won't be able to sign up. Continue?", 'nfwplus'),
		'regsite_warning' =>
			__("Your multisite installation allows users to register new sites: if you enable this option, they will likely get blocked when creating their blog. Continue?", 'nfwplus'),

		// Access Control
		'country_warning' =>
			__('Warning: you have selected to block all available countries in the Geolocation Access Control, you may lock yourself out of your site. Are you sure you want to continue?', 'nfwplus'),
		'input_warning' =>
			__('Enabling this option can result in a lot of entries written to the firewall log. Consider using it only for debugging purposes. Continue?', 'nfwplus'),

		// File Check
		'del_snapshot' =>
			__('Delete the current snapshot ?', 'nfwplus'),

		// Login Protection
		'invalid_char' =>
			__('Invalid character.', 'nfwplus'),
		'no_admin' =>
			__('"admin" is not acceptable, please choose another user name.', 'nfwplus'),
		'max_char' =>
			__('Please enter max 1024 character only.', 'nfwplus'),
		'select_when' =>
			__('Select when to enable the login protection.', 'nfwplus'),
		'missing_auth' =>
			__('Enter a name and a password for the HTTP authentication.', 'nfwplus'),

		// Web Filter
		'empty_fields' =>
			__('Enter at least one keyword or disable the Web Filter.', 'nfwplus'),
		'wrong_length' =>
			__('Keywords must be from 4 to maximum 150 characters.', 'nfwplus'),
		'disallow_char' =>
			__('The vertical bar "|" character is not allowed.', 'nfwplus'),

		// Antispam
		'as_empty' =>
			__('Please select at least one option for [Apply protection to] or disable the antispam protection.', 'nfwplus'),

		// Firewall Log
		'no_record' =>
			__('No records were found that match the specified search criteria.', 'nfwplus'),
		'invalid_key' =>
			__('Your public key is not valid.', 'nfwplus'),
		'missing_address' =>
			__('Please enter an IP address.', 'nfwplus'),

		// Centralized Logging
		'pukey_1' =>
			__('Click the "Save Options" button to generate your new public key.', 'nfwplus'),
		'pukey_2' =>
			__('You will need to upload that new key to the remote server(s).', 'nfwplus'),
		'missing_key' =>
			__('Please enter a secret key, from 30 to 100 ASCII printable characters. It will be used to generate your public key.', 'nfwplus'),
		'missing_ip' =>
			__('Please enter this server IP address.', 'nfwplus'),
		'missing_url' =>
			__('Please enter the remote websites URL.', 'nfwplus'),

		// Live Log
		'live_log_desc' =>
			__('Live Log lets you watch your blog traffic in real time. To enable it, click on the button below.', 'nfwplus'),
		'no_traffic' =>
			__('No traffic yet, please wait', 'nfwplus'),
		'seconds' =>
			' ' . __('seconds...', 'nfwplus'),
		'err_unexpected' =>
			__('Error: Live Log did not receive the expected response from your server:', 'nfwplus'),
		'error_404' =>
			__('Error: URL does not seem to exist (404 Not Found):', 'nfwplus'),
		'log_not_found' =>
			__('Error: Cannot find your log file. Try to reload this page.', 'nfwplus'),
		'http_error' =>
			__('Error: The HTTP server returned the following error code:', 'nfwplus')
	];

	wp_localize_script( 'nfw_javascript', 'nfwi18n', $nfw_js_array );
}

add_action( 'admin_enqueue_scripts', 'nfw_load_ext' );

// =====================================================================

function nfw_admin_init() {

	// We must make sure the current PHP session is updated
	// even for whitelisted non-admin users (must be logged-in
	// to prevent unauthenticated AJAX calls to trigger it):
	if ( is_user_logged_in() ) {
		NinjaFirewall_session::start();
		// Save user's capabilities
		$nf_user = wp_get_current_user();
		if ( $nf_user instanceof WP_User ) {
			NinjaFirewall_session::write( ['allcaps' => $nf_user->allcaps ] );
		}
	}

	$nfw_options = nfw_get_option( 'nfw_options' );
	$nfw_rules = nfw_get_option( 'nfw_rules' );

	// Post-update adjustment:
	require plugin_dir_path(__FILE__) . 'lib/init_update.php';

	// Make sure cronjobs are running as expected
	nfw_verify_scheduled_tasks();

	// Can apply to different roles (unlike the WP Edition):
	if (! empty( $nfw_options['ac_roles'] ) && nfw_is_goodguy( null ) ) {
		if (! empty( $nfw_options['bf_enable'] ) && ! empty( $nfw_options['bf_rand'] ) ) {
			NinjaFirewall_session::write( ['nfw_goodguy' => true, 'nfw_bfd' => $nfw_options['bf_rand'] ] );
		} else {
			NinjaFirewall_session::write( ['nfw_goodguy' => true ] );
		}

	} else {
		NinjaFirewall_session::delete('nfw_goodguy');
	}

	// --------------------------------------------
	// Anything below requires admin authentication
	// --------------------------------------------

	if ( nf_not_allowed(0, __LINE__) ) { return; }

	// Create our unique PID
	$nfw_pid = NFW_LOG_DIR .'/nfwlog/cache/.pid';
	if (! file_exists( $nfw_pid ) ) {
		file_put_contents( $nfw_pid, uniqid('', true) );
	}

	// Update fallback loader if needed
	if ( wp_doing_ajax() == false ) {
		nfw_enable_wpwaf();
	}

	// No shared memory option available when running in WordPress WAF mode
	if ( defined('NFW_WPWAF') && ! empty( $nfw_options['shmop'] ) ) {
		$nfw_options['shmop'] = 0;
		nfw_update_option( 'nfw_options', $nfw_options);
		nfw_shm_delete(0);
	}

	// Check/update our shared memory block on shutdown:
	if ( defined( 'NFW_STATUS' ) ) {
		register_shutdown_function('nfw_shm_check');
	}

	// Security update in WP plugins
	global $pagenow;
	if ( $pagenow == 'plugins.php' && current_user_can( 'update_plugins' ) ) {
		nfw_verify_secupdates();
	}

	/**
	 * Export NinjaFirewall's configuration.
	 */
	if ( isset( $_POST['ninjafirewall_export'] ) ) {

		require_once __DIR__ .'/lib/class-import-export.php';
		NinjaFirewall_ImpExp::export();
	}

	// Download the firewall log:
	if ( isset($_GET['nfw_export']) && ! empty($_GET['nfw_logname']) ) {
		if ( empty($_GET['nfwnonce']) || ! wp_verify_nonce($_GET['nfwnonce'], 'log_select') ) {
			wp_nonce_ays('log_select');
		}
		$log = trim($_GET['nfw_logname']);
		if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', $log, $match ) ) {
			wp_nonce_ays('log_select');
		}
		$name = $match[1];
		if (! file_exists(NFW_LOG_DIR . '/nfwlog/' . $log) ) {
			wp_nonce_ays('log_select');
		}
		$data = file(NFW_LOG_DIR . '/nfwlog/' . $log);
		$res = "Date\tIncident\tLevel\tRule\tIP\tRequest\tEvent\tHost\n";
		$levels = ['', 'MEDIUM', 'HIGH', 'CRITICAL', 'ERROR', 'UPLOAD', 'INFO', 'DEBUG_ON'];
		$severity = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
		foreach( $data as $line ) {
			if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})(?:-|\]\s+\[)(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-Fx, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:|b64:)?(.+)\]$/', $line, $match ) ) {
				if ( empty( $match[4]) ) { $match[4] = '-'; }
				if ( $match[10] == 'hex:' ) { $match[11] = pack('H*', $match[11]); }
				if ( $match[10] == 'b64:' ) { $match[11] = base64_decode( $match[11]); }
				$res .= date( 'd/M/y H:i:s', $match[1] ) . "\t" . $match[3] . "\t" .
				$levels[$match[5]] . "\t" . $match[4] . "\t" . $match[6] . "\t" .
				$match[7] . ' ' . $match[8] . "\t" .	$match[9] .
				' - [' . $match[11] . "]\t" . $match[2] . "\n";
			}
		}
		header('Content-Type: text/tab-separated-values');
		header('Content-Length: '. strlen( $res ) );
		header('Content-Disposition: attachment; filename="' . $name . 'tsv"');
		echo $res;
		exit;
	}

	// Download File Check modified files list:
	if ( isset($_POST['dlmods']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'filecheck_save') ) {
			wp_nonce_ays('filecheck_save');
		}
		if (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php') ) {
			$download_file = NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php';
		} elseif (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php.php') ) {
			$download_file = NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_diff.php.php';
		} else {
			wp_nonce_ays('filecheck_save');
		}
		$stat = stat($download_file);
		$data = '== NinjaFirewall File Check (diff)'. "\n";
		$data.= '== ' . site_url() . "\n";
		$data.= '== ' . date_i18n('M d, Y @ H:i:s O', $stat['ctime']) . "\n\n";
		$data.= '[+] = ' . __('New file', 'nfwplus') .
					'      [!] = ' . __('Modified file', 'nfwplus') .
					'      [-] = ' . __('Deleted file', 'nfwplus') .
					"\n\n";
		$fh = fopen($download_file, 'r');
		while (! feof($fh) ) {
			$res = explode('::', fgets($fh) );
			if ( empty($res[1]) ) { continue; }
			// New file
			if ($res[1] == 'N') {
				$data .= '[+] ' . $res[0] . "\n";
			// Deleted file
			} elseif ($res[1] == 'D') {
				$data .= '[-] ' . $res[0] . "\n";
			// Modified file
			} elseif ($res[1] == 'M') {
				$data .= '[!] ' . $res[0] . "\n";
			}
		}
		fclose($fh);
		$data .= "\n== EOF\n";

		// Download
		header('Content-Type: text/plain');
		header('Content-Length: '. strlen( $data ) );
		header('Content-Disposition: attachment; filename="'. $_SERVER['SERVER_NAME'] .'_diff.txt"');
		echo $data;
		exit;
	}

	// Download File Check snapshot:
	if ( isset($_POST['dlsnap']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'filecheck_save') ) {
			wp_nonce_ays('filecheck_save');
		}
		if (file_exists(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php') ) {
			$stat = stat(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php');
			$data = '== NinjaFirewall File Check (snapshot)'. "\n";
			$data.= '== ' . site_url() . "\n";
			$data.= '== ' . date_i18n('M d, Y @ H:i:s O', $stat['ctime']) . "\n\n";
			$fh = fopen(NFW_LOG_DIR . '/nfwlog/cache/nfilecheck_snapshot.php', 'r');
			while (! feof($fh) ) {
				$res = explode('::', fgets($fh) );
				if (! empty($res[0][0]) && $res[0][0] == '/') {
					$data .= $res[0] . "\n";
				}
			}
			fclose($fh);
			$data .= "\n== EOF\n";
			// Download
			header('Content-Type: text/plain');
			header('Content-Length: '. strlen( $data ) );
			header('Content-Disposition: attachment; filename="'. $_SERVER['SERVER_NAME'] .'_snapshot.txt"');
			echo $data;
			exit;
		} else {
			wp_nonce_ays('filecheck_save');
		}
	}

}

add_action('admin_init', 'nfw_admin_init' );

// ==================================================================
// Check if the user wants to remove her email from the notification list.

function nfw_init_emailremoval() {

	if (! empty( $_GET['nfw_stop_notification'] ) ) {
		require_once 'lib/email_sodium.php';
		nfw_sodium_decrypt( $_GET['nfw_stop_notification'] );
	}

}
add_action('init', 'nfw_init_emailremoval' );

// ==================================================================
// Check if the user is an admin and if we must whitelist them.

function nfw_login_hook( $user_login, $user ) {

	NinjaFirewall_session::start();

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Don't do anything if NinjaFirewall is disabled:
	if ( empty( $nfw_options['enabled'] ) ) { return; }

	// Fetch user roles:
	$whoami = '';
	foreach( $user->roles as $k => $v ) {
		if ( $v == 'administrator' ) {
			$admin_flag = 1;
		}
		$whoami .= "$v ";
	}
	$whoami = trim( $whoami );

	// Still nothing: Maybe an additional superadmin
	if ( empty( $whoami ) && is_multisite() ) {
		// $user->ID is required here
		if ( is_super_admin( $user->ID ) ) {
			$admin_flag = 1;
			$whoami = 'administrator';
		}
	}

	// Are we supposed to send an alert?
	if (! empty( $nfw_options['a_0'] ) ) {
		if ( ( $nfw_options['a_0'] == 1 && isset( $admin_flag ) ) || $nfw_options['a_0'] == 2 ) {

			nfw_send_loginemail( $user_login, $whoami );
			/**
			 * Write event to log.
			 */
			if (! empty( $nfw_options['a_41'] ) ) {
				NinjaFirewall_log::write(
					'Logged in user',
					"{$user_login} ({$whoami})",
					NFWLOG_INFO, 0, $nfw_options, NFW_LOG_DIR .'/nfwlog'
				);
			}
		}
	}

	if ( nfw_is_goodguy( $user->roles ) ) {
		// Set the goodguy flag
		NinjaFirewall_session::write( ['nfw_goodguy' => true ] );
		return;
	}
	NinjaFirewall_session::delete('nfw_goodguy');
}

// Hook priority can be defined in the wp-config.php or .htninja
if ( defined('NFW_LOGINHOOK') ) {
	$NFW_LOGINHOOK = (int) NFW_LOGINHOOK;
} else {
	$NFW_LOGINHOOK = -999999999;
}
add_action( 'wp_login', 'nfw_login_hook', $NFW_LOGINHOOK, 2 );

/* ================================================================== */

function nfw_logout_hook() {

	NinjaFirewall_session::start();

	// Whoever it was, we clear the goodguy flag
	NinjaFirewall_session::delete('nfw_goodguy');
	// And the Live Log flag as well
	NinjaFirewall_session::delete('nfw_livelog');
	NinjaFirewall_session::delete('allcaps');
}

add_action( 'wp_logout', 'nfw_logout_hook' );

/* ================================================================== */
// FullWAF upgrade AJAX function.

add_action( 'wp_ajax_nfw_fullwafsetup', 'nfw_fullwafsetup' );

function nfw_fullwafsetup() {

	nf_not_allowed( 'block', __LINE__ );

	if (! check_ajax_referer( 'events_save', 'nonce', false ) ) {
		esc_html_e('Error: Security nonces do not match. Reload the page and try again.', 'nfwplus');
		wp_die();
	}

	$nfw_options = nfw_get_option( 'nfw_options' );
	if ( empty( $nfw_options['enabled'] ) ) {
		esc_html_e('Error: NinjaFirewall is disabled', 'nfwplus');
		wp_die();
	}

	if ( empty( $_POST['httpserver'] ) ) {
		printf( esc_html__('Error: missing parameter (%s).', 'nfwplus'), 'httpserver' );
		wp_die();
	}
	if ( preg_match('/^[^1-8]$/', $_POST['httpserver'] ) ) {
		printf( esc_html__('Error: wrong parameter value (%s).', 'nfwplus'), 'httpserver' );
		wp_die();
	}
	if ( empty( $_POST['diy'] ) || ! preg_match( '/^(nfw|usr)$/', $_POST['diy'] ) ) {
		printf( esc_html__('Error: wrong parameter value (%s).', 'nfwplus'), 'diy' );
		wp_die();
	}

	// Retrieve the list of excluded folders, if any, and save it
	nfw_save_waf_exclusionlist( $_POST['exclude_waf_list'] );

	// Disable the sandbox?
	if ( empty( $_POST['sandbox'] ) ) {
		define('NFW_BYPASS_SANDBOX', true);
	}

	$time = time() + 300;

	// 1: Apache mod_php
	// 2: Apache + CGI/FastCGI or PHP-FPM
	// 3: Apache + suPHP
	// 4: Nginx + CGI/FastCGI or PHP-FPM
	// 5: Litespeed
	// 6: Openlitespeed
	// 7: Other webserver + CGI/FastCGI or PHP-FPM
	// 8: Apache + LSAPI
	$httpserver = (int) $_POST['httpserver'];

	// [6] Openlitespeed: nothing to do.
	if ( $httpserver == 6 ) {
		set_transient( 'nfw_fullwaf', "{$httpserver}:{$time}", 60 * 5 );
		echo '200';
		wp_die();
	}

	require_once __DIR__ .'/lib/install.php';

	// .htaccess mods only
	if ( $httpserver == 1 || $httpserver == 5 || $httpserver == 8 ) {
		// User wants to make the modification
		if ( $_POST['diy'] == 'usr' ) {
			// Nothing to do
			set_transient( 'nfw_fullwaf', "{$httpserver}:{$time}", 60 * 5 );
			echo '200';
			wp_die();
		}
		// Make changes
		$ret = nfw_fullwaf_htaccess( $httpserver );
		if ( $ret !== true ) {
			echo $ret;
		} else {
			set_transient( 'nfw_fullwaf', "{$httpserver}:{$time}", 60 * 5 );
			echo '200';
		}
		wp_die();
	}

	if ( $_POST['diy'] == 'usr' ) {
		// Nothing to do, but add 5-minute notice to the overview page
		// because an INI file is being used
		set_transient( 'nfw_fullwaf', "{$httpserver}:{$time}", 60 * 5 );
		echo '200';
		wp_die();
	}

	// [1] .user.ini
	// [2] php.ini
	if ( empty ( $_POST['initype'] ) || ! preg_match( '/^[12]$/', $_POST['initype'] ) ) {
		$initype = 1;
	} else {
		$initype = (int) $_POST['initype'];
	}

	if ( $httpserver == 3 ) { // Apache + suPHP
		// Set up the htaccess file
		$ret = nfw_fullwaf_htaccess( $httpserver );
		if ( $ret !== true ) {
			echo $ret;
			wp_die();
		}
	}
	// ini file
	$ret = nfw_fullwaf_ini( $httpserver, $initype );
	if ( $ret !== true ) {
		echo $ret;
		wp_die();
	} else {
		// Add 5-minute notice to the overview page
		// because an INI file is being used
		set_transient( 'nfw_fullwaf', "{$httpserver}:{$time}", 60 * 5 );
		echo 200;
	}
	wp_die();
}

// =====================================================================
// Configure Full WAF mode or fallback to WP WAF mode. AJAX action.

add_action( 'wp_ajax_nfw_fullwafconfig', 'nfw_fullwafconfig' );

function nfw_fullwafconfig() {

	nf_not_allowed( 'block', __LINE__ );

	if (! check_ajax_referer( 'events_save', 'nonce', false ) ) {
		esc_html_e('Error: Security nonces do not match. Reload the page and try again.', 'nfwplus');
		wp_die();
	}

	if ( empty( $_POST['what'] ) || ! preg_match( '/^[12]$/', $_POST['what'] ) ) {
		printf( esc_html__('Error: missing parameter (%s).', 'nfwplus'), 'what' );
		wp_die();
	}

	// Downgrade to WP WAF
	if ( $_POST['what'] == 2 ) {

		require __DIR__ .'/lib/install.php';
		nfw_get_constants();
		nfw_remove_directives();

	// Full WAF directories exclusion
	} else {
		// Retrieve the list of excluded folders, if any, and save it
		nfw_save_waf_exclusionlist( $_POST['list'] );
	}

	wp_die(200);
}

// =====================================================================
// Save new exclusion list.

function nfw_save_waf_exclusionlist( $input ) {

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Retrieve the list of excluded folders, if any, and save it
	$tmp_exclude_waf_list = json_decode( stripslashes( $input ) );
	if ( $tmp_exclude_waf_list === false || $tmp_exclude_waf_list === null ) {
		printf( esc_html__('Error: missing parameter (%s).', 'nfwplus'), 'list' );
		wp_die();
	}
	$exclude_waf_list = [];
	if (! empty( $tmp_exclude_waf_list ) ) {
		foreach( $tmp_exclude_waf_list as $folder ) {
			if ( is_dir( ABSPATH . $folder ) ) {
				$exclude_waf_list[] = $folder;
			}
		}
	}
	// Update/clear the list
	if (! empty( $exclude_waf_list ) ) {
		$nfw_options['exclude_waf_list'] = json_encode( $exclude_waf_list );
	} else {
		unset( $nfw_options['exclude_waf_list'] );
	}
	nfw_update_option( 'nfw_options', $nfw_options);
	// (Re)create the loader
	require_once __DIR__ .'/lib/install_default.php';
	nfw_create_loader();

}

/* ================================================================== */
// Add IP to whitelist/blacklist from the Firewall Log page (ajax).

add_action( 'wp_ajax_nfw_add_ip', 'nfw_add_ip' );

function nfw_add_ip() {

	nf_not_allowed( 'block', __LINE__ );

	if (! check_ajax_referer( 'nfw_ajax_ip', 'nonce', false ) ) {
		esc_html_e('Error: Security nonces do not match. Reload the page and try again.', 'nfwplus');
		wp_die();
	}

	if ( empty( $_POST['ac_list'] ) || ! in_array( $_POST['ac_list'], ['blacklist', 'whitelist'] ) ) {
		esc_html_e('Please select the list (whitelist or blacklist).', 'nfwplus' );
		wp_die();
	}

	if ( empty( $_POST['ip'] ) ) {
		esc_html_e('Please enter an IP address.', 'nfwplus');
		wp_die();
	}
	if (! filter_var( $_POST['ip'], FILTER_VALIDATE_IP ) )  {
		esc_html_e('Invalid IP address.', 'nfwplus');
		wp_die();
	}

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Add IP to the corresponding AC list:

	if ( $_POST['ac_list'] == 'whitelist' ) {
		// Whitelist
		$ip_list = [];
		if (! empty( $nfw_options['ac_allow_ip'] ) ) {
			$ip_list = unserialize( $nfw_options['ac_allow_ip'] );
		}
		$ip_list[] = strtolower( $_POST['ip'] );
		$nfw_options['ac_allow_ip'] = serialize( array_unique( $ip_list ) );

	} else {
		// Blacklist
		$ip_list = [];
		if (! empty( $nfw_options['ac_block_ip'] ) ) {
			$ip_list = unserialize( $nfw_options['ac_block_ip'] );
		}
		$ip_list[] = strtolower( $_POST['ip'] );
		$nfw_options['ac_block_ip'] = serialize( array_unique( $ip_list ) );
	}

	// Update options
	nfw_update_option( 'nfw_options', $nfw_options );

	echo '200';
	wp_die();
}

/* ================================================================== */

function ninjafirewall_admin_menu() {

	// Return immediately if user is not allowed
	if ( nf_not_allowed( 0, __LINE__ ) ) { return; }

	// Display phpinfo for the installer
	if (! empty($_REQUEST['nfw_act']) && $_REQUEST['nfw_act'] == 99) {
		if ( empty($_GET['nfwnonce']) || ! wp_verify_nonce($_GET['nfwnonce'], 'show_phpinfo') ) {
			wp_nonce_ays('show_phpinfo');
		}
		phpinfo(33);
		exit;
	}

	// Admin menu

	if ( isset( $_POST['nfw_act'] ) && $_POST['nfw_act'] == 'chklic' ) {
		nfw_check_license();
	}
	$nfw_options = nfw_get_option( 'nfw_options' );
	if ( empty( $nfw_options['lic'] ) ) {
		add_menu_page( 'NinjaFirewall', 'NinjaFirewall+', 'manage_options',
			'NinjaFirewall', 'nfw_request_license',	plugins_url() . '/nfwplus/images/nf_icon.png'
		);
		add_submenu_page( 'NinjaFirewall', __('Installation', 'nfwplus'), __('Installation', 'nfwplus'), 'manage_options',
			'NinjaFirewall', 'nfw_request_license' );
		return;
	}

	// Main menu
	add_menu_page( 'NinjaFirewall', 'NinjaFirewall+', 'manage_options',
		'NinjaFirewall', 'nf_sub_main',	plugins_url() . '/nfwplus/images/nf_icon.png'
	);

	// Submenu
	global $menu_hook;

	// Contextual help
	require_once plugin_dir_path(__FILE__) . 'lib/help.php';

	// Overview menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Dashboard', 'nfwplus'), __('Dashboard', 'nfwplus'), 'manage_options',
		'NinjaFirewall', 'nf_sub_main' );
	add_action( 'load-' . $menu_hook, 'help_nfsubmain' );

	// Firewall options menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Firewall Options', 'nfwplus'), __('Firewall Options', 'nfwplus'), 'manage_options',
		'nfsubopt', 'nf_sub_options' );
	add_action( 'load-' . $menu_hook, 'help_nfsubopt' );

	// Firewall policies menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Firewall Policies', 'nfwplus'), __('Firewall Policies', 'nfwplus'), 'manage_options',
		'nfsubpolicies', 'nf_sub_policies' );
	add_action( 'load-' . $menu_hook, 'help_nfsubpolicies' );

	// Access Control menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Access Control', 'nfwplus'), __('Access Control', 'nfwplus'), 'manage_options',
		'nfsubaccess', 'nf_sub_access' );
	add_action( 'load-' . $menu_hook, 'help_nfsubaccesscontrol' );

	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Monitoring', 'nfwplus'),  __('Monitoring', 'nfwplus'), 'manage_options',
		'nfsubfileguard', 'nf_sub_monitoring' );
	add_action( 'load-' . $menu_hook, 'help_nfsubfileguard' );

	$nscan_options = get_option( 'nscan_options' );
	if ( defined('NSCAN_NAME') && defined('NSCAN_SLUG') && ! empty( $nscan_options['scan_nfwpintegration'] ) ) {
		$menu_hook = add_submenu_page( 'NinjaFirewall', NSCAN_NAME, NSCAN_NAME, 'manage_options', NSCAN_NAME, 'nscan_main_menu' );
		require_once dirname( __DIR__ ).'/'. NSCAN_SLUG .'/lib/help.php';
		add_action( 'load-' . $menu_hook, 'nscan_help' );

	} else {
		// Anti-Malware menu
		$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Anti-Malware', 'nfwplus'), __('Anti-Malware', 'nfwplus'), 'manage_options',
			'nfsubmalwarescan', 'nf_sub_malwarescan' );
	}

	// Network menu (multisite only)
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Network', 'nfwplus'), __('Network', 'nfwplus'), 'manage_network',
		'nfsubnetwork', 'nf_sub_network' );
	add_action( 'load-' . $menu_hook, 'help_nfsubnetwork' );

	// Event Notifications menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Event Notifications', 'nfwplus'), __('Event Notifications', 'nfwplus'), 'manage_options',
		'nfsubevent', 'nf_sub_event' );
	add_action( 'load-' . $menu_hook, 'help_nfsubevent' );

	// Login protection menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Log-in Protection', 'nfwplus'), __('Login Protection', 'nfwplus'), 'manage_options',
		'nfsubloginprot', 'nf_sub_loginprot' );
	add_action( 'load-' . $menu_hook, 'help_nfsublogin' );

	// Antispam menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Antispam', 'nfwplus'), __('Antispam', 'nfwplus'), 'manage_options',
		'nfsubantispam', 'nf_sub_antispam' );
	add_action( 'load-' . $menu_hook, 'help_nfsubantispam' );

	// Firewall log menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Logs', 'nfwplus'), __('Logs', 'nfwplus'), 'manage_options',
		'nfsublog', 'nf_sub_log' );
	add_action( 'load-' . $menu_hook, 'help_nfsublog' );

	// Updates menu
	$menu_hook = add_submenu_page( 'NinjaFirewall', __('NinjaFirewall: Security Rules', 'nfwplus'), __('Security Rules', 'nfwplus'), 'manage_options',
		'nfsubupdates', 'nf_sub_updates' );
	add_action( 'load-' . $menu_hook, 'help_nfsubupdates' );

}
// Must load before NinjaScanner (11)
if (! is_multisite() )  {
	add_action( 'admin_menu', 'ninjafirewall_admin_menu', 10 );
} else {
	add_action( 'network_admin_menu', 'ninjafirewall_admin_menu', 10 );
}

/* ================================================================== */

function nf_admin_bar_status() {

	// Display the status icon to administrators (multi-site mode only)
	if (! current_user_can( 'manage_options' ) ) {
		return;
	}

	$nfw_options = nfw_get_option( 'nfw_options' );
	// Disable it, unless this is the superadmin
	if ( @$nfw_options['nt_show_status'] != 1 && ! current_user_can('manage_network') ) {
		return;
	}

	// Obviously, we don't put any icon if NinjaFirewall isn't running
	if (! defined('NF_DISABLED') ) {
		is_nfw_enabled();
	}
	if (NF_DISABLED) { return; }

	global $wp_admin_bar;
	$wp_admin_bar->add_menu( [
		'id'    => 'nfw_ntw1',
		'title' => '<img src="' . plugins_url() . '/nfwplus/images/ninjafirewall_20.png" ' .
				'style="vertical-align:middle;margin-right:5px" />'
	] );

	// Add sub menu link for Super Admin only
	if ( current_user_can( 'manage_network' ) ) {
		$wp_admin_bar->add_menu( [
			'parent' => 'nfw_ntw1',
			'id'     => 'nfw_ntw2',
			'title'  => __('NinjaFirewall Settings', 'nfwplus'),
			'href'   => network_admin_url() . 'admin.php?page=NinjaFirewall'
		] );
	// else, show status only (unless error)
	} else {
		if ( defined('NFW_STATUS') ) {
			$wp_admin_bar->add_menu( [
				'parent' => 'nfw_ntw1',
				'id'     => 'nfw_ntw2',
				'title'  => __('NinjaFirewall is enabled', 'nfwplus')
			] );
		}
	}
}

if ( is_multisite() )  {
	add_action('admin_bar_menu', 'nf_admin_bar_status', 95);
}

/* ================================================================== */

function nf_sub_main() {

	require plugin_dir_path(__FILE__) . 'lib/dashboard.php';

}

/* ================================================================== */

function nf_sub_options() {	// i18n

	// Firewall Options menu
	require plugin_dir_path(__FILE__) . 'lib/firewall_options.php';

}

/* ================================================================== */

function nf_sub_policies() {

	// Firewall Policies menu
	require plugin_dir_path(__FILE__) . 'lib/firewall_policies.php';

}

/* ================================================================== */

function nf_sub_access(){

	// Access Control
	require plugin_dir_path(__FILE__) . 'lib/access_control.php';

}

/* ================================================================== */

function nf_sub_monitoring(){

	require plugin_dir_path(__FILE__) . 'lib/monitoring.php';

}

add_action('nfscanevent', 'nfscando');

function nfscando() {

	define('NFSCANDO', 1);
	nf_sub_monitoring();
}

/* ================================================================== */

function nf_sub_malwarescan() {

	// Anti-Malware menu
	require plugin_dir_path(__FILE__) . 'lib/anti_malware.php';

}

/* ================================================================== */

function nf_sub_network() {

	// Network menu (multi-site only)
	require plugin_dir_path(__FILE__) . 'lib/network.php';

}

/* ================================================================== */

function nf_sub_event() {

	// Event Notifications menu
	require plugin_dir_path(__FILE__) . 'lib/event_notifications.php';

}

add_action('shutdown', 'nf_check_dbdata', 1);

// Daily report cronjob
add_action('nfdailyreport', 'nfdailyreportdo');

function nfdailyreportdo() {
	define('NFREPORTDO', 1);
	nf_sub_event();
}

/* ================================================================== */

function nf_sub_log() {

	require plugin_dir_path(__FILE__) . 'lib/logs.php';

}

/* ================================================================== */

function nf_sub_loginprot() {

	// WordPress login form protection
	require plugin_dir_path(__FILE__) . 'lib/login_protection.php';

}

/* ================================================================== */

// Antispam
require plugin_dir_path(__FILE__) . 'lib/antispam.php';

/* ================================================================== */

function nf_sub_updates() {

	require plugin_dir_path(__FILE__) . 'lib/security_rules.php';

}

add_action('nfsecupdates', 'nfupdatesdo');

function nfupdatesdo() {
	define('NFUPDATESDO', 1);
	nf_sub_updates();
}


/* ================================================================== */

function ninjafirewall_settings_link( $links ) {

	// Check if access is restricted to one or more specific admins
	// See: https://blog.nintechnet.com/restricting-access-to-ninjafirewall-wp-edition-settings/
	if ( nf_not_allowed( 0, __LINE__ ) ) {
		unset( $links );
		$links[] = __('Access Restricted', 'nfwplus');
		return $links;
	}

	if ( is_multisite() ) {	$net = 'network/'; } else { $net = '';	}

	$links[] = '<a href="'. get_admin_url(null, $net .'admin.php?page=NinjaFirewall') .'">'. __('Settings', 'nfwplus') .'</a>';
	unset( $links['edit'] );
   return $links;

}

if ( is_multisite() ) {
	add_filter( 'network_admin_plugin_action_links_' . plugin_basename(__FILE__), 'ninjafirewall_settings_link' );
} else {
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ninjafirewall_settings_link' );
}

/* ================================================================== */

function nfw_dashboard_widgets() {

	// Add dashboard widgets
	require plugin_dir_path(__FILE__) . 'lib/widget.php';

}

if ( is_multisite() ) {
	add_action( 'wp_network_dashboard_setup', 'nfw_dashboard_widgets' );
} else {
	add_action( 'wp_dashboard_setup', 'nfw_dashboard_widgets' );
}

/* ================================================================== */

function nfw_shm_check() {

	if (! function_exists('shmop_open')) {
		return;
	}

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Do nothing if shared memory or NF are disabled
	if ( empty($nfw_options['shmop']) || empty($nfw_options['enabled']) ) {
		return;
	}

	$nfw_rules = nfw_get_option( 'nfw_rules' );

	$shm_update = 1;

	$nf_shm_key = ftok( dirname( __DIR__ ), 'N' );
	$nf_shm_data =	serialize($nfw_options) . $nf_shm_key . serialize($nfw_rules);

	if ( $shm_id = @shmop_open($nf_shm_key, "w", 0, 0) ) {
		// Compare checksum
		if ( md5( shmop_read($shm_id, 0, shmop_size($shm_id))) != md5($nf_shm_data) ) {
			// Delete the current segment, it is outdated
			shmop_delete($shm_id);
		} else {
			// Keep it
			$shm_update = 0;
		}
	}

	if ($shm_update ) {
		// Create a new one
		if ( $shm_id = @shmop_open($nf_shm_key, "c", 0600, strlen($nf_shm_data)) ) {
			if (! $shm_nb = shmop_write( $shm_id, serialize($nfw_options) . $nf_shm_key . serialize($nfw_rules) , 0) ) {
				// Delete it if error
				shmop_delete($shm_id);
			}
			// Ensure we wrote the right amount of bytes
			if ( $shm_nb != strlen($nf_shm_data) ) {
				// Don't keep it
				shmop_delete($shm_id);
			}
			// Close it
		}
	}
}

/* ================================================================== */

function nfw_shm_delete($nf_shm_key) {

	if (! function_exists('shmop_open')) {
		return;
	}

	if ( empty( $nf_shm_key ) ) {
		$nf_shm_key = ftok( dirname( __DIR__ ), 'N' );
	}
	if ( $shm_id = @shmop_open($nf_shm_key, "w", 0, 0) ) {
		shmop_delete($shm_id);
		return 0;
	} else {
		// The block does not exist
		return 1;
	}
}

/* ================================================================== */

add_filter('pre_set_site_transient_update_plugins', 'nfw_check_update');

function nfw_check_update( $transient ) {

	if (! is_object( $transient ) ) {
		$transient = new stdClass;
	}

	if ( empty( $transient->checked['nfwplus/nfwplus.php']) ) {
		$version = NFW_ENGINE_VERSION;
	} else {
		$version = $transient->checked['nfwplus/nfwplus.php'];
	}

	$args = [
		'slug' => 'nfwplus',
		'version' => $version
	];

	if ( is_multisite() ) {
		$nfw_site_url = rtrim( strtolower( network_site_url('','http')), '/' );
	} else {
		$nfw_site_url = rtrim( strtolower(site_url('','http')), '/' );
	}

	global $wp_version;
	$nfw_options = nfw_get_option( 'nfw_options' );
	if (! isset( $nfw_options['lic'] ) ) { $nfw_options['lic'] = ''; }

	$request_string = [
			'body' => [
				'action' => 'version',
				'request'=> serialize( $args ),
				'ver'		=> NFW_ENGINE_VERSION,
				'host'	=> @strtolower( $_SERVER['HTTP_HOST'] ),
				'name'	=> @strtolower( $_SERVER['SERVER_NAME'] ),
				'lic' 	=> $nfw_options['lic']
			],
			'user-agent' => 'WordPress/' . $wp_version . '; ' . $nfw_site_url . '; ' . NFW_ENGINE_VERSION
		];

	$res = wp_remote_post('https://api.nintechnet.com/ninjafirewall/wpplus-update', $request_string);

	if (! is_wp_error($res) && $res['response']['code'] == 200 ) {
		$response = unserialize($res['body']);
	}
	if ( ! empty($response) && is_object($response) ) {
		$transient->response['nfwplus/nfwplus.php'] = $response;
	} else {
		// No update
		$item = (object) [
			'id'            => 'nfwplus/nfwplus.php',
			'slug'          => 'nfwplus',
			'plugin'        => 'nfwplus/nfwplus.php',
			'new_version'   => NFW_ENGINE_VERSION,
			'url'           => '',
			'package'       => '',
			'icons'         => [],
			'banners'       => [],
			'banners_rtl'   => [],
			'tested'        => '',
			'requires_php'  => '',
			'compatibility' => new stdClass()
		];
		// Adding the "mock" item to the `no_update` property is required
		// for the enable/disable auto-updates links to correctly appear in UI.
		$transient->no_update['nfwplus/nfwplus.php'] = $item;
	}
	return $transient;
}

/* ================================================================== */

add_filter('plugins_api', 'nfw_check_plugin_info', 10, 3);

function nfw_check_plugin_info($def, $action, $args) {

	// Get plugin information
	if (! isset($args->slug) || $args->slug != 'nfwplus' ||
		$action != 'plugin_information'  ) {

		return $def;
	}
	$plugin_info = get_site_transient('update_plugins');
	if ( isset( $plugin_info->checked['nfwplus/nfwplus.php'] ) ) {
		$current_version = $plugin_info->checked['nfwplus/nfwplus.php'];
	} else {
		$current_version = NFW_ENGINE_VERSION;
	}
	$args->version = $current_version;

	if ( is_multisite() ) {
		$nfw_site_url = rtrim( strtolower( network_site_url('','http')), '/' );
	} else {
		$nfw_site_url = rtrim( strtolower(site_url('','http')), '/' );
	}

	global $wp_version;
	$nfw_options = nfw_get_option( 'nfw_options' );

	$request_string = [
		'body' => [
			'action' => 'plugin_information',
			'request'=> serialize( $args ),
			'host'	=> @strtolower( $_SERVER['HTTP_HOST'] ),
			'name'	=> @strtolower( $_SERVER['SERVER_NAME'] ),
			'lic' 	=> $nfw_options['lic'],
			'ver'		=> NFW_ENGINE_VERSION
		],
		'user-agent' => "WordPress/{$wp_version}{$nfw_site_url}; " . NFW_ENGINE_VERSION
	];

	$res = wp_remote_post('https://api.nintechnet.com/ninjafirewall/wpplus-update', $request_string);

	if (! is_wp_error($res) && $res['response']['code'] == 200 ) {
		return unserialize( $res['body'] );
	}
	return false;
}

/* ================================================================== */

function nfw_request_license() {

?>
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="<?php echo plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) ?>">&nbsp;<?php _e('License', 'nfwplus') ?></h1>
	<br />
	<?php
	if ( defined('NFW_INVALID_LIC') ) {
		?>
		<div class="error notice is-dismissible"><p><?php echo NFW_INVALID_LIC ?></p></div>
		<?php
	}
	?>
	<form method="post">
		<table class="form-table nfw-table">
			<tr>
				<th scope="row" class="row-med"><?php _e('Enter your NinjaFirewall WP+ license and click on the save button', 'nfwplus') ?></th>
				<td>
					<input type="text" autocomplete="off" value="" style="width:100%" maxlength="1000" name="lic" required />
					<p><?php _e('Don\'t have a license yet?', 'nfwplus') ?> <a href="https://nintechnet.com/ninjafirewall/wp-edition/" target="_blank"><?php _e('Click here to get one', 'nfwplus') ?></a>.</p>
				</td>
			</tr>
		</table>
	<p><input class="button-primary" type="submit" name="Save" value="<?php _e('Save License', 'nfwplus') ?> »" /></p>
	<input type="hidden" name="nfw_act" value="chklic" />
	<?php wp_nonce_field('chk_license', 'nfwnonce') ?>
	</form>
</div>
<?php
}

/* ================================================================== */

function nfw_check_license() {

	if (! defined('WP_CLI') ) {
		if ( empty( $_POST['nfwnonce'] ) || ! wp_verify_nonce( $_POST['nfwnonce'], 'chk_license' ) ) {
			wp_nonce_ays('chk_license');
		}
	}

	// Prevent WordPress from escaping the user input
	$_POST['lic'] = stripslashes( trim( $_POST['lic'] ) );

	if ( is_multisite() ) {
		$nfw_site_url = rtrim( strtolower( network_site_url('','http') ), '/' );
	} else {
		$nfw_site_url = rtrim( strtolower( site_url('','http') ), '/' );
	}
	global $wp_version;

	$request_string = [
		'body' 	=> [
			'action' => 'checklicense',
			'host'	=> strtolower( $_SERVER['HTTP_HOST'] ),
			'name'	=> strtolower( $_SERVER['SERVER_NAME'] ),
			'lic' 	=> $_POST['lic'],
			'ver'		=> NFW_ENGINE_VERSION
		],
		'user-agent' => "WordPress/$wp_version; $nfw_site_url"
	];

	$res = wp_remote_post('https://api.nintechnet.com/ninjafirewall/wpplus-update', $request_string );

	if (! is_wp_error( $res ) ) {
		if ( $res['response']['code'] == 200 ) {
			$nfw_res = unserialize( $res['body'] );
			if (! empty( $nfw_res['exp'] ) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $nfw_res['exp'] ) ) {
				if ( $nfw_res['exp'] < date('Y-m-d', strtotime("-1 day")) ) {
					define('NFW_INVALID_LIC', __('This license has expired and is no longer valid.', 'nfwplus') );
					return;
				}
			} elseif (! empty($nfw_res['ret']) ) {
				if ( $nfw_res['ret'] > 9 && $nfw_res['ret'] < 20 ) {
					define('NFW_INVALID_LIC', __('Your license is not valid', 'nfwplus'). ' (#'. htmlspecialchars( $nfw_res['ret'] ) .')' );
					return;

				} else {
					define('NFW_INVALID_LIC', __('An unknown error occurred while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') );
					return;
				}
			} else {
				define('NFW_INVALID_LIC', __('An error occurred while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus'));
				return;
			}

		} else {
			define('NFW_INVALID_LIC', __('An error occurred while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus'). ' (#2)' );
			return;
		}
	} else {
		define('NFW_INVALID_LIC', __('An error occurred while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus'). ' (#3)');
		return;
	}

	$nfw_options = nfw_get_option('nfw_options');
	$nfw_options['lic'] = $_POST['lic'];
	$nfw_options['lic_exp'] = $nfw_res['exp'];
	nfw_update_option('nfw_options', $nfw_options);

}

/* ================================================================== */

function nf_not_allowed($block, $line = 0) {

	if ( is_multisite() ) {
		if ( current_user_can('manage_network') && is_main_site() ) {
			return false;
		}
	} else {
		if ( current_user_can('manage_options') &&
		     current_user_can('unfiltered_html') ) {
			// Check if that admin is allowed to use NinjaFirewall
			// (see NFW_ALLOWED_ADMIN at http://nin.link/nfwaa ):
			if ( defined('NFW_ALLOWED_ADMIN') ) {
				$current_user = wp_get_current_user();
				$admins = explode(',', NFW_ALLOWED_ADMIN );
				foreach ( $admins as $admin ) {
					if ( trim( $admin ) == $current_user->user_login ) {
						return false;
					}
				}
			} else {
				return false;
			}
		}
	}

	if ( $block ) {
		if ( defined('WP_CLI') && WP_CLI ) {
			// Format text for WP-CLI
			WP_CLI::error(
				sprintf(
					__('You are not allowed to perform this task (%s).', 'nfwplus'),
					"NinjaFirewall: $line"
				)
			);
		} else {
			die( '<br /><br /><br /><div class="error notice is-dismissible"><p>' .
				sprintf(
					esc_html__('You are not allowed to perform this task (%s).', 'nfwplus'),
					"NinjaFirewall: $line"
				) .'</p></div>'
			);
		}
	}
	return true;
}

// ===================================================================== 2024-08-06
// WP CLI commands.

if ( defined('WP_CLI') && WP_CLI ) {
	require_once __DIR__ . '/lib/class-cli.php';
}

/* ================================================================== */
// EOF //

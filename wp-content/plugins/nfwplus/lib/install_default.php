<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n++ / sa
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// =====================================================================
// Load and save default config

function nfw_load_default_conf() {

	$nfw_rules = array();

	$logo = plugins_url() . '/nfwplus/images/ninjafirewall_75.png';
	$logo = preg_replace( '/^https?:/', '', $logo );

	$nfw_options = array(
		// ---------------------------------------------------------------
		// The next 6 keys must always be present because they are used
		// by the nfw_validate_option() function to check whether $nfw_options
		// is corrupted or not:
		'enabled'			=> 1,
		'blocked_msg'		=> base64_encode(NFW_DEFAULT_MSG),
		'logo'				=> $logo,
		'ret_code'			=> 403,
		'scan_protocol'	=> 3,
		'get_scan'			=> 1,
		'widgetnews'		=>	4,
		// ---------------------------------------------------------------
		'shmop'				=> 0,
		'anon_ip'			=> 0,
		'debug'				=> 0,
		'uploads'			=> 2,	// Default value: 2 for WP+, 1 for WP
		'scan_zip'			=> 0,	// Default value: 0 for WP+, N/A for WP
		'sanitise_fn'		=> 0,
		'upload_maxsize'	=> 0, // Defined below since v3.5.4
		'get_sanitise'		=> 0,
		'post_scan'			=> 1,
		'post_sanitise'	=> 0,
		'request_sanitise'=> 0,
		'cookies_scan'		=> 1,
		'cookies_sanitise'=> 0,
		'ua_scan'			=> 1,
		'ua_sanitise'		=> 1,
		'referer_scan'		=> 0,
		'referer_sanitise'=> 1,
		'referer_post'		=> 0,
		'no_host_ip'		=> 0,
		'php_superglobals'=> 1,
		'php_errors'		=> 1,
		'php_self'			=> 1,
		'php_path_t'		=> 1,
		'php_path_i'		=> 1,
		'wp_dir'				=> '/wp-admin/(?:css|images|includes|js)/|' .
									'/wp-includes/(?!ms-files\.php)(?:(?:css|images|js(?!/tinymce/wp-tinymce\.php)|theme-compat)/|[^/]+\.php)|' .
									'/'. basename(WP_CONTENT_DIR) .'/(?:uploads|blogs\.dir)/',
		'no_post_themes'	=> 0,
		'force_ssl'			=> 0,
		'disallow_edit'	=> 0,
		'disallow_mods'	=> 0,
		'post_b64'			=> 1,
		// 3.8.2:
		'disable_error_handler'	=> 0,
		// v3.6.7:
		'disallow_creation'	=> 0,
		// 4.5.9
		'disallow_deletion'	=> 0,
		// v3.7.2:
		'disallow_settings'	=> 1,
		// v4.0.6
		'disallow_privesc'	=> 1,
		// v4.2.6
		'disallow_privesc_mu'	=> 0,
		// v4.2
		'disallow_publish'	=> 0,

		'no_xmlrpc'			=> 0,
		// v1.7 :
		'no_xmlrpc_multi'	=> 0,
		// v3.3.2
		'no_xmlrpc_pingback'=> 0,
		// 4.3.1
		'no_appswd'				=> 0,

		'enum_archives'	=> 0,
		'enum_sitemap'		=> 0,
		'enum_login'		=> 0,
		// v4.2
		'enum_feed'			=> 0,
		'no_restapi'		=> 0,
		'restapi_loggedin'=> 0,
		// Notifications
		'a_0' 				=> 1,
		'a_01' 				=> 0,
		'a_11' 				=> 1,
		'a_12' 				=> 1,
		'a_13' 				=> 0,
		'a_14' 				=> 0,
		'a_15' 				=> 1,
		'a_16' 				=> 0,
		'a_21' 				=> 1,
		'a_22' 				=> 1,
		'a_23' 				=> 0,
		'a_24' 				=> 0,
		'a_25' 				=> 0,
		'a_31' 				=> 1,
		// v1.1.3:
		'a_41' 				=> 1,
		// v1.1.4:
		'a_51' 				=> 1,
		'sched_scan'		=> 0,
		'report_scan'		=> 0,
		// 4.1
		'secupdates'		=>	1,
		// v1.7 (daily report cronjob):
		'a_52' 				=> 1,
		// v3.8.3 :
		'a_61' 				=> 1,

		'alert_email'	 	=> get_option('admin_email'),
		'alert_sa_only'	=> 1,
		// Network
		'nt_show_status'	=> 1,
		// Access control
		'ac_roles'			=>	'|administrator|',
		'ac_ip'				=> 1,
		'ac_ip_2'			=> 0,
		'allow_local_ip'	=> 1, // 1 == no !
		'ac_method'			=> 'GETPOSTHEADPUTDELETEPATCH',
		'ac_geoip'			=> 0,
		'ac_geoip_db'		=> 1,
		'ac_geoip_db2'		=> '',
		'ac_geoip_cn'		=> '',
		'ac_geo_url'		=>	'',
		'ac_geoip_ninja'	=> 0,
		'ac_allow_ip'		=> 0,
		'ac_block_ip'		=> 0,
		'ac_rl_on'			=> 0,
		'ac_rl_conn'		=> 10,
		'ac_rl_time'		=> 30,
		'ac_rl_intv'		=> 5,
		'ac_bl_url'			=> 0,
		'ac_wl_url' 		=> 0,
		'ac_bl_bot'			=> NFW_BOT_LIST,
		'ac_geoip_log'		=> 1,
		'ac_allow_ip_log'	=> 0,
		'ac_block_ip_log'	=> 1,
		'ac_rl_log'			=> 1,
		'ac_bl_url_log'	=> 1,
		'ac_bl_bot_log'	=> 1,
		'ac_wl_url_log' 	=> 0,
		// Web filter
		'wf_enable'			=> 0,
		'wf_pattern' 		=> 0,
		'wf_case'			=> 1, // Case sensitive
		'wf_alert'			=> '30',
		'wf_attach'			=> 1,
		// Anti-spam
		'as_enable'			=> 0,
		'as_level'			=> 1,
		'as_comment'		=> 1,
		'as_register'		=> 0,
		'as_salt'			=> wp_generate_password(),
		'as_field'			=> wp_generate_password( mt_rand(5, 12), FALSE),
		'as_field_2'		=> wp_generate_password( mt_rand(5, 12), FALSE),
		// Log
		'logging'			=> 1,
		'log_rotate'		=> 1,
		'auto_del_log'		=>	0,
		'log_maxsize'		=> 2 * 1048576,
		'log_line'			=>	1500,
		// v3.5.4:
		'syslog'				=>	0,
		// v1.0.2
		// File Guard :
		'fg_enable'			=>	0,
		'fg_mtime'			=>	10,
		'fg_exclude'		=>	'',
		// Updates :
		'enable_updates'	=>	1,
		'sched_updates'	=>	4,
		'notify_updates'	=>	1,
		// v3.3
		// Centralized Logging:
		'clogs_enable'		=>	0,
		'clogs_pubkey'		=>	'',
		'welcome'			=>	1
	);
	// v1.1.1 :
	// Some compatibility checks:
	// 1. header_register_callback(): requires PHP >=5.4
	// 2. headers_list() and header_remove(): some hosts may disable them.
	if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
		// X-XSS-Protection:
		$nfw_options['response_headers'] = '0003000000';
	}
	$nfw_options['referrer_policy_enabled'] = 0;

	// Try to get the current PHP configuration value for "upload_max_filesize":
	$nfw_options['upload_maxsize'] = return_bytes( ini_get('upload_max_filesize') );
	if ( empty( $nfw_options['upload_maxsize'] ) ) {
		// Set it to 10MB (10240 KB):
		$nfw_options['upload_maxsize'] = 10240;
	}

	// Fetch the latest rules from the WordPress repo:
	define('NFUPDATESDO', 2);
	@nf_sub_updates();

	if (! $nfw_rules = @unserialize(NFW_RULES) ) {
		$err_msg = esc_html__('Error: The installer cannot download the security rules from wordpress.org website.', 'nfwplus');
		$err_msg.= '<ol><li>'. esc_html__('The server may be temporarily down or you may have network connectivity problems? Please try again in a few minutes.', 'nfwplus') . '</li>';
		$err_msg.= '<li>'. esc_html__('NinjaFirewall downloads its rules over an HTTPS secure connection. Maybe your server does not support SSL? You can force NinjaFirewall to use a non-secure HTTP connection by adding the following directive to your <strong>wp-config.php</strong> file:', 'nfwplus') . ' <p><code>define("NFW_DONT_USE_SSL", 1);</code></p></li></ol>';
		exit( '<font style="font-size:14px;">'. $err_msg .'</font>' );
	}

	// dropins code:
	if ( isset( $nfw_rules['dropins'] ) ) {
		if ( $nfw_rules['dropins'] == 'delete' ) {
			if ( is_file( NFW_LOG_DIR .'/nfwlog/dropins.php' ) ) {
				@unlink( NFW_LOG_DIR .'/nfwlog/dropins.php' );
			}
		} else {
			$dropins = base64_decode( $nfw_rules['dropins'], true );
			if ( $dropins !== false ) {
				@file_put_contents( NFW_LOG_DIR .'/nfwlog/dropins.php', $dropins, LOCK_EX );
			}
		}
		unset( $nfw_rules['dropins'] );
	}

	// Save engine and rules versions :
	$nfw_options['engine_version'] = NFW_ENGINE_VERSION;
	$nfw_options['rules_version']  = NFW_NEWRULES_VERSION; // downloaded rules

	// If the user is using WP-CLI, we populate DOCUMENT_ROOT with ABSPATH:
	if ( defined('WP_CLI') && WP_CLI ) {
		$_SERVER['DOCUMENT_ROOT'] = ABSPATH;
	}
	// Create but disable by default "Block the DOCUMENT_ROOT server variable in HTTP request" rule
	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
	} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
	}
	$nfw_rules[NFW_DOC_ROOT]['ena'] = 0;

	// The WP+ Edition does not need rule #531 :
	if ( isset($nfw_rules[531])) {
		unset($nfw_rules[531]);
	}

	// ------------------------------------------------------------------
	// Update DB options and rules **BEFORE** (re)enabling scheduled tasks
	// (the garbage collect should be ran/scheduled last):
	nfw_update_option( 'nfw_options', $nfw_options);
	nfw_update_option( 'nfw_rules', $nfw_rules);
	// Create conjobs
	nfw_create_scheduled_tasks();
	// ------------------------------------------------------------------

	nfw_create_log_dir();

}
// =====================================================================
// Create NinjaFirewall's log & cache folders.

function nfw_create_log_dir() {

	$deny_rules = "<Files \"*\">
	<IfModule mod_version.c>
		<IfVersion < 2.4>
			Order Deny,Allow
			Deny from All
		</IfVersion>
		<IfVersion >= 2.4>
			Require all denied
		</IfVersion>
	</IfModule>
	<IfModule !mod_version.c>
		<IfModule !mod_authz_core.c>
			Order Deny,Allow
			Deny from All
		</IfModule>
		<IfModule mod_authz_core.c>
			Require all denied
		</IfModule>
	</IfModule>
</Files>";

	if (! is_writable(NFW_LOG_DIR) ) {
		$err_msg = sprintf( esc_html__('NinjaFirewall cannot create its <code>nfwlog/</code>log and cache folder; please make sure that the <code>%s</code> directory is writable', 'nfwplus'), htmlspecialchars( NFW_LOG_DIR ) );
		exit( '<font style="font-size:14px;">'. $err_msg .'</font>' );
	}

	if (! is_dir( NFW_LOG_DIR .'/nfwlog') ) {
		mkdir( NFW_LOG_DIR .'/nfwlog', 0755 );
		/**
		 * 2025-09-03: We temporarily force NinjaFirewall session on all new installs.
		 */
		touch( NFW_LOG_DIR .'/nfwlog/ninjasession');
	}
	if (! is_dir( NFW_LOG_DIR .'/nfwlog/cache') ) {
		mkdir( NFW_LOG_DIR .'/nfwlog/cache', 0755 );
	}

	touch( NFW_LOG_DIR . '/nfwlog/index.html' );
	touch( NFW_LOG_DIR . '/nfwlog/cache/index.html' );
	@file_put_contents(NFW_LOG_DIR . '/nfwlog/.htaccess', $deny_rules, LOCK_EX);
	@file_put_contents(NFW_LOG_DIR . '/nfwlog/cache/.htaccess', $deny_rules, LOCK_EX);
	@file_put_contents(
		NFW_LOG_DIR . '/nfwlog/readme.txt',
		"This is NinjaFirewall's logs, loader and cache directory. DO NOT alter or remove it as long as NinjaFirewall is running!\n\nIf you just uninstalled NinjaFirewall, WAIT 5 MINUTES before deleting this folder, otherwise your site will likely crash.",
		LOCK_EX
	);
	nfw_create_loader();
}

// =====================================================================
// Create NF's loader.

function nfw_create_loader() {

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Firewall loader
	$loader = "<?php
// ===============================================================//
// NinjaFirewall's loader.                                        //
// DO NOT alter or remove it as long as NinjaFirewall is running. //
// If this file is corrupted or wrong, you can re-generate it     //
// by deactivating and reactivating NinjaFirewall from your       //
// WordPress dashboard.                                           //
// ===============================================================//";

	if (! empty( $nfw_options['exclude_waf_list'] ) ) {
		$string = '';
		$exclude_waf_list = json_decode( $nfw_options['exclude_waf_list'] );
		foreach( $exclude_waf_list as $folder ) {
			if ( is_dir( ABSPATH . $folder ) ) {
				$string .= "'$folder',";
			}
		}
		$string = rtrim( $string, ',' );
		if (! empty( $string ) ) {
			$loader .= "
\$nfw_exclude_waf_list = array($string);
foreach( \$nfw_exclude_waf_list as \$nfw_exclude_waf_folder ) {
	if (strpos(\$_SERVER['SCRIPT_FILENAME'], \"". ABSPATH ."\$nfw_exclude_waf_folder/\") === 0) {
		return;
	}
}";
		}
	}

	$loader .= "
if ( is_file('". __DIR__ .'/firewall.php' . "') ) {
	@include_once '". __DIR__ .'/firewall.php' . "';
}
// EOF
";
	file_put_contents( NFW_LOG_DIR .'/nfwlog/ninjafirewall.php', $loader, LOCK_EX );
	return;

}

// =====================================================================
// EOF //

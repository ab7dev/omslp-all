<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined('NFW_ENGINE_VERSION') ) {
	die('Forbidden');
}

// Block immediately if user is not allowed :
nf_not_allowed('block', __LINE__ );

$nfw_options	= nfw_get_option('nfw_options');
$nfw_rules		= nfw_get_option('nfw_rules');

// Tab and div display
if ( empty( $_REQUEST['tab'] ) ) { $_REQUEST['tab'] = 'dashboard'; }

if ( $_REQUEST['tab'] == 'statistics' ) {
	$dashboard_tab = ''; $dashboard_div = ' style="display:none"';
	$statistics_tab = ' nav-tab-active'; $statistics_div = '';
	$license_tab = ''; $license_div = ' style="display:none"';
	$about_tab = ''; $about_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'license' ) {
	$dashboard_tab = ''; $dashboard_div = ' style="display:none"';
	$statistics_tab = ''; $statistics_div = ' style="display:none"';
	$license_tab = ' nav-tab-active'; $license_div = '';
	$about_tab = ''; $about_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'about' ) {
	$dashboard_tab = ''; $dashboard_div = ' style="display:none"';
	$statistics_tab = ''; $statistics_div = ' style="display:none"';
	$license_tab = ''; $license_div = ' style="display:none"';
	$about_tab = ' nav-tab-active'; $about_div = '';

} else {
	$_REQUEST['tab'] = 'dashboard';
	$dashboard_tab = ' nav-tab-active'; $dashboard_div = '';
	$statistics_tab = ''; $statistics_div = ' style="display:none"';
	$license_tab = ''; $license_div = ' style="display:none"';
	$about_tab = ''; $about_div = ' style="display:none"';
}

// Is NF enabled/working ?
if (! defined('NF_DISABLED') ) {
	is_nfw_enabled();
}

if (! defined( 'NFW_WPWAF' ) && defined( 'NFW_PID' ) ) {
	// Check if we have our PID. If we don't, that means there must
	// be a Full WAF instance of the firewall running in a parent
	// directory. Therefore, we need to allow Full WAF update from
	// this page:
	$nfw_pid = 0;
	if ( file_exists( NFW_LOG_DIR .'/nfwlog/cache/.pid' ) ) {
		$nfw_pid = trim( file_get_contents( NFW_LOG_DIR .'/nfwlog/cache/.pid' ) );
	}
	if ( NFW_PID != $nfw_pid ) {
		define('NFW_WPWAF', 2);
	}
}

// Search for Full WAF post-install
$res = get_transient( 'nfw_fullwaf' );
if ( $res !== false ) {
	if ( defined( 'NFW_WPWAF' ) ) {
		// 1: Apache mod_php
		// 2: Apache + CGI/FastCGI or PHP-FPM
		// 3: Apache + suPHP
		// 4: Nginx + CGI/FastCGI or PHP-FPM
		// 5: Litespeed
		// 6: Openlitespeed
		// 7: Other webserver + CGI/FastCGI or PHP-FPM
		list( $httpserver, $time ) = explode( ':', $res );
		$message = '';

		if ( $httpserver == 6 ) {
			$message = __('Make sure you followed the instructions and restarted Openlitespeed.', 'nfwplus' );
			delete_transient( 'nfw_fullwaf' );

		} elseif ( $httpserver == 1 || $httpserver == 5 ) {
			$message = sprintf( __('Make sure your HTTP server support the %s directive in .htaccess files. Maybe you need to restart your HTTP server to apply the change, or simply to wait a few seconds and reload this page?', 'nfwplus' ), '<code>php_value auto_prepend_file</code>' );
			delete_transient( 'nfw_fullwaf' );

		} else {
			$now = time();
			// <5 minutes
			if ( $now < $time ) {
				$time_left = $time - $now;
				$message = sprintf( __('Because PHP caches INI files, you may need to wait up to five minutes before the changes are reloaded by the PHP interpreter. <strong>Please wait for <font id="nfw-waf-count">%d</font> seconds</strong> before trying again (you can navigate away from this page and come back in a few minutes).', 'nfwplus'), (int) $time_left );
				$countdown = 1;
			} else {
				delete_transient( 'nfw_fullwaf' );
			}
		}
		if (! empty( $message ) ) {
			echo '<div class="notice-warning notice is-dismissible"><p>'.
				__('Oops! Full WAF mode is not enabled yet.', 'nfwplus' ) .'<br />'.
				$message .
				'</p></div>';
			if ( isset( $countdown ) ) {
				echo '<script>fullwaf_count='. $time_left .';fullwaf=setInterval(nfwjs_fullwaf_countdown,1000);</script>';
			}
		}
	}
}
// Error log deletion:
if (! empty( $_POST['delete-error-log'] ) ){
	if ( empty( $_POST['nfwnonce_errorlog'] ) || ! wp_verify_nonce( $_POST['nfwnonce_errorlog'], 'delete_error_log' ) ) {
		wp_nonce_ays('delete_error_log');
	}
	if ( file_exists( NFW_LOG_DIR .'/nfwlog/error_log.php' ) ) {
		@unlink( NFW_LOG_DIR .'/nfwlog/error_log.php' );
	}
}
?>
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="<?php echo plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) ?>">&nbsp;NinjaFirewall (<font color=#21759B>WP+</font> Edition)</h1>
	<?php

	// License expiration warning
	if ( empty( $nfw_options['lic'] ) ) {
		echo '<div class="error notice is-dismissible"><p>' .
			__('You do not have a valid NinjaFirewall license', 'nfwplus') . ' (#1)! <a href="admin.php?page=NinjaFirewall&tab=license">' .
			__('Click here to get one', 'nfwplus') . '</a>.</p></div>';
	}

	// Full WAF settings change
	if (! empty( $_GET['nfwafconfig'] ) ) {
		echo '<div class="updated notice is-dismissible"><p>' . esc_html__('Your changes have been saved.', 'nfwplus') . '</p></div>';
	}
	?>
	<br />
	<h2 class="nav-tab-wrapper wp-clearfix" style="cursor:pointer">
		<a id="tab-dashboard" class="nav-tab<?php echo $dashboard_tab ?>" onClick="nfwjs_switch_tabs('dashboard', 'dashboard:statistics:license:about')"><?php _e( 'Dashboard', 'nfwplus' ) ?></a>
		<a id="tab-statistics" class="nav-tab<?php echo $statistics_tab ?>" href="?page=NinjaFirewall&tab=statistics"><?php _e( 'Statistics', 'nfwplus' ) ?></a>
		<a id="tab-license" class="nav-tab<?php echo $license_tab ?>" onClick="nfwjs_switch_tabs('license', 'dashboard:statistics:license:about')"><?php _e( 'License', 'nfwplus' ) ?></a>
		<a id="tab-about" class="nav-tab<?php echo $about_tab ?>" onClick="nfwjs_switch_tabs('about', 'dashboard:statistics:license:about')"><?php _e( 'About...', 'nfwplus' ) ?></a>
		<?php nfw_contextual_help(); ?>
	</h2>

	<br />

	<!-- Dashboard -->

	<div id="dashboard-options"<?php echo $dashboard_div ?>>

		<h3><?php _e('Firewall Dashboard', 'nfwplus') ?></h3>

		<table class="form-table nfw-table">

		<?php
		if ( NF_DISABLED ) {
			// An instance of the firewall running in Full WAF (or Pro/Pro+ Edition)
			// in a parent directory will force us to run in Full WAF mode to override it.
			if ( defined( 'NFW_STATUS' ) && ( NFW_STATUS > 19 && NFW_STATUS < 24 ) ) {
				$msg = __('It seems that you may have another instance of NinjaFirewall running in a parent directory. Make sure to follow these instructions:', 'nfwplus');
				$msg.= '<ol><li>';
				$msg.= __('Temporarily disable the firewall in the parent folder by renaming its PHP INI or .htaccess file.', 'nfwplus');
				$msg.= '</li><li>';
				$msg.= __('Install NinjaFirewall on this site in Full WAF mode.', 'nfwplus');
				$msg.= '</li><li>';
				$msg.= __('Restore the PHP INI or .htaccess in the parent folder to re-enable the firewall.', 'nfwplus');
				$msg.= '</li></ol>';

			} elseif (! empty( $GLOBALS['err_fw'][NF_DISABLED] ) ) {
				$msg = $GLOBALS['err_fw'][NF_DISABLED];
			} else {
				$msg = __('Unknown error', 'nfwplus') .' #'. NF_DISABLED;
			}
		?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Firewall', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php echo $msg ?></td>
			</tr>

		<?php
		} else {
		?>

			<tr>
				<th scope="row" class="row-med"><?php _e('Firewall', 'nfwplus') ?></th>
				<td><?php _e('Enabled', 'nfwplus') ?></td>
			</tr>
		<?php
		}

		?>
			<tr>
				<th scope="row" class="row-med"><?php esc_html_e('Mode', 'nfwplus') ?></th>
				<td>
				<?php
				if ( defined( 'NFW_WPWAF' ) ) {
					printf( esc_html__('NinjaFirewall is running in %s mode. For better protection, activate its Full WAF mode:', 'nfwplus'), '<a href="https://blog.nintechnet.com/full_waf-vs-wordpress_waf/" target="_blank">WordPress WAF</a>');
					?>
					<p><input type="button" id="nfw-activate-thickbox" value="<?php esc_attr_e('Activate Full WAF mode', 'nfwplus') ?>" class="button-secondary"></p>
					<?php
				} else {
					if (! NF_DISABLED ) {
						printf( esc_html__('NinjaFirewall is running in %s mode.', 'nfwplus'), '<a href="https://blog.nintechnet.com/full_waf-vs-wordpress_waf/" target="_blank">Full WAF</a>');
						?>
						<p><input type="button" id="nfw-configure-thickbox" value="<?php esc_attr_e('Configure', 'nfwplus') ?>" class="button-secondary"></p>
						<?php
					} else {
						echo '-';
					}
				}
				?>
				</td>
			</tr>
		<?php

		if (! empty( $nfw_options['debug'] ) ) {
		?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Debugging mode', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php _e('Enabled.', 'nfwplus') ?>&nbsp;<a href="?page=nfsubopt"><?php _e('Click here to turn Debugging Mode off', 'nfwplus') ?></a></td>
			</tr>
		<?php
		}
		?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Edition', 'nfwplus') ?></th>
				<td>WP+ Edition</td>
			</tr>
			<tr>
				<th scope="row" class="row-med"><?php _e('Version', 'nfwplus') ?></th>
				<td><?php echo NFW_ENGINE_VERSION . ' ~ ' . __('Security rules:', 'nfwplus' ) . ' ' . preg_replace('/(\d{4})(\d\d)(\d\d)/', '$1-$2-$3', $nfw_options['rules_version']) ?></td>
			</tr>
			<tr>
				<th scope="row" class="row-med"><?php _e('PHP SAPI', 'nfwplus') ?></th>
				<td>
					<?php
					if ( defined('HHVM_VERSION') ) {
						echo 'HHVM';
					} else {
						echo strtoupper(PHP_SAPI);
					}
					echo ' ~ '. PHP_MAJOR_VERSION .'.'. PHP_MINOR_VERSION .'.'. PHP_RELEASE_VERSION;
					?>
				</td>
			</tr>
		<?php

		// If security rules updates are disabled, warn the user
		if ( empty( $nfw_options['enable_updates'] ) ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Updates', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><a href="?page=nfsubupdates&tab=updates"><?php _e( 'Security rules updates are disabled.', 'nfwplus' ) ?></a> <?php _e( 'If you want your blog to be protected against the latest threats, enable automatic security rules updates.', 'nfwplus' ) ?></td>
			</tr>
			<?php
		}

		if (! defined('NFW_WPWAF') ) {
			// Shared memory (unless runnning in WP WAF mode 2
			$icn = $msg = '';
			if ( empty( $nfw_options['shmop'] ) ) {
				$icn = '';
				$msg = '<a href="?page=nfsubopt">' . __('Disabled', 'nfwplus') . '</a>';
			} else {
				// Try to access it :
				$nf_shm_key = ftok( dirname( dirname( __DIR__ ) ), 'N' );

				if ( $shm_id = @shmop_open($nf_shm_key, "a", 0, 0) ) {
					// So far, so good. Check if it is up to date
					$nfw_data = serialize( $nfw_options ) . $nf_shm_key . serialize( $nfw_rules );
					$shmop_size = shmop_size( $shm_id );
					if ( md5( shmop_read( $shm_id, 0, $shmop_size ) ) != md5( $nfw_data ) ) {
						$icn = '<span class="dashicons dashicons-dismiss nfw-danger"></span>';
						$msg = sprintf(__('The shared memory block seems corrupted. Try to reload this page to fix it or, if this error persists, please <a href="%s">disable shared memory</a> to avoid any problem.', 'nfwplus'), '?page=nfsubopt');
					} else {
						$icn = '';
						$msg = __('Enabled', 'nfwplus') .' '. sprintf( __( '(RAM usage: %s bytes)', 'nfwplus'), number_format_i18n( $shmop_size ) );
					}
				} else {
					if ( $nfw_options['enabled'] ) {
						$icn = '<span class="dashicons dashicons-dismiss nfw-danger"></span>';
						$msg = sprintf(__('Unable to access/read the shared memory block. Try to reload this page or, if this error persists, please <a href="%s">disable shared memory</a> to avoid any problem.', 'nfwplus'), '?page=nfsubopt');
					} else {
						$icn = '';
						$msg = __('Firewall is disabled', 'nfwplus');
					}
				}
			}
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Shared memory', 'nfwplus') ?></th>
				<td><?php echo $icn .' '. $msg ?></td>
			</tr>
			<?php
			// Check if the admin is whitelisted, and warn if it is not
			if ( empty( NinjaFirewall_session::read('nfw_goodguy') ) ) {
				?>
				<tr>
					<th scope="row" class="row-med"><?php _e('Admin user', 'nfwplus') ?></th>
					<td><span class="dashicons dashicons-warning nfw-warning"></span><?php printf( __('You are not whitelisted. Ensure that the "Do not block the following users" option in the <a href="%s">Access Control menu</a> includes the Admin/Super Admin, otherwise you could get blocked by the firewall while working from the WordPress administration dashboard.', 'nfwplus'), '?page=nfsubaccess') ?></td>
				</tr>
			<?php
			}
		}

		// Try to find out if there is any "lost" session between the firewall
		// and the plugin part of NinjaFirewall (could be a buggy plugin killing
		// the session etc), unless we just installed it:
		if ( defined( 'NFW_SWL' ) && ! empty( NinjaFirewall_session::read('nfw_goodguy') ) && empty( $_REQUEST['nfw_firstrun'] ) ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php esc_html_e('User session', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-warning nfw-warning"></span><?php esc_html_e('It seems that the user session set by NinjaFirewall was not found by the firewall script.', 'nfwplus') ?></td>
			</tr>
			<?php
		} else {
			/**
			 * Don't display info about the session if we're using the NinjaFirewall's built-in session.
			 */
			if ( is_file( NFW_LOG_DIR .'/nfwlog/phpsession') ) {
				?>
				<tr>
					<th scope="row" class="row-med"><?php esc_html_e('User session', 'nfwplus') ?></th>
					<td><?php
						printf(
							/* Translators: path to the file */
							esc_html__('You are using PHP sessions. If you want to switch to NinjaFirewall sessions, please delete the following file: %s.', 'nfwplus'),
								'<code>'. esc_html( NFW_LOG_DIR .'/nfwlog/phpsession') .'</code>'
						); ?>
					</td>
				</tr>
			<?php
			}
		}

		// Centralized logging: remote server
		if ( ! empty( $nfw_options['clogs_pubkey'] ) ) {
			$err_msg = $ok_msg = '';
			if (! preg_match( '/^[a-f0-9]{40}:([a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'], $match ) ) {
				$err_msg = sprintf( __('the public key is invalid. Please <a href="%s">check your configuration</a>.', 'nfwplus'), '?page=nfsublog#clogs');

			} else {
				if ( $match[1] == '*' ) {
					$ok_msg = __( "No IP address restriction.", 'nfwplus');

				} elseif ( filter_var( $match[1], FILTER_VALIDATE_IP ) ) {
					$ok_msg = sprintf( __("IP address %s is allowed to access NinjaFirewall's log on this server.", 'nfwplus'), htmlspecialchars( $match[1]) );

				} else {
					$err_msg = sprintf( __('the whitelisted IP is not valid. Please <a href="%s">check your configuration</a>.', 'nfwplus'), '?page=nfsublog#clogs');
				}
			}
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Centralized Logging', 'nfwplus') ?></th>
			<?php
			if ( $err_msg ) {
				?>
					<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( __('Error: %s', 'nfwplus'), $err_msg) ?></td>
				</tr>
				<?php
				$err_msg = '';
			} else {
				?>
					<td><a href="?page=nfsublog#clogs"><?php _e('Enabled', 'nfwplus'); echo "</a>. $ok_msg"; ?></td>
				</tr>
			<?php
			}
		}

		// If the Source IP was changed by the user, ensure it exists
		// or warm about it :
		$warn_msg = '';
		if ( @$nfw_options['ac_ip'] == 3  && ! empty( $nfw_options['ac_ip_2'] ) && empty( $_SERVER[$nfw_options['ac_ip_2']] ) ) {
			$warn_msg = $nfw_options['ac_ip_2'];
		} elseif ( @$nfw_options['ac_ip'] == 2 && empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$warn_msg = 'HTTP_X_FORWARDED_FOR';
		}
		if ( $warn_msg ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Source IP', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( __('<a href="%s">Access Control Source IP</a> is setup to use %s, however your server does not support that variable. All IP-based directives will fail.', 'nfwplus'), '?page=nfsubaccess', '<code>'. htmlentities($warn_msg) .'</code>') ?></td>
			</tr>
			<?php
		} else {
			if (! empty( NinjaFirewall_session::read('nfw_goodguy') ) ) {
				$current_user = wp_get_current_user();
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Admin user', 'nfwplus') ?></th>
				<td><code><?php echo htmlspecialchars( $current_user->user_login ) ?></code>: <?php _e('You are whitelisted by the firewall.', 'nfwplus') ?></td>
			</tr>
		<?php
			}
		}

		// Display restrictions (not available to WPMU)
		if ( defined('NFW_ALLOWED_ADMIN') && ! is_multisite() ) {
		?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Restrictions', 'nfwplus') ?></th>
				<td><?php _e('Access to NinjaFirewall is restricted to specific users.', 'nfwplus') ?></td>
			</tr>
		<?php
		}

		// Check IP and warn if localhost or private IP
		if (! filter_var(NFW_REMOTE_ADDR, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ) {
			?>
			<tr valign="top">
				<th scope="row" class="row-med"><?php _e('Source IP', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-warning nfw-warning"></span><?php printf( __('You have a private IP: %s', 'nfwplus') .'<br />'.
					__('If your site is behind a reverse proxy or a load balancer, ensure that the <a href="%s">Source IP</a> is setup accordingly.', 'nfwplus'), htmlentities(NFW_REMOTE_ADDR), '?page=nfsubaccess') ?></td>
			</tr>
			<?php
		}

		// Look for CDN's (Incapsula/Cloudflare) and warn the user about using
		// the correct IPs, unless it was already copied to REMOTE_ADDR
		if (! empty( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
			// CloudFlare :
			if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_CF_CONNECTING_IP"] ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('CDN detection', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-warning nfw-warning"></span><?php printf( __('%s detected: you seem to be using Cloudflare CDN services. Ensure that the <a href="%s">Source IP</a> is setup accordingly.', 'nfwplus'), '<code>HTTP_CF_CONNECTING_IP</code>', '?page=nfsubaccess') ?></td>
			</tr>
			<?php
			}
		}
		if (! empty( $_SERVER["HTTP_INCAP_CLIENT_IP"] ) ) {
			// Incapsula :
			if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_INCAP_CLIENT_IP"] ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('CDN detection', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-warning nfw-warning"></span><?php printf( __('%s detected: you seem to be using Incapsula CDN services. Ensure that the <a href="%s">Source IP</a> is setup accordingly.', 'nfwplus'), '<code>HTTP_INCAP_CLIENT_IP</code>', '?page=nfsubaccess') ?></td>
			</tr>
			<?php
			}
		}

		// Check whether loggin is enable or not
		if ( empty( $nfw_options['logging'] ) ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Logging', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php _e('Logging is disabled.', 'nfwplus') ?> <a href="?page=nfsublog"><?php _e('Click here to re-enable it.', 'nfwplus') ?></a></td>
			</tr>
			<?php
		}

		// Ensure /log/ dir is writable
		if (! is_writable( NFW_LOG_DIR . '/nfwlog') ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Log dir', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( __('%s directory is not writable! Please chmod it to 0777 or equivalent.', 'nfwplus'), '<code>'. htmlspecialchars(NFW_LOG_DIR) .'/nfwlog/</code>') ?></td>
			</tr>
		<?php
		}

		// Ensure /log/cache dir is writable
		if (! is_writable( NFW_LOG_DIR . '/nfwlog/cache' ) ) {
			?>
			<tr>
				<th scope="row" class="row-med"><?php _e('Log dir', 'nfwplus') ?></th>
				<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( __('%s directory is not writable! Please chmod it to 0777 or equivalent.', 'nfwplus'), '<code>'. htmlspecialchars(NFW_LOG_DIR) .'/nfwlog/cache</code>') ?></td>
			</tr>
		<?php
		}

		if (! defined('NF_DISABLE_PHPINICHECK') && ! defined('NFW_WPWAF') ) {

			// Make sure the PHP INI is not viewable by webusers
			if ( file_exists( ABSPATH .'php.ini' ) ) {
				$res = nfw_is_inireadable( 'php.ini' );
				if ( $res !== false ) {
					?>
					<tr>
						<th scope="row" class="row-med">PHP INI</th>
						<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( esc_html__('The php.ini file is readable by web users: %s', 'nfwplus'), '<code>'. htmlspecialchars( $res ) .'</code>' ) ?> <br /><a href="https://blog.nintechnet.com/protecting-ninjafirewalls-php-ini-file/" target="_blank"><?php esc_html_e('Consult our blog for more info.', 'nfwplus') ?></a></td>
					</tr>
					<?php
				}
			}
			if ( file_exists( ABSPATH .'.user.ini' ) ) {
				$res = nfw_is_inireadable( '.user.ini' );
				if ( $res !== false ) {
					?>
					<tr>
						<th scope="row" class="row-med">PHP INI</th>
						<td><span class="dashicons dashicons-dismiss nfw-danger"></span><?php printf( esc_html__('The .user.ini file is readable by web users:  %s', 'nfwplus'), '<code>'. htmlspecialchars( $res ) .'</code>' ) ?><br /><a href="https://blog.nintechnet.com/protecting-ninjafirewalls-php-ini-file/" target="_blank"><?php esc_html_e('Consult our blog for more info.', 'nfwplus') ?></a></td>
					</tr>
					<?php
				}
			}
		}

		// Error log
		$log = NFW_LOG_DIR . '/nfwlog/error_log.php';
		if ( file_exists( $log ) ) {
			$errlog_content = file( $log );
			array_shift( $errlog_content );
			if (! empty( $errlog_content ) ) {
				?>
				<tr id="error-log-alert">
					<th scope="row" class="row-med"><?php _e('Error log', 'nfwplus') ?></th>
					<td><span class="dashicons dashicons-dismiss nfw-danger"></span><input type="button" id="nfw-errorlog-thickbox" value="<?php _e('View error log', 'nfwplus') ?>" class="button-secondary"></td>
				</tr>
				<?php
			}
		}

		/**
		 * Check for NinjaFirewall optional config file.
		 */
		$doc_root = rtrim( $_SERVER['DOCUMENT_ROOT'], '/' );
		if ( @file_exists( $file = $doc_root . '/.htninja') ||
			@file_exists( $file = dirname( $doc_root ) . '/.htninja') ) {

			echo '<tr>
				<th scope="row" class="row-med">'. esc_html__('Optional configuration file', 'nfwplus').
				'</th><td><code>'. htmlentities( $file ) .'</code></td>
			</tr>';
			/**
			 * Check if we have a MySQLi link identifier defined in the .htninja.
			 */
			if (! empty( $GLOBALS['nfw_mysqli'] ) && ! empty( $GLOBALS['nfw_table_prefix'] ) ) {
				echo '<tr>
					<th scope="row" class="row-med">' . esc_html__('MySQLi link identifier', 'nfwplus') .
					'</th><td>' .
					esc_html__('A MySQLi link identifier was detected in your <code>.htninja</code>.',
					'nfwplus') . '</td>
				</tr>';
			}
		}
		?>
		</table>
	</div>

	<!-- Monthly statistics -->
	<div id="statistics-options"<?php echo $statistics_div ?>>
		<?php include __DIR__ .'/dashboard_statistics.php'; ?>
	</div>

	<!-- License -->
	<div id="license-options"<?php echo $license_div ?>>
		<?php include __DIR__ .'/dashboard_license.php'; ?>
	</div>

	<!-- About... -->
	<div id="about-options"<?php echo $about_div ?>>
		<?php include __DIR__ .'/dashboard_about.php'; ?>
	</div>


</div>
<?php

// Load thickbox
require __DIR__ .'/thickbox.php';

// =====================================================================
// Verify if PHP INI file is readable by web users.

function nfw_is_inireadable( $ini ) {

	if ( is_multisite() ) {
		$url = network_home_url('/') . $ini;
	} else {
		$url = home_url('/') . $ini;
	}
	global $wp_version;
	$opts = array(
		'http' => array(
			// We only care about the returned HTTP code
			'ignore_errors' => true,
			// Max 2 seconds
			'timeout' => 2,
			'method' => "GET",
			'header' =>
				"Accept-language: en-US,en;q=0.5\r\n" .
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
				"User-Agent: Mozilla/5.0 (compatible; NinjaFirewall/". NFW_ENGINE_VERSION ."; WordPress/$wp_version)\r\n"
		)
	);

	if ( empty( $_SERVER['SERVER_ADDR'] ) ) {
		return false;
	}
	$addr = $_SERVER['SERVER_ADDR'];
	if (! filter_var( $addr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		// We don't want a fatal error if we're running on localhost e.g., dev site etc
		$opts['ssl']['verify_peer'] = false;
		$opts['ssl']['verify_peer_name'] = false;
	}
	$context  = stream_context_create( $opts );
	// As we don't want monitoring/debugging plugins to throw a warning or error
	// in the backend because the server returned a 403 error, we don't use
	// the WordPress's API
	@file_get_contents( $url, false, $context );
	/**
	 * $http_response_header is deprecated in PHP 8.5, hence we use the
	 * http_get_last_response_headers() function instead (PHP >= 8.4).
	 */
	if ( function_exists('http_get_last_response_headers') ) {
		$http_response_header = http_get_last_response_headers();
	}
	if ( empty( $http_response_header ) ) {
		return false;
	}
	$response = explode( ' ', $http_response_header[0] );
	if (! empty( $response[1] ) && (int) $response[1] == 200 ) {
		return $url;
	}
	return false;

}
// =====================================================================
// EOF

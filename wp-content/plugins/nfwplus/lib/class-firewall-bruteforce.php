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

if ( class_exists('NinjaFirewall_bruteforce') ) {
	return;
}


class NinjaFirewall_bruteforce {


	/**
	 * Check user authentication.
	 */
	static public function run( $where ) {

		if ( defined('NFW_STATUS') ) {
			return;
		}

		global $nfw_;
		$bf_conf_dir = "{$nfw_['log_dir']}/cache";

		/**
		 * Convert the old configuration format to the new one.
		 * v4.9.1+
		 */
		if ( is_file( "$bf_conf_dir/bf_conf.php") ) {
			self::convert( $bf_conf_dir );
		}

		/**
		 * Exit if the protection is disabled.
		 */
		if (! is_file( "$bf_conf_dir/login_protection.php" ) ) {
			return;
		}

		$config = self::read_config( "$bf_conf_dir/login_protection.php" );
		if ( empty ( $config ) ) {
			return;
		}

		if ( empty( $config['bf_enable'] ) ) {
			return;
		}

		// XML-RPC API
		if ( $where == 2 && empty( $config['bf_xmlrpc'] ) ) {
			return;
		}

		// NinjaFirewall <= 3.4.2
		if (! isset( $config['auth_msgtxt'] ) ) {
			$config['auth_msgtxt'] = $config['auth_msg'];
			$config['b64'] = 0;

		// NinjaFirewall > 3.4.2
		} else {
			$config['b64'] = 1;
		}

		// NinjaFirewall < 3.5
		if (! isset( $config['bf_allow_bot'] ) ) {
			$config['bf_allow_bot'] = 0;
		}

		if (! isset( $config['bf_type'] ) ) {
			$config['bf_type'] = 0;
		}

		/**
		 * Check if it is a bot accessing wp-login.php.
		 */
		if ( $where == 1 && $config['bf_allow_bot'] == 0 ) {
			nfw_is_bot('wp-login.php');
		}

		/**
		 * Make sure this is a login request.
		 */
		if ( $where == 1 && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'],
			[
				'confirmaction',
				'logout',
				'lostpassword',
				'postpass',
				'retrievepassword',
				'resetpass',
				'rp',
				'register'
			]
		) ) {

			return;
		}
		/**
		 * Protection set to "Always ON" (2).
		 */
		if ( $config['bf_enable'] == 2 ) {
			self::authentification( $config );
			return;
		}

		$now = time();

		$server_name = NinjaFirewall_data::sanitise_string_fn( $_SERVER['SERVER_NAME'] );
		$blocked_file = "{$bf_conf_dir}/bf_blocked-{$where}-{$server_name}-{$config['bf_rand']}";

		// The protection has already been triggered
		if ( is_file( $blocked_file ) ) {

			$mtime = filemtime( $blocked_file );
			if ( ($now - $mtime) < $config['bf_bantime'] * 60 ) {
				self::authentification( $config );
				return;

			} else {
				unlink( $blocked_file );
			}
		}
		/**
		 * HTTP request (GET or POST or both).
		 */
		if ( strpos( $config['bf_request'], $_SERVER['REQUEST_METHOD'] ) === false ) {
			return;
		}
		/**
		 * Read the existing log, if any.
		 */
		$log_file = "{$bf_conf_dir}/bf_{$where}-{$server_name}-{$config['bf_rand']}";
		if ( is_file( $log_file ) ) {
			$tmp_log = file( $log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

			if ( count( $tmp_log ) >= $config['bf_attempt'] ) {

				if ( ( $tmp_log[ count( $tmp_log ) - 1 ] - $tmp_log[ count( $tmp_log ) -
					$config['bf_attempt'] ] ) <= $config['bf_maxtime'] ) {

					/**
					 * Threshold has been reached, lock down the access to the page.
					 */
					$bfdh = fopen( $blocked_file, 'w');
					fclose( $bfdh );
					/**
					 * Clear the log.
					 */
					unlink( $log_file );
					/**
					 * Setup HTTP ret code here, because we do not have access to the DB yet.
					 */
					$nfw_['nfw_options']['ret_code'] = '401';
					/**
					 * We always log as we don't know whether we should or not yet.
					 */
					$nfw_['nfw_options']['logging'] = 1;

					if ( $where == 1 ) {
						$where = 'wp-login.php';
					} else {
						$where = 'XML-RPC API';
					}

					if ( $config['bf_type'] == 0 ) {
						$nfw_['incidentID'] = NinjaFirewall_log::write(
							"Brute-force attack detected on $where",
							"enabling HTTP authentication for {$config['bf_bantime']}mn",
							NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
						);

					} else {
						$nfw_['incidentID'] = NinjaFirewall_log::write(
							"Brute-force attack detected on $where",
							"enabling CAPTCHA for {$config['bf_bantime']}mn",
							NFWLOG_CRITICAL, 0, $nfw_['nfw_options'], $nfw_['log_dir']
						);
					}
					/**
					 * Write to the AUTH log.
					 */
					if (! empty( $config['bf_authlog'] ) ) {
						if (! defined('NFW_REMOTE_ADDR') ) {
							NinjaFirewall_IP::check_ip( $nfw_['nfw_options'] );
						}
						if ( defined('LOG_AUTHPRIV') ) {
							$tmp = LOG_AUTHPRIV;
						} else {
							$tmp = LOG_AUTH;
						}
						@ openlog('ninjafirewall', LOG_NDELAY|LOG_PID, $tmp);
						@ syslog(LOG_INFO, 'Possible brute-force attack from '. NFW_REMOTE_ADDR .
								" on $server_name ($where). Blocking access for {$config['bf_bantime']}mn." );
						@ closelog();
					}
					self::authentification( $config );
					return;

				}
			}
			/**
			 * Reset old log.
			 */
			$mtime = filemtime( $log_file );
			if ( $now - $mtime > $config['bf_bantime'] * 60 ) {
				unlink( $log_file );
			}
		}
		/**
		 * Record and allow the request.
		 */
		@ file_put_contents(	$log_file, "$now\n", FILE_APPEND | LOCK_EX );
	}


	/**
	 * Verify the user authentication.
	 */
	static private function authentification( $config ) {
		/**
		 * Prevent favicon.ico 302 redirection to the login page
		 * due to plugins that do not handle well the login page access.
		 */
		if ( isset( $_GET['redirect_to'] ) && strpos( $_GET['redirect_to'], 'favicon.ico') !== FALSE ) {
			exit;
		}

		NinjaFirewall_session::start();

		global $nfw_;

		/**
		 * Already authenticated?
		 */
		$nfw_bfd = NinjaFirewall_session::read('nfw_bfd');
		if ( isset( $nfw_bfd ) && $nfw_bfd == $config['bf_rand'] ) {
			return;
		}

		if ( $config['bf_type'] == 0 ) {
			/**
			 * Password protection.
			 */
			if (! empty( $_POST['u'] ) && ! empty( $_POST['p'] ) ) {
				if ( $_POST['u'] === $config['auth_name'] &&
					hash_equals( $config['auth_pass'], sha1( $_POST['p'] ) ) ) {

					NinjaFirewall_session::write( ['nfw_bfd' => $config['bf_rand'] ] );
					return;
				}
			}
		} else {
			/**
			 * Make sure the GD extension is loaded.
			 */
			if ( function_exists('gd_info') ) {
				/**
				 * Captcha protection.
				 */
				$nfw_bfd_c = NinjaFirewall_session::read('nfw_bfd_c');
				if (! empty( $_POST['c'] ) && isset( $nfw_bfd_c ) ) {
					if ( $nfw_bfd_c == strtolower( $_POST['c'] ) ) {
						NinjaFirewall_session::write( ['nfw_bfd' => $config['bf_rand'] ] );
						NinjaFirewall_session::delete('nfw_bfd_c');
						return;
					}
				}
			} else {
				/**
				 * Return in no GD extension.
				 */
				return;
			}
		}

		NinjaFirewall_session::delete();

		if ( $config['b64'] ) {
			$config['auth_msgtxt'] = base64_decode( $config['auth_msgtxt'] );
		}
		/**
		 * Ask for authentication.
		 */
		header('HTTP/1.0 401 Unauthorized');
		header('X-Frame-Options: SAMEORIGIN');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Expires: 0');
		if ( empty( $config['bf_nosig'] ) ) {
			$config['bf_nosig'] = 'Brute-force protection by NinjaFirewall';
		} else {
			$config['bf_nosig'] = '';
		}

		$header = '<html><head><title>'. $config['bf_nosig']  .'</title><link rel="stylesheet" '.
			'href="./wp-includes/css/buttons.min.css" type="text/css"><link rel="stylesheet" href="'.
			'./wp-admin/css/login.min.css" type="text/css"><link rel="stylesheet" href="./wp-admin/css'.
			'/forms.min.css" type="text/css"><meta http-equiv="Content-Type" content="text/html; '.
			'charset=utf-8"></head><body class="login wp-core-ui" style="color:#444"><div id="login">'.
			'<center>';

		if ( $config['bf_type'] == 0 ) {
			/**
			 * Password.
			 */
			$message = $header .'<h2>' . htmlentities( $config['auth_msgtxt'] ) . '</h2><form '.
				'method="post"><label>'. $config['bf_nosig']  .'</label><br><br><p><input class="input"'.
				' type="text" name="u" placeholder="Username" autofocus></p><p><input class="input" '.
				'type="password" name="p" placeholder="Password"></p><p align="right"><input type="submit"'.
				' value="Login Page&nbsp;&#187;" class="button-secondary"></p><input type="hidden" '.
				'name="reauth" value="1"></form></center></div></body></html>';
		} else {
			/**
			 * Captcha.
			 */
			$captcha = self::captcha();
			if ( $captcha === false ) {
				return;
			}
			$message = $header .'<form method="post"><p><label>'.
				htmlentities( base64_decode( $config['captcha_text'] ) ) .'</label></p><br><p>' .
				$captcha . '</p><p><input class="input" type="text" name="c" autofocus></p><p '.
				'align="right"><input type="submit" value="Login Page&nbsp;&#187;" class="button-'.
				'secondary"></p><input type="hidden" name="reauth" value="1"></form><br><label>'.
				$config['bf_nosig']  .'</label></center></div></body></html>';
		}

		if ( $config['bf_allow_bot'] == 0 ) {
			if ( @ ini_set('zlib.output_compression','Off') !== false ) {
				header('Content-Encoding: gzip');
				echo gzencode( $message, 1 );
				exit;
			}
		}
		header('Content-Type: text/html; charset=utf-8');
		echo $message;
		exit;
	}


	/**
	 * Return the brute-force protection captcha.
	 */
	static private function captcha() {

		if (! function_exists('imagettftext') ) {
			echo "<div id='login_error' style='padding:6px; color:white;background:red'>NinjaFirewall ".
				"error: PHP imagettftext() function doesn't exist, the captcha can't be displayed. ".
				"Make sure PHP is compiled with freetype support (--with-freetype-dir=DIR).</div>";
			return false;
		}

		NinjaFirewall_session::start();

		$characters  = 'AaBbCcDdEeFfGgHhiIJjKkLMmNnPpRrSsTtUuVvWwXxYyZz123456789';
		$captcha = '';
		while( strlen( $captcha ) < 5 ) {
			$captcha .= substr( $characters, mt_rand() % strlen( $characters ), 1 );
		}

		// Background image with dimensions
		$image = imagecreate( 200, 60 );
		// Background color
		imagecolorallocate( $image, 255, 255, 255 );
		// Text color
		$text_color = imagecolorallocate( $image, 77, 77, 77 );
		// Font
		global $nfw_;
		if ( is_file( "{$nfw_['log_dir']}/font.ttf" ) ) {
			imagettftext( $image, 35, 0, 15, 45, $text_color, "{$nfw_['log_dir']}/font.ttf", $captcha );

		} else {
			imagettftext( $image, 35, 0, 15, 45, $text_color, __DIR__ .'/share/font.ttf', $captcha );
		}

		ob_start();
		imagepng( $image );
		$img_content = ob_get_contents();
		ob_end_clean();

		$res = '<img src="data:image/png;base64,'. base64_encode( $img_content ) .'" />';
		NinjaFirewall_session::write( ['nfw_bfd_c' => strtolower( $captcha ) ] );

		return $res;
	}


	/**
	 * Retrieve the protection configuration.
	 */
	static public function read_config( $conf_file ) {

		if (! is_file( $conf_file ) ) {
			return [];
		}

		$data = file( $conf_file );
		if ( empty( $data[ 1 ] ) ) {
			return [];
		}
		$res = unserialize( $data[ 1 ], ['allowed_classes' => false] );
		if ( $res === false ) {
			return [];
		}
		return $res;
	}


	/**
	 * Convert the old configuration file to the new format.
	 */
	static public function convert( $bf_conf_dir ) {
		/**
		 * Read and delete the old file.
		 */
		$content = file_get_contents( "$bf_conf_dir/bf_conf.php" );
		unlink( "$bf_conf_dir/bf_conf.php" );

		if ( preg_match("/\\\$bf_enable=(\d+);\\\$bf_type=(\d+);\\\$bf_request='(.*?)';".
			"\\\$bf_bantime=(\d+);\\\$bf_attempt=(\d+);\\\$bf_maxtime=(\d+);".
			"\\\$bf_xmlrpc=(\d+);\\\$bf_allow_bot=(\d+);\\\$auth_name='(.*?)';".
			"\\\$auth_pass='(.*?)';\\\$auth_msgtxt='(.*?)';\\\$bf_rand='(\d+)';".
			"\\\$bf_authlog=(\d+);\\\$captcha_text='(.*?)';\\\$bf_nosig=(\d+);/", $content, $match ) ) {

			$config  = [];

			if ( count( $match ) == 16 ) {
				$config['bf_enable']    = $match[ 1 ];
				$config['bf_type']      = $match[ 2 ];
				$config['bf_request']   = $match[ 3 ];
				$config['bf_bantime']   = $match[ 4 ];
				$config['bf_attempt']   = $match[ 5 ];
				$config['bf_maxtime']   = $match[ 6 ];
				$config['bf_xmlrpc']    = $match[ 7 ];
				$config['bf_allow_bot'] = $match[ 8 ];
				$config['auth_name']    = $match[ 9 ];
				$config['auth_pass']    = $match[ 10 ];
				$config['auth_msgtxt']  = $match[ 11 ];
				$config['bf_rand']      = $match[ 12 ];
				$config['bf_authlog']   = $match[ 13 ];
				$config['captcha_text'] = $match[ 14 ];
				$config['bf_nosig']     = $match[ 15 ];
				/**
				 * We use serialize because it'll be *way* faster to decode than
				 * a json-encoded payload during a large-scale brute-force attack.
				 */
				file_put_contents(
					"$bf_conf_dir/login_protection.php", "<?php exit; ?>\n". serialize( $config )
				);
			}
		}
	}


	/**
	 * Verify the configuration (e.g., used for configuration import, backup restoration).
	 * `$bf_conf` is a string, not an array.
	 */
	static public function verify( $string ) {

		$array = preg_split('/[\r\n]/', $string );
		if ( empty( $array[ 1 ] ) ) {
			return [];
		}
		$res = unserialize( $array[ 1 ], ['allowed_classes' => false] );
		if ( $res === false ) {
			return [];
		}
		return $res;
	}


	/**
	 * Disable the brute-force protection when the plugin is deactivated.
	 */
	static public function disable( $bf_conf_dir ) {

		if ( is_file("$bf_conf_dir/login_protection.php") ) {
			rename(
				"$bf_conf_dir/login_protection.php",
				"$bf_conf_dir/login_protection_off.php"
			);
			return true;
		}
		return false;
	}


	/**
	 * Enable the brute-force protection when the plugin is activated.
	 */
	static public function enable( $bf_conf_dir ) {

		/**
		 * Backward compatibility with versions <4.9.1.
		 */
		if ( is_file("$bf_conf_dir/bf_conf_off.php") ) {
			rename(
				"$bf_conf_dir/bf_conf_off.php",
				"$bf_conf_dir/bf_conf.php"
			);
			return true;
		}

		if ( is_file("$bf_conf_dir/login_protection_off.php") ) {
			rename(
				"$bf_conf_dir/login_protection_off.php",
				"$bf_conf_dir/login_protection.php"
			);
			return true;
		}
		return false;
	}


}
// =====================================================================
// EOF

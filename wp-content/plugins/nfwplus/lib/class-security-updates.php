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

if ( class_exists('NinjaFirewall_security_updates') ) {
	return;
}


class NinjaFirewall_security_updates {

	private static $found				= [];
	private static $list					= [];
	private static $transient_name	= 'nfw_fetchsecupdates';
	private static $transient_int		= 3000;

	private static $main_site_only	= true;


	/**
	 * Retrieve and check the security updates (WordPress, plugins and themes).
	 * Called by NinjaFirewall's garbage collector.
	 */
	public static function check() {
		/**
		 * We run on the main site only.
		 */
		if ( self::$main_site_only && ! is_main_site() ) {
			return;
		}

		if ( get_transient( self::$transient_name ) !== false ) {
			return;
		}

		$nfw_checked = nfw_get_option('nfw_checked');
		if ( empty( $nfw_checked ) ) {
			$nfw_checked = [];
		}

		$nfw_options = nfw_get_option('nfw_options');
		if ( empty( $nfw_options['secupdates'] ) ) {
			// Option is disabled, exiting.
			return;
		}

		/**
		 * Connect to the remove API.
		 */
		require_once __DIR__ .'/class-api.php';
		self::$list = NinjaFirewall_api::download_security_updates();

		if (! isset( self::$list['wordpress'] ) ||
			! isset( self::$list['themes'] ) ||
			! isset( self::$list['plugins'] ) ) {

			nfw_log_error(	__('Downloaded list of vulnerabilities is corrupted', 'nwplus') );
			return false;
		}

		set_transient( self::$transient_name, 1, self::$transient_int );

		/**
		 * Check WordPress security updates.
		 */
		global $wp_version;
		if ( isset( self::$list['wordpress']['version'] ) ) {

			if ( version_compare( $wp_version, self::$list['wordpress']['version'], '<') &&
			version_compare( $wp_version, self::$list['wordpress']['mini'], '>=') ) {
				/**
				 * Make sure the user wasn't already warned about that.
				 */
				if (! isset( $nfw_checked['wordpress']['version'] ) ||
					version_compare( $nfw_checked['wordpress']['version'], self::$list['wordpress']['version'], '<') ) {

					self::$found['wordpress']['cur_version'] = $wp_version;
					self::$found['wordpress']['new_version'] = self::$list['wordpress']['version'];
					self::$found['wordpress']['level']       = self::$list['wordpress']['level'];
				}
			}
			/**
			 * Mark as checked.
			 */
			$nfw_checked['wordpress']['version'] = self::$list['wordpress']['version'];
			$nfw_checked['wordpress']['mini']    = self::$list['wordpress']['mini'];
		}

		/**
		 * Check themes security updates.
		 */
		if ( ! function_exists('wp_get_themes') ) {
			require_once ABSPATH .'wp-includes/theme.php';
		}
		$themes = wp_get_themes();

		foreach( $themes as $k => $v ) {
			/**
			 * No name or no version (unlike plugins, we're dealing with objects here).
			 */
			if ( $v->Name == '' || $v->Version == '') {
				continue;
			}
			$hash = hash('sha256', $k );
			/**
			 * Check if the theme is installed.
			 */
			if ( isset( self::$list['themes'][$hash] ) ) {

				if (	version_compare( $v->Version, self::$list['themes'][$hash]['version'], '<') &&
				version_compare( $v->Version, self::$list['themes'][$hash]['mini'], '>=') ) {
					/**
					* Make sure we didn't inform the user yet.
					*/
					if (! isset( $nfw_checked['themes'][$k] ) ||
						version_compare( $nfw_checked['themes'][$k]['version'], self::$list['themes'][$hash]['version'], '<') ) {

						self::$found['themes'][$k]['name']        = $v->Name;
						self::$found['themes'][$k]['cur_version'] = $v->Version;
						self::$found['themes'][$k]['new_version'] = self::$list['themes'][$hash]['version'];
						self::$found['themes'][$k]['level']       = self::$list['themes'][$hash]['level'];
					}
				}
				/**
				 * Mark as checked.
				 */
				$nfw_checked['themes'][$k]['version'] = self::$list['themes'][$hash]['version'];
				$nfw_checked['themes'][$k]['mini']    = self::$list['themes'][$hash]['mini'];
			}
		}

		/**
		 * Check plugins security updates.
		 */
		if ( ! function_exists('get_plugins') ) {
			require_once ABSPATH .'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();

		foreach( $plugins as $k => $v ) {
			/**
			 * No name or no version (unlike themes, we're dealing with arrays here).
			 */
			if ( empty( $v['Name'] ) || empty( $v['Version'] ) ) {
				continue;
			}
			$hash = hash('sha256', $k );
			/**
			 * Check if the plugin is installed.
			 */
			if ( isset( self::$list['plugins'][$hash] ) ) {

				if ( version_compare( $v['Version'], self::$list['plugins'][$hash]['version'], '<') &&
					version_compare( $v['Version'], self::$list['plugins'][$hash]['mini'], '>=') ) {
					/**
					 * Make sure we didn't inform the user yet.
					 */
					if (! isset( $nfw_checked['plugins'][$k] ) ||
						version_compare( $nfw_checked['plugins'][$k]['version'], self::$list['plugins'][$hash]['version'], '<') ) {
						/**
						 * Add it to the notification list.
						 */
						self::$found['plugins'][$k]['name']        = $v['Name'];
						self::$found['plugins'][$k]['cur_version'] = $v['Version'];
						self::$found['plugins'][$k]['new_version'] = self::$list['plugins'][$hash]['version'];
						self::$found['plugins'][$k]['level']       = self::$list['plugins'][$hash]['level'];
					}
				}
				/**
				 * Mark as checked.
				*/
				$nfw_checked['plugins'][$k]['version'] = self::$list['plugins'][$hash]['version'];
				$nfw_checked['plugins'][$k]['mini']    = self::$list['plugins'][$hash]['mini'];
			}
		}
		/**
		 * Send a email notification to the user.
		 */
		if (! empty( self::$found ) ) {
			self::alert();
		}

		/**
		 * Always update the checked list.
		*/
		nfw_update_option('nfw_checked', $nfw_checked, false );

		return;
	}


	/**
	 * Send a notification to the user with the list of security updates available.
	 */
	private static function alert() {

		$message = '';
		/**
		 * WordPress.
		 */
		if (! empty( self::$found['wordpress'] ) ) {
			$message .= "WordPress:\n" .
				sprintf(
					__('Your version: %s', 'nwplus'), self::$found['wordpress']['cur_version']
				) ."\n".
				sprintf(
					__('New version: %s', 'nwplus'), self::$found['wordpress']['new_version']
				) ."\n";
			if ( self::$found['wordpress']['level'] == 2 ) {
				$message .= __('Severity: This is an important security update', 'nwplus') ."\n";
			} elseif ( self::$found['wordpress']['level'] == 3 ) {
				$message .= __('Severity: **This is a critical security update**', 'nwplus') ."\n";
			}
			$message .= "\n";
		}
		/**
		 * Plugins.
		 */
		if (! empty( self::$found['plugins'] ) ) {
			foreach( self::$found['plugins'] as $k => $v ) {
				$message .= sprintf(
					__('Plugin: %s', 'nwplus'), self::$found['plugins'][$k]['name']
				) ."\n".
				sprintf(
					__('Your version: %s', 'nwplus'), self::$found['plugins'][$k]['cur_version']
				) ."\n".
				sprintf(
					__('New version: %s', 'nwplus'), self::$found['plugins'][$k]['new_version']
				) ."\n";

				if ( self::$found['plugins'][$k]['level'] == 2 ) {
					$message .= __('Severity: This is an important security update', 'nwplus') ."\n";
				} elseif ( self::$found['plugins'][$k]['level'] == 3 ) {
					$message .= __('Severity: **This is a critical security update**', 'nwplus') ."\n";
				}
				$message .= "\n";
			}
		}
		/**
		 * Themes.
		 */
		if (! empty( self::$found['themes'] ) ) {

			foreach( self::$found['themes'] as $k => $v ) {
				$message .= sprintf(
					__('Theme: %s', 'nwplus'), self::$found['themes'][$k]['name']
				) ."\n".
				sprintf(
					__('Your version: %s', 'nwplus'), self::$found['themes'][$k]['cur_version']
				) ."\n".
				sprintf(
					__('New version: %s', 'nwplus'), self::$found['themes'][$k]['new_version']
				) ."\n";

				if ( self::$found['themes'][$k]['level'] == 2 ) {
					$message .= __('Severity: This is an important security update', 'nwplus') ."\n";
				} elseif ( self::$found['themes'][$k]['level'] == 3 ) {
					$message .= __('Severity: **This is a critical security update**', 'nwplus') ."\n";
				}
				$message .= "\n";
			}
		}
		if ( is_multisite() ) {
			$url = network_home_url('/');
		} else {
			$url = home_url('/');
		}
		/**
		 * Email notification.
		 */
		$subject = [];
		$content = [ ucfirst( date_i18n('F j, Y @ H:i:s T') ), $url, $message ];

		NinjaFirewall_mail::send('security_updates', $subject, $content, '', [], 1 );
	}


	/**
	 * Display a red notice if there's a pending security update in the backend "Plugins" page.
	 */
	public static function display() {
		/**
		 * We run on the main site only.
		 */
		if ( self::$main_site_only && ! is_main_site() ) {
			return;
		}

		$nfw_checked = nfw_get_option('nfw_checked');
		if ( empty( $nfw_checked['plugins'] ) ) {
			return;
		}

		/**
		 * Check plugins updates.
		 */
		if (! function_exists('get_plugins') ) {
			require_once ABSPATH .'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$cleared = 0;
		$count   = 0;
		// Get the list of plugin updates that WordPress marked as available.
		$wp_updates = get_site_transient('update_plugins');

		foreach( $plugins as $k => $v ) {
			// No name or no version (unlike themes, we're dealing with arrays here)
			if ( empty( $v['Name'] ) || empty( $v['Version'] ) ) {
				continue;
			}

			if ( isset( $nfw_checked['plugins'][$k] ) ) {

				if ( empty( $nfw_checked['plugins'][$k]['mini'] ) ) {
					// Since version 4.9
					$nfw_checked['plugins'][$k]['mini'] = 0;
				}
				/**
				 * Compare current and available versions.
				 */
				if ( version_compare( $v['Version'], $nfw_checked['plugins'][$k]['version'], '<') &&
					version_compare( $v['Version'], $nfw_checked['plugins'][$k]['mini'], '>=') ) {

					$args = [
						'name'    => $v['Name'],
						'plugin'  => $k,
						'version' => $nfw_checked['plugins'][$k]['version'],
						'nonce'   => wp_create_nonce('pluginupgrade'),
						// We don't display the "Install now" button if WordPress allows the upgrade
						'upgrade' => isset( $wp_updates->response[ $k ] ) ? false : true,
						'count'   => ++$count
					];
					add_action( "after_plugin_row_{$k}",
					function() use ( $args ) {
						?>
						<tr class="plugin-update-tr active">
							<td colspan="4" class="plugin-update colspanchange">
								<div class="update-message notice inline notice-error notice-alt">
								 <?php
									echo esc_html__('Important: NinjaFirewall has detected that this is a security update.', 'nwplus') . ' '.
									esc_html__("Don't leave your blog at risk, make sure to update as soon as possible.", 'nwplus');
									echo '<br />';
									echo '<strong>'. esc_html__('Plugin:', 'nwplus') .'</strong> <em>'.
											esc_html( $args['name'] ) .'</em> - <strong>'. esc_html__('New version:', 'nwplus') .'</strong> <em>'.
											esc_html( $args['version'] ) .'</em>';

									if ( $args['upgrade'] ) {
										echo '<p>' . esc_html__('Because WordPress.org enforces a mandatory cooldown period of several hours on all new plugin releases, NinjaFirewall allows you to update this plugin immediately by clicking the button below.', 'nwplus') .'</p>';
										?>
										<button type="button" id="nf-progress-id-<?php echo esc_attr( $args['count'] ) ?>" class="button button-secondary" onClick="nfwjs_upgrade_plugin('<?php
										echo esc_attr( $args['plugin'] ) ?>','<?php
										echo esc_attr( $args['version'] ) ?>','<?php
										echo esc_attr( $args['nonce'] ) ?>','<?php
										echo esc_attr( $args['count'] ) ?>')" />
										<?php
										echo esc_html__('Update the plugin now!', 'nwplus')?></button>
										&nbsp;&nbsp;&nbsp;
										<img style="vertical-align:middle;display:none" id="nf-progress-gif-<?php echo esc_attr( $args['count'] ) ?>" src="<?php
											echo plugins_url('/images/progress.gif', dirname (__FILE__ ) ) ?>" />
									<?php
									}
									echo '<br/><a href="https://blog.nintechnet.com/how-to-get-informed-about-the-latest-security-updates-in-your-wordpress-plugins-and-themes/" target="_blank">' .
									esc_html__('More info about this warning', 'nwplus') .'</a>';
									?>
								</div>
							</td>
						</tr>
						<?php
						}
					);
				} else {
					// Remove if from our cache
					unset( $nfw_checked['plugins'][$k] );
					$cleared = 1;
				}
			}
		}
		/**
		 * Clear the list of plugins or themes that were uninstalled instead of updated.
		 */
		if (! empty( $nfw_checked['plugins'] ) ) {
			foreach( $nfw_checked['plugins'] as $k => $v ) {
				if (! file_exists( WP_PLUGIN_DIR ."/$k" ) ) {
					unset( $nfw_checked['plugins'][$k] );
					$cleared = 1;
				}
			}
		}
		if (! empty( $nfw_checked['themes'] ) ) {
			foreach( $nfw_checked['themes'] as $k => $v ) {
				if (! is_dir( WP_CONTENT_DIR ."/themes/$k" ) ) {
					unset( $nfw_checked['themes'][$k] );
					$cleared = 1;
				}
			}
		}
		/**
		 * Update our list, if needed.
		 */
		if ( $cleared ) {
			nfw_update_option('nfw_checked', $nfw_checked, false );
		}
	}

}
// =====================================================================
// EOF

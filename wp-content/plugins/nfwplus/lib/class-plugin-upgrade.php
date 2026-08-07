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

if ( class_exists('NinjaFirewall_plugin') ) {
	return;
}

class NinjaFirewall_plugin {

	private static $url     			= 'https://downloads.wordpress.org/plugin/%s.%s.zip';
	private static $slug    			= '';
	private static $main    			= '';
	private static $network 			= false;
	private static $active  			= false;

	private static $main_site_only	= true;


	/**
	 * Initialize the filesystem.
	 */
	private static function init() {

		require_once ABSPATH .'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH .'wp-admin/includes/class-wp-filesystem-direct.php';
		require_once ABSPATH .'wp-admin/includes/file.php';
		require_once ABSPATH .'wp-admin/includes/plugin.php';

		$res = WP_Filesystem();
		if ( $res === true ) {
			return;
		}
		$error = esc_html__('Error initialize the filesystem.', 'nfwplus');
		wp_send_json( ['status' => 'error', 'message' => $error ] );
	}


	/**
	 * Manual upgrade of the corresponding plugin.
	 * `nfw_pluginupgrade` AJAX action.
	 */
	public static function upgrade() {
		/**
		 * The superadmin must run the update from the main site only to avoid errors.
		 */
		if ( self::$main_site_only && ! is_main_site() ) {
			$error = esc_html__('Connect to the main site and try again.', 'nfwplus');
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		global $wp_filesystem;

		/**
		 * Verify user capabilities and the security nonce.
		 */
		nf_not_allowed('block', __LINE__, 'ajax');
		if (! check_ajax_referer('pluginupgrade', 'nonce', false ) ) {
			$error = esc_html__('Security nonces do not match. Reload the page and try again.',
				'nfwplus');
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		if ( empty( $_POST['plugin'] ) || empty( $_POST['version'] ) ) {
			$error = esc_html__('Missing parameters.', 'nfwplus');
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		list( self::$slug, self::$main ) = explode('/', stripslashes( urldecode( $_POST['plugin'] ) ), 2 );
		self::$slug = sanitize_title( self::$slug );
		self::$main = sanitize_file_name( self::$main );

		$version = stripslashes( $_POST['version'] );

		/**
		 * Make sure the plugin and its version match our list.
		 */
		$nfw_checked = nfw_get_option('nfw_checked');
		$error = esc_html__('Plugin name or version is invalid.', 'nfwplus');
		if ( empty( $nfw_checked['plugins'] ) ||
			! isset( $nfw_checked['plugins'][ self::$slug .'/'. self::$main ]['version'] ) ) {

			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}
		if ( $nfw_checked['plugins'][ self::$slug .'/'. self::$main ]['version'] != $version ) {
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		/**
		 * Initialize the filestem.
		 */
		self::init();

		/**
		 * Check if the plugin is activated across the entire network,
		 * if that's a multisite environment.
		 */
		if ( is_multisite() && is_plugin_active_for_network( self::$slug .'/'. self::$main ) ) {
			self::$network = true;
		}

		/**
		 * Deactivate the plugin, silently.
		 */
		if ( is_plugin_active( self::$slug .'/'. self::$main ) ) {
			deactivate_plugins( self::$slug .'/'. self::$main ,true );
			self::$active = true;
		}

		/**
		 * Download it from wordpress.org.
		 */
		self::$url = sprintf( self::$url, self::$slug, $version );
		$file = download_url( self::$url );
		if ( is_wp_error( $file ) ) {
			$error = sprintf(
				esc_html__('Error when trying to download the plugin: %s', 'nfwplus'),
				esc_html ( $file->get_error_message() )
			);
			/**
			 * Reactivate the plugin and quit.
			 */
			self::activate_plugin();
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		/**
		 * Delete the plugin folder, recursively.
		 */
		$plugin_dir = trailingslashit( WP_PLUGIN_DIR ) . self::$slug;
		if ( $wp_filesystem->is_dir( $plugin_dir ) ) {
			$res = $wp_filesystem->delete( $plugin_dir, true, 'd');
			if ( $res === false ) {
				$error = sprintf(
					esc_html__('Error when trying to delete the plugin folder: %s', 'nfwplus'),
					esc_html( $plugin_dir )
				);
				/**
				 * Try to reactivate the plugin, if possible, before quitting.
				 */
				self::activate_plugin();
				wp_send_json( ['status' => 'error', 'message' => $error ] );
			}
		}

		/**
		 * Unzip the new plugin.
		 */
		$res = unzip_file( $file, WP_PLUGIN_DIR );
		if ( is_wp_error( $res ) ) {
			$error = sprintf(
				esc_html__('Error when trying to extract the plugin: %s', 'nfwplus'),
				esc_html ( $res->get_error_message() )
			);
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		/**
		 * Delete the downloaded file.
		 */
		wp_delete_file( $file );

		/**
		 * Reactivate the plugin, if it was activated prior to the upgrade.
		 */
		$res = self::activate_plugin();
		if ( is_wp_error( $res ) ) {
			$error = sprintf(
				esc_html__('Error when trying to reactivate the plugin: %s', 'nfwplus'),
				esc_html ( $res->get_error_message() )
			);
			wp_send_json( ['status' => 'error', 'message' => $error ] );
		}

		$message = esc_html__('The plugin was successfully updated. The page will now reload.',
			'nfwplus');
		wp_send_json( ['status' => 'success', 'message' => $message ] );
	}


	/**
	 * Reactivate the plugin.
	 */
	private static function activate_plugin() {

		if ( self::$active === true ) {
			return activate_plugin( self::$slug .'/'. self::$main, '', self::$network, true );
		}
	}

}

// =====================================================================
// EOF

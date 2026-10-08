<?php

/**
 * Add support installing upgrading and downgrading
 * @since 2.8
 */
class FLBuilderAddonsInstaller {

	private $strings = [];

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_fl_addons_install', array( $this, 'addon_install' ) );
		add_action( 'wp_ajax_fl_addons_activate', array( $this, 'addon_activate' ) );
		add_action( 'fl_builder_after_subscription_downloads', array( $this, 'security_nonce' ) );

		add_filter( 'fl_builder_subscription_downloads', array( $this, 'subscription_downloads' ) );

		add_action( 'init', function () {
			$this->strings = array(
				'install'   => __( 'Install', 'fl-builder' ),
				'installed' => __( 'Installed', 'fl-builder' ),
				'activate'  => __( 'Activate', 'fl-builder' ),
				'reinstall' => __( 'Reinstall', 'fl-builder' ),
				'activated' => __( 'Activated', 'fl-builder' ),
			);
		});
	}

	public function addon_install() {

		check_ajax_referer( 'subscription_downloads' );
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}
		if ( 'plugin' === $_POST['type'] ) {
			$this->install_plugin();
		}
		if ( 'theme' === $_POST['type'] ) {
			$this->install_theme();
		}
	}

	public function addon_activate() {
		check_ajax_referer( 'subscription_downloads' );
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}
		$type = $_POST['type'];
		$slug = $_POST['slug'];

		if ( 'plugin' === $type ) {
			if ( ! is_plugin_active( $slug ) ) {
				activate_plugin( $slug );
				wp_send_json_success();
			}
		}
	}

	public function install_plugin() {
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

		$slug     = $_POST['slug'];
		$url      = sprintf( 'https://updates.wpbeaverbuilder.com/?fl-api-method=composer_download&download=%s.zip&license=%s', $slug, FLUpdater::get_subscription_license() );
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$defaults = array(
			'clear_update_cache' => true,
			'overwrite_package'  => true, // Do not overwrite files.
		);
		$result   = $upgrader->install( $url, $defaults );

		if ( true === $result ) {
			return wp_send_json_success();
		} else {
			return wp_send_json_error( $skin->get_errors() );
		}
	}

	public function install_theme() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

		$slug     = $_POST['slug'];
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		$defaults = array(
			'clear_update_cache' => true,
			'overwrite_package'  => true, // Do not overwrite files.
		);

		if ( 'bb-theme-child' === $slug ) {
			$url    = sprintf( 'https://updates.wpbeaverbuilder.com/?fl-api-method=composer_download&download=%s.zip&license=%s', 'bb-theme', FLUpdater::get_subscription_license() );
			$result = $upgrader->install( $url, $defaults );
			if ( ! $result ) {
				return wp_send_json_error( $skin->get_errors() );
			}
			$url    = sprintf( 'https://updates.wpbeaverbuilder.com/?fl-api-method=composer_download&download=%s.zip&license=%s', 'bb-theme-child', FLUpdater::get_subscription_license() );
			$result = $upgrader->install( $url, $defaults );
			if ( ! $result ) {
				return wp_send_json_error( $skin->get_errors() );
			}
			return wp_send_json_success();
		}

		$url = sprintf( 'https://updates.wpbeaverbuilder.com/?fl-api-method=composer_download&download=%s.zip&license=%s', 'bb-theme', FLUpdater::get_subscription_license() );

		$result = $upgrader->install( $url, $defaults );
		if ( true === $result ) {
			return wp_send_json_success();
		} else {
			return wp_send_json_error( $skin->get_errors() );
		}
	}

	public function scripts() {
		// Only load on the BB settings / network settings pages where the
		// subscription downloads list (Install/Activate buttons) renders.
		if ( ! isset( $_GET['page'] ) || ! in_array( $_GET['page'], array( 'fl-builder-settings', 'fl-builder-multisite-settings' ), true ) ) {
			return;
		}
		wp_enqueue_script( 'bb-addon-scripts', FL_BUILDER_ADDONS_PLUGINS_URL . 'js/addons-installer.js', array( 'jquery' ), FL_BUILDER_VERSION, true );
		wp_localize_script( 'bb-addon-scripts', 'bb_addon_data', array(
			'install'     => __( 'Install', 'fl-builder' ),
			'installed'   => __( 'Installed', 'fl-builder' ),
			'activate'    => __( 'Activate', 'fl-builder' ),
			'activated'   => __( 'Activated', 'fl-builder' ),
			'reinstall'   => __( 'Reinstall', 'fl-builder' ),
			'wait'        => __( 'Installing Please Wait', 'fl-builder' ),
			'plugins_url' => admin_url( 'plugins.php' ),
			'themes_url'  => admin_url( 'themes.php' ),
		) );
	}

	public function subscription_downloads( $downloads ) {

		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$themes = wp_get_themes();
		$theme  = wp_get_theme();

		foreach ( $downloads as $k => $download ) {
			$name        = $download;
			$status_html = '';
			$actions     = '';

			switch ( $download ) {
				case 'Beaver Builder Theme':
					$installed = isset( $themes['bb-theme'] );
					$active    = 'bb-theme' === get_stylesheet() || 'bb-theme' === $theme->get( 'Template' );
					if ( $installed ) {
						$status_html = $this->_status_html( 'installed' );
						if ( ! $active ) {
							$actions .= sprintf( '<a href="%s" class="fl-download-action fl-download-action-primary">%s</a>', admin_url( 'themes.php' ), $this->strings['activate'] );
						}
						$actions .= $this->_reinstall_link( 'theme', 'bb-theme' );
					} else {
						$status_html = $this->_status_html( 'not-installed' );
						$actions    .= $this->_install_button( 'theme', 'bb-theme' );
					}
					break;

				case 'Beaver Builder Child Theme':
					$installed = isset( $themes['bb-theme-child'] );
					$active    = 'bb-theme' === $theme->get( 'Template' );
					if ( $installed ) {
						$status_html = $this->_status_html( 'installed' );
						if ( ! $active ) {
							$actions .= sprintf( '<a href="%s" class="fl-download-action fl-download-action-primary">%s</a>', admin_url( 'themes.php' ), $this->strings['activate'] );
						}
						$actions .= $this->_reinstall_link( 'theme', 'bb-theme-child' );
					} else {
						$status_html = $this->_status_html( 'not-installed' );
						$actions    .= $this->_install_button( 'theme', 'bb-theme-child' );
					}
					break;

				case 'Beaver Themer':
					$installed = $this->check_plugin_installed( 'bb-theme-builder/bb-theme-builder.php' );
					if ( $installed ) {
						$status_html = $this->_status_html( 'installed' );
						if ( ! is_plugin_active( 'bb-theme-builder/bb-theme-builder.php' ) ) {
							$actions .= sprintf(
								'<a class="fl-download-action fl-download-action-primary fl-installer-addon-activate" data-type="plugin" data-slug="bb-theme-builder/bb-theme-builder.php" href="#">%s</a>',
								$this->strings['activate']
							);
						}
						$actions .= $this->_reinstall_link( 'plugin', 'bb-theme-builder' );
					} else {
						$status_html = $this->_status_html( 'not-installed' );
						$actions    .= $this->_install_button( 'plugin', 'bb-theme-builder' );
					}
					break;
			}

			// Handle BB Plugin entries
			if ( stristr( $download, 'beaver builder plugin' ) ) {
				$plugin = WP_PLUGIN_DIR . '/bb-plugin/fl-builder.php';
				if ( file_exists( $plugin ) ) {
					$data    = get_plugin_data( $plugin );
					$install = $this->_get_plugin_version( $download );

					if ( $data['Name'] === $download ) {
						// Currently installed version — show installed + reinstall
						$status_html = $this->_status_html( 'installed' );
						$slug        = 'bb-plugin-' . strtolower( $install );
						$actions    .= $this->_reinstall_link( 'plugin', $slug );
					} elseif ( $data['Name'] !== $download ) {
						// Different version available — show install link
						$current     = $this->_get_plugin_version( $data['Name'] );
						$status_html = $this->_status_html( 'not-installed' );
						$actions    .= $this->_install_button(
							'plugin',
							'bb-plugin-' . strtolower( $install ),
							sprintf(
								/* translators: %s: Version name (e.g. Pro, Agency) */
								__( 'Install %s Version', 'fl-builder' ),
								$install
							)
						);
					}
				} else {
					// BB Plugin not installed at all
					$install     = $this->_get_plugin_version( $download );
					$status_html = $this->_status_html( 'not-installed' );
					$actions    .= $this->_install_button( 'plugin', 'bb-plugin-' . strtolower( $install ) );
				}
			}

			// Build the card content
			if ( ! empty( $status_html ) || ! empty( $actions ) ) {
				$downloads[ $k ] = sprintf(
					'<span class="fl-download-name">%s</span><span class="fl-download-meta">%s%s</span>',
					esc_html( $name ),
					$status_html,
					! empty( $actions ) ? '<span class="fl-download-actions">' . $actions . '</span>' : ''
				);
			} else {
				$downloads[ $k ] = sprintf( '<span class="fl-download-name">%s</span>', esc_html( $name ) );
			}
		}
		return $downloads;
	}

	public function security_nonce() {
		wp_nonce_field( 'subscription_downloads' );
	}

	public function check_plugin_installed( $plugin_slug ) {
		$installed_plugins = get_plugins();
		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
	}

	/**
	 * Generates a styled install button.
	 */
	private function _install_button( $type, $slug, $custom = '' ) {
		$text = $custom ? $custom : $this->strings['install'];
		return sprintf(
			'<a href="#" class="fl-download-action fl-download-action-primary fl-installer-addon" data-type="%s" data-slug="%s">%s</a>',
			esc_attr( $type ),
			esc_attr( $slug ),
			esc_html( $text )
		);
	}

	/**
	 * Generates a reinstall link.
	 */
	private function _reinstall_link( $type, $slug ) {
		return sprintf(
			'<a href="#" class="fl-download-action fl-installer-addon fl-reinstall-addon" data-type="%s" data-slug="%s">%s</a>',
			esc_attr( $type ),
			esc_attr( $slug ),
			esc_html( $this->strings['reinstall'] )
		);
	}

	/**
	 * Generates a status badge.
	 */
	private function _status_html( $status ) {
		if ( 'installed' === $status ) {
			return '<span class="fl-download-status fl-download-status-installed"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html( $this->strings['installed'] ) . '</span>';
		}
		return '<span class="fl-download-status fl-download-status-available"><span class="dashicons dashicons-download"></span> ' . __( 'Available', 'fl-builder' ) . '</span>';
	}

	// Keep old methods for backwards compatibility with third-party filters
	public function get_plugin_install_link( $plugin, $custom = '' ) {
		$install_text = $custom ? $custom : $this->strings['install'];
		return sprintf( ' - <a href="#" class="fl-installer-addon" data-type="plugin" data-slug="%s">%s</a>', esc_attr( $plugin ), esc_html( $install_text ) );
	}

	public function get_theme_install_link( $theme ) {
		return sprintf( ' - <a href="#" class="fl-installer-addon" data-type="theme" data-slug="%s">%s</a>', $theme, $this->strings['install'] );
	}

	/**
	 * return standard/pro/agency
	 */
	public function _get_plugin_version( $plugin ) {
		if ( preg_match( '/\s\(([a-z]+)\s/i', $plugin, $matches ) ) {
			return $matches[1];
		}
	}
}

new FLBuilderAddonsInstaller();

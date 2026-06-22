<?php

/**
 * Add support installing upgrading and downgrading all versions
 * @since 2.9
 */
class FLBuilderVersionControl {

	private $strings = [];

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'fl_builder_admin_settings_nav_items', array( $this, 'add_menu' ) );
		add_action( 'fl_builder_admin_settings_render_forms', array( $this, 'render' ) );
		add_action( 'wp_ajax_fl_version_control', array( $this, 'installer' ) );
	}

	public function installer() {

		if ( ! wp_verify_nonce( $_POST['install_nonce'], 'fl_version_control' ) ) {
			wp_send_json_error( 'Security Error' );
		}
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}
		$type    = $_POST['type'];
		$slug    = $_POST['slug'];
		$version = $_POST['version'];
		$flavour = $_POST['flavour'] ? '-' . strtolower( $_POST['flavour'] ) : '';

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		$skin = new WP_Ajax_Upgrader_Skin();
		if ( 'plugin' === $type ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
			$upgrader = new Plugin_Upgrader( $skin );
		} else {
			require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';
			$upgrader = new Theme_Upgrader( $skin );
		}
		$defaults = array(
			'clear_update_cache' => true,
			'overwrite_package'  => true, // Do not overwrite files.
		);
		$url      = sprintf( 'https://updates.wpbeaverbuilder.com/?fl-api-method=composer_download&download=%s%s.zip&release=%s&product=%s&license=%s', $slug, $flavour, $version, $slug, FLUpdater::get_subscription_license() );

		$result = $upgrader->install( $url, $defaults );
		if ( ! $result ) {
			return wp_send_json_error( $skin->get_errors() );
		}
		return wp_send_json_success();
	}

	public function add_menu( $menu ) {

		$menu['versions'] = array(
			'title'    => __( 'Version Control', 'fl-builder' ),
			'show'     => true,
			'priority' => 999,
		);
		return $menu;
	}

	public function render() {
		$url = 'https://updates.wpbeaverbuilder.com/?fl-api-method=archive_info_public';

		$response = wp_remote_get( $url );

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $response_code ) {
			$body          = wp_remote_retrieve_body( $response );
			$data          = (array) json_decode( $body );
			$bb_data       = (array) $data['bb-plugin'];
			$themer        = (array) $data['bb-theme-builder'];
			$theme         = (array) $data['bb-theme'];
			$changeloglink = sprintf( '<a href="%s" target="_blank">%s</a>', FLBuilderModel::get_store_url( 'change-logs', array(
				'utm_medium'   => 'bb-pro',
				'utm_source'   => 'plugins-admin-page',
				'utm_campaign' => 'plugins-admin-changelog',
			) ), __( 'Changelogs Page', 'fl-builder' ) );

			include FL_BUILDER_VERSION_CONTROL_PLUGINS_DIR . 'includes/version-control.php';
		} else {
			echo __( 'There was an issue fetching the data, please try again later.', 'fl-builder' );
		}
	}

	public function scripts() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		wp_enqueue_script( 'fl-version-control', FL_BUILDER_VERSION_CONTROL_PLUGINS_URL . 'js/version-control.js', array( 'jquery' ), FL_BUILDER_VERSION, true );
		wp_enqueue_style( 'fl-version-control', FL_BUILDER_VERSION_CONTROL_PLUGINS_URL . 'css/version-control.css', array(), FL_BUILDER_VERSION );
		wp_localize_script( 'fl-version-control', 'FLBuilderAdminVersionControl', array(
			'installing' => esc_attr__( 'Installing, please wait!', 'fl-builder' ),
			'nonce'      => wp_create_nonce( 'fl_version_control', 'install_nonce' ),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * return standard/pro/agency etc
	 */
	public function _get_version_name() {
		$data = get_plugin_data( FL_BUILDER_FILE );
		if ( preg_match( '/\s\(([a-z]+)\s/i', $data['Name'], $matches ) ) {
			return strtolower( $matches[1] );
		}
	}

	public function format_versions( $versions ) {
		return array_reverse( array_slice( array_reverse( $versions ), -10 ) );
	}
}

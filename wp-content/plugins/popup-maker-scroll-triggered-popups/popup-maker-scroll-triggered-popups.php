<?php
/**
 * Plugin Name:  Popup Maker - Scroll Triggered Popups
 * Plugin URI:   https://wppopupmaker.com/extensions/scroll-triggered-popups/
 * Description:
 * Version:      1.3.2
 * Author:       WP Popup Maker
 * Author URI:   https://wppopupmaker.com/
 * Text Domain:  popup-maker-scroll-triggered-popups
 *
 * @author       WP Popup Maker
 * @copyright    Copyright (c) 2018, WP Popup Maker
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_STP
 */
class PUM_STP {

	/**
	 * @var int $download_id for EDD.
	 */
	public static $ID = 2610;

	/**
	 * @var string
	 */
	public static $NAME = 'Scroll Triggered Popups';

	/**
	 * @var string Plugin Version
	 */
	public static $VER = '1.3.2';

	/**
	 * @var string Required Version of Popup Maker
	 */
	public static $REQUIRED_CORE_VER = '1.7.29';

	/**
	 * @var int DB Version
	 */
	public static $DB_VER = 3;

	/**
	 * @var string Plugin Directory
	 */
	public static $DIR;

	/**
	 * @var string Plugin URL
	 */
	public static $URL;

	/**
	 * @var string Plugin FILE
	 */
	public static $FILE;

	/**
	 * @var self $instance
	 */
	private static $instance;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
			self::$instance->setup_constants();
			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Internationalization
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'popup-maker-scroll-triggered-popups', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Set up plugin constants.
	 */
	public function setup_constants() {
		self::$DIR  = plugin_dir_path( __FILE__ );
		self::$URL  = plugin_dir_url( __FILE__ );
		self::$FILE = __FILE__;
	}

	/**
	 * Include required files
	 */
	private function includes() {
	}


	/**
	 * Initialize the plugin.
	 */
	public static function init() {
		PUM_STP_Triggers::init();
		PUM_STP_Site::init();
		PUM_STP_Admin::init();
		PUM_STP_Upgrades::init();
		PUM_STP_Shortcode_ScrollTrigger::init();
	}
}

/**
 * Register this extensions autoload parameters to the pum_autoloaders array.
 *
 * @param array $autoloaders
 *
 * @return array
 */
function pum_stp_autoloader( $autoloaders = array() ) {
	return array_merge( $autoloaders, array(
		array(
			'prefix' => 'PUM_STP_',
			'dir'    => dirname( __FILE__ ) . '/classes/',
		),
	) );
}

add_filter( 'pum_autoloaders', 'pum_stp_autoloader' );

/**
 * Get the ball rolling.
 */
function pum_stp_init() {
	if ( ! class_exists( 'PUM_Extension_Activator' ) ) {
		require_once 'includes/pum-sdk/class-pum-extension-activator.php';
	}

	$activator = new PUM_Extension_Activator( 'PUM_STP' );
	$activator->run();
}

add_action( 'plugins_loaded', 'pum_stp_init', 11 );

if ( ! class_exists( 'PUM_STP_Activator' ) ) {
	require_once 'classes/Activator.php';
}
register_activation_hook( __FILE__, 'PUM_STP_Activator::activate' );

if ( ! class_exists( 'PUM_STP_Deactivator' ) ) {
	require_once 'classes/Deactivator.php';
}
register_deactivation_hook( __FILE__, 'PUM_STP_Deactivator::deactivate' );

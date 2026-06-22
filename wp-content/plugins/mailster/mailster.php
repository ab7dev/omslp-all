<?php
/*
Plugin Name: Mailster - Email Newsletter Plugin for WordPress (Premium)
Plugin URI: https://mailster.co
Description: Send Beautiful Email Newsletters in WordPress.
Version: 4.1.17
Update URI: https://api.freemius.com
Author: EverPress
Author URI: https://everpress.co
Text Domain: mailster
License: GPLv2 or later
*/

if ( defined( 'MAILSTER_VERSION' ) || ! defined( 'ABSPATH' ) ) {
	return;
}

define( 'MAILSTER_VERSION', '4.1.17' );
define( 'MAILSTER_BUILT', 1774268823 );
define( 'MAILSTER_ENVATO', false );
define( 'MAILSTER_DBVERSION', 20230517 );
define( 'MAILSTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAILSTER_URI', plugin_dir_url( __FILE__ ) );
define( 'MAILSTER_FILE', __FILE__ );
define( 'MAILSTER_SLUG', basename( MAILSTER_DIR ) . '/' . basename( __FILE__ ) );

$upload_folder = wp_upload_dir();

if ( ! defined( 'MAILSTER_UPLOAD_DIR' ) ) {
	define( 'MAILSTER_UPLOAD_DIR', $upload_folder['basedir'] . '/mailster' );
}
if ( ! defined( 'MAILSTER_UPLOAD_URI' ) ) {
	define( 'MAILSTER_UPLOAD_URI', $upload_folder['baseurl'] . '/mailster' );
}

require_once MAILSTER_DIR . 'vendor/autoload.php';
require_once MAILSTER_DIR . 'includes/check.php';
require_once MAILSTER_DIR . 'includes/functions.php';
require_once MAILSTER_DIR . 'includes/wp_mail.php';
require_once MAILSTER_DIR . 'includes/freemius.php';
require_once MAILSTER_DIR . 'includes/deprecated.php';
require_once MAILSTER_DIR . 'includes/3rdparty.php';
require_once MAILSTER_DIR . 'classes/mailster.class.php';

add_action( 'plugins_loaded', array( 'Mailster', 'get_instance' ), 1 );

register_activation_hook( MAILSTER_FILE, 'mailster_on_activate' );
register_deactivation_hook( MAILSTER_FILE, 'mailster_on_deactivate' );

<?php
/**
 * Plugin Name: OMS Custom Modules
 * Plugin URI: https://www.openmusicschool.de
 * Description: OMS Custom modules for the Beaver Builder Plugin.
 * Version: 1.1
 * Author: André Brückner
 * Author URI: https://tichypress.de
 */
define( 'OMS_MODULES_DIR', plugin_dir_path( __FILE__ ) );
define( 'OMS_MODULES_URL', plugins_url( '/', __FILE__ ) );

function oms_load_module_examples() {
  if ( class_exists( 'FLBuilder' ) ) {
      require_once 'oms-newsletter/oms-newsletter.php';
      require_once 'oms-member-button/oms-member-button.php';
  }
}
add_action( 'init', 'oms_load_module_examples' );
<?php
define( 'FL_BUILDER_VERSION_CONTROL_PLUGINS_DIR', FL_BUILDER_DIR . 'extensions/fl-builder-version-control/' );
define( 'FL_BUILDER_VERSION_CONTROL_PLUGINS_URL', FLBuilder::plugin_url() . 'extensions/fl-builder-version-control/' );
require_once 'classes/class-fl-builder-version-control.php';
if ( get_transient( 'fl_debug_mode' ) ) {
	new FLBuilderVersionControl();
}

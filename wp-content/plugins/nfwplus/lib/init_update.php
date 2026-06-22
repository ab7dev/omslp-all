<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n- / sa
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

if (! empty($nfw_options['engine_version']) && version_compare($nfw_options['engine_version'], NFW_ENGINE_VERSION, '<') ) {

	// Starting from v3.8.2 and its new backup auto-restore feature,
	// we must prevent any potential race condition here:
	if ( get_transient( 'nfw_version_update' ) === false ) {
		set_transient( 'nfw_version_update', NFW_ENGINE_VERSION, 60 );

		// v4.3.4 update ---------------------------------------------------
		if ( version_compare( $nfw_options['engine_version'], '4.3.4', '<' ) ) {
			$nfw_options['a_25'] = 0;
		}
		// ---------------------------------------------------------------

		// Adjust current version :
		$nfw_options['engine_version'] = NFW_ENGINE_VERSION;

		// Update options:
		nfw_update_option( 'nfw_options', $nfw_options);

		// Update MU plugin if needed
		nfw_enable_wpwaf();
	}
	// ------------------------------------------------------------------
}

// =====================================================================
// EOF

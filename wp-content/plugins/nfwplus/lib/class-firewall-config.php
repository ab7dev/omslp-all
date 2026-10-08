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

if ( class_exists('NinjaFirewall_rules') ) {
	return;
}


class NinjaFirewall_rules {

	static public function get( $mode ) {

		global $nfw_;

		if ( $mode != 'nfw_rules') {
			$mode = 'nfw_options';
		}

		/**
		 * WordPress WAF mode: use the WP API.
		 */
		if ( isset( $nfw_['wp_waf'] ) && $nfw_['wp_waf'] == 2 ) {
			if ( is_multisite() ) {
				$nfw_[ $mode ] = get_site_option( $mode );
			} else {
				$nfw_[ $mode ] = get_option( $mode );
			}
			return true;
		}

		/**
		 * Shared memory segment.
		 */
		if (! empty( $nfw_['shm_id'] ) ) {
			if ( $mode == 'nfw_options') {
				$nfw_['nfw_options'] = @ unserialize( $nfw_['shmop_options'], ['allowed_classes' => false ] );
			// Error.
				if (! isset( $nfw_['nfw_options']['enabled'] ) ) {
					$nfw_['shm_id'] = 0;
				}
			} else {
				$nfw_['nfw_rules'] = @ unserialize( $nfw_['shmop_rules'], ['allowed_classes' => false ] );
				// Error.
				if (! isset( $nfw_['nfw_rules']['1'] ) ) {
					$nfw_['shm_id'] = 0;
				}
			}
		}
		/**
		 * DB.
		 */
		if ( empty( $nfw_['shm_id'] ) && ! empty( $nfw_['mysqli'] ) ) {

			$table_prefix = $nfw_['mysqli']->real_escape_string( $nfw_['table_prefix'] );

			/***
			 * Rules.
			 */
			if ( $mode == 'nfw_rules' ) {

				if (! $nfw_['result'] = @ $nfw_['mysqli']->query(
					"SELECT * FROM `{$table_prefix}options` WHERE `option_name` = 'nfw_rules'"
				) ) {

					return 7;
				}

				if (! $nfw_['rules'] = @ $nfw_['result']->fetch_object() ) {
					return 8;
				}

				if (! $nfw_['nfw_rules'] = @ unserialize(
					$nfw_['rules']->option_value, ['allowed_classes' => false ]
				) ) {

					return 12;
				}
			/*
			 * Options.
			 */
			} else {
				/**
				 * Since PHP 8.1, MySQLi extension throws an Exception on errors
				 */
				try {
					$nfw_['result'] = @ $nfw_['mysqli']->query(
						"SELECT * FROM `{$table_prefix}options` WHERE `option_name` = 'nfw_options'"
					);
				}
				catch ( Exception $e ) {
					/**
					 * Maybe this is an old multisite install where the main site
					 * options table is named 'wp_1_options' instead of 'wp_options'
					 */
					try {
						$nfw_['result'] = @ $nfw_['mysqli']->query(
							"SELECT * FROM `{$table_prefix}1_options` WHERE `option_name` = 'nfw_options'"
						);
					}
					catch ( Exception $e ) {
						return 5;
					}
					/**
					 * Change the table prefix to match 'wp_1_options'
					 */
					$nfw_['table_prefix'] = "{$nfw_['table_prefix']}1_";
				}

				if (! $nfw_['options'] = @ $nfw_['result']->fetch_object() ) {
					return 6;
				}

				if (! $nfw_['nfw_options'] = @ unserialize(
					$nfw_['options']->option_value, ['allowed_classes' => false ]
				) ) {

					return 11;
				}
			}
		}

		/**
		 * Make sure we have something or return an error.
		 */
		if ( $mode == 'nfw_rules' && ! isset( $nfw_['nfw_rules']['1'] ) ) {
			return 16;

		} elseif ( $mode == 'nfw_options' && ! isset( $nfw_['nfw_options']['enabled'] ) ) {
			return 15;
		}

		/**
		 * All good.
		 */
		return true;
	}

}
// =====================================================================
// EOF

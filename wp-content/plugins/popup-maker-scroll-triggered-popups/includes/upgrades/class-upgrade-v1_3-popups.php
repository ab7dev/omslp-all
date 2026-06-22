<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch processor for migrating existing popups to new data structure.
 *
 * @since 1.3.0
 *
 * @see   PUM_Abstract_Upgrade_Popups
 */
class PUM_STP_Upgrade_v1_3_Popups extends PUM_Abstract_Upgrade_Popups {

	/**
	 * Batch process ID.
	 *
	 * @var    string
	 */
	public $batch_id = 'stp-v1_3-popups';

	/**
	 * Only load popups with specific meta keys.r
	 *
	 * @return array
	 */
	public function custom_query_args() {
		return array(
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'pum_stp_data_ver',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'pum_stp_data_ver',
					'value'   => '3',
					'compare' => '<'
				),

			),
		);
	}

	/**
	 * Process needed upgrades on each popup.
	 *
	 * @param int $popup_id
	 */
	public function process_popup( $popup_id = 0 ) {
		$popup = pum_get_popup( $popup_id );

		if ( ! $popup->has_trigger( 'scroll' ) ) {
			return;
		}

		$settings = $popup->get_settings();

		foreach( $settings['triggers'] as $key => $trigger ) {
			if ( $trigger['type'] != 'scroll' ) {
				continue;
			}

			$trigger_settings = array(
				'trigger_type' => 'distance',
				'distance' => '75%',
				'element_point' => 'e_top-s_bottom',
				'element_type' => 'shortcode',
				'element_selector' => '',
				'close_on_up' => false,
				'cookie_name' => array(),
			);

			switch( $trigger['settings']['trigger'] ) {
				case 'distance':
					$trigger_settings['trigger_type'] ='distance';
					$trigger_settings['distance'] = $trigger['settings']['distance'] . $trigger['settings']['unit'];
					break;

				case 'element':
					$trigger_settings['trigger_type'] ='element';
					$trigger_settings['element_type'] ='css_selector';
					$trigger_settings['element_selector'] = $trigger['settings']['trigger_element'];
					$trigger_settings['element_point'] = $trigger['settings']['trigger_point'] == 'bottom' ? 'e_top-s_bottom' : 'e_top-s_top';
					break;

				case 'manual':
					$trigger_settings['trigger_type'] ='element';
					$trigger_settings['element_type'] ='shortcode';
					$trigger_settings['element_point'] = $trigger['settings']['trigger_point'] == 'bottom' ? 'e_top-s_bottom' : 'e_top-s_top';
					break;
			}

			if ( ! empty( $trigger['settings']['cookie_name'] ) ) {
				$trigger_settings['cookie_name'] = $trigger['settings']['cookie_name'];
			} elseif( ! empty( $trigger['settings']['cookie']['name'] ) ) {
				$trigger_settings['cookie_name'] = $trigger['settings']['cookie']['name'];
			}

			$trigger_settings['close_on_up'] = isset( $trigger['settings']['close_on_up'] ) && $trigger['settings']['close_on_up'];

			$settings['triggers'][ $key ]['settings'] = $trigger_settings;
		}

		$popup->update_settings( $settings );

		// Update stp data version for this popup.
		$popup->update_meta( 'pum_stp_data_ver', 3 );
	}


	/**
	 *
	 */
	public function finish() {
	}
}

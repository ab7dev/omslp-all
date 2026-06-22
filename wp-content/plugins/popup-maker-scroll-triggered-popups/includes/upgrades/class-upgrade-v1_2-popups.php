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
 * @since 1.2.0
 *
 * @see   PUM_Abstract_Upgrade_Popups
 */
class PUM_STP_Upgrade_v1_2_Popups extends PUM_Abstract_Upgrade_Popups {

	/**
	 * Batch process ID.
	 *
	 * @var    string
	 */
	public $batch_id = 'stp-v1_2-popups';

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
					'key'     => 'popup_scroll_triggered_enabled',
					'compare' => 'EXISTS',
				),
			),
		);
	}

	/**
	 * Strips out prefixes.
	 *
	 * @param PUM_Model_Popup $popup
	 *
	 * @return array
	 */
	public function get_old_meta( $popup ) {
		$defaults = array(
			'enabled'         => null,
			'trigger'         => 'distance',
			'trigger_point'   => '',
			'distance'        => 75,
			'unit'            => '%',
			'trigger_element' => '',
			'close_on_up'     => null,
			'cookie_trigger'  => 'close',
			'cookie_time'     => '1 month',
			'cookie_path'     => '/',
			'cookie_key'      => '',
			'defaults_set'    => true,
		);

		$data = array();

		foreach ( $defaults as $key => $value ) {
			$old_value    = $popup->get_meta( 'popup_scroll_triggered_' . $key );
			$data[ $key ] = ! empty( $old_value ) ? $old_value : $value;
		}

		return $data;
	}

	/**
	 * @param $content
	 *
	 * @return array
	 */
	public function get_shortcodes_from_content( $content ) {
		$pattern    = get_shortcode_regex();
		$shortcodes = array();
		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches ) ) {
			foreach ( $matches[0] as $key => $value ) {
				$shortcodes[ $key ] = array(
					'full_text' => $value,
					'tag'       => $matches[2][ $key ],
					'atts'      => shortcode_parse_atts( $matches[3][ $key ] ),
					'content'   => $matches[5][ $key ],
				);

				if ( ! empty( $shortcodes[ $key ]['atts'] ) ) {
					foreach ( $shortcodes[ $key ]['atts'] as $attr_name => $attr_value ) {
						// Filter numeric keys as they are valueless/truthy attributes.
						if ( is_numeric( $attr_name ) ) {
							$shortcodes[ $key ]['atts'][ $attr_value ] = true;
							unset( $shortcodes[ $key ]['atts'] );
						}
					}
				}
			}
		}

		return $shortcodes;
	}

	/**
	 * Process needed upgrades on each popup.
	 *
	 * @param int $popup_id
	 */
	public function process_popup( $popup_id = 0 ) {

		$popup = pum_get_popup( $popup_id );

		$stp = $this->get_old_meta( $popup );

		if ( ! $stp || empty( $stp['enabled'] ) || ! $stp['enabled'] ) {
			return;
		}

		$settings = $popup->get_settings();

		// Set the new cookie name.
		$cookie_name = 'popmake-scroll-triggered-' . $popup->ID . ( ! empty( $stp['cookie_key'] ) ? '-' . $stp['cookie_key'] : '' );

		// If cookie trigger not disabled create a new cookie and add it to the auto open trigger.
		if ( $stp['cookie_trigger'] != 'disabled' ) {

			// Set the event based on the original option.
			switch ( $stp['cookie_trigger'] ) {
				case 'close':
					$event = 'on_popup_close';
					break;
				case 'open':
					$event = 'on_popup_close';
					break;
				default:
					$event = $stp['cookie_trigger'];
					break;
			}

			// Add the new cookie to the cookies array.
			$settings['cookies'][] = array(
				'event'    => $event,
				'settings' => array(
					'name'    => $cookie_name,
					'key'     => '',
					'time'    => $stp['cookie_time'],
					'path'    => isset( $stp['cookie_path'] ) ? 1 : 0,
					'session' => isset( $stp['session_cookie'] ) ? 1 : 0,
				),
			);
		}

		$settings['triggers'][] = array(
			'type'     => 'scroll',
			'settings' => array(
				'trigger'         => ! empty( $stp['trigger'] ) ? $stp['unit'] : 'trigger',
				'trigger_point'   => ! empty( $stp['trigger_point'] ) ? $stp['trigger_point'] : '',
				'distance'        => ! empty( $stp['distance'] ) ? absint( $stp['distance'] ) : 75,
				'unit'            => ! empty( $stp['unit'] ) ? $stp['unit'] : '%',
				'trigger_element' => ! empty( $stp['trigger_element'] ) ? $stp['trigger_element'] : '',
				'close_on_up'     => ! empty( $stp['close_on_up'] ) ? $stp['close_on_up'] : null,
				'cookie_name'     => $stp['cookie_trigger'] != 'disabled' ? array( $cookie_name ) : array(),
			),
		);

		$popup->update_settings( $settings );
		$this->clean_up_old_meta( $popup_id );
	}

	/**
	 * @param int $popup_id
	 */
	public function clean_up_old_meta( $popup_id = 0 ) {
		global $wpdb;

		$meta_keys = implode( "','", array(
			'popup_scroll_triggered',
			'popup_scroll_triggered_enabled',
			'popup_scroll_triggered_trigger',
			'popup_scroll_triggered_trigger_point',
			'popup_scroll_triggered_distance',
			'popup_scroll_triggered_unit',
			'popup_scroll_triggered_trigger_element',
			'popup_scroll_triggered_close_on_up',
			'popup_scroll_triggered_cookie_trigger',
			'popup_scroll_triggered_cookie_time',
			'popup_scroll_triggered_cookie_path',
			'popup_scroll_triggered_cookie_key',
			'popup_scroll_triggered_defaults_set',
		) );

		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id = " . (int) $popup_id . " AND meta_key IN('$meta_keys');" );
	}


	/**
	 *
	 */
	public function finish() {
	}
}

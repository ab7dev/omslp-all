<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_STP_Admin
 */
class PUM_STP_Admin {

	/**
	 *
	 */
	public static function init() {
		add_action( 'pum_save_popup', array( __CLASS__, 'save_popup' ) );
	}

	/**
	 * @param int $popup_id
	 */
	public static function save_popup( $popup_id ) {
		$popup = pum_get_popup( $popup_id );

		if ( $popup->get_meta( 'pum_stp_data_ver' ) === false ) {
			$popup->update_meta( 'pum_stp_data_ver', PUM_STP::$DB_VER );
		}
	}

	/**
	 * @param int $popup_id
	 */
	public static function enqueue_popup_assets( $popup_id = 0 ) {
		$popup = pum_get_popup( $popup_id );

		if ( ! pum_is_popup( $popup ) || ! wp_script_is( 'pum-stp', 'registered' ) ) {
			return;
		}

		if ( $popup->has_trigger( 'scroll' ) || $popup->has_trigger( 'scroll_return' ) ) {
			wp_enqueue_script( 'pum-stp' );
		}
	}

}

<?php
/*
Plugin Name: Mailster - OMS Customizations
Plugin URI: https://www.openmusicschool.de/
Description: Customizations for OpenMusicSchool Mailster
Version: 1.1
Author: André Brückner
Author URI: https://tichypress.de
*/

add_filter( 'cron_schedules', 'oms_custom_schedule', 10, 1 );
add_action( 'wp', 'oms_scheduled_event');
//add_action( 'oms_mailster_subscriptions_event', 'oms_change_new_member_subscription' );
add_action( 'oms_mailster_change_new_member_subscription', 'oms_change_new_member_subscription' );
add_action( 'oms_mailster_trompete_inactive', 'oms_trompete_move_to_inactive' );

/**
 * Write proper to debug.log with function write_log_mailster
 */

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
   if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ), 3, "/var/customers/webs/omsadmin/www.openmusicschool.de/lp/wp-content/debug-mailster.log" );
      } else {
         error_log( $log , 3, "/var/customers/webs/omsadmin/www.openmusicschool.de/lp/wp-content/debug-mailster.log"  );
      }
   }
}

/**
 * Set event time.
 *
 * @param array $schedules all schedules list.
 * @return array $schedules return event schedules.
 */
function oms_custom_schedule( $schedules ) {
    $time                                 = 60; // seconds
    $schedules['oms_set_crone_time'] = array(
        'interval' => $time,
        'display'  => __( 'OMS Every 1 Minutes', 'oms' ),
    );
    return $schedules;
}

/**
 * Schedule event.
 *
 * @return void
 */
function oms_scheduled_event() {
    // Schedule an action if it's not already scheduled.
    /*if ( ! wp_next_scheduled( 'oms_mailster_subscriptions_event' ) ) {
        wp_schedule_event( time(), 'oms_set_crone_time', 'oms_mailster_subscriptions_event' );
    }*/
    if ( ! wp_next_scheduled( 'oms_mailster_change_new_member_subscription' ) ) {
        wp_schedule_event( time(), 'oms_set_crone_time', 'oms_mailster_change_new_member_subscription' );
    }    
    if ( ! wp_next_scheduled( 'oms_mailster_trompete_inactive' ) ) {
        wp_schedule_event( time(), 'monthly', 'oms_mailster_trompete_inactive' );
    }
}


/**
 * Unsubscribe new members from mailster sales lists
 * TEST - handling instrument_ID
 *
 * @return void
 */
function oms_change_new_member_subscription() {
    //add_action( 'mailster', function(){
    if( function_exists( 'mailster' ) ){

        // Get all lists and create array of list_ids
        $lists = mailster( 'lists' )->get();
        $list_ids = array_map( 'intval', wp_list_pluck( $lists, 'ID' ) );
		//write_log_mailster("MAILSTER_UNSUB START ###################\n");
		//write_log_mailster("MAILSTER_UNSUB LIST-IDs ALL:\n ");
		//write_log_mailster($list_ids);

		// define exclusion lists for unassign at customer status switch
		// if member is already on one of these lists, don't unassign
		$not_list = array(31,47,60,30,52,53,54,55,56,57,58,59,63); // Kunden ID=31, Kunden Gutschein ID=47, Kunden Test ID=60
		$not_list = array_map( 'intval', $not_list ); 
		//write_log_mailster("MAILSTER_UNSUB LIST-IDs NOT:\n ");
		//write_log_mailster($not_list);

		// Remove exclusion lists as not_list is no longer a parameter in unassign_lists
		$list_ids_unassign = array();
		$list_ids_unassign = array_diff($list_ids, $not_list);
		//write_log_mailster("MAILSTER LIST-IDs UNASSIGN:\n ");
		//write_log_mailster($list_ids_unassign);

        // Get email addresses
	    //$dest = ABSPATH . 'wp-content/mailster-email-test.log';
	    $dest = ABSPATH . 'wp-content/mailster-added-order-email-instrument.log';
	    //$dest = ABSPATH . 'wp-content/mailster-added-order-email-instrument-sorted.log';
	    $members_data = file( $dest, FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);

	    // unsubscribe, subscribe for every email
	    foreach($members_data as $member_data) {

	    	list($email, $instrument) = explode(",", $member_data);
	        //mailster('subscribers')->unsubscribe_by_mail( $email);
	        //write_log_mailster("MAILSTER_UNSUB ON SALE EMAIL " . $email);
	        //write_log_mailster("MAILSTER_UNSUB ON SALE INSTRUMENT " . $instrument);
	    
	        if ( $subscriber = mailster( 'subscribers' )->get_by_mail( $email ) ) {
				$subscriber_id = $subscriber->ID;
				//write_log_mailster("MAILSTER_UNSUB SUBSCRIBER_ID get_by_mail: ".$subscriber_id);
			} else {
				$entry = [];
				$entry['email'] = $email;
				$subscriber_id = mailster( 'subscribers' )->add(
					$entry, 
					false, // overwrite
					null, // merge
					false // subscriber_notification
				);

				if ( is_wp_error( $subscriber_id ) ) {
					write_log_mailster(
						"MAILSTER_UNSUB add FAILED: " . 
						$subscriber_id->get_error_code() . " - " . 
						$subscriber_id->get_error_message()
					);
					return;
				} else {
					write_log_mailster("MAILSTER_UNSUB add: " . $subscriber_id . $email);
				}
			}

			// check if subscriber already moved to customer lists
			$lists_cust = mailster('subscribers')->get_lists( $subscriber_id );
			$subscriber_list_ids = wp_list_pluck( $lists_cust, 'ID' );
			$subscriber_list_ids = array_map( 'intval', $subscriber_list_ids );
			$intersection = array_intersect( $subscriber_list_ids, $not_list );
			//write_log_mailster("MAILSTER_UNSUB LIST-IDs INTERSECT :\n ");
			//write_log_mailster($intersection);

			
			if ( ! empty( $list_ids_unassign ) && ! is_wp_error( $subscriber_id ) && empty( $intersection ) ) {
				mailster( 'subscribers' )->unassign_lists( $subscriber_id, $list_ids_unassign );
        	 	//write_log_mailster("MAILSTER_UNSUB SUBSCRIBER_ID UNASSIGN: ".$subscriber_id);
				write_log_mailster("MAILSTER_UNSUB LIST-IDs UNASSIGN:\n ");
				write_log_mailster($list_ids_unassign);
			} else {
				write_log_mailster("MAILSTER_UNSUB NO UNASSIGN FOR: ".$subscriber_id);
			}


			// before split of WP instances: assign had been done by plugin Mailster for WooCommerce
			// Kunden ID = 31
			// Kunden Gutschein ID = 47
			// Kunden Test ID = 60

			switch ($instrument) {
				case 2:
					$assign_list = array(31,55); // Klavier
					break;
				case 3:
					$assign_list = array(31,52); // Gesang
					break;
				case 4:
					$assign_list = array(31,57); // Keyboard
					break;
				case 5:
					$assign_list = array(31,54); // Akustische Gitarre
					break;
				case 6:
					$assign_list = array(31,53); // E-Gitarre
					break;
				case 7:
					$assign_list = array(31,58); // Ukulele
					break;
				case 8:
					$assign_list = array(31,59); // Schlagzeug
					break;
				case 9:
					$assign_list = array(31,56); // E-Bass
					break;
				case 10:
					$assign_list = array(31,63); // Alle Instrumente
					break;
				case 11:
					$assign_list = array(47,63); // Gutschein + Alle Instrumente
					break;
				default:
					$assign_list = array();
					break;
			}

			if ( ! empty( $assign_list ) && ! is_wp_error( $subscriber_id ) && empty( $intersection ) ) {
				mailster( 'subscribers' )->assign_lists( $subscriber_id, $assign_list, false, true);
        	    //write_log_mailster('MAILSTER_UNSUB SUBSCRIBER_ID ASSIGN: '.$subscriber_id);
        	    write_log_mailster("MAILSTER_UNSUB ASSIGN_LIST ASSIGN:\n");
        	    write_log_mailster($assign_list);
			} else {
				write_log_mailster("MAILSTER_UNSUB NO ASSIGN FOR: ".$subscriber_id);
			}
	    }
	}
}

/**
 * Trigger Mailster automation / workflow by executing some code.
 * Automation "Move to inactive - Trompete" ID 244626
 */
function oms_trompete_move_to_inactive() {
	// Trigger for all subscribers
	//mailster_trigger( 'oms_hook_trompete_to_inactive' );
	mailster_trigger( 'oms_hook_trompete_to_inactive', array( 140809, 140808, 140800, 135419, 132301, 132293, 132291, 132290 ) );
}

<?php
/*
Plugin Name: Mailster - OMS Customizations
Plugin URI: https://www.openmusicschool.de/
Description: Customizations for OpenMusicSchool Mailster
Version: 1.2
Author: André Brückner
Author URI: https://tichypress.de
*/

/**
 * Unsubscribe new members of sales newsletter lists in Mailster
 * 1. www: write new members e-mail to logfile
 * 2. lp: read e-mail from logfile and trigger action
 * 3. lp: run mailster unsubscribe/subscribe on mailster lists
 */

if ( ! defined( 'OMS_MAILSTER_ERROR_LOG' ) ) {
	define( 'OMS_MAILSTER_ERROR_LOG', ABSPATH . 'wp-content/mailster-change-subscription-error.log' );
}
if ( ! defined( 'OMS_MAILSTER_ERROR_RECIPIENT' ) ) {
	define( 'OMS_MAILSTER_ERROR_RECIPIENT', 'webmaster@tichypress.net' );
}

add_filter( 'cron_schedules', 'oms_custom_schedule', 10, 1 );
add_action( 'wp', 'oms_scheduled_event');
//add_action( 'oms_mailster_subscriptions_event', 'oms_change_new_member_subscription' );
add_action( 'oms_mailster_change_new_member_subscription', 'oms_change_new_member_subscription' );
add_action( 'oms_mailster_trompete_inactive', 'oms_trompete_move_to_inactive' );

/**
 * Write proper to debug.log with function write_log_mailster
 */

if ( ! function_exists('write_log_mailster')) {
   function write_log_mailster ( $log )  {
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
 * Collect an error for later logging / reporting.
 *
 * @param array      $errors        Error collection (by reference).
 * @param string     $email         E-mail address concerned ('-' if unknown).
 * @param int|string $subscriber_id Mailster subscriber ID ('-' if unknown).
 * @param int|string $line          Line number in the order logfile ('-' if not applicable).
 * @param string     $message       Error message.
 * @return void
 */
function oms_mailster_add_error( array &$errors, $email, $subscriber_id, $line, $message ) {
	$errors[] = array(
		'time'          => wp_date( 'Y-m-d H:i:s' ),
		'email'         => (string) $email,
		'subscriber_id' => (string) $subscriber_id,
		'line'          => (string) $line,
		'message'       => (string) $message,
	);
}

/**
 * Write new errors to the error logfile and send them by e-mail.
 *
 * The order logfile is re-read every minute until it is rotated at midnight,
 * so the same error would occur up to 1440 times a day. Each error
 * (e-mail + message) is therefore logged and mailed only once per day.
 *
 * @param array $errors Collected errors.
 * @return void
 */
function oms_mailster_report_errors( array $errors ) {
	if ( empty( $errors ) ) {
		return;
	}

	$transient = 'oms_mailster_reported_' . wp_date( 'Ymd' );
	$reported  = get_transient( $transient );
	if ( ! is_array( $reported ) ) {
		$reported = array();
	}

	$new_errors = array();
	foreach ( $errors as $error ) {
		$hash = md5( $error['email'] . '|' . $error['message'] );
		if ( isset( $reported[ $hash ] ) ) {
			continue;
		}
		$reported[ $hash ] = 1;
		$new_errors[]      = $error;
	}

	if ( empty( $new_errors ) ) {
		return;
	}

	set_transient( $transient, $reported, 2 * DAY_IN_SECONDS );

	$lines = array();
	foreach ( $new_errors as $error ) {
		$lines[] = sprintf(
			'[%s] E-Mail: %s | Subscriber-ID: %s | Zeile: %s | Fehler: %s',
			$error['time'],
			$error['email'],
			$error['subscriber_id'],
			$error['line'],
			$error['message']
		);
	}

	error_log( implode( "\n", $lines ) . "\n", 3, OMS_MAILSTER_ERROR_LOG );

	$subject = sprintf( '[OMSLP] Mailster Listenwechsel: %d Fehler', count( $new_errors ) );
	$body    = "Beim Listenwechsel neuer Mitglieder in Mailster (OMSLP) sind Fehler aufgetreten:\n\n"
		. implode( "\n", $lines )
		. "\n\nFehler-Logfile: " . OMS_MAILSTER_ERROR_LOG
		. "\nJeder Fehler wird pro Tag nur einmal gemeldet.";

	if ( ! wp_mail( OMS_MAILSTER_ERROR_RECIPIENT, $subject, $body ) ) {
		error_log(
			sprintf( "[%s] Fehler-Mail an %s konnte nicht versendet werden.\n", wp_date( 'Y-m-d H:i:s' ), OMS_MAILSTER_ERROR_RECIPIENT ),
			3,
			OMS_MAILSTER_ERROR_LOG
		);
	}
}


/**
 * Unsubscribe new members from mailster sales lists
 * and assign them to customer + instrument lists.
 *
 * Reads the order logfile (format per line: email,instrument_id).
 * The logfile is rotated at midnight, no cleanup needed here.
 *
 * @return void
 */
function oms_change_new_member_subscription() {
	if ( ! function_exists( 'mailster' ) ) {
		return;
	}

	$errors = array();
	$dest   = ABSPATH . 'wp-content/mailster-added-order-email-instrument.log';

	// After the midnight rotation the file may not exist yet – that's fine.
	if ( ! file_exists( $dest ) ) {
		return;
	}

	$members_data = file( $dest, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES );
	if ( false === $members_data ) {
		oms_mailster_add_error( $errors, '-', '-', '-', 'Logfile nicht lesbar: ' . $dest );
		oms_mailster_report_errors( $errors );
		return;
	}
	if ( empty( $members_data ) ) {
		return;
	}

	// Get all lists and create array of list_ids
	$lists = mailster( 'lists' )->get();
	if ( empty( $lists ) || is_wp_error( $lists ) ) {
		$msg = is_wp_error( $lists ) ? $lists->get_error_message() : 'keine Listen gefunden';
		oms_mailster_add_error( $errors, '-', '-', '-', 'Mailster-Listen konnten nicht geladen werden: ' . $msg );
		oms_mailster_report_errors( $errors );
		return;
	}
	$list_ids = array_map( 'intval', wp_list_pluck( $lists, 'ID' ) );

	// Exclusion lists: if member is already on one of these lists, it has been processed before.
	// Kunden ID=31, Kunden Gutschein ID=47, Kunden Test ID=60, Instrument-Kundenlisten 52–59, 63
	$not_list          = array( 31, 47, 60, 30, 52, 53, 54, 55, 56, 57, 58, 59, 63 );
	$list_ids_unassign = array_values( array_diff( $list_ids, $not_list ) );

	// before split of WP instances: assign had been done by plugin Mailster for WooCommerce
	$instrument_lists = array(
		2  => array( 31, 55 ), // Klavier
		3  => array( 31, 52 ), // Gesang
		4  => array( 31, 57 ), // Keyboard
		5  => array( 31, 54 ), // Akustische Gitarre
		6  => array( 31, 53 ), // E-Gitarre
		7  => array( 31, 58 ), // Ukulele
		8  => array( 31, 59 ), // Schlagzeug
		9  => array( 31, 56 ), // E-Bass
		10 => array( 31, 63 ), // Alle Instrumente
		11 => array( 47, 63 ), // Gutschein + Alle Instrumente
	);

	foreach ( $members_data as $index => $member_data ) {
		$line_no       = $index + 1;
		$email         = '-';
		$subscriber_id = '-';

		try {
			$parts = array_map( 'trim', explode( ',', $member_data ) );
			if ( count( $parts ) < 2 ) {
				oms_mailster_add_error( $errors, $member_data, '-', $line_no, 'Zeile hat ungültiges Format (erwartet: email,instrument)' );
				continue;
			}

			$email      = sanitize_email( $parts[0] );
			$instrument = (int) $parts[1];

			if ( ! is_email( $email ) ) {
				oms_mailster_add_error( $errors, $parts[0], '-', $line_no, 'Ungültige E-Mail-Adresse' );
				continue;
			}

			// Get or create subscriber
			$subscriber = mailster( 'subscribers' )->get_by_mail( $email );
			if ( $subscriber ) {
				$subscriber_id = (int) $subscriber->ID;
			} else {
				$result = mailster( 'subscribers' )->add(
					array( 'email' => $email ),
					false, // overwrite
					null,  // merge
					false  // subscriber_notification
				);

				if ( is_wp_error( $result ) ) {
					oms_mailster_add_error( $errors, $email, '-', $line_no, 'Subscriber anlegen fehlgeschlagen: ' . $result->get_error_code() . ' - ' . $result->get_error_message() );
					continue;
				}
				if ( empty( $result ) ) {
					oms_mailster_add_error( $errors, $email, '-', $line_no, 'Subscriber anlegen fehlgeschlagen: keine ID zurückgegeben' );
					continue;
				}

				$subscriber_id = (int) $result;
				write_log_mailster( 'MAILSTER_UNSUB add: ' . $subscriber_id . ' ' . $email . "\n" );
			}

			// Check if subscriber already moved to customer lists (= processed in an earlier run)
			$lists_cust = mailster( 'subscribers' )->get_lists( $subscriber_id );
			if ( is_wp_error( $lists_cust ) ) {
				oms_mailster_add_error( $errors, $email, $subscriber_id, $line_no, 'Listen des Subscribers nicht lesbar: ' . $lists_cust->get_error_message() );
				continue;
			}
			$subscriber_list_ids = array_map( 'intval', wp_list_pluck( (array) $lists_cust, 'ID' ) );

			if ( array_intersect( $subscriber_list_ids, $not_list ) ) {
				continue; // already a customer – nothing to do
			}

			// Unknown instrument: don't touch the subscriber, otherwise sales lists would be
			// removed without assigning customer lists.
			if ( ! isset( $instrument_lists[ $instrument ] ) ) {
				oms_mailster_add_error( $errors, $email, $subscriber_id, $line_no, 'Unbekannte Instrument-ID: ' . $parts[1] . ' – keine Listen geändert' );
				continue;
			}
			$assign_list = $instrument_lists[ $instrument ];

			// Unassign sales lists
			if ( ! empty( $list_ids_unassign ) ) {
				$unassigned = mailster( 'subscribers' )->unassign_lists( $subscriber_id, $list_ids_unassign );
				if ( false === $unassigned || is_wp_error( $unassigned ) ) {
					oms_mailster_add_error( $errors, $email, $subscriber_id, $line_no, 'Austragen aus Verkaufslisten fehlgeschlagen' );
					continue;
				}
			}

			// Assign customer + instrument lists
			$assigned = mailster( 'subscribers' )->assign_lists( $subscriber_id, $assign_list, false, true );
			if ( false === $assigned || is_wp_error( $assigned ) ) {
				oms_mailster_add_error( $errors, $email, $subscriber_id, $line_no, 'Eintragen in Kundenlisten (' . implode( ',', $assign_list ) . ') fehlgeschlagen – Subscriber ist aus Verkaufslisten ausgetragen, aber keiner Kundenliste zugeordnet!' );
				continue;
			}

			write_log_mailster( 'MAILSTER_UNSUB ASSIGN: ' . $subscriber_id . ' ' . $email . ' -> ' . implode( ',', $assign_list ) . "\n" );

		} catch ( \Throwable $e ) {
			oms_mailster_add_error( $errors, $email, $subscriber_id, $line_no, 'Exception: ' . $e->getMessage() . ' (' . basename( $e->getFile() ) . ':' . $e->getLine() . ')' );
			continue;
		}
	}

	oms_mailster_report_errors( $errors );
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
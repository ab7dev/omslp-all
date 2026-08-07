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

if ( class_exists('NinjaFirewall_emailsodium') ) {
	return;
}

class NinjaFirewall_emailsodium {

	/**
	 * Check whether Sodium is available (WordPress >=5.2 or PHP >= 7.2.0).
	 */
	public static function check_sodium() {

		$sodium = 0;

		if ( function_exists('sodium_crypto_generichash') ) {
			$sodium = 'php';
		} elseif ( file_exists( ABSPATH . WPINC . '/sodium_compat/autoload.php') ) {
			$sodium = 'wordpress';
		}
		return $sodium;
	}


	/**
	 * Generate an encrypted link for notification emails.
	 */
	public static function sodium_encrypt( $email, $expire, $which_sodium ) {

		$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

		if ( $which_sodium == 'php') {
			// PHP native functions
			$key			= sodium_crypto_generichash( AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$ciphertext	= sodium_crypto_secretbox( "$email::$expire", $nonce, $key);
			return sodium_bin2hex( $nonce . $ciphertext );

		} else {
			// WP sodium libraries
			require ABSPATH . WPINC .'/sodium_compat/autoload.php';
			$key			= \Sodium\crypto_generichash( AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$ciphertext	= \Sodium\crypto_secretbox( "$email::$expire", $nonce, $key);
			return \Sodium\bin2hex( $nonce . $ciphertext );
		}
	}


	/**
	 * Verify encrypted signature.
	 */
	public static function sodium_decrypt( $hex ) {

		// Make sure we have Sodium
		$which_sodium = self::check_sodium();
		if ( empty( $which_sodium ) ) {
			return;
		}
		// Hexadecimal input only
		if (! preg_match('/^(?:[0-9a-f]{2})+$/', $hex ) ) {
			return;
		}

		$raw			= hex2bin( $hex );
		$nonce		= substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext	= substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

		if ( $which_sodium == 'php') {
			// PHP native functions
			$key			= sodium_crypto_generichash( AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$decrypted	= sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );

		} else {
			// WP sodium libraries
			require ABSPATH . WPINC .'/sodium_compat/autoload.php';
			$key			= \Sodium\crypto_generichash( AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$decrypted	= \Sodium\crypto_secretbox_open( $ciphertext, $nonce, $key );
		}

		if ( $decrypted === false ) {
			self::removal_error();
		}

		$data = explode('::', $decrypted );
		if ( empty( $data[0] ) || empty( $data[1] ) ) {
			self::removal_error();
		}

		// Verify expiry date
		$now = time();
		if ( $data[1] < $now ) {
			// Link has expired
			wp_die(
				esc_html__('The link you followed has expired.', 'nfwplus'),
				esc_html__('Error', 'nfwplus'),
				200
			);
		}

		// Confirm deletion
		if ( empty( $_REQUEST['nfw_confirm'] ) ) {
			self::removal_confimation( $_GET['nfw_stop_notification'] );
			exit;
		}

		$new_list		= '';
		$found			= 0;
		$nfw_options	= nfw_get_option('nfw_options');
		$recipients		= explode(',', $nfw_options['alert_email'] );
		foreach( $recipients as $recipient ) {
			$recipient = trim( $recipient );
			if ( $recipient == $data[0] ) {
				// Remove that email from the list
				$found = 1;
				continue;
			}
			$new_list .= "$recipient, ";
		}

		if ( $found ) {
			// Update options
			$nfw_options['alert_email'] = trim( $new_list, ', ');
			if ( empty( $nfw_options['alert_email'] ) ) {
				$nfw_options['alert_email'] = get_option('admin_email');
			}
			nfw_update_option('nfw_options', $nfw_options );

			$subject = __('Email removal confirmation', 'nfwplus');
			NinjaFirewall_log::write(
				"WordPress: $subject",
				"User: {$data[0]}",
				NFWLOG_INFO, 0, $nfw_options, NFW_LOG_DIR .'/nfwlog'
			);
			$subject = "[NinjaFirewall] $subject";
			$message = __('Your email address was removed from the "Event Notifications" option.', 'nfwplus') . "\n\n";
			$message.= __('Blog:', 'nfwplus') .' '. home_url('/') . "\n";
			$message.= __('Email address:', 'nfwplus') .' '. "{$data[0]}\n";
			$message.= __('User IP:', 'nfwplus') .' '. NFW_REMOTE_ADDR . "\n";
			$message.= __('Date:', 'nfwplus') .' '. date_i18n('F j, Y @ H:i:s T') . "\n\n";
			/**
			 * We don't use NinjaFirewall_mail::send() because the email
			 * must be sent to the corresponding user, not the admin.
			 */
			wp_mail( $data[0], $subject, $message );
		}
	}


	/**
	 * Fatal error.
	 */
	private static function removal_error() {

		wp_die(
			esc_html__('Error, your resquest cannot be processed.', 'nfwplus'),
			esc_html__('Error', 'nfwplus'),
			200
		);
	}


	/**
	 * Email removal confirmation.
	 */
	private static function removal_confimation( $hex ) {

		$home_url		= esc_url( home_url('/') );
		$removal_url	= esc_url( home_url("/?nfw_stop_notification=$hex&nfw_confirm=1") );
		wp_die(
			esc_html__('If you want to remove your email address from the Event Notifications option, click '.
				'the button below. If the operation is successful, a confirmation email will be sent to you.',
				'nfwplus'
			).	'<p>
			<button class="button button-large button-active" style="min-width:100px;" onclick=\'location.'.
			'href="'. $removal_url .'"\'>'. esc_html__('Yes', 'nfwplus') .'</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button class="button button-large button-active" style="min-width:100px;" onclick=\'location.'.
			'href="'. $home_url .'"\'>'. esc_html__('No', 'nfwplus') .'</button>
			</p>',
			esc_html__('Email removal confirmation', 'nfwplus'),
			200
		);
	}

}
// =====================================================================
// EOL

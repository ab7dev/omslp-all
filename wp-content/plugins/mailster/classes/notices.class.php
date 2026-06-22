<?php

class MailsterNotices {

	private static $notices;

	public function __construct() {

		add_action( 'mailster_admin_notices', array( &$this, 'admin_notices' ) );
		// add_action( 'admin_notices', array( &$this, 'admin_notices' ) ); // display it on every page
		add_filter( 'admin_body_class', array( &$this, 'admin_body_class' ) );
		add_action( 'mailster_cron_notice', array( &$this, 'cron_notice' ), 10, 2 );
		add_action( 'mailster_cron', array( &$this, 'hourly_cron' ) );
	}



	public function __call( $method, $arguments ) {
		if ( ! method_exists( $this, 'do_' . $method ) ) {
			return;
		}

		return call_user_func_array( array( &$this, 'do_' . $method ), $arguments );
	}

	public function hourly_cron() {

		if ( ! get_option( 'mailster_setup' ) ) {
			return;
		}

		// messages 6 momth after setup
		$this->schedule( 'legacy_promo', '6 Month', false, 'info' );

		// $this->schedule( 'legacy_promo', time() + 60, true, 'warning' );
		// $this->schedule( 'legacy_promo', '4 days', true );
	}

	/**
	 * Schedule a notice
	 *
	 * @param string $method
	 * @param string $delay
	 * @param string $relative
	 * @return void
	 */
	public function schedule( $method, $delay, $relative = false, $type = 'info' ) {

		$timestamp = is_numeric( $delay ) ? strtotime( '@' . $delay ) : strtotime( '' . $delay );

		if ( ! $relative ) {
			$delay     = $timestamp - time();
			$timestamp = get_option( 'mailster_setup' ) + $delay;
		}

		// already in the past
		if ( $timestamp < time() ) {
			return;
		}

		// if timestamp is to much in the future stop
		if ( $timestamp - time() > HOUR_IN_SECONDS * 12 ) {
			return;
		}

		if ( ! method_exists( $this, 'do_' . $method ) ) {
			return;
		}

		wp_schedule_single_event( $timestamp, 'mailster_cron_notice', array( $method, $type ) );
	}

	/**
	 *  Run a cron notice
	 *
	 * @param mixed $method
	 * @return void
	 */
	public function cron_notice( $method, $type = 'info' ) {

		if ( ! method_exists( $this, 'do_' . $method ) ) {
			return;
		}
		$msg = call_user_func( array( &$this, 'do_' . $method ) );

		if ( $msg ) {
			mailster_notice( $msg, $type, false, 'mailster-notice-' . $method, true );
		}
	}

	public function add( $args, $type = '', $once = false, $key = null, $capability = true, $screen = null, $append = false ) {

		if ( true === $key ) {
			$capability = true;
			$key        = null;
		}

		if ( ! is_array( $args ) ) {
			$args = array(
				'text' => $args,
				'type' => in_array( $type, array( 'success', 'error', 'info', 'warning' ) ) ? $type : 'success',
				'once' => $once,
				'key'  => $key ? $key : uniqid(),
			);
		}

		if ( true === $capability ) {
			$capability = get_current_user_id();
			// no logged in user => only for admins
			if ( ! $capability ) {
				$capability = 'manage_options';
			}
		}

		$args = wp_parse_args(
			$args,
			array(
				'text'   => '',
				'type'   => 'success',
				'once'   => false,
				'key'    => uniqid(),
				'cb'     => null,
				'cap'    => $capability,
				'screen' => $screen,
			)
		);

		if ( empty( $args['key'] ) ) {
			$args['key'] = uniqid();
		}

		if ( is_numeric( $args['once'] ) && $args['once'] < 1600000000 ) {
			$args['once'] = time() + $args['once'];
		}

		self::$notices = get_option( 'mailster_notices' );
		if ( ! is_array( self::$notices ) ) {
			self::$notices = array();
		}

		if ( $append && isset( self::$notices[ $args['key'] ] ) ) {
			$args['text'] = self::$notices[ $args['key'] ]['text'] . '<br>' . $args['text'];
		}

		self::$notices[ $args['key'] ] = array(
			'text'   => $args['text'],
			'type'   => $args['type'],
			'once'   => $args['once'],
			'cb'     => $args['cb'],
			'cap'    => $args['cap'],
			'screen' => $args['screen'],
		);

		update_option( 'mailster_notices', self::$notices );
		update_option( 'mailster_notices_count', count( self::$notices ) );

		do_action( 'mailster_notice', $args['text'], $args['type'], $args['key'] );

		return $args['key'];
	}

	/**
	 * Remove a notice
	 *
	 * @param string|array $ids
	 * @return bool
	 */
	public function remove( $ids ) {

		self::$notices = get_option( 'mailster_notices', array() );

		$ids = (array) $ids;

		foreach ( $ids as $id ) {
			if ( ! isset( self::$notices[ $id ] ) ) {
				continue;
			}
			unset( self::$notices[ $id ] );
			update_option( 'mailster_notices', self::$notices );
			update_option( 'mailster_notices_count', count( self::$notices ) );
			do_action( 'mailster_remove_notice', $id );
			do_action( 'mailster_remove_notice_' . $id );

		}

		return true;
	}

	/**
	 * Adds a class to the body tag if there are notices
	 *
	 * @param string $classes
	 * @return string
	 */
	public function admin_body_class( $classes = '' ) {

		$count = get_option( 'mailster_notices_count' );
		if ( ! $count ) {
			return $classes;
		}

		self::$notices = get_option( 'mailster_notices' );

		$screens              = wp_list_pluck( self::$notices, 'screen' );
		$displayed_everywhere = array_filter( $screens, 'is_null' );
		if ( ! empty( $displayed_everywhere ) ) {
			$classes .= ' mailster-has-notices';
		}

		return $classes;
	}

	/**
	 * Display admin notices
	 *
	 * @return void
	 */
	public function admin_notices() {

		// count is faster as it's autoload 'yes'
		$count = get_option( 'mailster_notices_count' );
		if ( ! $count ) {
			return;
		}

		self::$notices = get_option( 'mailster_notices' );
		if ( ! self::$notices ) {
			return;
		}

		$successes = array();
		$errors    = array();
		$infos     = array();
		$warnings  = array();
		$dismiss   = isset( $_GET['mailster_remove_notice_all'] ) ? esc_attr( $_GET['mailster_remove_notice_all'] ) : false;

		if ( ! is_array( self::$notices ) ) {
			self::$notices = array();
		}

		if ( isset( $_GET['mailster_remove_notice'] ) && isset( self::$notices[ $_GET['mailster_remove_notice'] ] ) ) {
			unset( self::$notices[ $_GET['mailster_remove_notice'] ] );
		}

		$notices = array_reverse( self::$notices, true );

		foreach ( $notices as $id => $notice ) {

			if ( isset( $notice['cap'] ) && ! empty( $notice['cap'] ) ) {

				// specific users or admin
				if ( is_numeric( $notice['cap'] ) ) {
					if ( get_current_user_id() != $notice['cap'] && ! current_user_can( 'manage_options' ) ) {
						continue;
					}

					// certain capability
				} elseif ( ! current_user_can( $notice['cap'] ) ) {
						continue;
				}
			}
			if ( isset( $notice['screen'] ) && ! empty( $notice['screen'] ) ) {
				$screen = get_current_screen();
				if ( ! in_array( $screen->id, (array) $notice['screen'] ) ) {
					continue;
				}
			}

			$type        = esc_attr( $notice['type'] );
			$dismissable = ! $notice['once'] || is_numeric( $notice['once'] );

			$classes = array( 'hidden', 'notice', 'mailster-notice', 'notice-' . $type );
			if ( 'success' == $type ) {
				$classes[] = 'updated';
			}
			if ( 'error' == $type ) {
				$classes[] = 'error';
			}
			if ( $dismissable ) {
				$classes[] = 'mailster-notice-dismissable';
			}

			$msg = '<div data-id="' . esc_attr( $id ) . '" id="mailster-notice-' . esc_attr( $id ) . '" class="' . implode( ' ', $classes ) . '">';

			$text = ( isset( $notice['text'] ) ? $notice['text'] : '' );
			$text = isset( $notice['cb'] ) && function_exists( $notice['cb'] )
				? call_user_func( $notice['cb'], $text )
				: $text;

			if ( $text === false ) {
				continue;
			}
			if ( ! is_string( $text ) ) {
				$text = print_r( $text, true );
			}

			if ( 'error' == $type ) {
				$text = '<strong>' . $text . '</strong>';
			}

			$msg .= ( $text ? $text : '&nbsp;' );
			if ( $dismissable ) {
				$msg .= '<a class="notice-dismiss" title="' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '" href="' . add_query_arg( array( 'mailster_remove_notice' => $id ) ) . '"><span class="screen-reader-text">' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '</span></a>';

				self::$notices[ $id ]['seen'] = true;
				if ( is_numeric( $notice['once'] ) && (int) $notice['once'] - time() < 0 ) {
					unset( self::$notices[ $id ] );
					if ( isset( $notice['seen'] ) ) {
						continue;
					}
				}
			} else {
				unset( self::$notices[ $id ] );
			}

			$msg .= '</div>';

			if ( $notice['type'] == 'success' && $dismiss != 'success' ) {
				$successes[] = $msg;
			}

			if ( $notice['type'] == 'error' && $dismiss != 'error' ) {
				$errors[] = $msg;
			}

			if ( $notice['type'] == 'info' && $dismiss != 'info' ) {
				$infos[] = $msg;
			}

			if ( $notice['type'] == 'warning' && $dismiss != 'warning' ) {
				$warnings[] = $msg;
			}

			if ( 'success' == $dismiss && isset( self::$notices[ $id ] ) ) {
				unset( self::$notices[ $id ] );
			}

			if ( 'error' == $dismiss && isset( self::$notices[ $id ] ) ) {
				unset( self::$notices[ $id ] );
			}

			if ( 'info' == $dismiss && isset( self::$notices[ $id ] ) ) {
				unset( self::$notices[ $id ] );
			}

			if ( 'warning' == $dismiss && isset( self::$notices[ $id ] ) ) {
				unset( self::$notices[ $id ] );
			}
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-notice', MAILSTER_URI . 'assets/css/notice-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-notice', MAILSTER_URI . 'assets/js/notice-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		echo implode( '', $successes );
		echo implode( '', $errors );
		echo implode( '', $infos );
		echo implode( '', $warnings );

		$notices = empty( self::$notices ) ? null : (array) self::$notices;
		$count   = ! empty( $notices ) ? count( $notices ) : 0;

		update_option( 'mailster_notices', $notices );
		update_option( 'mailster_notices_count', $count );
	}

	private function between( $a, $b ) {
		return $this->after( $a ) && $this->before( $b );
	}

	private function after( $value ) {
		$setup     = get_option( 'mailster_setup' );
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		$offset    = $timestamp - time();

		return time() > $setup + $offset;
	}

	private function before( $value ) {
		$setup     = get_option( 'mailster_setup' );
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		$offset    = $timestamp - time();

		return time() < $setup + $offset;
	}

	public function on_install( $new ) {

		if ( $new ) {
			update_option( 'mailster_notices', '', false );
			update_option( 'mailster_notices_count', 0 );
		}
	}



	private function do_legacy_promo() {

		if ( mailster_freemius()->is_whitelabeled() ) {
			return;
		}

		if ( ! mailster_freemius()->is_plan( 'legacy' ) && ! mailster_freemius()->is_plan( 'legacy_plus' ) ) {
			return;
		}

		$msg  = '<h2>' . sprintf( esc_html__( 'Get Professional 4 Free*', 'mailster' ) ) . '</h2>';
		$msg .= '<p>' . esc_html__( 'Your Envato license is eglibable for a free update! Upgrade your license and get the first year for free.', 'mailster' ) . '</p>';
		$msg .= '<p>';
		$msg .= mailster_freemius_upgrade_license( 'hide_license_key=1&hide_coupon=1&hide_licenses=1&coupon=LEGACYUPGRADE100&plan_id=22867', sprintf( esc_html__( 'Upgrade to %s', 'mailster' ), 'Professional' ), 'button button-primary button-hero' );
		$msg .= mailster_freemius_upgrade_license( 'hide_license_key=1&hide_coupon=1&hide_licenses=1&coupon=LEGACYUPGRADE100&plan_id=22868', sprintf( esc_html__( 'Upgrade to %s', 'mailster' ), 'Agency' ), 'button button-secondary button-hero' );
		$msg .= ' ' . esc_html__( 'or', 'mailster' ) . ' <a href="' . esc_url( mailster_freemius()->pricing_url() . '&hide_license_key=1&hide_coupon=1&coupon=LEGACYUPGRADE100' ) . '">' . esc_html__( 'compare plans', 'mailster' ) . '</a>';
		$msg .= '</p>';
		$msg .= '<sub> * ' . esc_html__( 'Subscription fee will be charged 12 months after promo activation (cancel anytime before renewal date)', 'mailster' ) . '</sub>';

		return $msg;
	}
}

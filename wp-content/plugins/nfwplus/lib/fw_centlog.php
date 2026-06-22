<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ sa
*/

if (! isset( $nfw_['nfw_options']['enabled']) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

/* ================================================================== */
function fw_centlog() {

	global $nfw_;

	$pubkey = explode( ':', $nfw_['nfw_options']['clogs_pubkey'], 2 );

	// IP restriction ?
	if ( isset( $pubkey[1]) &&  $pubkey[1] != '*' ) {

		if ( NFW_REMOTE_ADDR != $pubkey[1] ) {

			NinjaFirewall_log::write(
				'Centralized logging: IP not allowed',
				NFW_REMOTE_ADDR,
				NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
			);
			fw_centlog_die();
		}
	}

	// Check the hash key:
	if ( empty( $pubkey[0] ) || sha1( $_POST['clogs_req'] ) !== $pubkey[0] ) {

		NinjaFirewall_log::write(
			'Centralized logging: public key rejected',
			NFW_REMOTE_ADDR,
			NFWLOG_INFO, 0, $nfw_['nfw_options'], $nfw_['log_dir']
		);
		fw_centlog_die();
	}

	// Find the log and return its content
	$cur_month = date('Y-m');
	$log_file = $nfw_['log_dir']. '/firewall_' . $cur_month . '.php';

	// No log:
	if (! file_exists( $log_file ) ) {
		exit('1:');
	}

	// Error while reading the log?
	$data = file( $log_file, FILE_SKIP_EMPTY_LINES );
	if ( $data === false ) {
		exit('2:');
	}

	// Return the log content:
	echo '0:~*~:' . base64_encode( json_encode( $data ) );
	exit;
}

/* ================================================================== */

function fw_centlog_die() {

	header('HTTP/1.1 406 Not Acceptable');
	header('Status: 406 Not Acceptable');
	exit;
}

/* ================================================================== */
// EOF

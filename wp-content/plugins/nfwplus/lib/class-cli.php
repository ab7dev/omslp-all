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

if (! defined('ABSPATH') ) {
	die('Forbidden');
}

// =====================================================================

class NinjaFirewall_CLI extends WP_CLI_Command {

	private $cmd_license = 'wp ninjafirewall license';
	private $cmd_export  = 'wp ninjafirewall export';
	private $cmd_import  = 'wp ninjafirewall import';


	/**
	 * Import NinjaFirewall's configuration from a local file.
	 */
	public function import( $args, $assoc_args ) {

		if ( empty( $assoc_args['file'] ) || strlen( $assoc_args['file'] ) < 3 ) {
			$message = __('You must enter a source file. See help.', 'nfwplus');
			WP_CLI::error( $message );
			exit;
		}

		$nfw_options = nfw_get_option('nfw_options');

		require_once __DIR__ .'/class-import-export.php';
		$res = NinjaFirewall_ImpExp::import(
			$assoc_args['file'],
			$nfw_options['lic'], $nfw_options['lic_exp']
		);
		if (! empty( $res ) ) {
			WP_CLI::error( $res );

		} else {
			WP_CLI::log( __('Configuration successfully imported.', 'nfwplus') );
		}
		exit;
	}


	/**
	 * Export NinjaFirewall's configuration to a local file.
	 */
	public function export( $args, $assoc_args ) {

		if ( empty( $assoc_args['file'] ) || strlen( $assoc_args['file'] ) < 3 ) {
			$message = __('You must enter a destination file. See help.', 'nfwplus');
			WP_CLI::error( $message );
			exit;
		}

		require_once __DIR__ .'/class-import-export.php';
		$res = NinjaFirewall_ImpExp::export( $assoc_args['file'] );
		if ( $res === false ) {
			$message = sprintf(
					/* Translators: path of the file */
				__('Cannot create destination file "%s".', 'nfwplus'),
				$assoc_args['file']
			);
			WP_CLI::error( $message );
			exit;
		}

		/* Translators: path of the file */
		WP_CLI::log( sprintf(
			__('Configuration saved to "%s".', 'nfwplus'),
			$assoc_args['file']
		) );
	}


	/**
	 * Add/change NinjaFirewall's license.
	 *
	 */
	public function license( $args, $assoc_args ) {

		_e('Enter your license: ', 'nfwplus');
		$license = trim( stream_get_line( STDIN, 255, PHP_EOL) );
		 WP_CLI::log( __('Checking license...', 'nfwplus') ."\n" );

		$_POST['lic'] = $license;
		nfw_check_license();

		// Error
		if ( defined('NFW_INVALID_LIC') ) {
			WP_CLI::error( NFW_INVALID_LIC );
		} else {
			WP_CLI::log( __('Your new license has been accepted and saved.', 'nfwplus') );
		}
		exit;
	}


	/**
	 * Display help screen and quit.
	 *
	 */
	public function help() {

		WP_CLI::log( "\nNinjaFirewall WP+ v". NFW_ENGINE_VERSION .
			" (c)". date('Y') ." NinTechNet Limited ~ https://nintechnet.com/\n\n".
			"  {$this->cmd_import}         ". __('Import the firewall configuration.', 'nfwplus') ."\n".
			"  {$this->cmd_export}         ". __('Export the firewall configuration.', 'nfwplus') ."\n".
			"  {$this->cmd_license}        ". __('Enter your license at the prompt.', 'nfwplus') ."\n\n".
			__('GLOBAL PARAMETERS', 'nfwplus') ."\n\n".
			"  --file=<path/to/file>\n".
			"      ". __('Path to the file to import or export the configuration.', 'nfwplus') ."\n\n"
		);
		exit;
	}

}

WP_CLI::add_command(
	'ninjafirewall',
	'NinjaFirewall_CLI',
	['shortdesc' => 'NinjaFirewall WP+ Edition.']
);

// =====================================================================
// EOF

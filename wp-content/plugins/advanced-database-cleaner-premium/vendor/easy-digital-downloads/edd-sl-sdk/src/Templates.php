<?php
/**
 * Templates manager.
 *
 * @package EasyDigitalDownloads\Updater
 * @since 1.0.0
 */

namespace EasyDigitalDownloads\Updater;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

use EasyDigitalDownloads\Updater\Utilities\Path;
class Templates {

	public static function load( string $file, array $args = array() ) {
		// Strip any directory components to prevent path traversal.
		$file = basename( $file );
		if ( '' === $file || '.' === $file ) {
			return;
		}

		$templates_path = self::get_templates_path();
		$template       = $templates_path . $file . '.php';

		// Ensure the resolved path stays inside the templates directory.
		$real_template = realpath( $template );
		$real_dir      = realpath( $templates_path );
		if ( ! $real_template || ! $real_dir
			|| strpos( $real_template, rtrim( $real_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR ) !== 0
		) {
			return;
		}

		load_template( $real_template, false, $args );
	}

	/**
	 * Get the templates path.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private static function get_templates_path() {
		return apply_filters( 'edd_sl_sdk_templates_path', trailingslashit( Path::get_dir() ) . 'templates/' );
	}
}

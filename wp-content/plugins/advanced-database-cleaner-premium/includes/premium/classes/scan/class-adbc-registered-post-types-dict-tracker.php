<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks which plugin or theme registered each custom post type and saves the discovered origins to the registered post types dictionary file.
 */
class ADBC_Registered_Post_Types_Dict_Tracker extends ADBC_Singleton {

	// TODO: optimize the determine_origin_from_trace to detect mu plugins, all themes directories and direct files in root plugin directory as we are doing in get_folders_to_scan.
	public const DICT_FILE_PATH = ADBC_UPLOADS_DIR_PATH . '/registered_post_types_dictionary.txt';

	private const TRANSIENT_KEY = 'adbc_plugin_post_types_dict_updated';

	/**
	 * Post types discovered during this request.
	 *
	 * @var array The key is the post type name and the value is an array of typed addons slugs.
	 */
	private $pending_registered_post_types = [];

	private $shutdown_registered = false;

	/**
	 * Hook into the registered_post_type action to track the post types registered by plugins and themes if the transient is not set.
	 *
	 * @return void
	 */
	public function init() {

		// No point hooking at all if we won't write anything
		if ( get_transient( self::TRANSIENT_KEY ) ) {
			return;
		}

		add_action( 'registered_post_type', [ $this, 'track' ], 10, 2 );

	}

	/**
	 * Record origin for a newly registered post type when applicable.
	 *
	 * @param string       $post_type    Post type slug.
	 * @param WP_Post_Type $wp_post_type Post type object.
	 * 
	 * @return void
	 */
	public function track( $post_type, $_wp_post_type ) {

		if ( ! is_string( $post_type ) || $post_type === '' ) {
			return;
		}

		if ( ADBC_Hardcoded_Items::instance()->is_item_belongs_to_wp_core( $post_type, 'post_types' ) ) {
			return;
		}

		$origin = $this->determine_origin_from_trace();
		if ( $origin === null || $origin === '' ) {
			return;
		}

		$this->append_pending_registered_post_type( $post_type, $origin );

		if ( ! $this->shutdown_registered ) {
			$this->shutdown_registered = true;
			add_action( 'shutdown', [ $this, 'save_at_shutdown' ], 10, 0 );
		}

	}

	/**
	 * Save the pending post types to the dictionary file at shutdown.
	 * 
	 * @return void
	 */
	public function save_at_shutdown() {

		if ( empty( $this->pending_registered_post_types ) ) {
			return;
		}

		if ( get_transient( self::TRANSIENT_KEY ) ) {
			return;
		}

		$this->save_to_file();

	}

	/**
	 * Save the pending post types to the dictionary file and set the transient to indicate that the dictionary file has been saved.
	 * 
	 * @return void
	 */
	private function save_to_file() {

		if ( ! ADBC_Files::instance()->is_wp_fs_initialized() ) {
			return;
		}

		$dictionary = self::load_dictionary_from_file();

		// Loop over the pending post types and add/update them in the dictionary.
		foreach ( $this->pending_registered_post_types as $post_type => $addons ) {

			// If the post type is not in the dictionary, add it.
			if ( ! isset( $dictionary[ $post_type ] ) || ! is_array( $dictionary[ $post_type ] ) ) {
				$dictionary[ $post_type ] = [];
			}

			// Add/update the addons for the post type.
			foreach ( $addons as $addon ) {
				if ( ! in_array( $addon, $dictionary[ $post_type ], true ) ) {
					$dictionary[ $post_type ][] = $addon;
				}
			}

		}

		$json = wp_json_encode( $dictionary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( $json === false ) {
			return;
		}

		if ( ! ADBC_Files::instance()->put_contents( self::DICT_FILE_PATH, $json ) ) {
			return;
		}

		// Set the transient to indicate that the dictionary file has been saved.
		set_transient( self::TRANSIENT_KEY, 1, DAY_IN_SECONDS );

	}

	/**
	 * Append a pending registered post type to the pending_registered_post_types array.
	 * 
	 * @param string $post_type Post type slug.
	 * @param string $origin    p:plugin-slug or t:theme-slug.
	 * 
	 * @return void
	 */
	private function append_pending_registered_post_type( $post_type, $origin ) {

		// If the post type is not in the pending_registered_post_types array, add it.
		if ( ! isset( $this->pending_registered_post_types[ $post_type ] ) ) {
			$this->pending_registered_post_types[ $post_type ] = [];
		}

		// If the origin is not in slug's origins list, add it.
		if ( ! in_array( $origin, $this->pending_registered_post_types[ $post_type ], true ) ) {
			$this->pending_registered_post_types[ $post_type ][] = $origin;
		}

	}

	/**
	 * Load the dictionary from the file.
	 * 
	 * @return array The dictionary. The key is the post type slug and the value is an array of typed addons slugs.
	 */
	public static function load_dictionary_from_file() {

		if ( ! ADBC_Files::instance()->exists( self::DICT_FILE_PATH ) || ! ADBC_Files::instance()->is_readable( self::DICT_FILE_PATH ) ) {
			return [];
		}

		$file_contents = ADBC_Files::instance()->get_contents( self::DICT_FILE_PATH );
		if ( ! is_string( $file_contents ) || $file_contents === '' ) {
			return [];
		}

		$saved_post_types = json_decode( $file_contents, true );
		if ( ! is_array( $saved_post_types ) ) {
			return [];
		}

		$validated_post_types = [];
		foreach ( $saved_post_types as $post_type => $typed_addons ) {

			// skip invalid post type slugs or invalid addons list
			if ( ! is_string( $post_type ) || $post_type === '' || ! is_array( $typed_addons ) ) {
				continue;
			}

			if ( ! isset( $validated_post_types[ $post_type ] ) ) {
				$validated_post_types[ $post_type ] = [];
			}

			foreach ( $typed_addons as $typed_addon ) {

				// skip empty typed addon slugs
				if ( ! is_string( $typed_addon ) || $typed_addon === '' ) {
					continue;
				}

				// skip invalid typed addon slugs
				$addon_type = substr( $typed_addon, 0, 2 );
				if ( $addon_type !== 'p:' && $addon_type !== 't:' ) {
					continue;
				}

				// skip empty addon slugs
				$addon_slug = substr( $typed_addon, 2 );
				if ( $addon_slug === '' ) {
					continue;
				}

				// Deduplicate within the same post type's addon list.
				if ( ! in_array( $typed_addon, $validated_post_types[ $post_type ], true ) ) {
					$validated_post_types[ $post_type ][] = $typed_addon;
				}

			}

		}

		// Drop any post type that ended up with no valid addons.
		return array_filter( $validated_post_types );

	}

	/**
	 * Determine the typed addon slug of a post type from the debug backtrace.
	 * 
	 * @return string|null The typed addon slug or null if the origin could not be attributed to any known plugin or theme.
	 */
	private function determine_origin_from_trace() {

		// Cache all path constants once per request — these never change.
		static $current_file = null;
		static $plugin_root_prefix = null;
		static $child_theme_prefix = null;
		static $child_theme_slug = null;
		static $parent_theme_prefix = null;
		static $parent_theme_slug = null;
		static $has_child_theme = null;

		if ( $current_file === null ) {
			$current_file = wp_normalize_path( __FILE__ );
			$plugin_root_prefix = wp_normalize_path( WP_PLUGIN_DIR ) . '/';
			$child_theme_prefix = wp_normalize_path( get_stylesheet_directory() ) . '/';
			$child_theme_slug = get_stylesheet();
			$parent_theme_prefix = wp_normalize_path( get_template_directory() ) . '/';
			$parent_theme_slug = get_template();
			// True only when the active theme is a child theme.
			$has_child_theme = $child_theme_slug !== $parent_theme_slug;
		}

		// Limit depth to keep backtrace overhead minimal.
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );

		foreach ( $trace as $frame ) {

			if ( empty( $frame['file'] ) || ! is_string( $frame['file'] ) ) {
				continue;
			}

			$frame_file = wp_normalize_path( $frame['file'] );

			// Skip frames that belong to this tracker file itself.
			if ( $frame_file === $current_file ) {
				continue;
			}

			// Plugin: path starts with wp-content/plugins/<slug>/...
			if ( strpos( $frame_file, $plugin_root_prefix ) === 0 ) {
				$path_after_plugins_dir = substr( $frame_file, strlen( $plugin_root_prefix ) );
				$plugin_slug = strstr( $path_after_plugins_dir, '/', true );
				if ( $plugin_slug !== false && $plugin_slug !== '' ) {
					return 'p:' . $plugin_slug;
				}
			}

			// Child theme: checked before parent to avoid misattribution
			// when child and parent share a directory prefix.
			if ( $has_child_theme && strpos( $frame_file, $child_theme_prefix ) === 0 ) {
				return 't:' . $child_theme_slug;
			}

			// Parent theme.
			if ( strpos( $frame_file, $parent_theme_prefix ) === 0 ) {
				return 't:' . $parent_theme_slug;
			}

		}

		// Origin could not be attributed to any known plugin or theme.
		return null;

	}

}

<?php

if ( ! class_exists( 'FLTaxonomyTermsWalker' ) ) {
	/**
	 * Walks through each taxonomy term to build JSON data.
	 * Used in FLThemeBuilderRulesLocation::get_taxonomy_terms() below.
	 *
	 * @since 1.4
	 */
	class FLTaxonomyTermsWalker extends Walker_Category {
		/**
		 * Starts the element output.
		 *
		 * @since 1.4
		 *
		 * @param string  $output (passed by reference) Used to create a JSON element out of the $item (WP Term) object.
		 * @param WP_Term $item  WP Term data object.
		 * @param int     $depth  Depth of category in reference to parents.
		 * @param array   $args   An array of arguments. Unused here. See wp_list_categories().
		 * @param int     $id    ID of the current category. Unused here.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$mdash   = str_repeat( '&mdash; ', $depth );
			$name    = $item->name;
			$output .= wp_json_encode( array(
				'id'    => $item->term_id,
				'name'  => $name,
				'label' => $mdash . $name,
				'depth' => $depth,
			) ) . ',';
		}
	}
}

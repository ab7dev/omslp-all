<?php /* seo issues */

/**
 * YOAST schema.org data
 * https://developer.yoast.com/features/schema/api/#to-add-or-remove-graph-pieces
 */

//add_filter( 'wpseo_schema_graph_pieces', 'remove_datepublished_from_schema', 11, 2 );
//add_filter( 'wpseo_schema_graph_pieces', 'remove_datemodified_from_schema', 11, 2 );
add_filter( 'wpseo_schema_webpage', 'remove_dates_property_from_webpage', 11, 1 );

/**
 * Removes the dateModified graph pieces from the schema collector.
 *
 * @param array  $pieces  The current graph pieces.
 * @param string $context The current context.
 *
 * @return array The remaining graph pieces.
 *
function remove_datemodified_from_schema( $pieces, $context ) {
    return \array_filter( $pieces, function( $piece ) {
        return ! $piece instanceof \Yoast\WP\SEO\Generators\Schema\Webpage\dateModified;
    } );
}
*/

/**
 * Removes the breadcrumb property from the WebPage piece.
 *
 * @param array $data The WebPage's properties.
 *
 * @return array The modified WebPage properties.
 */
function remove_dates_property_from_webpage( $data ) {
    if (array_key_exists('datePublished', $data)) {
        unset($data['datePublished']);
    }
    if (array_key_exists('dateModified', $data)) {
        unset($data['dateModified']);
    }
    return $data;
}

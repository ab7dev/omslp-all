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

/**
 * Ergänzt im von Yoast erzeugten Breadcrumb-Schema beim letzten Eintrag
 * die aktuelle kanonische URL, falls das Feld "item" fehlt.
 *
 * cr-1206-yoast-breadcrumb-item-fix
 */
add_filter(
    'wpseo_schema_breadcrumb',
    function (array $piece): array {
        if (
            ! is_singular()
            || empty($piece['itemListElement'])
            || ! is_array($piece['itemListElement'])
        ) {
            return $piece;
        }

        $last_index = array_key_last($piece['itemListElement']);

        if ($last_index === null) {
            return $piece;
        }

        $current_url = wp_get_canonical_url(get_queried_object_id());

        if (! $current_url) {
            $current_url = get_permalink(get_queried_object_id());
        }

        if ($current_url && empty($piece['itemListElement'][$last_index]['item'])) {
            $piece['itemListElement'][$last_index]['item'] = esc_url_raw($current_url);
        }

        return $piece;
    },
    11
);

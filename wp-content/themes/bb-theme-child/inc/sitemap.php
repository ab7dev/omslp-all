<?php /* sitemap issues */

/**
 * Add LP1/GK to main sitemap
 * github #924 
 * https://developer.yoast.com/features/xml-sitemaps/api/#add-additionalexternal-xml-sitemaps-to-the-xml-sitemap-index
 */

/**
 * Writes additional/custom XML sitemap strings to the XML sitemap index.
 *
 * @param string $sitemap_custom_items XML describing one or more custom sitemaps.
 *
 * @return string The XML sitemap index with the additional XML.
 */
function add_sitemap_custom_items( $sitemap_custom_items ) {
    $sitemap_custom_items .= '
<sitemap>
<loc>http://www.openmusicschool.de/lp/page-sitemap.xml</loc>
<lastmod>' . date("Y-m") . '-01T00:04:33+00:00</lastmod>
</sitemap>';
    return $sitemap_custom_items;
}

add_filter( 'wpseo_sitemap_index', 'add_sitemap_custom_items' ); 

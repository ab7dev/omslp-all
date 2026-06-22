<?php
/*
Template Name: Single Course Page
Template Post Type: page, post
https://docs.wpbeaverbuilder.com/beaver-themer/layout-types-modules/singular-layout-type/add-a-singular-themer-layout-to-the-wordpress-page-template-field
*/
//Substitute your own page ID in the following line
//FLThemeBuilderLayoutRenderer::render_all( 245659 );

if (WP_SITEURL == 'https://test4712.openmusicschool.de') {
  FLThemeBuilderLayoutRenderer::render_all( 245659 );
} else {
  FLThemeBuilderLayoutRenderer::render_all( 248396 );
}
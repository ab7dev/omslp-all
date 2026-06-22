<?php /* ACF definitions and configurations */

if( function_exists('acf_add_options_page') ) {
  $parent = acf_add_options_page(array(
       'page_title'  => 'OMS Site Options',
       'menu_title'  => 'OMS Site Options',
       'menu_slug'  => 'acf-site-options',
       'redirect'   => false
  ));
  /*acf_add_options_sub_page(array(
      'page_title'  => 'Testimonial',
      'menu_title' => 'Testimonial',
      'parent_slug' => 'acf-theme-settings',
  ));
  acf_add_options_sub_page(array(
      'page_title'  => 'Photo Gallery',
      'menu_title' => 'Photo Gallery',
      'parent_slug' => 'acf-theme-settings',
  ));*/
}
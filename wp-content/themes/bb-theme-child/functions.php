<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

// load all files from inc folder (inherited from thm)   
foreach ( glob( dirname( __FILE__ ) . '/inc/*.php' ) as $file ) { include $file; }

// Logged in users get "panel" as new start page
// Not logged in user get the redirect
add_action('init', 'redirect_user');
// for users not logged in
function redirect_user(){
    //$post = get_post( $post );
    $current_ID = get_the_ID();
    if( !is_user_logged_in() && ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $current_ID ) ) {
        wp_redirect( home_url('/start') ); 
        exit;
    }
}

/* Enable webp handling */
//enable upload for webp image files.
function webp_upload_mimes($existing_mimes) {
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
}
add_filter('mime_types', 'webp_upload_mimes');

//enable preview / thumbnail for webp image files.
function webp_is_displayable($result, $path) {
    if ($result === false) {
        $displayable_image_types = array( IMAGETYPE_WEBP );
        $info = @getimagesize( $path );

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);



/**
 * Beaver Builder System Fonts Definition
 * https://kb.wpbeaverbuilder.com/article/234-add-web-fonts-to-your-theme-and-the-beaver-builder-plugin
 * Used to add Google Fonts locally and make available as Systems Font in Beaver
 * due to performance, avoiding additional requests outside domain and DSGVO
 *   Oswald
 *   Muli
 *   Lato
 *   Raleway
 *   Overpass
 *   Cabin Condensed
 *   Noto Serif
 *   Noto Sans
 *   Open Sans
 *   Kalam
 *   Playfair Display
 *   Lora
 */

function my_bb_custom_fonts ( $system_fonts ) {
  $system_fonts[ 'Oswald Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '500', '700',
    ),
  );
  $system_fonts[ 'Oswald' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '500', '700',
    ),
  );  
  $system_fonts[ 'Muli Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );
  $system_fonts[ 'Muli' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );  
  $system_fonts[ 'Lato Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );
  $system_fonts[ 'Lato' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );
  $system_fonts[ 'Raleway Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '500', '600', '700', '800', '900',
    ),
  );
  $system_fonts[ 'Overpass Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700', '900',
    ),
  );
  $system_fonts[ 'Cabin Condensed Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '500', '600', '700',
    ),
  );
  $system_fonts[ 'Noto Serif Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );
  $system_fonts[ 'Noto Sans Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );
  $system_fonts[ 'Open Sans Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700', '800',
    ),
  );
  $system_fonts[ 'Kalam Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '300', '400', '700',
    ),
  );
  $system_fonts[ 'Playfair Display Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700', '900',
    ),
  );
  $system_fonts[ 'Lora Local' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
        '400', '700',
    ),
  );    
  return $system_fonts;
}
//Add to Beaver Builder Theme Customizer
add_filter( 'fl_theme_system_fonts', 'my_bb_custom_fonts' );
//Add to Page Builder modules
add_filter( 'fl_builder_font_families_system', 'my_bb_custom_fonts' );


/**
 * Custom Image Sizes
 * https://wpshout.com/wordpress-custom-image-sizes/
 **/

// Make sure featured images are enabled
add_theme_support( 'post-thumbnails' );


// Add featured image sizes
//add_image_size( 'featured-large', 640, 294, true ); // width, height, crop
//add_image_size( 'featured-small', 320, 147, true );

// Add other useful image sizes for use through Add Media modal
add_image_size( 'beaver-custom-icon', 9999, 33 );
add_image_size( 'beaver-custom-icon-retina', 9999, 66 );
add_image_size( 'course-image-panel', 347, 191 );
//add_image_size( 'course-image-single', 9999, 480 );
add_image_size( 'course-image-single-thumb', 60, 60 );
add_image_size( 'video-poster', 753, 432 );

// Register the three useful image sizes for use in Add Media modal
add_filter( 'image_size_names_choose', 'oms_custom_sizes' );
function oms_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'beaver-custom-icon' => __( 'Beaver Custom Icon' ),
        'beaver-custom-icon-retina' => __( 'Beaver Custom Icon Retina' ),
        'course-image-panel' => __( 'Course Image Panel' ),
        'course-image-single-thumb' => __( 'Course Image Single Thumb' ),
        'video-poster' => __( 'Video Poster' ),
        //'t' => __( 'Medium Height' ),
        //'medium-something' => __( 'Medium Something' ),
    ) );
}


/*
 * Custom filter to remove default image sizes from WordPress.
 */
 
/* Add the following code in the theme's functions.php and disable any unset function as required */
function remove_default_image_sizes( $sizes ) {
  
  /* Default WordPress */
  //unset( $sizes[ 'thumbnail' ]);       // Remove Thumbnail (150 x 150 hard cropped)
  //unset( $sizes[ 'medium' ]);          // Remove Medium resolution (300 x 300 max height 300px)
  //unset( $sizes[ 'medium_large' ]);    // Remove Medium Large (added in WP 4.4) resolution (768 x 0 infinite height)
  //unset( $sizes[ 'large' ]);           // Remove Large resolution (1024 x 1024 max height 1024px)
  
  /* With WooCommerce */
  unset( $sizes[ 'shop_thumbnail' ]);  // Remove Shop thumbnail (180 x 180 hard cropped)
  unset( $sizes[ 'shop_catalog' ]);    // Remove Shop catalog (300 x 300 hard cropped)
  unset( $sizes[ 'shop_single' ]);     // Shop single (600 x 600 hard cropped)
  unset( $sizes[ 'woocommerce_single' ]);
  unset( $sizes[ 'woocommerce_gallery_thumbnail' ]);
  unset( $sizes[ 'woocommerce_thumbnail' ]);

  /* Custom Sizes */
  unset( $sizes[ 'course-image-single-thumb' ]);
  
  return $sizes;
}

add_filter( 'intermediate_image_sizes_advanced', 'remove_default_image_sizes' );


/**
 * Set sender name and email after sending by "wordpress@" occured
 * See issue: Mail - Sender = Return-Path = info@openmusicschool.de #326
 */

function wpb_sender_email( $original_email_address ) {
    return 'info@openmusicschool.de';
}
 
function wpb_sender_name( $original_email_from ) {
    return 'OpenMusicSchool';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );

/**
 * Added to enhance mail sender quality
 * Sender was set to www-data@openmusicschool.de
 * in difference to sender info@
 * see issue mail - webmaster@tichypress.net empfängt keine mails von sendmail #210 
 */
add_action('phpmailer_init', function($phpmailer){

  $phpmailer->Sender = $phpmailer->From;

  //Set Sender = Return-Path as woocommerce uses customer email as reply-to
  //See issue: Mail - Bestellbestätigung Kopie an OMS falsche Reply-To-Adresse #346
  $phpmailer->ClearReplyTos();
  $phpmailer->addReplyTo('info@openmusicschool.de', 'OpenMusicSchool');

});


/**
 * Write proper to debug.log with function write_log
 */

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
   if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

// Declaring WooCommerce support in themes
// https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );


/**
 * Customize My Account Page
 * https://sanjeebaryal.com.np/add-a-new-custom-tab-in-the-woocommerce-account-page/
 */

/**
 * Remove Account tabs.
 * 
 * @return array Items.
 */
function remove_account_tabs( $items ) {

    unset( $items['downloads'] );
    unset( $items['payment-methods'] );
    unset( $items['customer-logout'] );
    return $items;
}
add_filter ( 'woocommerce_account_menu_items', 'remove_account_tabs' );

/**
 * Rename tabs.
 * 
 * @return array Items.
 */
function rename_account_tabs( $items ) {

    $items['dashboard'] = "Dashboard";
    $items['orders'] = "Meine Bestellung";
    $items['subscriptions'] = "Meine Mitgliedschaft";
    $items['edit-account'] = "Mein Account";
    $items['edit-address'] = "Adresse ändern";
    return $items;
}
add_filter ( 'woocommerce_account_menu_items', 'rename_account_tabs' );

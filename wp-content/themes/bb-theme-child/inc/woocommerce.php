<?php


/* Login/Logout Redirects */

add_filter( 'woocommerce_login_redirect', 'dd_custom_login_redirect', 10, 2 );
function dd_custom_login_redirect() {
    return site_url() . '/panel';
}

// https://developer.wordpress.org/reference/hooks/logout_redirect/
add_filter( 'logout_redirect', function( $url, $query, $user ) {
    //return home_url();
    return site_url() . '/panel';
}, 10, 3 );


/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
unset($fields['billing']['billing_company']);
unset($fields['billing']['billing_address_2']);
unset($fields['billing']['billing_state']);
unset($fields['billing']['billing_phone']);
unset($fields['order']['order_comments']);
/* unset($fields['billing']['billing_email']);
unset($fields['account']['account_username']);
unset($fields['account']['account_password']);
unset($fields['account']['account_password-2']); */

$fields['billing']['billing_country']['label'] = 'Land';
$fields['billing']['billing_email']['label'] = 'E-Mail';

// Order fields
unset( $fields['order']['order_comments'] );

return $fields;
}


/**
 * WooCommerce
 * checkout page
 * Add link to monthly payment
 * Issue #250
 */
add_action( 'woocommerce_checkout_before_customer_details', 'oms_link_payment_monthly' );

function oms_link_payment_monthly( $checkout ) {

  $product_id_from = 0;
  $product_id_to = 0;
  $payment_term = '';

  foreach( WC()->cart->get_cart() as $cart_item ){
    $product_id_from = $cart_item['product_id'];
  }

  switch ($product_id_from) {
    case 40962: // Klavier LP2
        $product_id_to = 106547;
        $payment_term = 'monatliche';
        break;
    case 106547:
        $product_id_to = 40962;
        $payment_term = 'jährliche';
        break;  
/*    case 40250: // A-Gitarre LP2
        $product_id_to = 168908;
        $payment_term = 'monatliche';
        break;
    case 168908:
        $product_id_to = 40250;
        $payment_term = 'jährliche';
        break;
    case 41118: // Schlagzeug LP2
        $product_id_to = 168909;
        $payment_term = 'monatliche';
        break;
    case 168909:
        $product_id_to = 41118;
        $payment_term = 'jährliche';
        break;
    case 162582: // Ukulele LP2
        $product_id_to = 168910;
        $payment_term = 'monatliche';
        break;
    case 168910:
        $product_id_to = 162582;
        $payment_term = 'jährliche';
        break;
    case 47131: // E-Gitarre LP2
        $product_id_to = 168922;
        $payment_term = 'monatliche';
        break;
    case 168922:
        $product_id_to = 47131;
        $payment_term = 'jährliche';
        break;
    case 40985: // Bass LP2
        $product_id_to = 168923;
        $payment_term = 'monatliche';
        break;
    case 168923:
        $product_id_to = 40985;
        $payment_term = 'jährliche';
        break;
    case 41218: // Keyboard LP2
        $product_id_to = 168924;
        $payment_term = 'monatliche';
        break;
    case 168924:
        $product_id_to = 41218;
        $payment_term = 'jährliche';
        break;
    case 128513: // HOME LP2
        $product_id_to = 168925;
        $payment_term = 'monatliche';
        break;
    case 168925:
        $product_id_to = 128513;
        $payment_term = 'jährliche';
        break;
    case 164297: // IN-VIDEO
        $product_id_to = 168951;
        $payment_term = 'monatliche';
        break;
    case 168951:
        $product_id_to = 164297;
        $payment_term = 'jährliche';
        break;
    case 114865: // KLAVIERNOTEN PDF
        $product_id_to = 168961;
        $payment_term = 'monatliche';
        break;
    case 168961:
        $product_id_to = 114865;
        $payment_term = 'jährliche';
        break; */                
    case 956: // TEST
        $product_id_to = 956;
        $payment_term = 'jährliche';
        break; 
  }

  if ($product_id_to != 0) {
    echo '<div id="oms_link_zahlweise">';
    echo 'Für '.$payment_term.' Zahlweise/Abrechnung: <a href="https://'.$_SERVER['HTTP_HOST'].'/?add-to-cart='.$product_id_to.'">Hier klicken.</a>';
    echo '</div>';
  }

}


// Issue #446 WooCommerce Blacklist für unerwünschte Besteller #446
function oms_check_email_on_registration( $errors, $username, $email ) {
    $blacklist = array( 
      'test-wc-blacklist@tichypress.net',
      'hkossack@web.de'
    );
    if ( in_array( $email, $blacklist ) ) {
        $errors->add( 'blacklist_error', 'Fehler bei der Registrierung.' );
    }
    return $errors;
}
add_filter( 'woocommerce_registration_errors', 'oms_check_email_on_registration', 10, 3 );


// Issue  Shop - Button "Kündigen" hinzufügen und IHK-Kriterien erfüllen #677 

/* Send email to a customer on cancelled subscription in WooCommerce */
add_action( 'woocommerce_subscription_status_pending-cancel', 'sendCustomerCancellationEmail' );

/**
 * @param WC_Subscription $subscription
 */
function sendCustomerCancellationEmail( $subscription ) {
    $customer_email = $subscription->get_billing_email();
    //$customer_email = 'tech@openmusicschool.de';
    $wc_emails = WC()->mailer()->get_emails();
    $wc_emails['WCS_Email_Cancelled_Subscription']->recipient = $customer_email;
    $wc_emails['WCS_Email_Cancelled_Subscription']->trigger( $subscription );
}

// hotfix redirect bug add_to_card
// https://stackoverflow.com/questions/15592633/woocommerce-add-to-cart-button-redirect-to-checkout
add_filter ('add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout() {
//    return WC()->cart->get_checkout_url();
return $_SERVER['HTTP_HOST'].'/shop/checkout/';
}



/* Create shortcode to display product ACF content_in_order
   https://wp-qa.com/how-to-display-product-description-of-woo-commerce-products-in-normal-post-of-wordpress
   */

function product_content_in_order_shortcode( $atts ){
    
/*
    // use shortcode_atts() to set defaults then extract() to variables
    extract( shortcode_atts( array( 'id' => false ), $atts ) );

    // if an $id was passed, and we could get a Post for it, and it's a product....
    if ( ! empty( $id ) && null != ( $product = get_post( $id ) ) && $product->post_type = 'product' ){
        // apply woocommerce filter to the excerpt
        echo apply_filters( 'woocommerce_short_description', $product->post_excerpt );
    }
*/


    $contents = WC()->cart->cart_contents;
    if( $contents ) foreach ( $contents as $cart_item ){
      $product_id = $cart_item['product_id'];
    }
    echo get_field('content_in_order',$product_id);
}
// process [product_content_in_order] using product_content_in_order_shortcode()
add_shortcode( 'product_content_in_order', 'product_content_in_order_shortcode' );


/* Remove Free Trial Text WOO
   Copied from snippet plugin
   Inserted by Ben */
add_filter( 'woocommerce_subscriptions_product_price_string', 'subscriptions_custom_price_string', 20, 3 );
function subscriptions_custom_price_string( $price_string, $product, $args ) {
    // Get the trial length to check if it's enabled
    $trial_length = $product->get_meta('_subscription_trial_length');
    if( $trial_length > 0 )
        $price_string = $args['price'];

    return $price_string;
}
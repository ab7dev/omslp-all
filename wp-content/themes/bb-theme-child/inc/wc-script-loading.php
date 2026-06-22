<?php

/**
 * Callback function that returns true if the current page is a WooCommerce page or false if otherwise.
 *
 * see: https://wpdevdesign.com/how-to-remove-woocommerce-css-js-from-non-woocommerce-pages/
 * 
 * @return boolean true for WC pages and false for non WC pages
 */
function is_wc_page() {
	return class_exists( 'WooCommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() );
}

add_action( 'template_redirect', 'conditionally_remove_wc_assets' );
/**
 * Remove WC stuff on non WC pages.
 */
function conditionally_remove_wc_assets() {
	// if this is a WC page, abort.
	if ( is_wc_page() ) {
		return;
	}

	// remove WC generator tag
	remove_filter( 'get_the_generator_html', 'wc_generator_tag', 10, 2 );
	remove_filter( 'get_the_generator_xhtml', 'wc_generator_tag', 10, 2 );

	// unload WC scripts
	remove_action( 'wp_enqueue_scripts', [ WC_Frontend_Scripts::class, 'load_scripts' ] );
	remove_action( 'wp_print_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );
	remove_action( 'wp_print_footer_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );

	// remove "Show the gallery if JS is disabled"
	remove_action( 'wp_head', 'wc_gallery_noscript' );

	// remove WC body class
	remove_filter( 'body_class', 'wc_body_class' );
}

add_filter( 'woocommerce_enqueue_styles', 'conditionally_woocommerce_enqueue_styles' );
/**
 * Unload WC stylesheets on non WC pages
 *
 * @param array $enqueue_styles
 */
function conditionally_woocommerce_enqueue_styles( $enqueue_styles ) {
	return is_wc_page() ? $enqueue_styles : array();
}

add_action( 'wp_enqueue_scripts', 'conditionally_wp_enqueue_scripts' );
/**
 * Remove inline style on non WC pages
 */
function conditionally_wp_enqueue_scripts() {
	if ( ! is_wc_page() ) {
		wp_dequeue_style( 'woocommerce-inline' );
	}
}

// add_action( 'init', 'remove_wc_custom_action' );
function remove_wc_custom_action(){
	remove_action( 'wp_head', 'wc_gallery_noscript' );
}
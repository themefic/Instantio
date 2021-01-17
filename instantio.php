<?php
/**
 * Plugin Name: Instantio Lite
 * Plugin URI: https://themefic.com/plugins/wooinstant/
 * Bitbucket Plugin URI: https://github.com/themefic/instantio
 * Description: Instantio converts multistep checkout into WooCommerce One Page Checkout. The Whole WooCommerce checkout process would take only 10-15 seconds. Yes, you heard it right! Only 10-15 Seconds! All your customer have to do is “Add to Cart”, a popup/cart drawer will appear with the cart view. Your customer can direct checkout WooCommerce based store and order from that single window!. No Page Reload whatsoever!
 * Author: BootPeople
 * Text Domain: instantio
 * Domain Path: /lang/
 * Author URI: https://psdtowpservice.com
 * Tags: instantio,responsive,woocommerce
 * Version: 1.0.0
 * WC tested up to: 4.9.0
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Define INSTANTIO_VERSION.
if ( ! defined( 'INSTANTIO_VERSION' ) ) {
	define( 'INSTANTIO_VERSION', '1.0.1' );
}

/**
 * Including Plugin file for security
 * Include_once
 *
 * @since 1.0.0
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Loading Text Domain
 */
add_action('plugins_loaded', 'wooinstant_plugin_loaded_action', 10, 2);

function wooinstant_plugin_loaded_action() {
	//Internationalization
	load_plugin_textdomain( 'instantio', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );



    // Load the plugin options
    if ( class_exists( 'ReduxFramework' ) ) {
        require_once dirname( __FILE__ ) . '/inc/options.php';
    }

}

/**
 *	Enqueue Instantio scripts
 *
 */
function wooinstant_enqueue_scripts(){

	$INSTANTIO_VERSION = current_time('timestamp');

	wp_enqueue_style('instantio-stylesheet', plugin_dir_url( __FILE__ ) . 'assets/css/styles.css','',$INSTANTIO_VERSION );
	wp_enqueue_script( 'instantio-drawer', plugin_dir_url( __FILE__ ) . 'assets/js/drawer.js', array('jquery'), $INSTANTIO_VERSION, true );
    wp_enqueue_script( 'instantio-ajax-script', plugin_dir_url( __FILE__ ) . 'assets/js/wi-ajax-script.js', array('jquery'), $INSTANTIO_VERSION, true );
	wp_enqueue_script('instantio-ajax-quick-view.js', plugin_dir_url(__FILE__) . 'assets/js/wi-ajax-quick-view.js', array('jquery'), $INSTANTIO_VERSION, true);

	wp_localize_script( 'instantio-ajax-script', 'instantio_ajax_params',
		array(
	        'wi_ajax_nonce' => wp_create_nonce( 'wi_ajax_nonce' ),
	        'wi_ajax_url' => admin_url( 'admin-ajax.php' )
	    )
    );

	/**
	 * Handle WC frontend scripts
	 *
	 * @package WooCommerce/Classes
	 * @version 2.3.0
	 * http://woocommerce.wp-a2z.org/oik_api/wc_frontend_scriptsget_script_data/
	 */
	//remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));

	//first check that woo exists to prevent fatal errors
	if( function_exists('is_woocommerce') ) {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? : '.min';
		$lightbox_en          = 'yes' === get_option( 'woocommerce_enable_lightbox' );
		$ajax_cart_en         = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		//global $wp_scripts;
    	//$wp_scripts->registered[ 'wc-checkout' ]->src = plugin_dir_url( __FILE__ ) . 'assets/js/checkout.js';

		wp_enqueue_script( 'wc-cart', $frontend_script_path . 'cart' . $suffix . '.js', array( 'jquery', 'wc-country-select', 'wc-address-i18n' ) );

	    wp_localize_script('wc-cart', 'wc_cart_params', apply_filters('wc_cart_params', array(
			'ajax_url' => WC()->ajax_url() ,
			'wc_ajax_url' => WC_AJAX::get_endpoint(' %%endpoint%%') ,
			'ajax_loader_url' => apply_filters('woocommerce_ajax_loader_url', $assets_path . 'images / ajax - loader@2x . gif') ,
			'update_shipping_method_nonce' => wp_create_nonce('update-shipping-method') ,
		)));

		wp_enqueue_script( 'wc-checkout', $frontend_script_path . 'checkout' . $suffix . '.js', array( 'jquery', 'wc-address-i18n' ) );

	    wp_localize_script('wc-checkout', 'wc_checkout_params', apply_filters('wc_checkout_params', array(
			'ajax_url'                  => WC()->ajax_url(),
			'wc_ajax_url'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
			'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
			'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
			'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
			'checkout_url'              => WC_AJAX::get_endpoint( 'checkout' ),
			'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
			'debug_mode'                => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'i18n_checkout_error'       => esc_attr__( 'Error processing checkout. Please try again.', 'woocommerce' ),
		)));

		wp_enqueue_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery', 'wp-util', 'jquery-blockui' ) );

	    wp_localize_script('wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', apply_filters('wc_add_to_cart_variation_params', array(
			'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
			'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
			'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
		)));

		wp_enqueue_style('select2');
		wp_enqueue_script('select2');
		wp_enqueue_script( 'wc-country-select' );

	}
}
add_filter( 'wp_enqueue_scripts', 'wooinstant_enqueue_scripts', 200 ,2 ); // Giving high priority

/**
 *	Instantio Menu Layout
 */
require_once( dirname( __FILE__ ) . '/inc/layout.php' );

/**
 *	Instantio Functions
 */
require_once( dirname( __FILE__ ) . '/inc/functions.php' );

/**
 *	Plugin activation hook
 */
function wooinstant_activation_redirect( $plugin ) {
	if( $plugin == plugin_basename( __FILE__ ) ) {
	    // redirect option page after installed
	    wp_redirect( admin_url( 'admin.php?page=_woinstant' ) );
	    exit;
	}
}
add_action( 'activated_plugin', 'wooinstant_activation_redirect' );


/**
 * Add plugin action links.
 *
 * @since 1.0.0
 * @version 4.0.0
 */
function wi_plugin_action_links( $links ) {
	$plugin_links = array(
		'<a href="admin.php?page=_woinstant">' . esc_html__( 'Settings', 'wooinstant' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wi_plugin_action_links' );
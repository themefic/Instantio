<?php
/**
 * Plugin Name: Instantio Lite
 * Plugin URI: https://themefic.com/plugins/instantio/
 * Github Plugin URI: https://github.com/themefic/instantio
 * Description: Instantio converts multistep checkout into WooCommerce One Page Checkout. The Whole WooCommerce checkout process would take only 10-15 seconds. Yes, you heard it right! Only 10-15 Seconds! All your customer have to do is “Add to Cart”, a popup/cart drawer will appear with the cart view. Your customer can direct checkout WooCommerce based store and order from that single window!. No Page Reload whatsoever!
 * Author: BootPeople
 * Text Domain: instantio
 * Domain Path: /lang/
 * Author URI: https://psdtowpservice.com
 * Tags: instantio,responsive,woocommerce
 * Version: 1.1.1
 * WC tested up to: 4.9.2
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Define INSTANTIO_VERSION.
if ( ! defined( 'INSTANTIO_VERSION' ) ) {
	define( 'INSTANTIO_VERSION', '1.1.1' );
}

/**
 * Including Plugin file for security
 * Include_once
 *
 * @since 1.0.0
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 *	Instantio Functions
 */
require_once( dirname( __FILE__ ) . '/inc/functions.php' );

/**
 * Loading Text Domain
 */
add_action('plugins_loaded', 'instantio_lite_plugin_loaded_action', 10, 2);
function instantio_lite_plugin_loaded_action() {
	//Internationalization
	load_plugin_textdomain( 'instantio', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

	//Redux Framework calling
	if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/inc/redux-framework/ReduxCore/framework.php' ) ) {
	    require_once( dirname( __FILE__ ) . '/inc/redux-framework/ReduxCore/framework.php' );
	}

    // Load the plugin options
    if ( file_exists( dirname( __FILE__ ) . '/inc/options-init.php' ) ) {
        require_once dirname( __FILE__ ) . '/inc/options-init.php';
    }
}

/**
 *	Enqueue Instantio scripts
 *
 */
function instantio_lite_enqueue_scripts(){

	if ( defined( 'WI_VERSION' ) ) {
		return;
	}

	$INSTANTIO_VERSION = current_time('timestamp');

	wp_enqueue_style('instantio-common-styles', plugin_dir_url( __FILE__ ) . 'assets/css/common.css','', $INSTANTIO_VERSION );
	wp_enqueue_script( 'instantio-common-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/common.js', array('jquery'), $INSTANTIO_VERSION, true );

	wp_localize_script( 'instantio-common-scripts', 'instantio_ajax_params',
		array(
	        'wi_ajax_nonce' => wp_create_nonce( 'wi_ajax_nonce' ),
	        'wi_ajax_url' => admin_url( 'admin-ajax.php' ),
	        'cart_icon' => instantio_lite_get_svg_icon('shopping_cart'),
	    )
    );


	/**
	 * Handle WC frontend scripts
	 *
	 * @package WooCommerce/Classes
	 * @version 2.3.0
	 * http://woocommerce.wp-a2z.org/oik_api/wc_frontend_scriptsget_script_data/
	 */

	//first check that woo exists to prevent fatal errors
	if( function_exists('is_woocommerce') ) {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? : '.min';
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		wp_enqueue_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery', 'wp-util', 'jquery-blockui' ) );

	    wp_localize_script('wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', apply_filters('wc_add_to_cart_variation_params', array(
			'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
			'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
			'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
		)));

	}

}
add_filter( 'wp_enqueue_scripts', 'instantio_lite_enqueue_scripts' );

/**
 * Notice if WooCommerce is inactive
 */
function instantio_admin_notice_warn() {
	if ( !class_exists( 'WooCommerce' ) ) { ?>
	    <div class="notice notice-warning is-dismissible">
	        <p>
	        	<strong><?php esc_attr_e( 'Instantio requires WooCommerce to be activated ', 'wooinstant' ); ?> <a href="<?php echo esc_url( admin_url('/plugin-install.php?s=slug:woocommerce&tab=search&type=term') ); ?>">Install Now</a></strong>
	        </p>
	    </div> <?php
    }

}
add_action( 'admin_notices', 'instantio_admin_notice_warn' );

/**
 * Add plugin action links.
 *
 * @since 1.0.0
 * @version 4.0.0
 */
function instantio_plugin_action_links( $links ) {
	$plugin_links = array(
		'<a href="admin.php?page=_woinstant">' . esc_html__( 'Settings', 'wooinstant' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'instantio_plugin_action_links' );
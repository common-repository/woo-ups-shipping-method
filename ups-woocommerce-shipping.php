<?php
/**
 * Plugin Name: Shipping Methods for UPS on WooCommerce
 * Plugin URI: 
 * Description: Add UPS Shipping Rates to your WooCommerce store and provide your customer with wide range of shipping options offered by UPS.
 * Version: 1.0.0
 * Author: PluginFence
 * Author URI: 
 * Text Domain: ups_woocommerce_shipping
 * WC requires at least: 2.6.0
 * WC tested up to: 3.6
 */

register_activation_hook( __FILE__, function() {

});


/**
 * Define PVALLEY_UPS_PLUGIN_MAIN_FILE.
 */
if ( ! defined( 'PVALLEY_UPS_PLUGIN_MAIN_FILE' ) ) {
	define( 'PVALLEY_UPS_PLUGIN_MAIN_FILE', __FILE__ );
}

// Include Common file.
if( ! class_exists("Pvalley_Ups_Common")) {
	require_once "dependencies/class-pvalley-ups-common.php";
}

// UPS Constant
if( ! class_exists("Pvalley_Ups_Shipping_Constant") ) {
	require_once "inc/class-pvalley_ups_shipping_constant.php";
}

// Translation Text Domain
if( ! defined("PVALLEY_UPS_SHIPPING_TEXT_DOMAIN") ) {
	define( "PVALLEY_UPS_SHIPPING_TEXT_DOMAIN", "ups_woocommerce_shipping" );
}

if( Pvalley_Ups_Common::plugin_active_check("woocommerce/woocommerce.php") ) {		// Proceed if WooCommerce is active
	if( ! class_exists("Pvalley_Ups_Shipping_Main") ) {
		class Pvalley_Ups_Shipping_Main {

			/**
			 * Shipping Method Id.
			 */
			public static $id = "pvalley_ups_shipping_method";
			/**
			 * Constructor.
			 */
			public function __construct() {
				add_filter( "woocommerce_shipping_init", array( $this, "shipping_method_init" ) );
				add_filter( "woocommerce_shipping_methods", array( $this, "add_shipping_method" ) );
				add_filter( 'plugin_action_links_' . plugin_basename( PVALLEY_UPS_PLUGIN_MAIN_FILE ), __CLASS__. '::plugin_action_links' );
			}

			function shipping_method_init() {
				require_once "inc/class-pvalley_ups_shipping_method.php";
			}

			function add_shipping_method( $methods ) {
				$methods[ self::$id ] = "Pvalley_Ups_Shipping_Method";
				return $methods;
			}

			/**
			 * Plugin action link.
			 */
			public static function plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=pvalley_ups_shipping_method' ) . '">' . __( 'Settings', PVALLEY_UPS_SHIPPING_TEXT_DOMAIN )
				);
				return array_merge( $plugin_links, $links );
			}
		}
	}
	new Pvalley_Ups_Shipping_Main();
}
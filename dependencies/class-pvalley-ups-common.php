<?php
/**
 * Common file.
 */

if( ! defined("ABSPATH") ) {
	exit;
}

if( ! class_exists("Pvalley_Ups_Common") ) {
	class Pvalley_Ups_Common {
		/**
		 * Array of active plugins.
		 */
		private static $active_plugins;

		/**
		 * Initialize the active plugins.
		 * @return array Active Plugins.
		 */
		public static function get_active_plugins() {

			self::$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() )
				self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			return self::$active_plugins;
		}

		/**
		 * Check whether Plugin is active or not.
		 * @param string (Optional) Plugin base file path relative to that plugin folder, bydefault checks for WooCommerce.
		 * @return boolean True if woocommerce is active else false.
		 */
		public static function plugin_active_check( $plugin_path = 'woocommerce/woocommerce.php' ) {

			if ( ! self::$active_plugins ) self::get_active_plugins();

			return in_array( $plugin_path, self::$active_plugins ) || array_key_exists( $plugin_path, self::$active_plugins );
		}
	}
}
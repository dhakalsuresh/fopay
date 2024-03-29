<?php
/**
 * Plugin Name: WooCommerce Fopay
 * Description: Fopay
 * Version: 1.2.0
 * License: GPLv2 or later 
 * Author: Suresh Dhakal
 * Author URI: https://github.com/dhakalsuresh/fopay
 * Text Domain: woocommerce-fopay
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('WC_Fopay')) :

	/**
	 * WooCommerce Fopay main class.
	 */
	class WC_Fopay
	{

		/**
		 * Plugin version.
		 * @var string
		 */
		const VERSION = '1.2.0';

		/**
		 * Instance of this class.
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 */
		private function __construct()
		{
			// Load plugin text domain
			add_action('init', array($this, 'load_plugin_textdomain'));

			// Checks with WooCommerce is installed.
			if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.3', '>=')) {
				$this->includes();

				// Hooks
				add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));
				add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'plugin_action_links'));
			} else {
				add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
			}
		}


		/**
		 * Return an instance of this class.
		 * @return object A single instance of this class.
		 */
		public static function get_instance()
		{
			// If the single instance hasn't been set, set it now.
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path()
		{
			return untrailingslashit(plugin_dir_path(__FILE__));
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain()
		{
			$locale = apply_filters('plugin_locale', get_locale(), 'woocommerce-fopay');

			load_textdomain('woocommerce-fopay', trailingslashit(WP_LANG_DIR) . 'woocommerce-fopay/woocommerce-fopay-' . $locale . '.mo');
			load_plugin_textdomain('woocommerce-fopay', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		/**
		 * Includes.
		 */
		private function includes()
		{
			include_once('includes/class-wc-fopay-gateway.php');
		}

		/**
		 * Add the gateway to WooCommerce.
		 *
		 * @param  array $methods WooCommerce payment methods.
		 * @return array          Payment methods with Fopay.
		 */
		public function add_gateway($methods)
		{
			$methods[] = 'WC_Gateway_Fopay';
			return $methods;
		}

		/**
		 * Show action links on the plugin screen.
		 * @param  mixed $links Plugin Action links
		 * @return array
		 */
		public static function plugin_action_links($links)
		{
			$action_links = array(
				'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_fopay') . '" title="' . esc_attr(__('View Settings', 'woocommerce-fopay')) . '">' . __('Settings', 'woocommerce-fopay') . '</a>',
			);

			return array_merge($action_links, $links);
		}

		/**
		 * WooCommerce fallback notice.
		 * @return string
		 */
		public function woocommerce_missing_notice()
		{
			echo '<div class="error notice is-dismissible"><p>' . sprintf(__('WooCommerce Fopay depends on the last version of %s or later to work!', 'woocommerce-fopay'), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __('WooCommerce 2.3', 'woocommerce-fopay') . '</a>') . '</p></div>';
		}
	}

	add_action('plugins_loaded', array('WC_Fopay', 'get_instance'), 0);

endif;

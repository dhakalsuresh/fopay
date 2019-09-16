<?php
/**
 * Fopay Payment Gateway
 *
 * Provides a Fopay Payment Gateway.
 *
 * @class       WC_Gateway_Fopay
 * @extends     WC_Payment_Gateway
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * WC_Gateway_Fopay Class
 */
class WC_Gateway_Fopay extends WC_Payment_Gateway
{

	/** @var boolean Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var WC_Logger Logger instance */
	public static $log = false;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{
		$this->id                 = 'fopay';
		$this->icon               = apply_filters('woocommerce_fopay_icon', plugins_url('assets/images/fopay.png', plugin_dir_path(__FILE__)));
		$this->has_fields         = false;
		$this->order_button_text  = __('Proceed to Fopay', 'woocommerce-fopay');
		$this->method_title       = __('Fopay', 'woocommerce-fopay');
		$this->method_description = __('The Fopay epay system sends customers to Fopay to enter their payment information.', 'woocommerce-fopay');

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title        = $this->get_option('title');
		$this->description  = $this->get_option('description');
		$this->testmode     = 'yes' === $this->get_option('testmode', 'no');
		$this->debug        = 'yes' === $this->get_option('debug', 'no');
		$this->service_code = $this->get_option('service_code');

		self::$log_enabled  = $this->debug;

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

		if (!$this->is_valid_for_use()) {
			$this->enabled = 'no';
		} else {
			include_once('includes/class-wc-gateway-fopay-ipn-handler.php');
			new WC_Gateway_Fopay_IPN_Handler($this, $this->testmode, $this->service_code);

			if ($this->service_code) {
				include_once('includes/class-wc-gateway-fopay-pdt-handler.php');
				new WC_Gateway_Fopay_PDT_Handler($this->testmode, $this->service_code);
			}
		}
	}

	/**
	 * Logging method
	 * @param string $message
	 */
	public static function log($message)
	{
		if (self::$log_enabled) {
			if (empty(self::$log)) {
				self::$log = new WC_Logger();
			}
			self::$log->add('fopay', $message);
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @return bool
	 */
	public function is_valid_for_use()
	{
		return in_array(get_woocommerce_currency(), apply_filters('woocommerce_fopay_supported_currencies', array('NPR')));
	}

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options()
	{
		if ($this->is_valid_for_use()) {
			parent::admin_options();
		} else {
			?>
		<div class="inline error">
			<p><strong><?php _e('Gateway Disabled', 'woocommerce-fopay'); ?></strong>: <?php _e('Fopay does not support your store currency.', 'woocommerce-fopay'); ?></p>
		</div>
	<?php
}
}

/**
 * Initialise Gateway Settings Form Fields
 */
public function init_form_fields()
{
	$this->form_fields = include('includes/settings-fopay.php');
}

/**
 * Get the transaction URL.
 *
 * @param  WC_Order $order
 * @return string
 */
public function get_transaction_url($order)
{
	if ($this->testmode) {
		$this->view_transaction_url = 'https://dev.fopay.com.np/merchant#!mpyments/!mpd;tid=%s';
	} else {
		$this->view_transaction_url = 'https://fopay.com.np/merchant#!mpyments/!mpd;tid=%s';
	}
	return parent::get_transaction_url($order);
}

/**
 * Process the payment and return the result
 *
 * @param  int $order_id
 * @return array
 */
public function process_payment($order_id)
{
	include_once('includes/class-wc-gateway-fopay-request.php');

	$order         = wc_get_order($order_id);
	$fopay_request = new WC_Gateway_Fopay_Request($this);

	return array(
		'result'   => 'success',
		'redirect' => $fopay_request->get_request_url($order, $this->testmode)
	);
}
}

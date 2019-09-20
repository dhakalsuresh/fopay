<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Generates requests to send to Fopay
 */
class WC_Gateway_Fopay_Request
{

	/**
	 * Pointer to gateway making the request
	 * @var WC_Gateway_Fopay
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from Fopay
	 * @var string
	 */
	protected $notify_url;

	/**
	 * Constructor
	 * @param WC_Gateway_Fopay $gateway
	 */
	public function __construct($gateway)
	{
		$this->gateway    = $gateway;
		$this->notify_url = WC()->api_request_url('WC_Gateway_Fopay');
	}

	/**
	 * Get the Fopay request URL for an order
	 * @param  WC_Order $order
	 * @param  boolean  $sandbox
	 * @return string
	 */
	public function get_request_url($order, $endpoint, $token)
	{
		$requestData = $this->get_fopay_args($order);
		
		return wp_remote_post($endpoint, [
			'body'    => $requestData,
			'headers' => [
				'Authorization' => 'Basic ' . $token,
			],
		]);
	}



	/**
	 * Get Fopay Args for passing to Fopay
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function get_fopay_args($order)
	{
		WC_Gateway_Fopay::log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url);

		return apply_filters('woocommerce_fopay_args', array(
			'amt'   => wc_format_decimal($order->get_subtotal() - $order->get_total_discount(), 2),
			'txAmt' => wc_format_decimal($order->get_total_tax(), 2),
			'pdc'   => wc_format_decimal($order->get_total_shipping(), 2),
			'psc'   => wc_format_decimal($this->get_service_charge($order), 2),
			'tAmt'  => wc_format_decimal($order->get_total(), 2),
			'scd'   => $this->gateway->get_option('service_code'),
			'pid'   => $this->gateway->get_option('invoice_prefix') . $order->get_order_number(),
			'su'    => add_query_arg(array('payment_status' => 'success', 'key' => $order->order_key), $this->notify_url),
			'fu'    => add_query_arg(array('payment_status' => 'failure', 'key' => $order->order_key), $this->notify_url),
		), $order);
	}

	/**
	 * Get the service charge to send to Fopay
	 * @param  WC_Order $order
	 * @return float
	 */
	private function get_service_charge($order)
	{
		$fee_total = 0;
		if (sizeof($order->get_fees()) > 0) {
			foreach ($order->get_fees() as $item) {
				$fee_total += (isset($item['line_total'])) ? $item['line_total'] : 0;
			}
		}

		return $fee_total;
	}
}

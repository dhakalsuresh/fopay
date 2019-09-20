<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Settings for Fopay Gateway
 */
return array(
	'enabled' => array(
		'title'   => __('Enable/Disable', 'woocommerce-fopay'),
		'type'    => 'checkbox',
		'label'   => __('Enable Fopay Payment', 'woocommerce-fopay'),
		'default' => 'yes'
	),
	'title' => array(
		'title'       => __('Title', 'woocommerce-fopay'),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __('This controls the title which the user sees during checkout.', 'woocommerce-fopay'),
		'default'     => __('Fopay', 'woocommerce-fopay')
	),
	'description' => array(
		'title'       => __('Description', 'woocommerce-fopay'),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __('This controls the description which the user sees during checkout.', 'woocommerce-fopay'),
		'default'     => __('Pay via Fopay; you can pay with Fopay account securly.', 'woocommerce-fopay')
	),
	'service_code' => array(
		'title'       => __('Service Code', 'woocommerce-fopay'),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __('Please enter your Fopay Service Code or ; this is needed in order to take payment.', 'woocommerce-fopay'),
		'default'     => '',
		'placeholder' => 'Eg: WooCommerce'
	),
	'invoice_prefix' => array(
		'title'       => __('Invoice Prefix', 'woocommerce-fopay'),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __('Please enter a prefix for your invoice numbers. If you use your Fopay account for multiple stores ensure this prefix is unique as Fopay will not allow orders with the same invoice number.', 'woocommerce-fopay'),
		'default'     => 'WC-'
	),
	'testmode' => array(
		'title'       => __('Sandbox Mode', 'woocommerce-fopay'),
		'type'        => 'checkbox',
		'label'       => __('Enable Sandbox Mode', 'woocommerce-fopay'),
		'default'     => 'no',
		'description' => sprintf(__('Enable Fopay sandbox to test payments. Sign up for a developer account %shere%s.', 'woocommerce-fopay'), '<a href="https://fopay.io/" target="_blank">', '</a>')
	),
	'debug' => array(
		'title'       => __('Debug Log', 'woocommerce-fopay'),
		'type'        => 'checkbox',
		'label'       => __('Enable logging', 'woocommerce-fopay'),
		'default'     => 'no',
		'description' => sprintf(__('Log Fopay events, such as IPN requests, inside <code>%s</code>', 'woocommerce-fopay'), wc_get_log_file_path('fopay'))
	)
);

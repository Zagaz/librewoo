<?php
/**
 * Plugin Name: Woo Order Complete Message
 * Description: Prints "Woo Completed" message when an order is completed.
 * Version: 1.0.0
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook into WooCommerce order status change to "processing"
add_action('woocommerce_order_status_processing', 'woo_order_complete_message');

function woo_order_complete_message($order_id) {
    // Make and object with all the order data

    $order = wc_get_order($order_id);

    $woo_client_info = new stdClass();

    $woo_client_info->customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $woo_client_info->customer_email = $order->get_billing_email();
    $woo_client_info->payment_method = $order->get_payment_method_title(); // e.g., 'PayPal'
    $woo_client_info->payment_date = $order->get_date_paid(); // Payment date
    $woo_client_info->transaction_id = $order->get_transaction_id(); // PayPal transaction ID
    $woo_client_info->customer_ip = $order->get_customer_ip_address(); // Customer IP

  
   

    error_log(
        'Payment via' . $woo_client_info->payment_method . ' was completed on ' . $woo_client_info->payment_date . ' for ' . $woo_client_info->customer_name . ' with transaction ID ' . $woo_client_info->transaction_id . ' from IP ' . $woo_client_info->customer_ip 
    );




}

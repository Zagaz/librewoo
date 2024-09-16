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
    $order = wc_get_order($order_id);
    $username = $order->get_billing_first_name();
    // Log the message to the error log
    error_log('Woo Completed for Order ID: ' . $order_id. ' for ' . $username);
    // alert the user with a message
    // Get user email

    if( $order->get_status() == 'processing' ) {
        echo '<div class="woocommerce-message">Woo Completed</div>';
    }


}

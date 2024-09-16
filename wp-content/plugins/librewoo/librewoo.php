<?php
/**
 * Plugin Name: Woo Order Complete Message
 * Description: Prints "Woo Completed" message when an order is completed and restricts cart to one product.
 * Version: 1.0.1
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook into WooCommerce order status change to "processing"
add_action('woocommerce_order_status_processing', 'woo_order_complete_message');

function woo_order_complete_message($order_id) {
    // Existing code...

    // Make an object with all the order data
    $order = wc_get_order($order_id);

    $woo_client_info = new stdClass();

    $woo_client_info->customer_name = $order->get_billing_first_name();
    $woo_client_info->customer_last_name = $order->get_billing_last_name();
    $woo_client_info->customer_email = $order->get_billing_email();
    $woo_client_info->customer_phone = $order->get_billing_phone();
    $woo_client_info->payment_method = $order->get_payment_method_title(); // e.g., 'PayPal'
    $payment_date = $order->get_date_paid();
    $woo_client_info->payment_date = $payment_date ? $payment_date->date('F j, Y @ h:i a') : 'Not paid yet'; // Format the date
    $woo_client_info->transaction_id = $order->get_transaction_id(); // PayPal transaction ID
    $woo_client_info->customer_ip = $order->get_customer_ip_address(); // Customer IP

    // Retrieve purchased items
    $woo_client_info->purchased_items = [];
    
    foreach ($order->get_items() as $item_id => $item) {
        $woo_client_info->purchased_items[] = array(
            'name'     => $item->get_name(), // Product name
            'quantity' => $item->get_quantity(), // Quantity ordered
            'total'    => wc_price($item->get_total()), // Total price (formatted with currency symbol)
        );
    }

    // Log the order details, including products purchased
    $log_message = 'Payment via ' . $woo_client_info->payment_method . 
        ' was completed on ' . $woo_client_info->payment_date . 
        ' for ' . $woo_client_info->customer_name . 
        ' ' . $woo_client_info->customer_last_name . 
        ' with transaction ID ' . $woo_client_info->transaction_id . 
        ' from IP ' . $woo_client_info->customer_ip . "\n";

    $log_message .= "Purchased products:\n";
    
    foreach ($woo_client_info->purchased_items as $item) {
        $log_message .= '- ' . $item['name'] . ': ' . $item['quantity'] . ' x ' . $item['total'] . "\n";
    }

    error_log($log_message);
}
//Once I have one product in the cart, I want to restrict the user from adding more products to the cart.

add_filter('woocommerce_add_to_cart_validation', 'restrict_cart_to_one_product', 10, 3);

function restrict_cart_to_one_product($passed, $product_id, $quantity) {
    // If the cart is not empty
    if (WC()->cart->get_cart_contents_count() > 0) {
        // Display an error message
        wc_add_notice(__('You can only have one product in your cart at a time.', 'woocommerce'), 'error');
        // Return false to prevent the product from being added to the cart
        return false;
    }
    return $passed;
}



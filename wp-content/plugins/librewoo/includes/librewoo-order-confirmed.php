
<?php 
/**
 * When an order is completed, will trigger a function to start the LibreSign pipeline.
 */
add_action('woocommerce_order_status_processing', 'librewoo_order_complete_message');

function librewoo_order_complete_message($order_id) {
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
    librewoo_trigger_pipeline($woo_client_info);
}

function librewoo_trigger_pipeline($woo_client_info){

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

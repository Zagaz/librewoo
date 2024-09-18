<?php 
// You shall not pass!
if (!defined('ABSPATH')) {
    exit;
}


class WooOrderComplete {
    public function __construct() {
        // Hook into WooCommerce order status change to "processing"
        add_action('woocommerce_order_status_processing', [$this, 'order_complete_message']);
    }

    public function order_complete_message($order_id) {

        // Ensure the order ID is valid     
        $order_id = absint($order_id);
       
        if (!$order_id) {
            return;
        }
        if (!is_numeric($order_id)){
            return;
        }

        $order = wc_get_order($order_id);
        $woo_client_info = $this->get_order_data($order, $order_id);

        // Log the order details, including products purchased
        $log_message = $this->create_log_message($woo_client_info);
        
        // Trigger LibreSign pipeline
        $this->librewoo_trigger($log_message);
        
        // Log the message
        $this->error_logger($log_message);
    }

    private function get_order_data($order, $order_id) {
        $woo_client_info = new stdClass();

        $woo_client_info->order_id = $order_id;
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
            $woo_client_info->purchased_items[] = [
                'id'       => $item->get_id(), // Product ID
                'name'     => $item->get_name(), // Product name
                'quantity' => $item->get_quantity(), // Quantity ordered
                'total'    => wc_price($item->get_total()), // Total price (formatted with currency symbol)
            ];
        }

        return $woo_client_info;
    }

    private function create_log_message($woo_client_info) {
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

        return $log_message;
    }

    private function librewoo_trigger($log_message) {
        // Here triggers the LibreSign pipeline.
        // You can use the log message or order details.
    }

    private function error_logger($log_message) {
        error_log($log_message);
    }
}



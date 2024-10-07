<?php
// You shall not pass!
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WooOrderComplete
 * 
 * Triggers LibreSign API when an order is completed
 * 
 * 
 * @since 1.0.0
 */

class WooOrderComplete
{
    public function __construct()
    {
        add_action('woocommerce_order_status_completed', [$this, 'order_complete_message']);
    }

    // Function to handle order complete message
    public function order_complete_message($order_id)
    {
        $this->get_order_data($order_id);
    }

    // Function to get order data by order ID
    public function get_order_data($order_id)
    {
        // Get the order object
        $order = wc_get_order($order_id);
        if ($order) {
            $this->librewoo_trigger($order);
        } else {
            $this->librewoo_trigger_log(false, false, false);
        }
    }

    // Trigger function to handle order data
    private function librewoo_trigger($order_data)
    {
        // Ensure $order_data is an instance of WC_Order
        if ($order_data instanceof WC_Order) {
            // Access the customer email
            $email = $order_data->get_billing_email();
            
            // Access the customer name
            $first_name = $order_data->get_billing_first_name();
            $last_name = $order_data->get_billing_last_name();
            $name = $first_name . ' ' . $last_name;
            
            // Access the purchased items
            $items = $order_data->get_items();
            $quota = '';
            if (!empty($items)) {
                $first_item = reset($items);
                $quota = $first_item->get_name();
            }

            // Log the details
            $this->librewoo_trigger_log($email, $name, $quota);
        } else {
            // Log the error
            $this->librewoo_trigger_log(false, false, false);
        }
    }

    private function librewoo_trigger_log($email, $name, $quota)
    {
        $logger = wc_get_logger();
        $context = array('source' => 'librewoo-order-confirmed');

        if ($email && $name && $quota) {
            $logger->info('LibreSign API triggered', array_merge($context, array('email' => $email, 'name' => $name, 'quota' => $quota)));
        } else {
            $logger->error('LibreSign API not triggered', array_merge($context, array('email' => $email, 'name' => $name, 'quota' => $quota)));
        }
    }
}
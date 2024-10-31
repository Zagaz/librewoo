<?php
// You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}




/**
 * Class LibreSignSubscribe
 *
 * Triggers LibreSign API when an order is completed
 *
 * @since 1.0.0
 */
class LibreSignSubscribe
{
    private $order_id;

    public function __construct()
    {

        add_action("woocommerce_order_status_active", [
            $this,
            "order_complete_message",
        ]);
        // setter getter 

    }

    public function order_complete_message($order_id)
    {
        // Ensure the order ID is valid
        $order_id = absint($order_id);

        if (!$order_id || !is_numeric($order_id)) {
            return;
        }

        // Get the order object
        $order = wc_get_order($order_id);
        $this->order_id = $order_id;
        if (!$order) {
            return;
        }

        // Get order data and trigger API
        $order_data = $this->get_order_data($order);
        $this->librewoo_trigger($order_data);
    }

    /**
     * Get order data from WooCommerce order object
     * Data: customer name, customer email, purchased items
     *
     * @param WC_Order $order
     * @return stdClass
     * @since 1.0.0
     */
    private function get_order_data($order)
    {
        $woo_client_info = new stdClass();
        $woo_client_info->customer_name = $order->get_billing_first_name();
        $woo_client_info->customer_last_name = $order->get_billing_last_name();
        $woo_client_info->customer_email = $order->get_billing_email();
        // Retrieve purchased items
        $woo_client_info->purchased_items = [];
        // Loop through each order item
        foreach ($order->get_items() as $item_id => $item) {
            $woo_client_info->purchased_items[] = [
                "id" => $item->get_id(), // Product ID
                "name" => $item->get_name(), // Product name
                "quantity" => $item->get_quantity(), // Quantity ordered
                "total" => wc_price($item->get_total()), // Total price (formatted with currency symbol)
            ];
        }

        return $woo_client_info;
    }


    /**
     * Trigger LibreSign API
     * @return void
     * @since 1.0.0
     */
    private function librewoo_trigger($order_data)
    {
        $is_update = $this->is_update() ? "true" : "false";

       


        // Convert stdClass to array
        $order_data = get_object_vars($order_data);
        $email = $order_data["customer_email"];
        $display_name = $order_data["customer_name"] . " " . $order_data["customer_last_name"];
        $quota = $order_data["purchased_items"][0]["name"];
        $apps = $authorization = "Placeholder";


        // Trigger API NEW USER

        // Trigger API Update


  

    }

    private function is_update(){
        // check subscriptions
        $user_id = get_current_user_id();
        $subscriptions = wcs_get_users_subscriptions($user_id);
        if(count($subscriptions) > 0){
            return true;
        } else {
            return false;
        }
        
    }




}

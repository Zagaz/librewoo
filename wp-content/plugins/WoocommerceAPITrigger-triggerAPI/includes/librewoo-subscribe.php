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

        add_action("woocommerce_order_status_processing", [
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
        // Convert stdClass to array
        $order_data = get_object_vars($order_data);
        $email = $order_data["customer_email"];
        $display_name = $order_data["customer_name"] . " " . $order_data["customer_last_name"];
        $quota = $order_data["purchased_items"][0]["name"];
        $apps = $authorization = "Placeholder";

        // check if the user is already subscribed

        $user_id = get_current_user_id();
        $cart_item = WC()->cart->get_cart();

        $is_subscription_active = new LibreSignSubscruptionStatusChecker();
        $check_subs = $is_subscription_active->check_subscription_status( $user_id, reset( $cart_item )['product_id'] );

        $is_upgradable = false;
        //Compare the product in the cart and all the subscriptions of the user
        // if there is a subscription with a product id less than the product in the cart, then the user is upgradable

        for ( $i = 0; $i < count( $check_subs ); $i++ ) {
            if ( reset( $cart_item )['product_id'] > $check_subs[$i]['product_id'] ) {
                $logger1 = wc_get_logger();
                $context = array( 'source' => 'Is_upgradable_' );
                $logger1->info( 'User is upgradable', $context );
                $is_upgradable = true;
                break;
            } else{
                $logger2 = wc_get_logger();
                $context = array( 'source' => 'Is_upgradable_' );
                $logger2->info( 'User is not upgradable', $context );
            }
        }





    
        



  
        $subscribe = new LibreSignEndpoint();
        $subscribe->subscribe_libreSign($email, $display_name, $quota, $apps, $authorization);


        
    }
}

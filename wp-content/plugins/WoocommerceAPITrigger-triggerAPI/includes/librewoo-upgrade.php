<?php
// You shall not pass!
if (! defined('ABSPATH')) {
    exit;
}

/**
 * If a user already has any subscription, he will purchase thea higher-tier product and cancel 
 * all  previous subscription.
 */
class LibreSignUpgrade
{
    public function __construct()
    {
        
        add_action('woocommerce_order_status_completed', array($this, 'cancel_previous_subscriptions'));
    }

    public function cancel_previous_subscriptions($order_id)
    {
        // error log for debugging
        



        // erroe log for debugging
      
        
        // $order = wc_get_order($order_id);
        // $user_id = $order->get_user_id();
        // $items = $order->get_items();
        // $product_id = $items[0]->get_product_id();

        // $is_subscription_active = new LibreSignSubscruptionStatusChecker();
        // $check_subs = $is_subscription_active->check_subscription_status($user_id, $product_id);

        // for ($i = 0; $i < count($check_subs); $i++) {
        //     $subscription_id = $check_subs[$i]['subscription_id'];
        //     $subscription = wcs_get_subscription($subscription_id);
        //     $subscription->cancel_order($order_id);
        // }
    }

    public function upgrade_subscription()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id        = get_current_user_id();
        $cart_items     = WC()->cart->get_cart();
        $cart_product_id = reset($cart_items)['product_id'];

        // Check if the user has any active subscription for the same product. 
        $is_subscription_active = new LibreSignSubscruptionStatusChecker();
        $check_subs = $is_subscription_active->check_subscription_status($user_id, $cart_product_id);

        // If any subscription product ID is lesser than the cart product ID, allow the purchase.
        for ( $i = 0; $i < count($check_subs); $i++ ) {
            if ($cart_product_id > $check_subs[$i]['product_id']) {
                      
                // change add_to_cart button text to "Upgrade"
                add_filter('woocommerce_product_add_to_cart_text', array($this, 'custom_add_to_cart_button_text'));
               break;
            }
        }
    }

    public function custom_add_to_cart_button_text()
    {
        return 'Upgrade';
    }
}
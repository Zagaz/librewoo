<?php
// You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}




/**
 * Class BlockPurchaseSameSubscription
 *
 * Only one subscription can be purchased for a user
 *
 * @since 1.0.0
 */

class LibreSignBlockPurchaseSameSubscription
{
    private $order_id;

    public function __construct()
    {

        add_action('woocommerce_checkout_order_processed', [$this,'block_purchase_same_subscription'], 10, 1);
    }

    public function block_purchase_same_subscription($order_id)
    {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $items = $order->get_items();
        $product_id = $items[0]->get_product_id();
        $product = wc_get_product($product_id);
        $product_type = $product->get_type();
        $subscriptions = wcs_get_users_subscriptions($user_id);
        $subscription_product_id = 0;
        $subscription_id = 0;
        foreach ($subscriptions as $subscription) {
            $subscription_product_id = $subscription->get_product_id();
            $subscription_id = $subscription->get_id();
        }
        if ($product_type == 'subscription' && $subscription_product_id == $product_id) {
            $order->update_status('failed');
            $order->add_order_note('You already have an active subscription for this product');
            $subscription = wcs_get_subscription($subscription_id);
            $subscription->update_status('active');
        }
    }

}
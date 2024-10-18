<?php

if (!defined('ABSPATH')) {
    exit;
}

class LibreSignSubscruptionStatusChecker
{
    public function __construct()
    {
        $this->test();
        
    }

    public function test(){
        return "test";
    }

    public function check_subscription_status($user_id, $cart_product_id)
    {
        return $cart_product_id;
        exit();
        
        
        $subscriptions = wcs_get_users_subscriptions($user_id);
        foreach ($subscriptions as $sub) {
            $order = wc_get_order($sub->get_parent_id());
            $items = $order->get_items();
            foreach ($items as $item) {
                if ($item->get_product_id() == $product_id) {
                    return $sub->get_status();
                }
            }
        }
        return false;
    }

}


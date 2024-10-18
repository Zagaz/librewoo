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
        $subscriptions = wcs_get_users_subscriptions($user_id);
        $subscription_data = array();

          foreach ($subscriptions as $sub) {
      $order = wc_get_order($sub->get_parent_id());
      $items = $order->get_items();
      foreach ($items as $item) {
        $data = $item->get_data();
        $subscription_data['product_id'] = $data['product_id'];
      }
      $subscription_data[] = array(
        'subscription_id' => $sub->get_id(),
        'parent_id' => $sub->get_parent_id(),
        'status'    => $sub->get_status(),
        'product_id' => $subscription_data['product_id'],

      );
    }
    

      



        var_dump($subscription_data);

        return $subscription_data;



     
    }

}


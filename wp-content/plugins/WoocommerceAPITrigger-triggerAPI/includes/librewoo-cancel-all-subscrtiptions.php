<?php 

//You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}

class LibreSignCancelAllSubscriptions
{
    public function __construct()
    {
        // add_action('woocommerce_thankyou', array($this, 'cancel_all_subscriptions'), 10, 1);
        


        
    }

    public function cancel_all_subscriptions($order_id)
    {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $subscriptions = wcs_get_users_subscriptions($user_id);
        foreach ($subscriptions as $subscription) {
            $subscription->cancel_order();
        }
        
    }
   

  

  
}
<?php 

//You shall not pass
if (!defined("ABSPATH")) {
  exit();
}


/**
 * It checks if the user has already purchased the same subscription product
 * and if it's active. If so, returns true.
 */
class LibreSignCheckSubscription
{
  public $has_subscription ;


  public function __construct()
  {
    $this->set_has_subscription(false);
  }

        
  function set_has_subscription($data)
  {
    $this->has_subscription = $data;
  }
   
  /**
   * It checks if the user has already purchased the same subscription product and if it's active
   * if so, return true
   * 
   * @return mixed
   */
  function check_subscription()
    {
      if(is_user_logged_in())
      {
        $user_id = get_current_user_id();
        $subscriptions = wcs_get_users_subscriptions($user_id);
        $subs =  $subscriptions;
        $parents = [];
        foreach ($subs as $sub) {
          $parents[] = $sub->get_parent_id();
        }
        $orders = [];
        for ($i = 0; $i < count($parents); $i++) {

          // get order data by subscription id
          $order = wc_get_order($parents[$i]);
          $orders[] = $order;
        }
        json_encode($orders, JSON_PRETTY_PRINT);
        $items_list = [];
        for ($i = 0; $i < count($orders); $i++) {
          // append the items to the items_list array
          $items = $orders[$i]->get_items();
          $items_list[] = $items;
        }
        $product_id_cart = 0;
        foreach (WC()->cart->get_cart() as $cart_item) {
          $product_id_cart = $cart_item['product_id'];
          break;
        }
    

        // loop the $item_list, if  $product_id_cart ==

        for ($i = 0; $i < count($items_list); $i++) {
          foreach ($items_list[$i] as $item) {
            $data = $item->get_data();
            if (isset($data['product_id'])) {
              if ($data['product_id'] == $product_id_cart) {
          
                $this->set_has_subscription(true);

                                  
                  
                }
                break 2; // Exit both loops
              }
            }
          }

          return $this->has_subscription;
        }
      }
    }
  

                
     
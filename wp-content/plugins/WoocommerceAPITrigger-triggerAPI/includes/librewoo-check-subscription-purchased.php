<?php 

class LibreSignCheckSubscriptionPurchased
{
        
    function check_purchase_same_subscription()
    {
  
      if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $subscriptions = wcs_get_users_subscriptions($user_id);
  
        // convert the object to an array Preetty print
  
        $subs =  $subscriptions;
        // Loop thru all  subscriptions and get the parent id
        $parents = [];
  
        foreach ($subs as $sub) {
          $parents[] = $sub->get_parent_id();
        }
  
  
  
        // order parent
  
        $orders = [];
  
        for ($i = 0; $i < count($parents); $i++) {
  
          // get order data by subscription id
          $order = wc_get_order($parents[$i]);
          $orders[] = $order;
        }
        // for each order id, get the order data
  
        // convert the object to an array Preetty print
  
        json_encode($orders, JSON_PRETTY_PRINT);
  
        // Loop thru all  orders and get 
        // print_r($orders);
  
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
  
        $error_printed = false; // Flag to control error printing
  
        for ($i = 0; $i < count($items_list); $i++) {
          foreach ($items_list[$i] as $item) {
            $data = $item->get_data();
            if (isset($data['product_id'])) {
              if ($data['product_id'] == $product_id_cart) {
                if (!$error_printed) {
                  wc_add_notice($product_id_cart . " You already have this subscription", 'error');
      
                  $error_printed = true; // Set the flag to true after printing the error
                     // Temporarily remove the add-to-cart message
                     add_filter('wc_add_to_cart_message_html', function($message, $products) {
                      return ''; // Return an empty string to remove the message
                  }, 10, 2);
  
                  // remove this product from the cart
                  WC()->cart->remove_cart_item(key(WC()->cart->get_cart()));
                  
                }
                break 2; // Exit both loops
              }
            }
          }
        }
        
      }
    }
    }
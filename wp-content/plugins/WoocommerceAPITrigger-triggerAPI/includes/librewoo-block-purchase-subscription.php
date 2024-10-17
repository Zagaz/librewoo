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
  // Woocommerce - I want to block the purchase of the same subscription product for a client.

  // if not logged in, check if the same email (client) already has the same subscription product in the cart. Id yes, pŕint messagem: "You already have this subscription.

  public function __construct()
  {
    // add action when the user clicks on the add to cart button
    add_action(
      'woocommerce_add_to_cart',

      array($this, 'block_purchase_same_subscription'),
      10,
      6
    );
    
  }

  function block_purchase_same_subscriptionOLD(){
    $has_subscription = new LibreSignCheckSubscription();
    echo "<pre>";

    var_dump($has_subscription);
    $test = $has_subscription->check_subscription() !== null;
    echo "</pre>";

    if (    $has_subscription ) {
      $warning = "You already have this subscri|tion";

      WC()->cart->remove_cart_item(key(WC()->cart->get_cart()));
      // woocommerce error message
      wc_add_notice((string) $warning, 'error');
      // Temporarily remove the add-to-cart message
      add_filter('wc_add_to_cart_message_html', function($message, $products) {
        return ''; // Return an empty string to remove the message
    }, 10, 2);
    }
   

  
  
    // if( $has_subscription ){
      
    //   $warning = "You already have this subscription";

    //   WC()->cart->remove_cart_item(key(WC()->cart->get_cart()));
    //   // woocommerce error message
    //   wc_add_notice((string) $warning, 'error');
    //   // Temporarily remove the add-to-cart message
    //   add_filter('wc_add_to_cart_message_html', function($message, $products) {
    //     return ''; // Return an empty string to remove the message
    // }, 10, 2);
    // }


    
    // $error_printed = false; // Flag to control error printing


    

    

  }





  function block_purchase_same_subscription()
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

      $parent_orders = [];
      // get the order data by subscription id
      for ($i = 0; $i < count($parents); $i++) {
        $order = wc_get_order($parents[$i]);
        $parent_orders[] = $order;
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

      // Loop through all orders and get items
      $items_list = [];
      for ($i = 0; $i < count($orders); $i++) {
          // Ensure the order is a valid object
          if (is_object($orders[$i])) {
              // Append the items to the items_list array
              $items = $orders[$i]->get_items();
              $items_list[] = $items;
          } else {
              error_log("Invalid order object at index $i"); // Debugging
          }
      }
      echo "<pre>";
      // var_dump($items_list[0]);
      echo "</pre>";

      $product_id_cart = 0;
      foreach (WC()->cart->get_cart() as $cart_item) {
          $product_id_cart = $cart_item['product_id'];
          break;
      }

      $error_printed = false; // Flag to control error printing

      for ($i = 0; $i < count($items_list); $i++) {
          foreach ($items_list[$i] as $item) {
              $data = $item->get_data();
              echo '<pre>';
              print_r($data);
              echo '</pre>';
              // clear $data
            
              
              // Print the $data array for debugging
              error_log(print_r($data, true));

              if (isset($data['product_id'])) {
                  if ($data['product_id'] == $product_id_cart) {
                    // check if this product status is active
                 

                    
                      if (!$error_printed) {
                          wc_add_notice($product_id_cart . " You already have this subscription", 'error');
                          $error_printed = true; // Set the flag to true after printing the error
                          // Temporarily remove the add-to-cart message
                          add_filter('wc_add_to_cart_message_html', function($message, $products) {
                              return ''; // Return an empty string to remove the message
                          }, 10, 2);
                      }
                      break 2; // Exit both loops
                  }
              }
          }
      }
      
    }
  }
}

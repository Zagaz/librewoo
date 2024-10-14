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
  add_action('woocommerce_add_to_cart',
  
  array($this, 'block_purchase_same_subscription'), 10, 6);
  }


 
  
  function block_purchase_same_subscription() {
    echo "<pre>";
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $subscriptions = wcs_get_users_subscriptions( $user_id );
        
        // convert the object to an array Preetty print
        
        $subs =  $subscriptions;
        // Loop thru all  subscriptions and get the parent id
        $parents = [];

        foreach ( $subs as $sub ) {
          $parents[] = $sub->get_parent_id();
        }
        echo "Subscription Parents";
        print_r($parents);
        


      // order parent

      $orders = [];

        for ($i = 0 ; $i < count ($parents); $i++) {
         
         // get order data by subscription id
          $order = wc_get_order( $parents[$i] );
          $orders[] = $order;    

          }
          // for each order id, get the order data
          echo "Orders <br>";
          // convert the object to an array Preetty print

          json_encode($orders,JSON_PRETTY_PRINT);

          // Loop thru all  orders and get 
          // print_r($orders);

          $items_list = [];
          for ($i = 0 ; $i < count ($orders); $i++) {
// append the items to the items_list array
            $items = $orders[$i]->get_items();
            $items_list[] = $items;
          }
          /**
           Array
(
    [198] => WC_Order_Item_Product Object
        (
            [id:protected] => 198
            [data:protected] => Array
                (
                    [order_id] => 253
                    [name] => Pro
                    [product_id] => 61
                    [variation_id] => 0
           */


        //    foreach ($items_list as $item_group) {
        //     foreach ($item_group as $item) {
        //         $data = $item->get_data();
        //         if (isset($data['product_id'])) {
        //            if ($data['product_id'] == 61) {
        //                echo 'Product ID found.';
        //            } 
        //         } 
        //     }
        // }
        // convert it into  FOR loop
         for ($i = 0 ; $i < count ($items_list); $i++) {
            foreach ($items_list[$i] as $item) {
                $data = $item->get_data();
                if (isset($data['product_id'])) {
                   if ($data['product_id'] == 61) {
                       echo 'Product ID found||.';
                   } 
                } 
            }
        }
           
          exit();


        

          foreach ($orders as $order) {
            $order = wc_get_order( $order );
            $items = $order->get_items();
            foreach ( $items as $item ) {
                $product_id = $item->get_id();
                echo  $product_id;
                $product_name = $item->get_name();
                echo $product_name;
                break;
            }
          }



          echo "Orders";
          print_r($orders);
          // print_r($items[158]->get_name());
          echo "<pre>";
          exit();
          
   

      








        // $subscription_parent = $subscriptions[250]->get_parent_id();
        // // get order data by subscription id 
        // $order = wc_get_order( $subscriptions[250]->get_parent_id() );
        // // On this order, get the product id
        // $items = $order->get_items();
        // foreach ( $items as $item ) {
        //     $product_id = $item->get_id();
        //     $product_name = $item->get_name();
        //     break;
        // }

        
   
        
        // get the product id of the subscription
        $product_id = 0;
        foreach ( $subscriptions as $subscription ) {
            $product_id = $subscription->get_id();
            break;
        }
        
        // get the product id of the product being added to the cart
        $product_id_cart = 0;
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $product_id_cart = $cart_item['product_id'];
            break;
        }
        
        // if the product being added to the cart is the same as the subscription product
        if ( $product_id == $product_id_cart ) {
            wc_add_notice( 'You already have this subscription.', 'error' );
            WC()->cart->empty_cart();
        }


       
    }
}









}
   
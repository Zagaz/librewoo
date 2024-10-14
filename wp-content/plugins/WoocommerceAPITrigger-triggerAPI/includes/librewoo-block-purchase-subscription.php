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
       


      // order parent

      $orders = [];

        for ($i = 0 ; $i < count ($parents); $i++) {
         
         // get order data by subscription id
          $order = wc_get_order( $parents[$i] );
          $orders[] = $order;    

          }
          // for each order id, get the order data

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


$product_id_cart = 0;
foreach ( WC()->cart->get_cart() as $cart_item ) {
    $product_id_cart = $cart_item['product_id'];
    break;
}

         for ($i = 0 ; $i < count ($items_list); $i++) {
            foreach ($items_list[$i] as $item) {
                $data = $item->get_data();
                if (isset($data['product_id'])) {
                   if ($data['product_id'] == $product_id_cart ) {
                    wc_add_notice( $product_id_cart . "You already have this subscription"  , 'error' );
                  
                   } 
                } 
            }
        }

        // fix the code above.
        // As soon as the first mactch found, exit the loop.




           
         


        




          
   

      








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

        
   

       
    }
}









}
   
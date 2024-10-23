<?php 
// You shall not pass!
if (! defined('ABSPATH')) {
  exit;
}

// class LibreSignSubscriptionChecker {

// private $user_id;
// private $subscription_data = array();
// private $cart_product_id;
// private $is_active_subscription = false;

// public function __construct() {
    
//         $this->user_id = get_current_user_id();
//         $this->get_user_subscriptions();
//         $this->get_cart_items();

// }

// private function get_user_subscriptions() {
//     // Get all subscriptions of the user.
//     $subscriptions = wcs_get_users_subscriptions( $this->user_id );

//     // Filter out the parent_id and status from each subscription.
//     foreach ( $subscriptions as $sub ) {
//         $order = wc_get_order( $sub->get_parent_id() );
//         $items = $order->get_items();
//         foreach ( $items as $item ) {
//             $data = $item->get_data();
//             $product_id = $data['product_id'];
//         }

//         $this->subscription_data[] = array(
//             'subscription_id' => $sub->get_id(),
//             'parent_id'       => $sub->get_parent_id(),
//             'status'          => $sub->get_status(),
//             'product_id'      => $product_id,
//         );
//     }
// }

// private function get_cart_items() {
//     // Get the cart items.
//     $cart_items = WC()->cart->get_cart();
//     $this->cart_product_id = reset( $cart_items )['product_id'];
// }

// public function is_subscription_active_for_cart_product() {
//     foreach ( $this->subscription_data as $subscription ) {
//         if ( $this->cart_product_id == $subscription['product_id'] && 'active' === $subscription['status'] ) {
//             return true; // Returns true if a matching active subscription is found.
//         }
//     }

//     return false; // Returns false if no matching active subscription is found.
// }
// }


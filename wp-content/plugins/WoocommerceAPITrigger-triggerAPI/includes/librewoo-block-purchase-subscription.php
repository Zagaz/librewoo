<?php
// You shall not pass!
if (! defined('ABSPATH')) {
  exit;
}

/**
 * Class LibreSignBlockPurchaseSameSubscription
 *
 * Only one subscription can be purchased for a user.
 * If a user has any active subscription for the same product,
 * then block the purchase.
 *
 * @since 1.0.0
 */
class LibreSignBlockPurchaseSameSubscription
{

  /**
   * Constructor
   */
  public function __construct()
  {
    // Add action when the user clicks on the add to cart button
    add_action(
      'woocommerce_add_to_cart',
      array($this, 'block_purchase_same_subscription'),
      10,
      6
    );
  }

  /**
   * Block purchase if the user already has an active subscription for the same product.
   */
  public function block_purchase_same_subscription()
  {
  
    if (is_user_logged_in()) {

      $user_id      = get_current_user_id();
      // get all subscriptions of the user
      $subscriptions = wcs_get_users_subscriptions($user_id);
      // var_dump($subscriptions);

      // On $subscription, filter out the parent_id and status
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

     // get the cart items
      $cart_items = WC()->cart->get_cart();
      
      $cart_product_id = (reset($cart_items)['product_id']);
      for($i = 0; $i < count($subscription_data); $i++) {
        if($cart_product_id == $subscription_data[$i]['product_id'] && $subscription_data[$i]['status'] == 'active') {
          $is_active_subscription = true;
          break;
        }
      }

      if ($is_active_subscription) {
        wc_add_notice( 'You already subscribed this product and it\'s active', 'error');
        add_filter('wc_add_to_cart_message_html', '__return_empty_string', 10, 2);
      }   
    }
  }
}

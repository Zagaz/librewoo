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
    echo '<pre>';
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
          'product_id' => $subscription_data['product_id']
        );
        
      }



     






      // The Subscriptions has a parent_id which is the order_id -1 e.g. 1234-1
      $parents = [];
      foreach ($subscriptions as $sub) {
        $parents[] = $sub->get_parent_id();
      }

      $orders = array();
      // Get order data by subscription parent ID
      foreach ($parents as $parent_id) {
        $order = wc_get_order($parent_id);
        if (is_object($order)) {
          $orders[] = $order;
        }
      }

      // Using the parend_id to get the order data

      // get data by subscription parent ID

      // var_dump($subscription_data);






      // Loop through all orders and get items
      $items_list = array();
      foreach ($orders as $order) {
        $items_list[] = $order->get_items();
      }




      $product_id_cart = 0;
      foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id_cart = $cart_item['product_id'];
        break;
      }

      $error_printed = false; // Flag to control error printing

      foreach ($items_list as $items) {
        foreach ($items as $item) {
          $data      = $item->get_data();
          $order_id  = $item->get_order_id();
          $subscription = wcs_get_subscriptions_for_order($order_id);
          $product_id = $data['product_id'];

          $is_subscription = false;
          $is_active       = false;

          if (isset($data['product_id']) && $data['product_id'] == $product_id_cart && $subscription) {
            $subscription_status = reset($subscription)->get_status();

            if ('active' === $subscription_status) {
              $is_subscription = true;
              $is_active       = true;
              if (! $error_printed) {
                wc_add_notice(esc_html($product_id_cart) . ' You already have an active subscription for this product', 'error');
                $error_printed = true; // Set the flag to true after printing the error
                // Temporarily remove the add-to-cart message
                add_filter('wc_add_to_cart_message_html', '__return_empty_string', 10, 2);
              }
              break 2; // Exit both loops
            }
          }
        }
      }

      // Debugging outputs (can be removed in production)
      if ($is_subscription && $is_active) {
        echo 'Test<br>';
        echo 'Is subscription BOOL: ' . esc_html($is_subscription) . '<br>';
        echo 'Is active BOOL: ' . esc_html($is_active) . '<br>';
        echo 'Product id: ' . esc_html($product_id) . '<br>';
        echo 'Subscription status: ' . esc_html($subscription_status) . '<br>';
      }
    }
  }
}

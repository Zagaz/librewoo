<?php
// You shall not pass!
if (! defined('ABSPATH')) {
  exit;
}

include LW_PLUGIN_DIR . 'includes/librewoo-logger.php';

/**
 * Class LibreSignBlockPurchaseSameSubscription
 *
 * Only one subscription can be purchased for a user.
 * If a user has any active subscription for the same product,
 * then block the purchase.
 *
 * @since 1.0.0
 */
class LibreSignBlockPurchaseSameSubscriptionAndStatus
{

  /**
   * Constructor
   */
  public function __construct()
  {
    // Add action when the user clicks on the add to cart button
    add_action(
      'woocommerce_add_to_cart',
      array($this, 'block_purchase_same_subscription_and_active'),
      10,
      6
    );
  }

  /**
   * Block purchase if the user already has an active subscription for the same product.
   */
  public function block_purchase_same_subscription_and_active()
  {
    if (! is_user_logged_in()) {
      return;
    }

    $user_id        = get_current_user_id();
    $cart_items     = WC()->cart->get_cart();
    $cart_product_id = reset($cart_items)['product_id'];

    // Check if the user has any active subscription for the same product. 
    $is_subscription_active = new LibreSignSubscruptionStatusChecker();
    $check_subs = $is_subscription_active->check_subscription_status($user_id, $cart_product_id);

    // If any subscription is active for the same product, block the purchase.
    for ($i = 0; $i < count($check_subs); $i++) {
      if ($cart_product_id == $check_subs[$i]['product_id'] && $check_subs[$i]['status'] == 'active') {
        $this_status = $check_subs[$i]['status'];

        // This message will be displayed to the user when the purchase is blocked.
        $message = '';
        $message .= 'Sorry... You can\'t subscribe to this product because you already have it and it\'s <strong>';
        $message .= '<span style="color: green; text-transform: Capitalize;">';
        // This part of the message will be replaced with the subscription status.
        $message .= $this_status;
        $message .= '</span>';
        $message .= '</strong> Please consider an ';
        $message .= '<strong><a href="#">';
        $message .= 'upgrade</a> </strong> ';
        $message .= 'or ';
        $message .= '<strong><a href="#">contact support</a></strong> if you have any questions.';

        $this->remove_cart_item_now($cart_items);
        $this->add_wc_add_notice($message, 'notice');
        $this-> delete_default_notice();

        $log = new LibreSignLogger();
        $log -> librewoo_log(
          'Client #'. $user_id .' can not subscribe product #'. $cart_product_id .', already has '. $this_status .' subscription.', 
          'info');

      }
    }
  }

  function add_wc_add_notice($message, $notice_type = 'error')
  {
    wc_add_notice($message, $notice_type);
  }
  function delete_default_notice()
  {
    // Avoids conflicts with the default message "Product has been added to your cart"
    add_filter('wc_add_to_cart_message_html', '__return_empty_string', 10, 2);
  }
  

  function remove_cart_item_now($cart_items)
  {
    WC()->cart->remove_cart_item(key($cart_items));
  }
}

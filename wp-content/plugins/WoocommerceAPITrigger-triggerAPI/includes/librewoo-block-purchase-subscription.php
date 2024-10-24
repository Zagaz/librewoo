<?php
// You shall not pass!
if ( ! defined( 'ABSPATH' ) ) {
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
class LibreSignBlockPurchaseSameSubscriptionAndStatus
{

    /**
     * Constructor
     */
    public function __construct() {
        // Add action when the user clicks on the add to cart button
        add_action(
            'woocommerce_add_to_cart',
            array( $this, 'block_purchase_same_subscription_and_active' ),
            10,
            6
        );
    }
        /**
     * Block purchase if the user already has an active subscription for the same product.
     */
    public function block_purchase_same_subscription_and_active() 
    {
      if ( ! is_user_logged_in() ) {
          return;
      }

      $user_id        = get_current_user_id();
      $cart_items     = WC()->cart->get_cart();
      $cart_product_id = reset( $cart_items )['product_id'];

      // Check if the user has any active subscription for the same product. 
      $is_subscription_active = new LibreSignSubscruptionStatusChecker();
      $check_subs = $is_subscription_active->check_subscription_status( $user_id, $cart_product_id );

      // If any subscription is active for the same product, block the purchase.
      for ( $i = 0; $i < count( $check_subs ); $i++ ) {
          if ( $cart_product_id == $check_subs[ $i ]['product_id'] && $check_subs[ $i ]['status'] == 'active' ) {
              wc_add_notice( 
                  'You can\'t subscribe to this product because you already have it and it\'s <strong> 
                  <span style="color: green; text-transform: Capitalize;">
                  ' . $check_subs[ $i ]['status'] . '. 
                  </span>
                  </strong> Please consider an 
                  <strong><a href="#">
                  upgrade</a> </strong> 
                  or 
                  <strong><a href="#">contact support</a></strong> if you have any questions.', 
                  'error' 
              );
              add_filter( 'wc_add_to_cart_message_html', '__return_empty_string', 10, 2 );
              WC()->cart->remove_cart_item( key( $cart_items ) );
          }
      }
  }




}

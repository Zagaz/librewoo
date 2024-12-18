<?php
// You shall not pass!
if (! defined('ABSPATH')) {
    exit;
}
/*
    * Class LibreSignAvoidDowngrade
    *
    * Avoid downgrading the subscription.
    * If a user has an active subscription for a higher-tier product,
    * then block the purchase of a lower-tier product.
    *
    * @since 1.0.0
    */
class LibreSignAvoidDowngrade
{

    public function __construct()
    {
        add_action('woocommerce_add_to_cart', array($this, 'avoid_downgrade'));
    }

    public function avoid_downgrade()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id        = get_current_user_id();
        $cart_items     = WC()->cart->get_cart();
        $cart_product_id = reset($cart_items)['product_id'];

        // Check if the user has any active subscription for the same product. 
        $is_subscription_active = new LibreSignSubscruptionStatusChecker();
        $check_subs = $is_subscription_active->check_subscription_status($user_id, $cart_product_id);

        // If any subscription product ID is lesser than the cart product ID, block the purchase.
        for ( $i = 0; $i < count($check_subs); $i++ ) {
            if ($cart_product_id < $check_subs[$i]['product_id']) {
                wc_add_notice(
                    'You already have a subscription to a higher-tier product with the status
                    <strong><span style="color: green; text-transform: Capitalize;">' . $check_subs[$i]['status'] . '.</span></strong>so downgrades to this plan are not available. If you need assistance or have any questions, please contact<strong><a href="#">contact support</a></strong> if you have any questions.',
                    'error'
                );
             
                wc_empty_cart();
                wp_redirect(wc_get_cart_url());

                // change add_to_cart button text to "Cannot downgrade"


                exit;
            }
        }
    }

   public function custom_add_to_cart_button_text()
    {
        return 'Cannot downgrade';
    }

}

            
            
    
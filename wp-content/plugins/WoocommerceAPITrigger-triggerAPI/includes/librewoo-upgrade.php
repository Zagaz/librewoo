<?php
// You shall not pass!
if (! defined('ABSPATH')) {
    exit;
}

/**
 * If a user already has any subscription, he will purchase thea higher-tier product and cancel 
 * all  previous subscription.
 */
class LibreSignUpgrade
{
    public function __construct()
    {
        add_action('woocommerce_add_to_cart', array($this, 'upgrade_subscription'));
    }

    public function upgrade_subscription()
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

        // If any subscription product ID is lesser than the cart product ID, allow the purchase.
        for ( $i = 0; $i < count($check_subs); $i++ ) {
            if ($cart_product_id > $check_subs[$i]['product_id']) {
                wc_add_notice(
                    'You already have a subscription to a lower-tier product with the status
                    <strong><span style="color: green; text-transform: Capitalize;">' . $check_subs[$i]['status'] . '.</span></strong>so upgrades to this plan are available. If you need assistance or have any questions, please contact<strong><a href="#">contact support</a></strong> if you have any questions.',
                    'success'
                );
             
                // change add_to_cart button text to "Cannot upgrade"
                add_filter('woocommerce_product_add_to_cart_text', array($this, 'custom_add_to_cart_button_text'));
               break;
            }
        }
    }

    public function custom_add_to_cart_button_text()
    {
        return 'Upgrade';
    }
}
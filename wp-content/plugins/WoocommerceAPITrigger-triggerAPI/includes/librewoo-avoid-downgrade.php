<?php
// You shall not pass!
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include the required class file


/**
 * Class LibreSignAvoidDowngrade
 *
 * Avoid downgrading the subscription.
 * If a user has an active subscription for a higher-tier product,
 * block the purchase of a lower-tier product.
 *
 * @since 1.0.0
 */
class LibreSignAvoidDowngrade {

    public function __construct() {
        add_action( 'woocommerce_add_to_cart', array( $this, 'avoid_downgrade' ) );
    }

    public function avoid_downgrade() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $user_id         = get_current_user_id();
        $cart_items      = WC()->cart->get_cart();
        $cart_product_id = reset( $cart_items )['product_id'];

        // Check if the user has any active subscription for the same product.
        $is_subscription_active = new LibreSignSubscruptionStatusChecker();
        $check_subs             = $is_subscription_active->check_subscription_status( $user_id, $cart_product_id );
        
        foreach ( $check_subs as $sub ) {
            if ( $cart_product_id < $sub['product_id'] ) {
                wc_add_notice(
                    sprintf(
                        'You already have a subscription to a higher-tier product with the status
                        <strong><span style="color: green; text-transform: capitalize;">%s</span></strong>. 
                        So downgrades to this plan are not available. If you need assistance or have any questions, 
                        please <strong><a href="#">contact support</a></strong>.',
                        esc_html( $sub['status'] )
                    ),
                    'error'
                );
                wc_empty_cart();
                wp_redirect( wc_get_cart_url() );
                exit;
            }
        }
    }
}

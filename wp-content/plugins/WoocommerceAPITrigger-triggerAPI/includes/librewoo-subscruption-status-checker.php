<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LibreSignSubscruptionStatusChecker {

    /**
     * Returns an object with clients subscriptions data to be used in other classes.
     *
     * @param int $user_id
     * @param int $cart_product_id
     *
     * @return array
     */
    public function check_subscription_status( $user_id, $cart_product_id ) {

        $subscriptions     = wcs_get_users_subscriptions( $user_id );
        $subscription_data = array();

        // Filter $subscriptions if any subscription is active.
        foreach ( $subscriptions as $sub ) {
            $order = wc_get_order( $sub->get_parent_id() );
            $items = $order->get_items();

            foreach ( $items as $item ) {
                $data              = $item->get_data();
                $temp_item['item'] = $data['product_id'];
            }

            $subscription_data[] = array(
                'subscription_id'           => $sub->get_id(),
                'parent_id'                 => $sub->get_parent_id(),
                'status'                    => $sub->get_status(),
                'product_id'                => $temp_item['item'],
                'subscription_date_expires' => $sub->get_date( 'end' ),
            );
        }

        return $subscription_data;
    }
}

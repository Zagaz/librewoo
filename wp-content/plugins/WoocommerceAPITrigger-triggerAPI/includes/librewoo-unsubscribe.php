<?php
// You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}






/**
 * Class LibreSignUnubscribe
 *
 * Triggers LibreSign API when subscriprion is cancelled
 *
 * @since 1.0.0
 */
class LibreSignUnsubscribe
{
    private $order_id;

    public function __construct()
    {

        add_action('woocommerce_subscription_status_cancelled', 'librewoo_unsubscribe', 10, 1);
        
    }

    public function librewoo_unsubscribe($subscription)
    {
        $unsubscribe = new LibreSignEndpoint();
        $unsubscribe->unsubscribe_libreSign($subscription);

    }

}
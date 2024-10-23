<?php
// You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}






/**
 * Class LibreSignUnubscribe
 *
 * Triggers LibreSign API when payment failed
 *
 * @since 1.0.0
 */
class LibreSignPaymentFailed
{
    private $order_id;

    public function __construct()
    {

        add_action('woocommerce_subscription_status_expired' , [$this,'librewoo_payment_failed'], 10, 1);
    }

    public function librewoo_payment_failed($subscription)
    {
        $payment_failed = new LibreSignEndpoint();
        $payment_failed->payment_failed_libreSign($subscription);

    }

}
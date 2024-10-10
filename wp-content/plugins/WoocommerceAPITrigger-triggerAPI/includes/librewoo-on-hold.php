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
class LibreSignOnHold
{
    private $order_id;

    public function __construct()
    {

        add_action('woocommerce_subscription_status_on-hold' , [$this,'librewoo_on_hold'], 10, 1);
    }

    public function librewoo_on_hold($subscription)
    {
        $on_hold = new LibreSignEndpoint();
        $on_hold->hold_libreSign($subscription);

    }

}
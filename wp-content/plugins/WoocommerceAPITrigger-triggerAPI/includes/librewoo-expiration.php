<?php
// You shall not pass!
if (!defined("ABSPATH")) {
    exit();
}






/**
 * Class LibreSignUnubscribe
 *
 * Triggers LibreSign API when subscriprion is exprired
 *
 * @since 1.0.0
 */
class LibreSignExpiration
{
    private $order_id;

    public function __construct()
    {

        add_action('woocommerce_subscription_status_expired' , [$this,'librewoo_expiration'], 10, 1);
        
    }

    public function librewoo_expiration($subscription)
    {
        $expiration = new LibreSignEndpoint();
        $expiration->expiration_libreSign($subscription);

    }

}
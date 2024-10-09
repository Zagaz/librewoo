<?php 

class CancelSubscription
{
    private $subscription_id;

    public function __construct()
    {
        add_action("woocommerce_subscription_status_cancelled", [
            $this,
            "subscription_cancelled_message",
        ]);
    }

    public function subscription_cancelled_message($subscription_id)
    {
        // Ensure the subscription ID is valid
        $subscription_id = absint($subscription_id);

        if (!$subscription_id || !is_numeric($subscription_id)) {
            return;
        }

        // Get the subscription object
        $subscription = wcs_get_subscription($subscription_id);
        $this->subscription_id = $subscription_id;
        if (!$subscription) {
            return;
        }

        // Get subscription data and trigger API
        $subscription_data = $this->get_subscription_data($subscription);
        $this->librewoo_trigger($subscription_data);
    }

    /**
     * Get subscription data from WooCommerce subscription object
     * Data: customer name, customer email, purchased items
     *
     * @param WC_Subscription $subscription
     * @return stdClass
     * @since 1.0.0
     */
    private function get_subscription_data($subscription)
    {
        $subscription_data = new stdClass();
        $subscription_data->customer_name = $subscription->get_billing_first_name() . " " . $subscription->get_billing_last_name();
        $subscription_data->customer_email = $subscription->get_billing_email();
        $subscription_data->purchased_items = $subscription->get_items();

        return $subscription_data;
    }

    /**
     * Trigger LibreSign API
     *
     * @param stdClass $subscription_data
     * @since 1.0.0
     */
    private function librewoo_trigger($subscription_data)
    {
        $groupid = 'groupid';
        $display_name = 'display_name';
        $quota = 'quota';
        $apps = 'apps';
        $authorization = 'authorization';

        $libresign_endpoint = new LibreSignEndpoint($groupid, $display_name, $quota, $apps, $authorization);
        $libresign_endpoint->triggerAPI("unscription");
    }
}
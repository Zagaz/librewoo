<?php

class LibreSignEndpoint
{
    private $groupid;
    private $display_name;
    private $quota;
    private $apps;
    private $authorization;
    private $email;

    public function __construct() {}

    /**
     * Unsubscribe user from LibreSign.
     * Important: Once unsubscribed, the user loses access to the service permanently.
     *
     * @param WC_Subscription $subscription
     * @return void
     * @since 1.0.0
     */
    public function unsubscribe_libreSign($subscription)
    {
        // IMPORTANT: Once unsubscribed, the user loses access to the service permanently.
        if (!$subscription instanceof WC_Subscription) {
            error_log("Subscription is not valid");
            return;
        }

        $subscription_id = $subscription->get_id();
        $this->logAPI("He's dead, Jim.");
        $this->logAPI("Subscription cancelled ID:" . $subscription_id);
    }

    /**
     * The client purchases a subscription to the service.
     * it will trigger the LibreSign API to create a new user.
     * 
     * @param mixed $email
     * @param mixed $display_name
     * @param mixed $quota
     * @param mixed $apps
     * @param mixed $authorization
     * @return string
     */
    public function subscribe_libreSign($email, $display_name, $quota, $apps, $authorization)
    {
        $this->email = $email;
        $this->groupid = 'groupid';
        $this->display_name = $display_name;
        $this->quota = $quota;
        $this->apps = $apps;
        $this->authorization = $authorization;

        $url = 'http://localhost/ocs/v2.php/apps/admin_group_manager/api/v1/admin-group';

        $body = [
            'email'       => $this->email,
            'groupid'     => $this->groupid,
            'displayname' => $this->display_name,
            'quota'       => $this->quota,
            'apps'        => $this->apps
        ];

        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->authorization),
            'Content-Type'  => 'application/json',
            'OCS-APIRequest' => 'true',
        ];

        $response = wp_remote_post($url, [
            'body'    => json_encode($body),
            'headers' => $headers,
        ]);



        if (is_wp_error($response)) {

            $this->logAPI("Subscribe error: WP ERROR");
            return 'Erro: ' . $response->get_error_message();
        }

        // if response is  200 OK
        if (wp_remote_retrieve_response_code($response) != 200) {
            $this->logAPI("Subscribe error - NOT 200: ");
            return 'Erro: ' . wp_remote_retrieve_response_message($response);
        }

        $this->logAPI("Make it so.");

        return wp_remote_retrieve_body($response);
    }

    /**
     * Cancel subscription
     * IMPORTANT: Once cancelled, the user loses this access to the service permanently.
     * The subscription can salso be cancelled if it's an upgrade.
     * 
     */

    public function cancel_libreSign($subscription)
    { 

        ?>
        <script>
            alert("Subscription cancelled");
        </script>
        <?php
        
        
        $subscription_id = $subscription->get_id();
        $this->logAPI("Subscription cancelled ID:" . $subscription_id);
    }





    /**
     * Hold subscription
     * IMPORTANT: Tipically occurs when the user has not paid the subscription fee
     * but there is still a chance to recover the user.
     * 
     * 
     * @param mixed $subscription
     * @return void
     */
    public function hold_libresign($subscription)
    {
        $subscription_id = $subscription->get_id();
        $this->logAPI("Hold still! This won't hurt a bit.");
        $this->logAPI("Subscription on hold ID:" . $subscription_id);

    }
    /**
     * Payment failed
     * IMPORTANT: It automatically set the subscription status to "on-hold".
     * @param mixed $subscription
     * @return void
     */
    public function payment_failed_libreSign($subscription)
    {
        $subscription_id = $subscription->get_id();
        $this->logAPI("Payment failed ID:" . $subscription_id);
    }

    /**
     * Subscription expired
     * IMPORTANT: Once expired, the subscription is cancelled and the user loses access to the service permanently.
     * @param mixed $subscription
     * @return void
     */
    public function expiration_libreSign($subscription)
    {
        // IMPORTANT: Once expired, the subscription is cancelled and the user loses access to the service permanently.
        $subscription_id = $subscription->get_id();
        $this->logAPI("Subscription expired ID:" . $subscription_id);
    }
  

    /**
     * Log API
     * It logs the API request. 
     * @param mixed $content
     * @return void
     */
    private function logAPI($content)
    {
        $context = array('source' => 'LibreSignAPI----');
        $logger = wc_get_logger();
        $logger->info('test' .$content, $context);
    }
}

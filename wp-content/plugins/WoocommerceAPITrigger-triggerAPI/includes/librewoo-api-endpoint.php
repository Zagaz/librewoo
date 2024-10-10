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

    public function unsubscribe_libreSign($subscription)
    {

        if (!$subscription instanceof WC_Subscription) {
            error_log("Subscription is not valid");
            return;
        }

        $subscription_id = $subscription->get_id();
        $this->logAPI("He's dead, Jim.");
        $this->logAPI("Subscription cancelled ID:" . $subscription_id);
    }

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

    public function hold_libresign()
    {
        $this->logAPI("Hold still! This won't hurt a bit.");

    }


    private function logAPI($content)
    {
        $context = array('source' => 'LibreSignAPI----');
        $logger = wc_get_logger();
        $logger->info($content, $context);
    }
}

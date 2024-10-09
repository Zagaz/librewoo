<?php

class LibreSignEndpoint
{
    private $groupid;
    private $display_name;
    private $quota;
    private $apps;
    private $authorization;

    private $transaction;



    public function __construct()
    {
    
    }

    public function unsubscribe_libreSign()
    {

        $this->logAPI("He's dead, Jim.");
    }

    public function subscribe_libreSign()
    {
        $url = 'http://localhost/ocs/v2.php/apps/admin_group_manager/api/v1/admin-group';

        $body = [
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
            return 'Erro: ' . $response->get_error_message();
        }


        $this->logAPI("One to beam up.");

        return wp_remote_retrieve_body($response);
    }


    private function logAPI($content)
    {
        $context = array('source' => 'LibreSignAPI----');
        $logger = wc_get_logger();
        $logger->info($content, $context);
    }
}

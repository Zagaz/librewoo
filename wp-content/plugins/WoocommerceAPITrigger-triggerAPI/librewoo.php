<?php

/**
 * Plugin Name: Woo Order Complete Message
 * Description: Prints "Woo Completed" message when an order is completed and restricts cart to one product.
 * Version: 1.0.1
 * Author: Libre Code
 * Author URI: https://librecode.coop
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('LW_PLUGIN_DIR', plugin_dir_path(__FILE__));

$includes =[];

// get all files in the includes directory extentions .php
$files = scandir(LW_PLUGIN_DIR . 'includes');

foreach ($files as $file) {
    if (strpos($file, '.php') !== false) {
        $includes[] = $file;
    }
}

foreach ($includes as $include) {
    include LW_PLUGIN_DIR . 'includes/' . $include;
}

new LibreSignAddToCartValidation();
new LibreSignEndpoint();
new LibreSignSubscribe();
new LibreSignUnsubscribe();
new LibreSignOnHold();
new LibreSignExpiration();
new LibreSignPaymentFailed();
new LibreSignBlockPurchaseSameSubscriptionAndStatus();
new LibreSignSubscruptionStatusChecker();





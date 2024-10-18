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


$includes = [
    'librewoo-subscribe.php',
    'librewoo-unsubscribe.php',
    'librewoo-on-hold.php',
    'librewoo-payment-failed.php',
    'librewoo-expiration.php',
    'librewoo-add-to-cart-validation.php',
    'librewoo-api-endpoint.php',
    'librewoo-block-purchase-subscription.php',
    'librewoo-check-subscription.php',
    'librewoo-subscruption-status-checker.php',
];

foreach ($includes as $file) {
    include LW_PLUGIN_DIR . 'includes/' . $file;
}

// Instantiate each class
foreach ($classes as $class) {
    if (class_exists($class)) {
        new $class();
    } else {
        error_log("Class $class does not exist.");
    }
}




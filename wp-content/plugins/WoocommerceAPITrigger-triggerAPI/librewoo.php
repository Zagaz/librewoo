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

include LW_PLUGIN_DIR . 'includes/librewoo-order-confirmed.php';
include LW_PLUGIN_DIR . 'includes/librewoo-add-to-cart-validation.php';


new WooOneProductCart();
new WooOrderComplete();


add_action('woocommerce_subscription_status_cancelled', 'your_custom_function_to_handle_cancellation', 10, 1);

function your_custom_function_to_handle_cancellation($subscription) {

    // Your custom logic here
    $log = wc_get_logger();
    $context = array('source' => 'TEST');
    $log->info('Subscription Cancelled: ' . $subscription->get_id() , $context);

    // Trigger API
    $unsubscribe = new LibreSignEndpoint(
        'groupid',
        'display_name',
        'quota',
        'apps',
        'authorization'
    );
    $unsubscribe->triggerAPI('unscription');
    
}






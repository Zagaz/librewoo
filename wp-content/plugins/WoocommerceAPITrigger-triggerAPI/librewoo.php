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


add_action('woocommerce_subscription_status_active', 'log_subscription_id_on_pending', 10, 1);

function log_subscription_id_on_pending($subscription) {
    // Obter ID da assinatura
    $subscription_id = $subscription->get_id();

    // Registrar o ID da assinatura no log
    error_log('Assinatura ' . $subscription_id . ' está agora pendente.');
}








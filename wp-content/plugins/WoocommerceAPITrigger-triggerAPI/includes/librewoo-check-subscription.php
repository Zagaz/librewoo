<?php

if (!defined('ABSPATH')) {
    exit;
}

class LibreSignCheckSubscription
{
    private $check = false;
    public function __construct()
    {
        add_action('woocommerce_add_to_cart', [$this, 'check_subscription'], 10, 1);
    }

    public function check_subscription()
    {
        $cart = WC()->cart->get_cart();
        $product_id_cart = 0;

        foreach ($cart as $cart_item) {
            $product_id_cart = $cart_item['product_id'];
            break;
        }

        $orders = wc_get_orders([
            'customer_id' => get_current_user_id(),
            'status' => 'completed',
        ]);

        $items_list = [];
        for ($i = 0; $i < count($orders); $i++) {
            $items = $orders[$i]->get_items();
            $items_list[] = $items;
        }

        $is_subscription_purchased = false;
        $is_subscription_active = false;

        for ($i = 0; $i < count($items_list); $i++) {
            foreach ($items_list[$i] as $item) {
                $data = $item->get_data();
                if (isset($data['product_id'])) {
                    if ($data['product_id'] == $product_id_cart) {
                        $is_subscription_purchased = true;
                        // Verificar se a assinatura está ativa
                        $subscription = wcs_get_subscription($item->get_order_id());
                        if ($subscription && $subscription->has_status('active')) {
                            $is_subscription_active = true;
                        }
                        break 2; // Sair dos dois loops
                    }
                }
            }
        }
        
    //    $this->check =  ($is_subscription_active && $is_subscription_purchased) ? true : false;
       
return false;

    }
}

// Instanciar a classe para garantir que o construtor seja chamado

new LibreSignCheckSubscription();

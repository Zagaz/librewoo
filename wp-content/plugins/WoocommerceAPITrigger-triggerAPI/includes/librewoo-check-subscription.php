<?php

if (!defined('ABSPATH')) {
    exit;
}

class LibreSignCheckSubscription
{ 
    
    public $check;
    private $is_subsciption;
    private $is_active;

    public function __construct()
    {

        add_action('woocommerce_add_to_cart', [$this, 'check_subscription'], 10, 1);
        
        $this->set_check(false);
        $this->set_is_subscription(false);
        $this->set_is_active(false);
    }

    // geters and setters
    public function get_check()
    {
        return $this->check;
    }
    public function set_check($check)
    {
        $this->check = $check;
    }

    private function get_is_subscription()
    {
        return $this->is_subsciption;
    }
    private function set_is_subscription($is_subsciption)
    {
        $this->is_subsciption = $is_subsciption;
    }

    private function get_is_active()
    {
        return $this->is_active;
    }

    private function set_is_active($is_active)
    {
        $this->is_active = $is_active;
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

        for ($i = 0; $i < count($items_list); $i++) {
            foreach ($items_list[$i] as $item) {
                $data = $item->get_data();
                if (isset($data['product_id'])) {
                    if ($data['product_id'] == $product_id_cart) {
                        $this->set_is_subscription(true);
                        // Verificar se a assinatura está ativa
                        $subscription = wcs_get_subscription($item->get_order_id());
                        if ($subscription && $subscription->has_status('active')) {
                            $this->set_is_active(true);
                            
                        }
                        break 2; // Sair dos dois loops
                    }
                }
            }
        }
        $this->set_is_active(false);
        $this->set_is_subscription(true);

        // Verificar se a assinatura está ativa
        if ($this->get_is_subscription() && $this->get_is_active()) {
            $this->set_check(true);
        }

      
        // $this->set_check(true);
     
        // return $check
        return $this->get_check();
    }
}

// Instanciar a classe para garantir que o construtor seja chamado



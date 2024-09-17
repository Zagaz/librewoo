<?php 

class WooOneProductCart {
    
    public function __construct() {
        // Hook into WooCommerce add to cart validation
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_single_product'], 10, 3);
    }

    public function validate_single_product($passed, $product_id, $quantity) {
        // If there's more than one product in the cart, empty it
        if (WC()->cart->get_cart_contents_count() > 0) {
            $this->empty_cart();
        }

        return $passed;
    }

    private function empty_cart() {
        // Empty the WooCommerce cart
        wc_empty_cart();
    }
}




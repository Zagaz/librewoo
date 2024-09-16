<?php 
// Restrict only 1 product in cart. If more than 1 product is added, empty the cart to keep only the last product added.

add_filter('woocommerce_add_to_cart_validation', 'only_one_product_in_cart', 10, 3);

function only_one_product_in_cart($passed, $product_id, $quantity) {
    if (WC()->cart->get_cart_contents_count() > 0) {
        wc_empty_cart();
    }

    return $passed;
}

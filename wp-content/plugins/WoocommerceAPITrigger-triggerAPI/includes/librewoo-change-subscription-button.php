<?php 

//You shall not pass!
if (! defined('ABSPATH')) {
    exit;
}

/*
    * Class LibreSignChangeSubscriptionButton
    *
    * Avoid downgrading the subscription.
    * If a user has an active subscription for a higher-tier product,
    * then block the purchase of a lower-tier product.
    *
    * @since 1.0.0
    */

    class LibreSignChangeSubscriptionButton
    {
    

    public function __construct() {

        add_action('woocommerce_after_shop_loop_item', array($this, 'has_any_subscrition'));
        add_action('woocommerce_after_single_product', array($this, 'has_any_subscrition'));
        // call the function whenever the add to cart button appears.
        add_action('woocommerce_after_shop_loop_item', array($this, 'has_any_subscrition'));
        add_action('woocommerce_after_single_product', array($this, 'has_any_subscrition'));
        

    }
    /**
     * Check if client has any subscription
     */

    public function has_any_subscrition(){

        if (!is_user_logged_in()) {
            return;
        }

        $user_id        = get_current_user_id();
        $check_subs = new LibreSignSubscruptionStatusChecker();
        $check_subs = $check_subs-> check_subscription_status($user_id, '');
    

        $product_id_button = [];

        foreach ($check_subs as $sub) {
            if ($sub['status'] == 'active') {
                $product_id_button[] = $sub['product_id'];
            }
            
        }  
        // filter the product_id_button array to remove duplicates
        $product_id_button = array_unique($product_id_button);

        // Java script get the data-product_id of the button

        if (count($product_id_button) > 0) {
            ?>
            <script>
                // change add_to_cart button text to "Cannot downgrade"
                // if $product_id_button[$i]==data-product_id -> change button text TO "Your current subscription".
                // if $product_id_button[$i] > data-product_id -> change button text TO "Cannot downgrade".

                var product_id_button = <?php echo json_encode($product_id_button); ?>; 
                var buttons = document.querySelectorAll('.add_to_cart_button');
                for (var i = 0; i < product_id_button.length; i++) {
                    for (var j = 0; j < buttons.length; j++) {
                        if (product_id_button[i] == buttons[j].getAttribute('data-product_id')) {
                            buttons[j].innerHTML = 'Your current subscription';
                        } else if (product_id_button[i] > buttons[j].getAttribute('data-product_id')) {
                            buttons[j].innerHTML = 'Unable to downgrade';
                            // disable the button
                            // change color background to gray
                            buttons[j].style.backgroundColor = 'gray';
                            buttons[j].classList.add('disable-bt');   
                            // The element above is part of the button, I want to dizabled the button
                                                 
                            //disable
                            buttonRoot.disabled = true;

                           
                           
                        }
                    }
                }
                
                
                
                
            </script>
            <?php
        }
       

        


        




    
        







    }
    

}


<?php 
// You shall not pass!
if (!defined('ABSPATH')) {
    exit;
}


class WooOrderComplete {
    public function __construct() {
        // Hook into WooCommerce order status change to "processing"
        add_action('woocommerce_order_status_processing', [$this, 'order_complete_message']);
    }

    public function order_complete_message($order_id) {

        // Ensure the order ID is valid     
        $order_id = absint($order_id);
       
        if (!$order_id) {
            return;
        }
        if (!is_numeric($order_id)){
            return;
        }

        $order = wc_get_order($order_id);
        $woo_client_info = $this->get_order_data($order, $order_id);

        // Log the order details, including products purchased
        $log_message = $this->create_log_message($woo_client_info);
        
        // Trigger LibreSign pipeline
        //$this->librewoo_trigger($log_message);
        
        // Log the message
        //$this->error_logger($log_message);

       
        $this->librewoo_trigger($woo_client_info);

        // get get_order_data() and store in a variable.

        $order_id = $order->get_id();

        $test = $this->get_order_data($order_id);
        $this->librewoo_trigger($test);


    }

    private function get_order_data($order_id) {
        $order= $order_id;
        $woo_client_info = new stdClass();
        //Id do pedido
        $woo_client_info->order_id = $order_id;
        // Informações do cliente - Primeiro nome
        //$woo_client_info->customer_name = $order->get_billing_first_name();
        // Informações do cliente - Sobrenome
        $woo_client_info->customer_last_name = $order->get_billing_last_name();
        // Informações do cliente - E-mail
        $woo_client_info->customer_email = $order->get_billing_email();
        // Informações do cliente - Telefone
        $woo_client_info->customer_phone = $order->get_billing_phone();
        // Informações do cliente - Endereço
        $woo_client_info->payment_method = $order->get_payment_method_title(); // e.g., 'PayPal'
        // Informações do cliente - Data de pagamento
        $payment_date = $order->get_date_paid();
        // Data do pagamento formatada
        $woo_client_info->payment_date = $payment_date ? $payment_date->date('F j, Y @ h:i a') : 'Not paid yet'; // Format the date
        // Informações do cliente - ID da transação
        $woo_client_info->transaction_id = $order->get_transaction_id(); // PayPal transaction ID
        // Informações do cliente - IP
        $woo_client_info->customer_ip = $order->get_customer_ip_address(); // Customer IP

        // Retrieve purchased items
        $woo_client_info->purchased_items = [];
        /// Loop through each order item
        foreach ($order->get_items() as $item_id => $item) {
            $woo_client_info->purchased_items[] = [
                'id'       => $item->get_id(), // Product ID
                'name'     => $item->get_name(), // Product name
                'quantity' => $item->get_quantity(), // Quantity ordered
                'total'    => wc_price($item->get_total()), // Total price (formatted with currency symbol)
            ];
        }

        return $woo_client_info;
    }

    private function create_log_message($woo_client_info) {
        $log_message = 'Payment via ' . $woo_client_info->payment_method . 
            ' was completed on ' . $woo_client_info->payment_date . 
            ' for ' . $woo_client_info->customer_name . 
            ' ' . $woo_client_info->customer_last_name . 
            ' with transaction ID ' . $woo_client_info->transaction_id . 
            ' from IP ' . $woo_client_info->customer_ip . "\n";

        $log_message .= "Purchased products:\n";
        
        foreach ($woo_client_info->purchased_items as $item) {
            $log_message .= '- ' . $item['name'] . ': ' . $item['quantity'] . ' x ' . $item['total'] . "\n";
        }

        return $log_message;
    }

   
        
    private function librewoo_trigger($order_data) {

        
        $array = get_object_vars($order_data);
        

        // print the array

        echo '<pre>';
        print_r($array['transaction_id']);
        echo '</pre>';

   


     
    }



    }

   



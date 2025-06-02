<?php 

add_action('plugins_loaded', 'init_gateway_payment_cash_cashier');
function init_gateway_payment_cash_cashier() {

    class WC_Gateway_Payment_Cash_Cashier extends WC_Payment_Gateway {

        public function __construct() {
            $this->id = 'payment_cash_cashier';
            $this->icon = ''; 
            $this->has_fields = false;
            $this->method_title = 'Cash (cashier)';
            $this->method_description = 'Cash (cashier) for WooCommerce';

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => 'Enable/Disable',
                    'type'    => 'checkbox',
                    'label'   => 'Enable Cash (cashier)',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Title shown during checkout.',
                    'default'     => 'Cash (cashier)',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Description shown during checkout.',
                    'default'     => 'Pay via our cash (cashier).',
                ),
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            $order->update_status('on-hold', 'Awaiting custom payment');

            wc_reduce_stock_levels($order_id);

            WC()->cart->empty_cart();

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            );
        }

        public function is_available() {
            return is_admin() || isset($_GET['token']);
        }
    }
}

add_filter('woocommerce_payment_gateways', 'add_payment_cash_cashier_class');
function add_payment_cash_cashier_class($methods) {
    $methods[] = 'WC_Gateway_Payment_Cash_Cashier';
    return $methods;
}
<?php



class PledgValidationModuleFrontController extends ModuleFrontController

{

    /**

     * @see FrontController::postProcess()

     */

    public function postProcess()

    {

        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active || empty($_POST['token']) ) {

            Tools::redirect('index.php?controller=order&step=1');

        }



        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process

        $authorized = false;

        foreach (Module::getPaymentModules() as $module) {

            if ($module['name'] == 'pledg') {

                $authorized = true;

                break;

            }

        }



        if (!$authorized) {

            die($this->module->l('This payment method is not available.', 'validation'));

        }



        $this->context->smarty->assign([

            'params' => $_REQUEST,

        ]);



        //$this->setTemplate('payment_return.tpl');

        $this->setTemplate('module:pledg/views/templates/front/payment_return.tpl');





        $mailVars = array();

        $currency = $this->context->currency;

        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);



        $id_customer = $cart->id_customer;

        $customer = New Customer($id_customer);



        $this->module->validateOrder((int)$cart->id, 2, $total, $this->module->displayName, null, $mailVars, (int)$currency->id, false, $customer->secure_key);



        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);





        // $customer = new Customer($cart->id_customer);

        // if (!Validate::isLoadedObject($customer))

        //     Tools::redirect('index.php?controller=order&step=1');



        // $currency = $this->context->currency;

        // $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        // $mailVars = array(

        //     '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),

        //     '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),

        //     '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))

        // );



        // $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);

        // Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);

    }

}
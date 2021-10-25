<?php

require_once _PS_MODULE_DIR_ . '/pledg/pledg.php';
require_once _PS_MODULE_DIR_ . 'pledg/class/PledgpaiementsConfirm.php';

class PledgValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        // Check if pledg module is activated
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'pledg') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.'));
        }

        $reference = null;
        $reference = $_POST['reference'] ?? $_POST['transaction'];
        PrestaShopLogger::addLog(sprintf($this->module->l('Pledg Payment Validation - Reference payment : %s'), $reference));

        if (empty($reference)) {
            PrestaShopLogger::addLog($this->module->l('Pledg Payment Validation - Reference payment is null'), 2);
        }

        $cartId = intval(str_replace(Pledg::PLEDG_REFERENCE_PREFIXE, '', $reference));
        if (!is_int($cartId)) {
            PrestaShopLogger::addLog(
                sprintf(
                    $this->module->l('Pledg Payment Validation - Reference ID doesn\'t seems to be a associated to a Cart : %s'),
                    $cartId
                ),
                2
            );
            Tools::redirect('index.php?controller=order&step=1');
            exit;
        }

        $cart = new Cart($cartId);
        $order = new Order(Order::getIdByCartId((int)$cartId));
        if (!Validate::isLoadedObject($cart) && !Validate::isLoadedObject($order)) {
            PrestaShopLogger::addLog(sprintf($this->module->l('Pledg Payment Validation - Cart doesn\t exist : '), $cartId), 2);
            Tools::redirect('index.php?controller=order&step=1');
        }
        $currency = new Currency((int) $cart->id_currency);
        $customer = new Customer($cart->id_customer);
        $priceConverted = $cart->getOrderTotal();
        if (!Validate::isLoadedObject($order)) {
            if ($order->current_state !== Configuration::get("PS_OS_OUTOFSTOCK_PAID") && $order->current_state !== Configuration::get("PS_OS_PAYMENT")) {
                $this->module->validateOrder(
                    (int)($cartId),
                    Configuration::get('PLEDG_STATE_WAITING_NOTIFICATION'),
                    $priceConverted,
                    $this->module->name,
                    null,
                    null,
                    null,
                    false,
                    $customer->secure_key
                );
            } else {
                Logger::addLog(sprintf($this->module->l('Pledg Payment Validation - Notification already processed : %s'), $cartId));
            }
        } else {
            PrestaShopLogger::addLog(sprintf($this->module->l('Pledg Payment Validation - Reference ID has already been validated by notification : %s'), $cartId));
        }
        Tools::redirect(
            'index.php?controller=order-confirmation&id_cart=' .
                (int)$cart->id .
                '&id_module=' . (int)$this->module->id .
                '&id_order=' . $order->id .
                '&key=' . $customer->secure_key
        );
    }
}

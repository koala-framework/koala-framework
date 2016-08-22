<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('PayPal');
        $ret['generators']['child']['component']['confirmLink'] = 'Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component';
        $ret['generators']['confirm']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component';
        $ret['generators']['confirm']['name'] = trlKwfStatic('done');
        $ret['generators']['ipn'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Ipn_Component'
        );
        $ret['generators']['cancel'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Cancel_Component',
            'name' => trlKwfStatic('Cancel')
        );

        /**
         * Do not prompt buyers for a shipping address. Allowable values are:
         * 0 - prompt for an address, but do not require one
         * 1 - do not prompt for an address
         * 2 - prompt for an address, and require one
         */
        $ret['noShipping'] = 1;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (isset($settings['business'])) {
            throw new Kwf_Exception('setting "business" is deprectated, use paypalId in config');
        }
    }

    public function confirmOrder($order)
    {
        throw new Kwf_Exception("Not valid for PayPal");
    }

    public function getItemName($order)
    {
        return $this->getData()->trlKwf('Order at {0}', Kwf_Registry::get('config')->application->name);
    }

    public function processIpn(Kwf_Util_PayPal_Ipn_LogModel_Row $row, $param)
    {
        if ($row->txn_type == 'web_accept') {
            $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->parent->parent->componentClass, 'childModel'))
                ->getReferencedModel('Order')->getRow($param['orderId']);
            if (!$order) {
                throw new Kwf_Exception("Order not found!");
            }

            $order->payment_component_id = $this->getData()->componentId;
            $order->checkout_component_id = $this->getData()->parent->componentId;
            $order->cart_component_class = $this->getData()->parent->parent->componentClass;

            if ($row->payment_status == 'Completed') {
                $order->status = 'payed';
                $order->payed = date('Y-m-d H:i:s');
            }

            if (!$order->confirm_mail_sent) {
                foreach ($this->getData()->parent->parent->getComponent()->getShopCartPlugins() as $p) {
                    $p->orderConfirmed($order);
                }
                foreach ($order->getChildRows('Products') as $p) {
                    $addComponent = Kwc_Shop_AddToCartAbstract_OrderProductData::getAddComponentByDbId(
                        $p->add_component_id, $this->getData()
                    );
                    $addComponent->getComponent()->orderConfirmed($p);
                }
                $this->sendConfirmMail($order);

                $order->date = date('Y-m-d H:i:s');
                $order->confirm_mail_sent = date('Y-m-d H:i:s');
            }

            $order->save();
            return true;
        } else {
            $mail = new Zend_Mail('utf-8');
            $mail->setBodyText(print_r($row->toArray(), true))
                ->setSubject('shop paypal: unbekannte notification');
            $mail->addTo('ns@vivid-planet.com');
            $mail->send();
        }
        return false;
    }
}

<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('PayPal');
        $ret['generators']['child']['component']['confirmLink'] = 'Vpc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component';
        $ret['generators']['confirm']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component';
        $ret['generators']['confirm']['name'] = trlVps('done');

        $ret['business'] = '';
        return $ret;
    }

    public function confirmOrder($order)
    {
        throw new Vps_Exception("Not valid for PayPal");
    }

    public function getItemName($order)
    {
        return trlVps('Order at {0}', Vps_Registry::get('config')->application->name);
    }

    public function processIpn(Vps_Util_PayPal_Ipn_LogModel_Row $row, $param)
    {
        if ($row->txn_type == 'web_accept') {
            $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                ->getRow($param['orderId']);
            if (!$order) {
                throw new Vps_Exception("Order not found!");
            }

            $order->payment_component_id = $this->getData()->componentId;
            $order->checkout_component_id = $this->getData()->parent->componentId;
            $order->cart_component_class = $this->getData()->parent->parent->componentClass;

            $order->status = 'payed';
            $order->date = date('Y-m-d H:i:s');
            $order->payed = date('Y-m-d H:i:s');
            $order->save();

            foreach ($this->getData()->parent->parent->getComponent()->getShopCartPlugins() as $p) {
                $p->orderConfirmed($order);
            }
            foreach ($order->getChildRows('Products') as $p) {
                $addComponent = Vps_Component_Data_Root::getInstance()
                    ->getComponentByDbId($p->add_component_id);
                $addComponent->getComponent()->orderConfirmed($p);
            }
            $this->sendConfirmMail($order);

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

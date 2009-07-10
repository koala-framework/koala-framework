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
        $ret['itemName'] = trlVps('Order at {0}', Vps_Registry::get('config')->application->name);
        return $ret;
    }

    public function confirmOrder($order)
    {
        $mail = $this->getConfirmMail($this->_order);
        $mail->send();

        $this->_order->status = 'ordered';
        $this->_order->date = new Zend_Db_Expr('NOW()');
        $this->_order->save();
    }

    public function processIpn(Vps_Util_PayPal_Ipn_LogModel_Row $row, $param)
    {
        if ($row->txn_type == 'web_accept') {
            $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                ->getRow($param['orderId']);
            if (!$order) {
                throw new Vps_Exception("Order not found!");
            }

            $mail = $this->getConfirmMail($order);
            $mail->send();

            $order->status = 'payed';
            $order->date = new Zend_Db_Expr('NOW()');
            $order->save();
        } else {
            $mail = new Zend_Mail('utf-8');
            $mail->setBodyText(print_r($row->toArray(), true))
                ->setSubject('shop paypal: unbekannte notification');
            $mail->addTo('ns@vivid-planet.com');
            $mail->send();
        }
    }
}

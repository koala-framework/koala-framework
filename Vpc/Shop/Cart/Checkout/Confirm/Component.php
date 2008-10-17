<?php
class Vpc_Shop_Cart_Checkout_Confirm_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function processInput($data)
    {
        $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        if (!$order || !$order->data) {
            throw new Vpc_AccessDeniedException("No Order exists");
        }

        $mail = new Vps_Mail($this);
        $mail->order = $order;
        $mail->products = $order->getChildRows('Products');
        $mail->addTo($order->email);
        $mail->addBcc('niko@vivid.vps');
        $mail->subject = 'Ihre Bestellung bei www.babytuch.com';
        $mail->send();

        $order->status = 'ordered';
        $order->date = new Zend_Db_Expr('NOW()');
        $order->save();

    }
}

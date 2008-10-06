<?php
class Vpc_Shop_Cart_Checkout_Confirm_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($data)
    {
        $t = new Vpc_Shop_Cart_Orders();
        $order = $t->getCartOrder();
        if (!$order || !$order->data) {
            throw new Vpc_AccessDeniedException("No Order exists");
        }
        $order->status = 'ordered';
        $order->date = new Zend_Db_Expr('NOW()');
        $order->save();
    }
}

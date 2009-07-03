<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $order = $this->_getOrder();

        $this->_order->status = 'ordered';
        $this->_order->date = new Zend_Db_Expr('NOW()');
        $this->_order->save();
    }
}

<?php
class Kwc_Shop_Cart_Checkout_Form_Trl_Component extends Kwc_Form_Trl_Component
{
    public final function getTotal($order)
    {
        return $order->getTotal();
    }

    public final function getSumRows($order)
    {
        return $order->getSumRows();
    }

    public function getPayments()
    {
        return Kwc_Abstract::getChildComponentClasses($this->getData()->parent->chained->componentClass, 'payment');
    }

    public function getPayment($order)
    {
        return $this->getData()->parent->getChildComponent('-'.$order->payment);
    }
}

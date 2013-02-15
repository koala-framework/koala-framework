<?php
class Kwc_Shop_Cart_Checkout_Form_Trl_Component extends Kwc_Form_Trl_Component
{
    public final function getTotal($order)
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->getData()->parent->parent->chained->componentClass)->getTotal($order);
    }

    public final function getSumRows($order)
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->getData()->parent->parent->chained->componentClass)->getSumRows($order);
    }

    public function getPayments()
    {
        return Kwc_Abstract::getChildComponentClasses($this->getData()->parent->chained->componentClass, 'payment');
    }
}

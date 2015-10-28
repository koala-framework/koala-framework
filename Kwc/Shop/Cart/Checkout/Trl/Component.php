<?php
class Kwc_Shop_Cart_Checkout_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public final function getTotal($order)
    {
        return $order->getTotal();
    }

    public final function getSumRows($order)
    {
        return $order->getSumRows();
    }
}


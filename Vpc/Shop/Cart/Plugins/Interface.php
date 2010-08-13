<?php
interface Vpc_Shop_Cart_Plugins_Interface
{
    public function alterCheckoutForm(Vpc_Shop_Cart_Checkout_Form_Form $form);
    public function getAdditionalSumRows(Vpc_Shop_Cart_Order $order, $total);
    public function orderConfirmed(Vpc_Shop_Cart_Order $order);
}

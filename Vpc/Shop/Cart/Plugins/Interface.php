<?php
interface Vpc_Shop_Cart_Plugins_Interface
{
    public function getAdditionalSumRows(Vpc_Shop_Cart_Order $order, $total);
    public function orderConfirmed(Vpc_Shop_Cart_Order $order);
    public function alterBackendOrderForm(Vps_Form $form);

    /**
     * Placeholders für Mails, Confirm Seite etc
     */
    public function getPlaceholders(Vpc_Shop_Cart_Order $order);
}

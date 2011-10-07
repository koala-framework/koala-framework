<?php
interface Kwc_Shop_Cart_Plugins_Interface
{
    public function getAdditionalSumRows(Kwc_Shop_Cart_Order $order, $total);
    public function orderConfirmed(Kwc_Shop_Cart_Order $order);
    public function alterBackendOrderForm(Kwf_Form $form);

    /**
     * Placeholders für Mails, Confirm Seite etc
     */
    public function getPlaceholders(Kwc_Shop_Cart_Order $order);
}

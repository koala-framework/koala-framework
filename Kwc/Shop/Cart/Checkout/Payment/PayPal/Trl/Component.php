<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Trl_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Trl_Component
{
    public function processIpn(Kwf_Util_PayPal_Ipn_LogModel_Row $row, $param)
    {
        $this->getData()->chained->getComponent()->processIpn($row, $param);
    }

    public function getItemName($order)
    {
        return trlKwf('Order at {0}', Kwf_Registry::get('config')->application->name);
    }

}

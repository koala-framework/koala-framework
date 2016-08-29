<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Ipn_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput(array $postData)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Kwf_Util_PayPal_Ipn::process();
        }
        exit;
    }
}

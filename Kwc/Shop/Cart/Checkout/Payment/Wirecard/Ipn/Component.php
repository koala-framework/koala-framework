<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Ipn_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput(array $postData)
    {
        Kwf_Util_Wirecard::process('Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel', $this->getData()->getBaseProperty('wirecard.secret'));
        exit;
    }
}

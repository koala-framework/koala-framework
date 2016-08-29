<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Failure_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($data)
    {
        if (isset($data['message'])) {
            throw new Kwf_Exception('Payment error! Message: ' . $data['message']);
        } else {
            throw new Kwf_Exception_Client($this->getData()->trlKwf('No data received'));
        }
    }
}

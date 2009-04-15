<?php
class Vps_Util_PayPal_Ipn_LogModel extends Vps_Model_Db
{
    protected $_table = 'paypal_ipn_log';
    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}

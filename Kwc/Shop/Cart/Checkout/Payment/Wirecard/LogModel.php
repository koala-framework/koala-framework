<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_wirecard_log';
    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Kwf_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}

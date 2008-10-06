<?php
class Vpc_Shop_Cart_Checkout_Model extends Vps_Model_Field
{
    protected function _init()
    {
        $this->_fieldName = 'data';
        $this->_parentModel = new Vps_Model_Db(array('tableName'=>'Vpc_Shop_Cart_Orders'));
        parent::_init();
    }
}

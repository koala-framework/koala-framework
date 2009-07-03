<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component extends Vpc_Abstract
{
    private $_order;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    protected function _getOrder()
    {
        $ret = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        if (!$ret || !$ret->data) {
            throw new Vps_Exception_AccessDenied("No Order exists");
        }
        return $ret;
    }

    public function processInput($data)
    {
        $this->getData()->parent->getComponent()->confirmOrder($this->_getOrder());
    }
}

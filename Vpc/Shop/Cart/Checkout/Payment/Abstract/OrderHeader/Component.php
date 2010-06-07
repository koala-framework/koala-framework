<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderHeader_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['order'] = $this->_getOrder();
        $ret['paymentTypeText'] = Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName');
        return $ret;
    }

    protected function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }
}

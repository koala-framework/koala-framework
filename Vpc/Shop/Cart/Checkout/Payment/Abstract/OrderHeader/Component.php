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
        $ret['paymentTypeText'] = null;
        $c = $this->getData()->parent->componentClass;
        if (!is_instance_of($c, 'Vpc_Shop_Cart_Checkout_Payment_None_Component')) {
            $ret['paymentTypeText'] = Vpc_Abstract::getSetting($c, 'componentName');
        }
        return $ret;
    }

    protected function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }
}

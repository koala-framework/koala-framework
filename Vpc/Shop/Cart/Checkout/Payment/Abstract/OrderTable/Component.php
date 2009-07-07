<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderTable_Component extends Vpc_Abstract
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
        $ret['orderProducts'] = $ret['order']->getChildRows('Products');
        $ret['sumRows'] = $this->_getSumRows($this->_getOrder());
        return $ret;
    }

    protected function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }
    
    protected function _getSumRows($order)
    {
        return $this->getData()->parent->parent->getComponent()->getSumRows($order);
    }
}

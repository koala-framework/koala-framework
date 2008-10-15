<?php
class Vpc_Shop_Box_Cart_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['cart'] = $this->_getCart();
        $ret['checkout'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Checkout_Component');
        $ret['items'] = $ret['cart']->getChildComponents(array('generator'=>'detail'));
        foreach ($ret['items'] as $i) {
            $i->product = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($i->row->add_component_id)
                ->parent;
        }
        $ret['order'] = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();

        return $ret;
    }
    
    private function _getCart()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Component');
    }
    public function hasContent()
    {
        return (bool)$this->_getCart()->countChildComponents(array('generator'=>'detail'));
    }
}

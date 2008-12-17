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
            ->getComponentByClass(
                'Vpc_Shop_Cart_Checkout_Component',
                array('subroot' => $this->getData())
            );
        $ret['order'] = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
        $items = $ret['order']->getChildRows('Products'); //$ret['cart']->getChildComponents(array('generator'=>'detail'));
        $ret['items'] = array();
        foreach ($items as $i) {
            $ret['items'][] = (object)array(
                'product' => Vps_Component_Data_Root::getInstance()
                                ->getComponentByDbId($i->add_component_id)
                                ->parent,
                'row' => $i
            );
        }

        return $ret;
    }

    private function _getCart()
    {
        return Vps_Component_Data_Root::getInstance()->getComponentByClass(
            'Vpc_Shop_Cart_Component',
            array('subroot' => $this->getData())
        );
    }
    public function hasContent()
    {
        return (bool)$this->_getCart()->countChildComponents(array('generator'=>'detail'));
    }
}

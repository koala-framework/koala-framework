<?php
class Vpc_Shop_Box_Cart_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['toCart'] = trlVps('To cart');
        $ret['placeholder']['toCheckout'] = trlVps('To Checkout');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['cart'] = $this->_getCart();
        $ret['checkout'] = $ret['cart']->getChildComponent('_checkout');

        $ret['order'] = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
        $items = $ret['order']->getChildRows('Products');
        $ret['items'] = array();
        foreach ($items as $i) {
            $addComponent = Vps_Component_Data_Root::getInstance()
                            ->getComponentByDbId($i->add_component_id);
            $ret['items'][] = (object)array(
                'product' => $addComponent->parent,
                'row' => $i,
                'additionalOrderData' => $addComponent->getComponent()->getAdditionalOrderData($i)
            );
        }
        $ret['sumRows'] = $ret['checkout']->getComponent()->getSumRows($ret['order']);

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

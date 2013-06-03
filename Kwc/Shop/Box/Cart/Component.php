<?php
class Kwc_Shop_Box_Cart_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Box/Cart/Component.js';
        $ret['placeholder']['toCart'] = trlKwfStatic('To cart');
        $ret['placeholder']['toCheckout'] = trlKwfStatic('To Checkout');
        $ret['ordersModel'] = 'Kwc_Shop_Cart_Orders';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['order'] = Kwf_Model_Abstract::getInstance($this->_getSetting('ordersModel'))
                            ->getCartOrder();
        $ret['items'] = $ret['order']->getProductsDataWithProduct($this->getData());
        $ret['sumRows'] = $this->_getCart()->getChildComponent(array('generator' => 'checkout'))
                                ->getComponent()->getSumRows($ret['order']);

        $ret['links'] = $this->_getLinks();

        return $ret;
    }

    protected function _getLinks()
    {
        $ret = array();
        $placeholder = $this->_getSetting('placeholder');
        $ret['cart'] = array(
            'component' => $this->_getCart(),
            'text' => $placeholder['toCart']
        );
        $ret['checkout'] = array(
            'component' => $this->_getCart()->getChildComponent(array('generator' => 'checkout')),
            'text' => $placeholder['toCheckout']
        );
        return $ret;
    }

    private function _getCart()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            array('Kwc_Shop_Cart_Component', 'Kwc_Shop_Cart_Trl_Component'),
            array('ignoreVisible' => true, 'subroot' => $this->getData())
        );
    }
    public function hasContent()
    {
        if (!$this->_getCart()) return false;
        return (bool)$this->_getCart()->countChildComponents(array('generator'=>'detail'));
    }
}

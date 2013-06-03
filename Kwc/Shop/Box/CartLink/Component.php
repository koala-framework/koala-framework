<?php
class Kwc_Shop_Box_CartLink_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard kwcShopBoxCartLink';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Box/CartLink/Component.js';
        $ret['placeholder']['toCart'] = trlKwfStatic('To cart');
        $ret['ordersModel'] = 'Kwc_Shop_Cart_Orders';
        return $ret;
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['links'] = $this->_getLinks();

        $ret['hasContent'] = $this->hasContent();
        
        $ret['totalAmount'] = Kwf_Model_Abstract::getInstance($this->_getSetting('ordersModel'))
            ->getCartOrder()->getTotalAmount();
        
        return $ret;
    }

    protected function _getLinks()
    {
        $ret = array();
        $ret['cart'] = array(
            'component' => $this->_getCart(),
            'text' => $this->_getPlaceholder('toCart')
        );
        return $ret;
    }

    private function _getCart()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_Shop_Cart_Component',
            array('subroot' => $this->getData())
        );
    }

    public function hasContent()
    {
        if (!$this->_getCart()) return false;
        return (bool)$this->_getCart()->countChildComponents(array('generator'=>'detail'));
    }
}

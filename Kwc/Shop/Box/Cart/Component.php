<?php
class Kwc_Shop_Box_Cart_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['toCart'] = trlKwf('To cart');
        $ret['placeholder']['toCheckout'] = trlKwf('To Checkout');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['order'] = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders')
                            ->getCartOrder();
        $ret['items'] = $ret['order']->getProductsDataWithProduct();
        $ret['sumRows'] = $this->_getCart()->getChildComponent(array('componentClass' => 'Kwc_Shop_Cart_Checkout_Component'))
                                ->getComponent()->getSumRows($ret['order']);

        $ret['links'] = $this->_getLinks();

        return $ret;
    }

    protected function _getLinks()
    {
        $ret = array();
        $ret['cart'] = array(
            'component' => $this->_getCart(),
            'text' => $this->_getPlaceholder('toCart')
        );
        $ret['checkout'] = array(
            'component' => $this->_getCart()->getChildComponent(array('componentClass' => 'Kwc_Shop_Cart_Checkout_Component')),
            'text' => $this->_getPlaceholder('toCheckout')
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

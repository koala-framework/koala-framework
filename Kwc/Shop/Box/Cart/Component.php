<?php
class Kwc_Shop_Box_Cart_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['placeholder']['toCart'] = trlKwfStatic('To cart');
        $ret['placeholder']['toCheckout'] = trlKwfStatic('To Checkout');
        $ret['ordersModel'] = 'Kwc_Shop_Cart_Orders';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

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
        $ret['cart'] = array(
            'component' => $this->_getCart(),
            'text' => $this->_getPlaceholder('toCart')
        );
        $ret['checkout'] = array(
            'component' => $this->_getCart()->getChildComponent(array('generator' => 'checkout')),
            'text' => $this->_getPlaceholder('toCheckout')
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

<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderTable_Component extends Kwc_Abstract
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
        $ret['items'] = $ret['order']->getProductsData();

        $items = $ret['order']->getChildRows('Products');
        $ret['items'] = array();
        $ret['additionalOrderDataHeaders'] = array();
        foreach ($items as $i) {
            $addComponent = Kwf_Component_Data_Root::getInstance()
                            ->getComponentByDbId($i->add_component_id);
            $additionalOrderData = $addComponent->getComponent()->getAdditionalOrderData($i);
            foreach ($additionalOrderData as $d) {
                if (!isset($ret['additionalOrderDataHeaders'][$d['name']])) {
                    $ret['additionalOrderDataHeaders'][$d['name']] = array(
                        'class' => $d['class'],
                        'text' => $d['name']
                    );
                }
            }
            $ret['items'][] = (object)array(
                'product' => $addComponent->parent,
                'row' => $i,
                'additionalOrderData' => $additionalOrderData,
                'price' => $i->getProductPrice(),
                'text' => $i->getProductText(),
            );
        }

        $ret['sumRows'] = $this->_getSumRows($this->_getOrder());

        $ret['tableFooterText'] = '';
        $ret['footerText'] = '';
        return $ret;
    }

    protected function _getOrder()
    {
        return Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
    }

    protected function _getSumRows($order)
    {
        return $this->getData()->parent->parent->getComponent()->getSumRows($order);
    }
}

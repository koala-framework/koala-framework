<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderTable_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['order'] = $this->_getOrder();
        $items = $ret['order']->getChildRows('Products');
        $ret['items'] = array();
        $ret['additionalOrderDataHeaders'] = array();
        foreach ($items as $i) {
            $addComponent = Kwc_Shop_AddToCartAbstract_OrderProductData::getAddComponentByDbId(
                $i->add_component_id, $this->getData()
            );
            $additionalOrderData = $addComponent->getComponent()->getAdditionalOrderData($i);
            foreach ($additionalOrderData as $d) {
                if (!isset($ret['additionalOrderDataHeaders'][$d['name']])) {
                    $ret['additionalOrderDataHeaders'][$d['name']] = array(
                        'class' => $d['class'],
                        'text' => $d['name']
                    );
                }
            }

            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($i->add_component_class);
            $ret['items'][] = (object)array(
                'product' => $addComponent->parent,
                'row' => $i,
                'additionalOrderData' => $additionalOrderData,
                'price' => $i->getProductPrice(),
                'text' => $data->getProductTextDynamic($i),
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

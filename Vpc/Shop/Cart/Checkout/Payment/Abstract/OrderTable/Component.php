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
        $ret['items'] = $ret['order']->getProductsData();

        $items = $ret['order']->getChildRows('Products');
        $ret['items'] = array();
        $ret['additionalOrderDataHeaders'] = array();
        foreach ($items as $i) {
            $addComponent = Vps_Component_Data_Root::getInstance()
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
                'price' => $addComponent->getComponent()->getPrice($i),
                'text' => $addComponent->getComponent()->getProductText($i),
            );
        }

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

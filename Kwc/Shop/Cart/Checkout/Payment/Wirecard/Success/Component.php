<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Success_Component extends Kwc_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        $ret['plugins']['placeholders'] = 'Kwf_Component_Plugin_Placeholders';
        $ret['viewCache'] = false;
        $ret['generators']['content']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Component';
        return $ret;
    }

    public function getNameForEdit()
    {
        return trlKwf('Shop Confirmation Text') . ' (' .$this->getData()->getSubroot()->id . ') '
            . Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName'));
    }

    protected function _getOrder()
    {
        $ret = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
        if (!$ret || !$ret->data) {
            return null;
        }
        return $ret;
    }

    public function processInput($data)
    {
        $custom = isset($data['custom']) ? rawurldecode($data['custom']) : null;
        $data = Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel::decodeCallback($custom);
        if ($data) {
            $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
                $this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
            ))->getReferencedModel('Order')->getRow($data['data']['orderId']);
            if ($order->status == 'processing' || $order->status == 'cart') {
                $order->payment_component_id = $this->getData()->parent->componentId;
                $order->checkout_component_id = $this->getData()->parent->parent->componentId;
                $order->cart_component_class = $this->getData()->parent->parent->parent->componentClass;

                $order->status = 'ordered';
                $order->date = date('Y-m-d H:i:s');
                $order->save();
            }
            Kwc_Shop_Cart_Orders::setOverriddenCartOrderId($order->id);
            if (Kwc_Shop_Cart_Orders::getCartOrderId() == $order->id) {
                Kwc_Shop_Cart_Orders::resetCartOrderId();
            }
        }
    }

    public function getPlaceholders()
    {
        $o = $this->_getOrder();
        if (!$o) return array();
        return $o->getPlaceholders();
    }

    public final function getCurrentOrder()
    {
        return $this->_getOrder();
    }
}

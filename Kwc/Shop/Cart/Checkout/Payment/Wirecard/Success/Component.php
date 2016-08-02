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
            $db = Kwf_Registry::get('db');

            $date = date('Y-m-d H:i:s');
            $sql = "UPDATE `kwc_shop_orders` SET
              `payment_component_id` = {$db->quote($this->getData()->parent->componentId)},
              `checkout_component_id` = {$db->quote($this->getData()->parent->parent->componentId)},
              `cart_component_class` = {$db->quote($this->getData()->parent->parent->parent->componentClass)},
              `date` = {$db->quote($date)},
              `status` = 'ordered'
              WHERE `id` = {$db->quote($data['data']['orderId'])} AND (`status` = 'processing' OR `status` = 'cart')";
            $db->query($sql);

            Kwc_Shop_Cart_Orders::setOverriddenCartOrderId($data['data']['orderId']);
            if (Kwc_Shop_Cart_Orders::getCartOrderId() == $data['data']['orderId']) {
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

<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component extends Vpc_Editable_Component
{
    private $_order;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Component';
        $ret['flags']['processInput'] = true;
        $ret['cssClass'] = 'webStandard';
        $ret['componentName'] = trlVps('Shop Conformation Text');
        $ret['plugins']['placeholders'] = 'Vps_Component_Plugin_Placeholders';
        return $ret;
    }

    protected function _getOrder()
    {
        $ret = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        if (!$ret || !$ret->data) {
            throw new Vps_Exception_AccessDenied("No Order exists");
        }
        return $ret;
    }

    public function processInput($data)
    {
        $this->getData()->parent->getComponent()->confirmOrder($this->_getOrder());
    }

    public function getPlaceholders()
    {
        $o = $this->_getOrder();
        $ret = $o->toArray();
        $m = new Vps_View_Helper_Money();
        $ret['total'] = $m->money($o->getTotal());
        $ret['orderNumber'] = $o->getOrderNumber();
        return $ret;
    }
}

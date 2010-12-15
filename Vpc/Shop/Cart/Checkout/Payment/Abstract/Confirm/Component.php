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
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _getOrder()
    {
        $ret = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        if (!$ret || !$ret->data) {
            return null;
        }
        return $ret;
    }

    public function processInput($data)
    {
        $o = $this->_getOrder();
        if (!$o) {
            //bestellung wurde bereits bestaetigt
            header("Location: ".$this->getData()->parent->parent->parent->parent->url);
            exit;
        }
        $this->getData()->parent->getComponent()->confirmOrder($o);
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

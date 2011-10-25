<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component extends Kwc_Editable_Component
{
    private $_order;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Component';
        $ret['flags']['processInput'] = true;
        $ret['cssClass'] = 'webStandard';
        $ret['plugins']['placeholders'] = 'Kwf_Component_Plugin_Placeholders';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getNameForEdit()
    {
        return trlKwf('Shop Confirmation Text') . ' ' . Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName');
    }

    protected function _getOrder()
    {
        $ret = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders')->getCartOrder();
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

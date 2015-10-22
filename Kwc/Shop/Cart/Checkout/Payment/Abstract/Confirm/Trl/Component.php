<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Trl_Component extends Kwc_Editable_Trl_Component
{
    public function getPlaceholders()
    {
        return $this->getData()->chained->getComponent()->getPlaceholders();
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

    protected function _getOrder()
    {
        $ret = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Trl_Component')->chained->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
        if (!$ret || !$ret->data) {
            return null;
        }
        return $ret;
    }
}

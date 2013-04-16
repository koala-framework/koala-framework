<?php
class Kwc_Shop_Cart_Checkout_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Kwc_Shop_Cart_Checkout_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwfStatic('Next');
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(
            Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
                ->getReferencedModel('Order')
        );
        $this->_form->setId(Kwc_Shop_Cart_Orders::getCartOrderId()); //can be null

        $this->_form->setPayments($this->_getFrontendPayments());
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        //if getCartOrderId was null (tough it should not happen as the cart is empty in that case and the form doesn't validate then)
        Kwc_Shop_Cart_Orders::setCartOrderId($row->id);
    }


    protected function _getFrontendPayments()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')
            ->getCartOrder();
        $totalAmount = $this->getData()->parent->getComponent()->getTotal($order);
        $cc = $this->getData()->parent->getComponent()->getPayments();
        $ret = array();
        foreach ($cc as $k=>$c) {
            if ($totalAmount <= 0) {
                if (is_instance_of($c, 'Kwc_Shop_Cart_Checkout_Payment_None_Component')) {
                    $ret[$k] = Kwc_Abstract::getSetting($c, 'componentName');
                }
            } else {
                if (!is_instance_of($c, 'Kwc_Shop_Cart_Checkout_Payment_None_Component')) {
                    $ret[$k] = Kwc_Abstract::getSetting($c, 'componentName');
                }
            }
        }
        return $ret;
    }
}

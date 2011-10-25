<?php
class Kwc_Shop_Cart_Checkout_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Kwc_Shop_Cart_Checkout_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwf('Next');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        if (!Kwc_Shop_Cart_Orders::getCartOrderId()) {
            throw new Kwf_Exception_AccessDenied("No Order exists");
        }
        $this->_form->setId(Kwc_Shop_Cart_Orders::getCartOrderId());

        $this->_form->setPayments($this->_getFrontendPayments());
    }

    protected function _getFrontendPayments()
    {
        $order = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders')
                            ->getCartOrder();
        $totalAmount = $this->getData()->parent->getComponent()->getTotal($order);
        $cc = Kwc_Abstract::getChildComponentClasses($this->getData()->parent->componentClass, 'payment');
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

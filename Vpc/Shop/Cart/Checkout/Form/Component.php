<?php
class Vpc_Shop_Cart_Checkout_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Shop_Cart_Checkout_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlVps('Next');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        if (!Vpc_Shop_Cart_Orders::getCartOrderId()) {
            throw new Vps_Exception_AccessDenied("No Order exists");
        }
        $this->_form->setId(Vpc_Shop_Cart_Orders::getCartOrderId());
    }
}

<?php
class Vpc_Shop_Cart_Plugins_Voucher_Redeem_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component';
        $ret['componentName'] = trlVps('Shop').'.'.trlVps('Redeem Voucher');
        $ret['placeholder']['submitButton'] = trlVps('Redeem Voucher');
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

    /*
    drei möglichkeiten:
    - ungültiger code (validator-fehler)
    - bereits verbraucher code (validator-fehler)
    - teilweise verbrauchter code
    */
}

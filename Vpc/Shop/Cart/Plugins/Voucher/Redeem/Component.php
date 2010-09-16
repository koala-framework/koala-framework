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
        $this->_form->setId(Vpc_Shop_Cart_Orders::getCartOrderId());
    }

    //wenn warenkorb noch ler wird erst beim einfügen der neuen order (mit dem gutschein)
    //die id in der session gespeichert
    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        Vpc_Shop_Cart_Orders::setCartOrderId($row->id);
    }


    /*
    drei möglichkeiten:
    - ungültiger code (validator-fehler)
    - bereits verbraucher code (validator-fehler)
    - teilweise verbrauchter code
    */
}

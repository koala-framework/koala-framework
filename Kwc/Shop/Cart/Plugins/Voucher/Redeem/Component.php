<?php
class Kwc_Shop_Cart_Plugins_Voucher_Redeem_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = 'Kwc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component';
        $ret['componentName'] = trlKwfStatic('Shop').'.'.trlKwfStatic('Redeem Voucher');
        $ret['placeholder']['submitButton'] = trlKwfStatic('Redeem Voucher');
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setId(Kwc_Shop_Cart_Orders::getCartOrderId());
    }

    //wenn warenkorb noch ler wird erst beim einfügen der neuen order (mit dem gutschein)
    //die id in der session gespeichert
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        Kwc_Shop_Cart_Orders::setCartOrderId($row->id);
    }


    /*
    drei möglichkeiten:
    - ungültiger code (validator-fehler)
    - bereits verbraucher code (validator-fehler)
    - teilweise verbrauchter code
    */
}

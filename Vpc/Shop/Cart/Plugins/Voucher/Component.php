<?php
class Vpc_Shop_Cart_Plugins_Voucher_Component extends Vps_Component_Plugin_Abstract
    implements Vpc_Shop_Cart_Plugins_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function alterCheckoutForm(Vpc_Shop_Cart_Checkout_Form_Form $form)
    {
        $form->insertAfter('payment', new Vps_Form_Field_TextField('voucher_code', trlVps('Voucher Code')));
    }
}

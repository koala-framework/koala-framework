<?php
class Kwc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwfStatic('Your Voucher has been successfully redeemed.');
        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Cart/Plugins/Voucher/Redeem/Success/Component.js';
        return $ret;
    }
}

<?php
class Kwc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwf('Your Voucher has been successfully redeemed.');
        return $ret;
    }
}

<?php
class Vpc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Your Voucher has been successfully redeemed.');
        return $ret;
    }
}

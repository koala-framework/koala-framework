<?php
class Kwc_Shop_Cart_Plugins_Voucher_Redeem_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('Your Voucher has been successfully redeemed.');
        return $ret;
    }
}

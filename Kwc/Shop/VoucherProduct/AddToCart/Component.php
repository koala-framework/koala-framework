<?php
class Kwc_Shop_VoucherProduct_AddToCart_Component extends Kwc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['orderProductData'] = 'Kwc_Shop_VoucherProduct_AddToCart_OrderProductData';
        $ret['productTypeText'] = trlKwfStatic('Voucher');
        return $ret;
    }
}

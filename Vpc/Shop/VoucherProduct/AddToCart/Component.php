<?php
class Vpc_Shop_VoucherProduct_AddToCart_Component extends Vpc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['orderProductData'] = 'Vpc_Shop_VoucherProduct_AddToCart_OrderProductData';
        $ret['productTypeText'] = trlVps('Voucher');
        return $ret;
    }
}

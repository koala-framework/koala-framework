<?php
class Vpc_Shop_VoucherProduct_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['addToCart'] = 'Vpc_Shop_VoucherProduct_AddToCart_Component';
        $ret['componentName'] = trlVps('Shop.Product: Voucher');
        return $ret;
    }
}

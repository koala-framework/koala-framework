<?php
class Kwc_Shop_VoucherProduct_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['addToCart'] = 'Kwc_Shop_VoucherProduct_AddToCart_Component';
        $ret['componentName'] = trlKwf('Shop.Product: Voucher');
        return $ret;
    }
}

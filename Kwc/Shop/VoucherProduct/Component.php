<?php
class Kwc_Shop_VoucherProduct_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['addToCart'] = 'Kwc_Shop_VoucherProduct_AddToCart_Component';
        $ret['componentName'] = trlKwfStatic('Shop.Product: Voucher');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}

<?php
class Kwc_Shop_AddToCart_Trl_Component extends Kwc_Shop_AddToCartAbstract_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['form']['component'] = 'Kwc_Shop_AddToCart_Trl_Form_Component';
        $ret['orderProductData'] = 'Kwc_Shop_AddToCart_Trl_OrderProductData';
        return $ret;
    }

    public function getProductRow()
    {
        return $this->getData()->chained->parent->row;
    }
}

<?php
class Vpc_Shop_Products_Preview_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['addToCart'] = 'Vpc_Shop_Products_Preview_AddToCart_Component';
        return $ret;
    }
}

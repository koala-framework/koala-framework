<?php
class Vpc_Shop_Products_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Vpc_Paragraphs_Component';
        $ret['generators']['child']['component']['addToCart'] = 'Vpc_Shop_AddToCart_Component';
        return $ret;
    }
}

<?php
class Vpc_Shop_AddToCartAbstract_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('The product has been added the the cart.');
        return $ret;
    }

}

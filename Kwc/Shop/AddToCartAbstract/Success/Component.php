<?php
class Kwc_Shop_AddToCartAbstract_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwf('The product has been added to the cart.');
        return $ret;
    }

}

<?php
class Vpc_Composite_LinkImage_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimension'] = array(150, 0);
        return $ret;
    }
}
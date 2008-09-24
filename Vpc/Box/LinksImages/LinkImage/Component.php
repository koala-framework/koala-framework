<?php
class Vpc_Box_LinksImages_LinkImage_Component extends Vpc_Composite_LinkImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['image'] = 'Vpc_Box_LinksImages_LinkImage_Image_Component';
        unset($ret['componentName']);
        return $ret;
    }
}

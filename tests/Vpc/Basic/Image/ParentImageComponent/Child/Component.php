<?php
class Vpc_Basic_Image_ParentImageComponent_Child_Component
    extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(16, 16, Vps_Media_Image::SCALE_DEFORM);
        $ret['useParentImage'] = true;
        return $ret;
    }
}

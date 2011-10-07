<?php
class Vpc_List_Switch_Preview_Large_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
        'default'=>array(
            'width' => self::CONTENT_WIDTH,
            'height' => 0,
            'scale' => Vps_Media_Image::SCALE_CROP
        ));
        $ret['useParentImage'] = true;
        return $ret;
    }
}

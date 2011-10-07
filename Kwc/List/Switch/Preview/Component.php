<?php
class Vpc_List_Switch_Preview_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['large'] =
            'Vpc_List_Switch_Preview_Large_Component';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 100,
                'height' => 75,
                'scale' => Vps_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}

<?php
class Kwc_List_Switch_Preview_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['large'] =
            'Kwc_List_Switch_Preview_Large_Component';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 100,
                'height' => 75,
                'cover' => true,
            )
        );
        return $ret;
    }
}

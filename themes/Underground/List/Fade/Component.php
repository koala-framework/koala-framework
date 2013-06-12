<?php
class Underground_List_Fade_Component extends Kwc_List_Fade_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Underground_List_Fade_Image_Component';
        return $ret;
    }
}

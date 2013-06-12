<?php
class Default_List_Fade_Component extends Kwc_List_Fade_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Default_List_Fade_Image_Component';
        $ret['componentName'] = trlStatic('Stage');
        return $ret;
    }
}

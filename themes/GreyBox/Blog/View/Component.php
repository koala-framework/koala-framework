<?php
class GreyBox_Blog_View_Component extends Kwc_Blog_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['readMore'] = trlKwfStatic('Continue reading').' →';
        return $ret;
    }
}

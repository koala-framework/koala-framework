<?php
class Vpc_News_List_View_Component extends Vpc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['readMore'] = trlVpsStatic('Read more').' &raquo;';
        return $ret;
    }
}

<?php
class Kwc_News_List_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['readMore'] = trlKwfStatic('Read more').' &raquo;';
        return $ret;
    }
}

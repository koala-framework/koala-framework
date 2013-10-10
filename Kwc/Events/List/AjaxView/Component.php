<?php
class Kwc_Events_List_AjaxView_Component extends Kwc_Directories_List_ViewAjax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['readMore'] = trlKwfStatic('Read more').' &raquo;';
        return $ret;
    }
}

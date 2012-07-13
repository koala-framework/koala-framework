<?php
class Kwc_Directories_AjaxView_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Directories_List_ViewAjax_DetailContentSender';
        return $ret;
    }
}

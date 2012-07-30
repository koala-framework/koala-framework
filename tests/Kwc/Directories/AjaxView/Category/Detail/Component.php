<?php
class Kwc_Directories_AjaxView_Category_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Directories_Category_Detail_AjaxViewContentSender';
        //$ret['generators']['child']['component']['list'] = 'Kwc_Basic_Empty_Component';
        return $ret;
    }
}

<?php
class Kwc_Box_Tags_RelatedNews_View_Component extends Kwc_Directories_List_ViewPage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['limit'] = 10;
        return $ret;
    }
}

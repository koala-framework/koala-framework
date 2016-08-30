<?php
class Kwc_Directories_MapView_View_SearchForm_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = false;
        return $ret;
    }
}

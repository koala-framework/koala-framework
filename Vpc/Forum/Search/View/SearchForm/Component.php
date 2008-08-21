<?php
class Vpc_Forum_Search_View_SearchForm_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        $ret['method'] = 'get';
        return $ret;
    }

}

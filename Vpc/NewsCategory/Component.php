<?php
class Vpc_NewsCategory_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_NewsCategory_Model';
        $ret['generators']['child']['component']['view'] = 'Vpc_NewsCategory_View_Component';
        return $ret;
    }
}

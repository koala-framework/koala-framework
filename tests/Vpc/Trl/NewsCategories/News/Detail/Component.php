<?php
class Vpc_Trl_NewsCategories_News_Detail_Component extends Vpc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Vpc_Basic_Empty_Component';
        return $ret;
    }
}

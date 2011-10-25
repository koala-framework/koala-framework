<?php
class Kwc_Trl_News_News_Detail_Component extends Kwc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Kwc_Basic_Empty_Component';
        return $ret;
    }
}

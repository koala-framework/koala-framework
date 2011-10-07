<?php
class Vpc_Trl_News_News_Trl_Component extends Vpc_News_Directory_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['childModel'] = 'Vpc_Trl_News_News_Trl_TestModel';
        return $ret;
    }
}

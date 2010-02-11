<?php
class Vpc_Trl_Paragraphs_Paragraphs_Child_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentName'] = 'Child';
        return $ret;
    }
}

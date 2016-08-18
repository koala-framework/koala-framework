<?php
class Kwc_Trl_Paragraphs_Paragraphs_Child_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentName'] = 'Child';
        return $ret;
    }
}

<?php
class Vpc_Root_Category_Cc_Component extends Vpc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Vpc_Root_Category_Cc_Generator';
        return $ret;
    }
}

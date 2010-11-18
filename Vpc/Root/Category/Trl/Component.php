<?php
class Vpc_Root_Category_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Vpc_Root_Category_Trl_Generator';
        $ret['generators']['page']['model'] = 'Vpc_Root_Category_Trl_GeneratorModel';
        return $ret;
    }
}

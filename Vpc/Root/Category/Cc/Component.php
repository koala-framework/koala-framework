<?php
class Vpc_Root_Category_Cc_Component extends Vpc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        //$ret['generators']['page']['class'] = 'Vpc_Root_Category_Cc_Generator';
        //$ret['generators']['page']['model'] = 'Vpc_Root_Category_Cc_GeneratorModel';
        return $ret;
    }
}

<?php
class Kwc_Root_Category_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Kwc_Root_Category_Cc_Generator';
        return $ret;
    }
}

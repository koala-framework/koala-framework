<?php
class Kwc_Trl_FormDynamic_Form_Trl_Component extends Kwc_Form_Dynamic_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_FormDynamic_Form_Trl_TestModel';
        return $ret;
    }
}

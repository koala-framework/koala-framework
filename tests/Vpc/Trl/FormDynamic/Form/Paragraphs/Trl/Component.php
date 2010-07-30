<?php
class Vpc_Trl_FormDynamic_Form_Paragraphs_Trl_Component extends Vpc_Paragraphs_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_Trl_FormDynamic_Form_Paragraphs_Trl_TestModel';
        return $ret;
    }
}

<?php
class Kwc_Trl_FormDynamic_Form_Paragraphs_Trl_Component extends Kwc_Paragraphs_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Trl_FormDynamic_Form_Paragraphs_Trl_TestModel';
        return $ret;
    }
}

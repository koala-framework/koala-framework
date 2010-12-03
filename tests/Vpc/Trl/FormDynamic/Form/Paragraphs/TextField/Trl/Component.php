<?php
class Vpc_Trl_FormDynamic_Form_Paragraphs_TextField_Trl_Component extends Vpc_Form_Field_TextField_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vpc_Trl_FormDynamic_Form_Paragraphs_TextField_Trl_TestModel';
        return $ret;
    }
}

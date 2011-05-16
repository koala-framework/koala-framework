<?php
class Vpc_FormDynamic_Basic_Form_Paragraphs_Checkbox_Component extends Vpc_Form_Field_Checkbox_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_FormDynamic_Basic_Form_Paragraphs_Checkbox_TestModel';
        return $ret;
    }
}
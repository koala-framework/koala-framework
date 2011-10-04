<?php
class Vpc_FormDynamic_Basic_Form_Paragraphs_TextField_Component extends Vpc_Form_Field_TextField_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_FormDynamic_Basic_Form_Paragraphs_TextField_TestModel';
        return $ret;
    }
}
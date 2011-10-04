<?php
class Vpc_FormDynamic_Basic_Form_Paragraphs_File_Component extends Vpc_Form_Field_File_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_FormDynamic_Basic_Form_Paragraphs_File_TestModel';
        return $ret;
    }
}
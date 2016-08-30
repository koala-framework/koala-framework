<?php
class Kwc_FormDynamic_Basic_Form_Paragraphs_File_Component extends Kwc_Form_Field_File_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_FormDynamic_Basic_Form_Paragraphs_File_TestModel';
        return $ret;
    }
}
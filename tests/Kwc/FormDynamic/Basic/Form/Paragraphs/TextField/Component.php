<?php
class Kwc_FormDynamic_Basic_Form_Paragraphs_TextField_Component extends Kwc_Form_Field_TextField_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_FormDynamic_Basic_Form_Paragraphs_TextField_TestModel';
        return $ret;
    }
}
<?php
class Kwc_Trl_FormDynamic_Form_Paragraphs_TextField_Trl_Component extends Kwc_Form_Field_TextField_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_FormDynamic_Form_Paragraphs_TextField_Trl_TestModel';
        return $ret;
    }
}

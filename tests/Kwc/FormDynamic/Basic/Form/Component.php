<?php
class Vpc_FormDynamic_Basic_Form_Component extends Vpc_Form_Dynamic_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paragraphs'] = 'Vpc_FormDynamic_Basic_Form_Paragraphs_Component';
        $ret['generators']['child']['component']['form'] = 'Vpc_FormDynamic_Basic_Form_Form_Component';
        $ret['ownModel'] = 'Vpc_FormDynamic_Basic_Form_TestModel';
        return $ret;
    }
}

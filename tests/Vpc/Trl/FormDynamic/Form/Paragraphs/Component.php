<?php
class Vpc_Trl_FormDynamic_Form_Paragraphs_Component extends Vpc_Form_Dynamic_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_FormDynamic_Form_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'textField' => 'Vpc_Trl_FormDynamic_Form_Paragraphs_TextField_Component',
        );
        return $ret;
    }
}
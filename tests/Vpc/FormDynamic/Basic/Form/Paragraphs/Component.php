<?php
class Vpc_FormDynamic_Basic_Form_Paragraphs_Component extends Vpc_Form_Dynamic_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_FormDynamic_Basic_Form_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'textField' => 'Vpc_FormDynamic_Basic_Form_Paragraphs_TextField_Component',
            'checkbox' => 'Vpc_FormDynamic_Basic_Form_Paragraphs_Checkbox_Component',
            'file' => 'Vpc_FormDynamic_Basic_Form_Paragraphs_File_Component',
            'multiCheckbox' => 'Vpc_FormDynamic_Basic_Form_Paragraphs_MultiCheckbox_Component',
        );
        return $ret;
    }
}
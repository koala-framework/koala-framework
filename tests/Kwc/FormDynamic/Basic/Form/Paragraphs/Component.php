<?php
class Kwc_FormDynamic_Basic_Form_Paragraphs_Component extends Kwc_Form_Dynamic_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_FormDynamic_Basic_Form_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'textField' => 'Kwc_FormDynamic_Basic_Form_Paragraphs_TextField_Component',
            'checkbox' => 'Kwc_FormDynamic_Basic_Form_Paragraphs_Checkbox_Component',
            'file' => 'Kwc_FormDynamic_Basic_Form_Paragraphs_File_Component',
            'multiCheckbox' => 'Kwc_FormDynamic_Basic_Form_Paragraphs_MultiCheckbox_Component',
        );
        return $ret;
    }
}
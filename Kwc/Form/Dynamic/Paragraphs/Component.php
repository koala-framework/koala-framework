<?php
class Kwc_Form_Dynamic_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array_merge(
            array(
                'textField' => 'Kwc_Form_Field_TextField_Component',
                'checkbox' => 'Kwc_Form_Field_Checkbox_Component',
                'textArea' => 'Kwc_Form_Field_TextArea_Component',
                'fieldSet' => 'Kwc_Form_Container_FieldSet_Component',
                'select' => 'Kwc_Form_Field_Select_Component',
                'radio' => 'Kwc_Form_Field_Radio_Component',
                'multiCheckbox' => 'Kwc_Form_Field_MultiCheckbox_Component',
                'file' => 'Kwc_Form_Field_File_Component',
            ),
            $ret['generators']['paragraphs']['component']
        );
        if (isset($ret['generators']['paragraphs']['component']['form'])) {
            unset($ret['generators']['paragraphs']['component']['form']);
        }
        return $ret;
    }
}

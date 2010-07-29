<?php
class Vpc_Form_Dynamic_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array_merge(
            array(
                'textField' => 'Vpc_Form_Field_TextField_Component',
                'checkbox' => 'Vpc_Form_Field_Checkbox_Component',
                'textArea' => 'Vpc_Form_Field_TextArea_Component',
                'fieldSet' => 'Vpc_Form_Container_FieldSet_Component',
                'select' => 'Vpc_Form_Field_Select_Component',
                'radio' => 'Vpc_Form_Field_Radio_Component',
                'file' => 'Vpc_Form_Field_File_Component'
            ),
            $ret['generators']['paragraphs']['component']
        );
        if (isset($ret['generators']['paragraphs']['component']['form'])) {
            unset($ret['generators']['paragraphs']['component']['form']);
        }
        return $ret;
    }
}

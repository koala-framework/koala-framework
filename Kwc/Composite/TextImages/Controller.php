<?php
class Kwc_Composite_TextImages_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_ComboBox('image_position', 'Position of Images'))
            ->setValues(array('left' => 'Left', 'right' => 'Right', 'alternate' => 'Alternate'))
            ->setTriggerAction('all')
            ->setEditable(false);
    }
}

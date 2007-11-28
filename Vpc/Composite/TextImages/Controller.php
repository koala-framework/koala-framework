<?php
class Vpc_Composite_TextImages_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_ComboBox('image_position', 'Position of Images'))
            ->setValues(array('left' => 'Left', 'right' => 'Right', 'alternate' => 'Alternate'))
            ->setTriggerAction('all')
            ->setEditable(false);
    }
}
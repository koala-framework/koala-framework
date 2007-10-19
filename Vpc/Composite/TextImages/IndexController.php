<?php
class Vpc_Composite_TextImages_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vps_Auto_Vpc_Form($this->component);
        $this->_form->setBodyStyle('padding: 10px');
        $this->_form->add(new Vps_Auto_Field_ComboBox('image_position', 'Position of Images'))
            ->setValues(array('left' => 'Left', 'right' => 'Right', 'alternate' => 'Alternate'))
            ->setTriggerAction('all')
            ->setEditable(false);
    }
}
<?php
class Vpc_Formular_Checkbox_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('text', trlVps('Text')))
            ->setWidth(150);
        $this->_form->add(new Vps_Form_Field_Checkbox('maxlength', trlVps('Maximum Length')));
        $this->_form->add(new Vps_Form_Field_TextField('value', trlVps('Default Value')))
            ->setWidth(150);
    }
}

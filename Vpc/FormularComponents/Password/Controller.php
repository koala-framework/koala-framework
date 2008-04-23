<?php
class Vpc_Formular_Password_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('width', trlVps('Width')))
            ->setWidth(50);
        $this->_form->add(new Vps_Form_Field_TextField('maxlength', trlVps('Maximum Length')))
            ->setWidth(50);
    }
}

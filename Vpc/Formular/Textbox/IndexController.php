<?php
class Vpc_Formular_Textbox_IndexController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    protected function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('width', 'Width'))
            ->setWidth(50);
        $this->_form->add(new Vps_Auto_Field_TextField('maxlength', 'Maximum Length'))
            ->setWidth(50);
        $this->_form->add(new Vps_Auto_Field_TextField('value', 'Default Value'))
            ->setWidth(150);
        $this->_form->add(new Vps_Auto_Field_ComboBox('validator', 'Validator'))
            ->setStore(array ('data' => array(array('',                     'No Validator'),
                                        array('Zend_Validate_EmailAddress', 'E-Mail'),
                                        array('Zend_Validate_Date',         'Date'))))
            ->setEditable(false)
            ->setTriggerAction('all');
    }
}

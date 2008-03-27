<?php
class Vpc_Formular_Textbox_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    protected function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('width', trlVps('Width')))
            ->setWidth(50);
        $this->_form->add(new Vps_Auto_Field_TextField('maxlength', trlVps('Maximum Length')))
            ->setWidth(50);
        $this->_form->add(new Vps_Auto_Field_TextField('value', trlVps('Default Value')))
            ->setWidth(150);
        $this->_form->add(new Vps_Auto_Field_ComboBox('validator', trlVps('Validator')))
            ->setStore(array ('data' => array(array('',                     trlVps('No Validator')),
                                        array('Zend_Validate_EmailAddress', trlVps('E-Mail')),
                                        array('Zend_Validate_Date',         trlVps('Date')))))
            ->setEditable(false)
            ->setTriggerAction('all');
    }
}

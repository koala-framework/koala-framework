<?php
class Vpc_User_Register_Form_Form extends Vpc_User_Edit_Form_Form
{
    protected function _init()
    {
        parent::_init();
        $this->fields['email']->addValidator(new Vpc_User_Register_Form_ValidateEMail());
    }
}

<?php
class Vpc_User_LostPassword_Form_UserEMail extends Vps_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setVtype('email');
    }
    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Vpc_User_LostPassword_Form_ValidateEMail());
    }
}

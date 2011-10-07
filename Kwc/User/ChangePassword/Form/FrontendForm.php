<?php
class Kwc_User_ChangePassword_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_Password('old_password', trlKwf('Current Password')))
            ->addValidator(new Kwf_Validate_UserPassword())
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_DoublePassword('new_password', trlKwf('New Password')));
    }

}

<?php
class Vpc_User_ChangePassword_Form_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_Password('old_password', trlVps('Current Password')))
            ->addValidator(new Vps_Validate_UserPassword())
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_DoublePassword('new_password', trlVps('New Password')));
    }

}

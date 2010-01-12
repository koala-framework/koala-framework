<?php
class Vpc_User_DeleteAccount_Form_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_Password('old_password', trlVps('Your Password')))
            ->addValidator(new Vps_Validate_UserPassword())
            ->setAllowBlank(false);
    }

}

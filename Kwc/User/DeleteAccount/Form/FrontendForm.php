<?php
class Kwc_User_DeleteAccount_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_Password('old_password', trlKwfStatic('Your Password')))
            ->addValidator(new Kwf_Validate_UserPassword())
            ->setAllowBlank(false);
    }

}

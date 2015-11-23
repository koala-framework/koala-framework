<?php
class Kwc_User_ChangePassword_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_Password('old_password', trlKwfStatic('Current Password')))
            ->addValidator(new Kwf_Validate_UserPassword())
            ->setAllowBlank(false);

        $newPasswordField = new Kwf_Form_Field_DoublePassword('new_password', trlKwfStatic('New Password'));
        $validatorClass = Kwf_Registry::get('config')->user->passwordValidator;
        if ($validatorClass) {
            $newPasswordField->getChildren()->getByName('new_password')->addValidator(new $validatorClass());
        }
        $this->add($newPasswordField);
    }

}

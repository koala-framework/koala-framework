<?php
class Kwc_User_Activate_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $passwordField = $this->add(new Kwf_Form_Field_DoublePassword('password', trlKwfStatic('Password')));

        $validatorClass = Kwf_Registry::get('config')->user->passwordValidator;
        if ($validatorClass) {
            $passwordField->getChildren()->getByName('password')->addValidator(new $validatorClass());
        }
    }
}

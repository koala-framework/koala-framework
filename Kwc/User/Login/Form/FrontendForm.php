<?php
class Kwc_User_Login_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_model = new Kwf_Model_FnF();

        $this->add(new Kwf_Form_Field_TextField('email', trlKwfStatic('E-Mail')))
                    ->setVtype('email')
                    ->setAutofocus(true)
                    ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_Password('password', trlKwfStatic('Password')))
                    ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_Checkbox('auto_login', trlKwfStatic('Auto Login')));
    }
}

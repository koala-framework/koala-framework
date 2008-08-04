<?php
class Vpc_User_Login_Formular_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_model = new Vps_Model_FnF();

        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
                    ->setAllowBlank(false)
                    ->setVType('email');

        $this->add(new Vps_Form_Field_Password('password', trlVps('Password')));
    }
}

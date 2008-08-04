<?php
class Vpc_User_Login_Formular_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_DoublePassword('password', trlVps('Password'));
    }

}

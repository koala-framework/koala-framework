<?php
class Vpc_User_Activate_Form_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_DoublePassword('password', trlVps('Password')));
    }

}

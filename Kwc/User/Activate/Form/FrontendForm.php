<?php
class Kwc_User_Activate_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_DoublePassword('password', trlKwf('Password')));
    }

}

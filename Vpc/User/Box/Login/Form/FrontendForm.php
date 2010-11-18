<?php
class Vpc_User_Box_Login_Form_FrontendForm extends Vpc_User_Login_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->fields['email']->setLabelAlign('top');
        $this->fields['password']->setLabelAlign('top');
    }
}

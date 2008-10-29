<?php
class Vpc_User_Box_Login_Form_Form extends Vpc_User_Login_Form_Form
{
    protected function _init()
    {
        parent::_init();
        $this->fields['email']->setLabelAlign('top');
        $this->fields['password']->setLabelAlign('top');
    }
}

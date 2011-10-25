<?php
class Kwc_User_Box_Login_Form_FrontendForm extends Kwc_User_Login_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->fields['email']->setLabelAlign('top');
        $this->fields['password']->setLabelAlign('top');
    }
}

<?php
class Vps_Component_Plugin_Password_LoginForm_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Form();
        $this->_form->setModel(new Vps_Model_FnF());
        $this->_form->add(new Vps_Form_Field_Password('login_password', trlVps('Password')));
    }
}

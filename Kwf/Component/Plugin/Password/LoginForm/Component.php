<?php
class Kwf_Component_Plugin_Password_LoginForm_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        $ret['useAjaxRequest'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Kwf_Form();
        $this->_form->setModel(new Kwf_Model_FnF());
        $this->_form->add(new Kwf_Form_Field_Password('login_password', trlKwfStatic('Password')));
    }
}

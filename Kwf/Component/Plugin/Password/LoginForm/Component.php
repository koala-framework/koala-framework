<?php
class Kwf_Component_Plugin_Password_LoginForm_Component extends Kwc_Form_NonAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Kwf_Form();
        $this->_form->setModel(new Kwf_Model_FnF());
        $this->_form->add(new Kwf_Form_Field_Password('login_password', trlKwfStatic('Password')));
    }
}

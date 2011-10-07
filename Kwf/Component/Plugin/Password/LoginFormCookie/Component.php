<?php
class Kwf_Component_Plugin_Password_LoginFormCookie_Component extends Kwf_Component_Plugin_Password_LoginForm_Component
{
    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->add(new Kwf_Form_Field_Checkbox('save_cookie'))
            ->setBoxLabel(trlKwf('Remember password on this computer'));
    }
}

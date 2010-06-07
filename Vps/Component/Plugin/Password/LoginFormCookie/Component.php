<?php
class Vps_Component_Plugin_Password_LoginFormCookie_Component extends Vps_Component_Plugin_Password_LoginForm_Component
{
    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->add(new Vps_Form_Field_Checkbox('save_cookie'))
            ->setBoxLabel(trlVps('Remember password on this computer'));
    }
}

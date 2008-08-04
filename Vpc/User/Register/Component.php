<?php
class Vpc_User_Register_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['standardRole'] = 'guest';
        $ret['placeholder']['submitButton'] = trlVps('create account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Register_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vpc_User_Register_Form();
        $this->_form->fields['email']->addValidator(new Vpc_User_Register_ValidateEMail());
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        $row->role = $this->_getSetting('standardRole');
    }
}

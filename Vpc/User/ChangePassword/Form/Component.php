<?php
class Vpc_User_ChangePassword_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Change Password');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_ChangePassword_Form_Success_Component';
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Vps_Model_FnF());
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        $user->setPassword($this->_form->getRow()->new_password);
        $user->save();
    }
}
